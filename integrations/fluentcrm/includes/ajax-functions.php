<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\FluentCRM\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting contents
 *
 * @since 1.0.0
 */
function automatorwp_fluentcrm_ajax_get_tags() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    $results = $wpdb->get_results( $wpdb->prepare(
        "SELECT t.id AS id, t.title AS text
		FROM   {$wpdb->prefix}fc_tags AS t
		WHERE  t.title LIKE %s",
        "%%{$search}%%"
    ) );

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_fluentcrm_get_tags', 'automatorwp_fluentcrm_ajax_get_tags' );

/**
 * Ajax function for selecting contents
 *
 * @since 1.0.0
 */
function automatorwp_fluentcrm_ajax_get_lists() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    $results = $wpdb->get_results( $wpdb->prepare(
        "SELECT t.id AS id, t.title AS text
		FROM   {$wpdb->prefix}fc_lists AS t
		WHERE  t.title LIKE %s",
        "%%{$search}%%"
    ) );

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_fluentcrm_get_lists', 'automatorwp_fluentcrm_ajax_get_lists' );