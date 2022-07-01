<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\Autonami\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting contents
 *
 * @since 1.0.0
 */
function automatorwp_autonami_ajax_get_tags() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    $tags = automatorwp_autonami_get_tags();

    // Parse tag results to match select2 results
    foreach ( $tags as $tag ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $tag['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }
        
        $results[] = array(
            'id' => $tag['id'],
            'text' => $tag['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_autonami_get_tags', 'automatorwp_autonami_ajax_get_tags' );

/**
 * Ajax function for selecting contents
 *
 * @since 1.0.0
 */
function automatorwp_autonami_ajax_get_lists() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    $lists = automatorwp_autonami_get_lists();

    // Parse list results to match select2 results
    foreach ( $lists as $list ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $list['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }
        
        $results[] = array(
            'id' => $list['id'],
            'text' => $list['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_autonami_get_lists', 'automatorwp_autonami_ajax_get_lists' );