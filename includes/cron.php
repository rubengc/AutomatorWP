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