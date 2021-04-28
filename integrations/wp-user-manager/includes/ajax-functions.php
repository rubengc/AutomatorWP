<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\WP_User_Manager\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting forms
 *
 * @since 1.0.0
 */
function automatorwp_wp_user_manager_ajax_get_forms() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    // Get the forms
    $results = $wpdb->get_results( $wpdb->prepare(
        "SELECT f.id AS id, f.name AS text
		FROM   {$wpdb->prefix}wpum_registration_forms AS f
		WHERE  f.name LIKE %s",
        "%%{$search}%%"
    ) );

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_wp_user_manager_get_forms', 'automatorwp_wp_user_manager_ajax_get_forms', 5 );