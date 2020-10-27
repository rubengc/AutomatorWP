<?php
/**
 * 1.3.0 Upgrades
 *
 * @package     AutomatorWP\Admin\Upgrades\1.3.0
 * @since       1.3.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return 1.3.0 as last required upgrade
 *
 * @return string
 */
function automatorwp_130_is_last_required_upgrade() {

    return '1.3.0';

}
add_filter( 'automatorwp_get_last_required_upgrade', 'automatorwp_130_is_last_required_upgrade', 130 );

/**
 * Process 1.3.0 upgrades
 *
 * @param string $stored_version
 *
 * @return string
 */
function automatorwp_130_upgrades( $stored_version ) {

    // Already upgrade
    if ( version_compare( $stored_version, '1.3.0', '>=' ) ) {
        return $stored_version;
    }

    // Ensure that AutomatorWP tables have been created
    if( automatorwp_database_table_exists( AutomatorWP()->db->automations ) ) {
        // Process 1.3.0 upgrade
        automatorwp_process_130_upgrade();

        // There is nothing to update, so upgrade
        $stored_version = '1.3.0';
    }

    return $stored_version;

}
add_filter( 'automatorwp_process_upgrades', 'automatorwp_130_upgrades', 130 );

/**
 * Process 1.3.0 upgrades
 */
function automatorwp_process_130_upgrade() {

    global $wpdb;

    ignore_user_abort( true );
    set_time_limit( 0 );

    // Bail if AutomatorWP tables haven't been created yet
    if( ! automatorwp_database_table_exists( AutomatorWP()->db->automations ) ) {
        return;
    }

    $automations = AutomatorWP()->db->automations;

    // Update old automations type
    $wpdb->query( "UPDATE {$automations} AS a SET a.type = 'user' WHERE a.type = ''" );

    // Updated stored version
    update_option( 'automatorwp_version', '1.3.0' );

}