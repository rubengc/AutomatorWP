<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\Groundhogg\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting contents
 *
 * @since 1.0.0
 */
function automatorwp_groundhogg_ajax_get_tags() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    $tags = Groundhogg\Plugin::$instance->dbs->get_db( 'tags' )->search( $search );
    $results = array();

    // Sort by tag name
    usort( $tags, Groundhogg\sort_by_string_in_array( 'tag_name' ) );

    // Parse tags results to match select2 results
    foreach ( $tags as $i => $tag ) {
        $results[] = array(
            'id'   => $tag->tag_id,
            'text' => $tag->tag_name
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_groundhogg_get_tags', 'automatorwp_groundhogg_ajax_get_tags' );