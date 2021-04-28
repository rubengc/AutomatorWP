<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\BuddyBoss\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting groups
 *
 * @since 1.0.0
 */
function automatorwp_buddyboss_ajax_get_groups() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    $results = array();

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';
    $page = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;

    if( bp_is_active( 'groups' ) ) {

        // Get the groups
        $groups = groups_get_groups( array(
            'search_terms' => $search,
            'show_hidden' => true,
            'per_page' => 20,
            'page' => $page
        ) );

        if( isset( $groups['groups'] ) && ! empty( $groups['groups'] ) ) {
            foreach ( $groups['groups'] as $group ) {

                // Results should meet Select2 structure
                $results[] = array(
                    'id' => $group->id,
                    'text' => $group->name,
                );

            }
        }

    }

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_buddyboss_get_groups', 'automatorwp_buddyboss_ajax_get_groups' );