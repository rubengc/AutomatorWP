<?php
/**
 * Upgrades
 *
 * @package     AutomatorWP\Admin\Upgrades
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.1.6
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once AUTOMATORWP_DIR . 'includes/admin/upgrades/1.1.6.php';
require_once AUTOMATORWP_DIR . 'includes/admin/upgrades/1.3.0.php';

/**
 * AutomatorWP upgrades
 *
 * @since 1.1.6
 */
function automatorwp_process_upgrades() {

    if ( ! current_user_can( automatorwp_get_manager_capability() ) ) {
        return;
    }

    // Get stored version
    $stored_version = get_option( 'automatorwp_version', '1.0.0' );

    if( $stored_version === AUTOMATORWP_VER ) {
        return;
    }

    /**
     * Before process upgrades action
     *
     * @since 1.1.6
     *
     * @param string $stored_version Latest upgrade version
     */
    do_action( 'automatorwp_before_process_upgrades', $stored_version );

    /**
     * Version upgrade filter
     *
     * @since 1.1.6
     *
     * @param string $stored_version Latest upgrade version
     *
     * @return string
     */
    $stored_version = apply_filters( 'automatorwp_process_upgrades', $stored_version );

    /**
     * After process upgrades action
     *
     * @since 1.1.6
     *
     * @param string $stored_version Latest upgrade version
     */
    do_action( 'automatorwp_after_process_upgrades', $stored_version );

    // Updated stored version
    update_option( 'automatorwp_version', $stored_version );

}
add_action( 'admin_init', 'automatorwp_process_upgrades' );

/**
 * Get the latest AutomatorWP version that requires an upgrade
 *
 * @since 1.5.1
 *
 * @return string   Last version that required an upgrade
 */
function automatorwp_get_last_required_upgrade() {

    $version = '1.0.0';

    /**
     * Get the last required upgrade (useful to meet if version stored and current required is the same)
     *
     * @since 1.5.1
     *
     * @param string $stored_version Latest upgrade version
     *
     * @return string
     */
    return apply_filters( 'automatorwp_get_last_required_upgrade', $version );

}

/**
 * Helper function to check if AutomatorWP has been upgraded successfully
 *
 * @since 1.1.6
 *
 * @param string $desired_version
 *
 * @return bool
 */
function is_automatorwp_upgraded_to( $desired_version = '1.0.0' ) {

    // Get stored version
    $stored_version = get_option( 'automatorwp_version', '1.0.0' );

    return (bool) version_compare( $stored_version, $desired_version, '>=' );

}

/**
 * Get's the array of completed upgrade actions
 *
 * @since  1.1.6
 *
 * @return array The array of completed upgrades
 */
function automatorwp_get_completed_upgrades() {

    $completed_upgrades = get_option( 'automatorwp_completed_upgrades' );

    if ( false === $completed_upgrades ) {
        $completed_upgrades = array();
    }

    return $completed_upgrades;

}

/**
 * Check if the upgrade routine has been run for a specific action
 *
 * @since  1.1.6
 *
 * @param  string $upgrade_action The upgrade action to check completion for
 *
 * @return bool                   If the action has been added to the completed actions array
 */
function is_automatorwp_upgrade_completed( $upgrade_action = '' ) {

    if ( empty( $upgrade_action ) ) {
        return false;
    }

    $completed_upgrades = automatorwp_get_completed_upgrades();

    return in_array( $upgrade_action, $completed_upgrades );

}

/**
 * Adds an upgrade action to the completed upgrades array
 *
 * @since  1.1.6
 *
 * @param  string $upgrade_action The action to add to the completed upgrades array
 *
 * @return bool                   If the function was successfully added
 */
function automatorwp_set_upgrade_complete( $upgrade_action = '' ) {

    if ( empty( $upgrade_action ) ) {
        return false;
    }

    $completed_upgrades   = automatorwp_get_completed_upgrades();
    $completed_upgrades[] = $upgrade_action;

    // Remove any blanks, and only show uniques
    $completed_upgrades = array_unique( array_values( $completed_upgrades ) );

    return update_option( 'automatorwp_completed_upgrades', $completed_upgrades );
}

/**
 * Utility function to check if a database table exists
 *
 * @since   1.0.1
 *
 * @param  string $table_name The desired table name
 *
 * @return bool               Whatever if table exists or not
 */
function automatorwp_database_table_exists( $table_name ) {

    global $wpdb;

    $cache = automatorwp_get_cache( 'installed_tables', array(), false );

    // If result already cached, return it
    if( isset( $cache[$table_name] ) ) {
        return $cache[$table_name];
    }

    $table_exist = $wpdb->get_var( $wpdb->prepare(
        "SHOW TABLES LIKE %s",
        $wpdb->esc_like( $table_name )
    ) );

    if( empty( $table_exist ) ) {
        $table_exist = $wpdb->get_var( $wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $wpdb->esc_like( $wpdb->prefix . $table_name )
        ) );
    }

    if( empty( $table_exist ) ) {
        $table_exist = $wpdb->get_var( $wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $wpdb->esc_like( $wpdb->base_prefix . $table_name )
        ) );
    }

    // Cache function result
    $cache[$table_name] = ( ! empty( $table_exist ) );

    automatorwp_set_cache( 'installed_tables', $cache );

    return ! empty( $table_exist );

}

/**
 * Utility function to check if a database table has a specific field
 *
 * @since 1.4.7
 *
 * @param  string $table_name   The desired table name
 * @param  string $column_name  The desired column name
 *
 * @return bool                 Whatever if table exists and has this column or not
 */
function automatorwp_database_table_has_column( $table_name, $column_name ) {

    global $wpdb;

    $cache = automatorwp_get_cache( 'installed_table_columns', array(), false );

    // If result already cached, return it
    if( isset( $cache[$table_name] ) && isset( $cache[$table_name][$column_name] ) ) {
        return $cache[$table_name][$column_name];
    }

    if( ! automatorwp_database_table_exists( $table_name ) ) {
        return false;
    }

    $column_exists = $wpdb->get_var( $wpdb->prepare(
        "SHOW COLUMNS FROM {$table_name} LIKE %s",
        $wpdb->esc_like( $column_name )
    ) );

    // Check if already cached any column from this table, if not, initialize it
    if( ! isset( $cache[$table_name] ) ) {
        $cache[$table_name] = array();
    }

    // Cache function result
    $cache[$table_name][$column_name] = ( ! empty( $column_exists ) );

    automatorwp_set_cache( 'installed_table_columns', $cache );

    return ! empty( $column_exists );

}