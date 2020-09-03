<?php
/**
 * 1.1.6 Upgrades
 *
 * @package     AutomatorWP\Admin\Upgrades\1.1.6
 * @since       1.1.6
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Return 1.1.6 as last required upgrade
 *
 * @return string
 */
function automatorwp_116_is_last_required_upgrade() {

    return '1.1.6';

}
add_filter( 'automatorwp_get_last_required_upgrade', 'automatorwp_116_is_last_required_upgrade', 116 );

/**
 * Process 1.1.6 upgrades
 *
 * @param string $stored_version
 *
 * @return string
 */
function automatorwp_116_upgrades( $stored_version ) {

    // Already upgrade
    if ( version_compare( $stored_version, '1.1.6', '>=' ) ) {
        return $stored_version;
    }

    // Ensure that AutomatorWP tables have been created
    if( automatorwp_database_table_exists( AutomatorWP()->db->automations ) ) {
        // Process 1.1.6 upgrade
        automatorwp_process_116_upgrade();

        // There is nothing to update, so upgrade
        $stored_version = '1.1.6';
    }

    return $stored_version;

}
add_filter( 'automatorwp_process_upgrades', 'automatorwp_116_upgrades', 116 );

/**
 * Process 1.1.6 upgrades
 */
function automatorwp_process_116_upgrade() {

    global $wpdb;

    ignore_user_abort( true );
    set_time_limit( 0 );

    // Bail if AutomatorWP tables haven't been created yet
    if( ! automatorwp_database_table_exists( AutomatorWP()->db->automations ) ) {
        return;
    }

    // Setup tables to update
    $tables = array(
        AutomatorWP()->db->automations,
        AutomatorWP()->db->automations_meta,
        AutomatorWP()->db->triggers,
        AutomatorWP()->db->triggers_meta,
        AutomatorWP()->db->actions,
        AutomatorWP()->db->actions_meta,
        AutomatorWP()->db->logs,
        AutomatorWP()->db->logs_meta,
    );
    foreach( $tables as $table ) {
        // Alter table to use InnoDB
        $wpdb->query( "ALTER TABLE {$table} ENGINE = InnoDB;" );
    }

    // Updated stored version
    update_option( 'automatorwp_version', '1.1.6' );

}