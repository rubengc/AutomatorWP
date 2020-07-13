<?php
/**
 * Log
 *
 * @package     AutomatorWP\Log
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get log registered types
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_get_log_types() {

    return apply_filters( 'automatorwp_log_tupes', array(
        'automation' => __( 'Automation', 'automatorwp' ),
        'trigger' => __( 'Trigger', 'automatorwp' ),
        'action' => __( 'Action', 'automatorwp' ),
    ) );

}

/**
 * Insert a new log
 *
 * @since 1.0.0
 *
 * @param array $log_data   The log data to insert
 * @param array $log_meta   The log meta data to insert
 *
 * @return int|WP_Error     The log ID on success. The value 0 or WP_Error on failure.
 */
function automatorwp_insert_log( $log_data = array(), $log_meta = array() ) {

    global $wpdb;

    $log_data = wp_parse_args( $log_data, array(
        'title'     => '',
        'type'      => '',
        'object_id' => 0,
        'user_id'   => 0,
        'post_id'   => 0,
        'date'      => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
    ) );

    $ct_table = ct_setup_table( 'automatorwp_logs' );

    // Store log entry
    $log_id = $ct_table->db->insert( $log_data );

    // If log correctly inserted, insert all meta data received
    if( $log_id && ! empty( $log_meta ) ) {

        $metas = array();

        foreach( $log_meta as $meta_key => $meta_value ) {
            // Sanitize vars
            $meta_key = sanitize_key( $meta_key );
            $meta_key = wp_unslash( $meta_key );
            $meta_value = wp_unslash( $meta_value );
            $meta_value = sanitize_meta( $meta_key, $meta_value, $ct_table->name );
            $meta_value = maybe_serialize( $meta_value );

            // Setup the insert value
            $metas[] = "{$log_id}, '{$meta_key}', '{$meta_value}'";
        }

        $logs_meta = AutomatorWP()->db->logs_meta;
        $metas = implode( '), (', $metas );

        // Since the log is recently inserted, is faster to run a single query to insert all metas instead of insert them one-by-one
        $wpdb->query( "INSERT INTO {$logs_meta} (id, meta_key, meta_value) VALUES ({$metas})" );

    }

    ct_reset_setup_table();

    // Flush cache to prevent meta data cached values
    wp_cache_flush();

    return $log_id;

}