<?php
/**
 * Cron
 *
 * @package     AutomatorWP\Cron
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once AUTOMATORWP_DIR . 'includes/cron/auto-logs-cleanup.php';
require_once AUTOMATORWP_DIR . 'includes/cron/run-scheduled-automations.php';

/**
 * Register custom cron schedules
 *
 * @since 1.0.0
 *
 * @param array $schedules
 *
 * @return array
 */
function automatorwp_cron_schedules( $schedules ) {

    $schedules['five_minutes'] = array(
        'interval' => 300,
        'display'  => __( 'Every five minutes', 'automatorwp' ),
    );

    return $schedules;

}
add_filter( 'cron_schedules', 'automatorwp_cron_schedules' );

/**
 * Register schedule events
 *
 * @since 1.3.2
 */
function automatorwp_schedule_events() {

    /**
     * Action triggered on activation to schedule events
     *
     * @since 1.3.2
     */
    do_action( 'automatorwp_schedule_events' );

}

/**
 * Clear scheduled events
 *
 * @since 1.3.2
 */
function automatorwp_clear_scheduled_events() {

    /**
     * Action triggered on deactivation to clear scheduled events
     *
     * @since 1.3.2
     */
    do_action( 'automatorwp_clear_scheduled_events' );

}