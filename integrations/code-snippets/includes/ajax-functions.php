<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\Code_Snippets\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting snippets
 *
 * @since 1.0.0
 */
function automatorwp_code_snippets_ajax_get_snippets() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );
    
    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    $snippets = automatorwp_code_snippets_get_snippets( );

    $results = array();

    // Parse snippet results to match select2 results
    foreach ( $snippets as $snippet ) {
        
        $results[] = array(
            'id' => $snippet['id'],
            'text' => $snippet['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_code_snippets_get_snippets', 'automatorwp_code_snippets_ajax_get_snippets' );