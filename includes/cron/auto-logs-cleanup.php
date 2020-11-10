<?php
/**
 * Auto Logs Cleanup
 *
 * @package     AutomatorWP\Cron\Auto_Logs_Cleanup
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register schedule events
 *
 * @since 1.3.2
 */
function automatorwp_auto_logs_cleanup_schedule_events() {

    if ( function_exists( 'as_schedule_recurring_action' ) ) {

        // Action scheduler support
        if ( ! as_next_scheduled_action( 'automatorwp_auto_logs_cleanup_event' ) ) {
            as_schedule_recurring_action( time(), DAY_IN_SECONDS, 'automatorwp_auto_logs_cleanup_event' );
        }

    } else {

        // WP Cron
        if ( ! wp_next_scheduled( 'automatorwp_auto_logs_cleanup_event' ) ) {
            wp_schedule_event( time(), 'daily', 'automatorwp_auto_logs_cleanup_event' );
        }

    }

}
add_action( 'automatorwp_schedule_events', 'automatorwp_auto_logs_cleanup_schedule_events' );

/**
 * Clear scheduled events
 *
 * @since 1.3.2
 */
function automatorwp_auto_logs_cleanup_clear_scheduled_events() {
    wp_clear_scheduled_hook( 'automatorwp_auto_logs_cleanup_event' );
}
add_action( 'automatorwp_clear_scheduled_events', 'automatorwp_auto_logs_cleanup_clear_scheduled_events' );

/**
 * Process the auto logs cleanup
 *
 * @since 1.3.2
 */
function automatorwp_auto_logs_cleanup() {

    global $wpdb;

    $days = absint( automatorwp_get_option( 'auto_logs_cleanup_days', '' ) );

    // Bail if no days configured
    if( $days === 0 ) {
        return;
    }

    // Setup vars
    $date = date( 'Y-m-d', strtotime( "-{$days} day", current_time( 'timestamp' ) ) );
    $logs = AutomatorWP()->db->logs;
    $logs_ids = array();
    $automations = array();

    // Triggers logs
    $results = $wpdb->get_results( "SELECT l.id, l.object_id, l.user_id, l.date FROM {$logs} AS l WHERE l.type = 'trigger' AND l.date < '{$date}'" );

    foreach( $results as $log ) {

        $trigger = automatorwp_get_trigger_object( $log->object_id );

        // If trigger not found, add the log to being removed
        if( ! $trigger ) {
            $logs_ids[] = $log->id;
            continue;
        }

        $trigger_args = automatorwp_get_trigger( $trigger->type );

        // Anonymous triggers get removed directly
        if( $trigger_args['anonymous'] ) {
            $logs_ids[] = $log->id;
            continue;
        }

        $automation_id = absint( $trigger->automation_id );

        // Check if the automation has not been handled yet by this function
        if( ! isset( $automations[$automation_id] ) )  {
            $last_completion = automatorwp_get_user_last_completion( $automation_id, $log->user_id, 'automation' );

            $automations[$automation_id] = ( $last_completion ? $last_completion->date : false );
        }

        // Bail if user has not completed this automation yet
        if( $automations[$automation_id] === false ) {
            continue;
        }

        // If user has completed the automation, mark the log to being removed
        if( strtotime( $automations[$automation_id] ) >= strtotime( $log->date ) ) {
            $logs_ids[] = $log->id;
        }
    }

    // Remove all logs marked to get removed
    if( ! empty( $logs_ids ) ) {
        $wpdb->query( "DELETE FROM {$logs} WHERE id IN ( " . implode( ', ', $logs_ids ) . " )" );
    }

    // Actions and anonymous logs
    // They don't need any specific check so is safe to remove them directly
    $wpdb->query( "DELETE FROM {$logs} WHERE type IN ( 'action', 'anonymous' ) AND date < '{$date}'" );

    /**
     * Available action to let other plugins process anything after the logs cleanup
     *
     * @since 1.3.2
     *
     * @param string $date Date from logs has been removed
     */
    do_action( 'automatorwp_auto_logs_cleanup_finished', $date );

}
add_action( 'automatorwp_auto_logs_cleanup_event', 'automatorwp_auto_logs_cleanup' );