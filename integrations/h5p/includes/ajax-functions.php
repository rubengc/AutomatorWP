<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\H5P\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting contents
 *
 * @since 1.0.0
 */
function automatorwp_h5p_ajax_get_contents() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    // Get the contents
    $results = $wpdb->get_results( $wpdb->prepare(
        "SELECT c.id AS id, c.title AS text
        FROM {$wpdb->prefix}h5p_contents AS c
        WHERE c.title LIKE %s",
        "%%{$search}%%"
    ) );

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_h5p_get_contents', 'automatorwp_h5p_ajax_get_contents', 5 );