<?php
/**
 * Install
 *
 * @package     AutomatorWP\Install
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * AutomatorWP installation
 *
 * @since 1.0.0
 */
function automatorwp_install() {

    // Setup default AutomatorWP installation date
    $automatorwp_install_date = ( $exists = get_option( 'automatorwp_install_date' ) ) ? $exists : '';

    if ( empty( $automatorwp_install_date ) ) {
        update_option( 'automatorwp_install_date', date( 'Y-m-d H:i:s' ) );
    }

    // Register AutomatorWP custom DB tables
    automatorwp_register_custom_tables();

    // Schedule events
    automatorwp_schedule_events();

}
