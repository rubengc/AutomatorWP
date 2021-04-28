<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\Paid_Memberships_Pro\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting contents
 *
 * @since 1.0.0
 */
function automatorwp_paid_memberships_pro_ajax_get_memberships() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    // Get the contents
    $results = $wpdb->get_results( $wpdb->prepare(
        "SELECT m.id AS id, m.name AS text
        FROM {$wpdb->pmpro_membership_levels} AS m
        WHERE m.name LIKE %s",
        "%%{$search}%%"
    ) );

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_paid_memberships_pro_get_memberships', 'automatorwp_paid_memberships_pro_ajax_get_memberships' );