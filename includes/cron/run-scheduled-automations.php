<?php
/**
 * Run Scheduled Automations
 *
 * @package     AutomatorWP\Cron\Run_Scheduled_Automations
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register schedule events
 *
 * @since 2.2.2
 */
function automatorwp_run_scheduled_automations_schedule_events() {

    if ( function_exists( 'as_schedule_recurring_action' ) ) {

        // Action scheduler support
        if ( ! as_next_scheduled_action( 'automatorwp_run_scheduled_automations_event' ) ) {
            as_schedule_recurring_action( time(), 60 * 5, 'automatorwp_run_scheduled_automations_event' );
        }

    } else {

        // WP Cron
        if ( ! wp_next_scheduled( 'automatorwp_run_scheduled_automations_event' ) ) {
            wp_schedule_event( time(), 'five_minutes', 'automatorwp_run_scheduled_automations_event' );
        }

    }

}
add_action( 'automatorwp_schedule_events', 'automatorwp_run_scheduled_automations_schedule_events' );

/**
 * Clear scheduled events
 *
 * @since 2.2.2
 */
function automatorwp_run_scheduled_automations_clear_scheduled_events() {
    wp_clear_scheduled_hook( 'automatorwp_run_scheduled_automations_event' );
}
add_action( 'automatorwp_clear_scheduled_events', 'automatorwp_run_scheduled_automations_clear_scheduled_events' );

/**
 * Process the run scheduled automations process
 *
 * @since 2.2.2
 */
function automatorwp_run_scheduled_automations() {

    global $wpdb;

    $datetime = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
    $automations = AutomatorWP()->db->automations;
    $automations_meta = AutomatorWP()->db->automations_meta;

    // Get scheduled automations
    $results = $wpdb->get_results( "
        SELECT a.id
        FROM {$automations} AS a 
        LEFT JOIN {$automations_meta} AS am1 ON ( am1.id = a.id AND am1.meta_key = 'schedule_run' )
        LEFT JOIN {$automations_meta} AS am2 ON ( am2.id = a.id AND am2.meta_key = 'recurring_run' )
        LEFT JOIN {$automations_meta} AS am3 ON ( am3.id = a.id AND am3.meta_key = 'next_run_date' )
        WHERE a.type IN ( 'all-users', 'all-posts' ) 
        AND a.status = 'active' 
        AND ( am1.meta_value = 'on' OR am2.meta_value = 'on' )
        AND am3.meta_value <= '{$datetime}'" );

    if( is_array( $results ) ) {
        foreach ( $results as $automation ) {
            // Run automations
            automatorwp_run_automation( $automation->id );
        }
    }

}
add_action( 'automatorwp_run_scheduled_automations_event', 'automatorwp_run_scheduled_automations' );