<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\Thrive_Leads\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting forms
 *
 * @since 1.0.0
 */
function automatorwp_thrive_leads_ajax_get_forms() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    $results = array();

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    // Get the forms
    $forms = automatorwp_thrive_leads_get_forms( );

    foreach( $forms as $form ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $form['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        // Results should meet the Select2 structure
        $results[] = array(
            'id' => $form['id'],
            'text' => $form['name'],
        );

    }

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_thrive_leads_get_forms', 'automatorwp_thrive_leads_ajax_get_forms', 5 );