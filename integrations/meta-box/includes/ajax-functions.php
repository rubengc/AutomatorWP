<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\Meta_Box\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting tags
 *
 * @since 1.0.0
 */
function automatorwp_meta_box_ajax_get_post_fields() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    $post_fields = automatorwp_meta_box_get_post_fields( );

    $results = array();

    // Parse tag results to match select2 results
    foreach ( $post_fields as $post_field ) {

        $results[] = array(
            'id' => $post_field['id'],
            'text' => $post_field['title']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_meta_box_get_post_fields', 'automatorwp_meta_box_ajax_get_post_fields' );