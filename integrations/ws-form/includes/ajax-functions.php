<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\WS_Form\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting forms
 *
 * @since 1.0.0
 */
function automatorwp_ws_form_ajax_get_forms() {

    global $wpdb;

    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    $results = array();

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';
    $search = strtolower( $search );

    // Get the forms
    $forms = wsf_form_get_all($published = false, $order_by = 'label');

    foreach( $forms as $form ) {

        if( $search && ( strpos( strtolower( $form['label'] ), $search) === false) ) {
            continue;
        }

        // Results should meet the Select2 structure
        $results[] = array(
            'id' => $form['id'],
            'text' => $form['label'],
        );

    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_ws_form_get_forms', 'automatorwp_ws_form_ajax_get_forms', 5 );