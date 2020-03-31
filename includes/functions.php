<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Generates the required HTML with the dashicon provided
 *
 * @since 1.0.0
 *
 * @param string $dashicon      Dashicon class
 * @param string $tag           Optional, tag used (recommended i or span)
 *
 * @return string
 */
function automatorwp_dashicon( $dashicon = 'automatorwp', $tag = 'i' ) {

    return '<' . $tag . ' class="dashicons dashicons-' . $dashicon . '"></' . $tag . '>';

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

    $cache = automatorwp_get_cache( 'installed_tables', array() );

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