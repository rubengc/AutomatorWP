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

/**
 * Get the log object data
 *
 * @param int       $log_id         The log ID
 * @param string    $output         Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which correspond to
 *                                  a object, an associative array, or a numeric array, respectively. Default OBJECT.
 * @return array|stdClass|null
 */
function automatorwp_get_log_object( $log_id, $output = OBJECT ) {

    ct_setup_table( 'automatorwp_logs' );

    $log = ct_get_object( $log_id );

    ct_reset_setup_table();

    return $log;

}

/**
 * Get the log object data
 *
 * @param int       $log_id         The log ID
 * @param string    $meta_key       Optional. The meta key to retrieve. By default, returns
 *                                  data for all keys. Default empty.
 * @param bool      $single         Optional. Whether to return a single value. Default false.
 *
 * @return mixed                    Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function automatorwp_get_log_meta( $log_id, $meta_key = '', $single = false ) {

    ct_setup_table( 'automatorwp_logs' );

    $meta_value = ct_get_object_meta( $log_id, $meta_key, $single );

    ct_reset_setup_table();

    return $meta_value;

}