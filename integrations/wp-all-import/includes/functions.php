<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Integrations\WP_All_Import\Includes\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get history data for tags
 *
 * @since 1.0.0
 *
 * @return array|false
 */
function automatorwp_wp_all_import_get_services( $import_id ) {

    global $wpdb;
    $history_data = array( );

    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}pmxi_history WHERE import_id = {$import_id} ORDER BY id DESC LIMIT 1" );

    foreach ( $results as $history ){   

        $history_data[] = array(
            'id'        => $history->id,
            'type'      => $history->type,
            'time_run'  => $history->time_run,
            'date'      => $history->date,
            'summary'   => $history->summary
        );         

    }

    return $history_data;

}

