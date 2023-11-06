<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\Integrations\Mailchimp\Ajax_Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting membership plans
 *
 * @since 1.0.0
 */
function automatorwp_armember_ajax_get_plans() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );
    
    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';
    
    $plans = automatorwp_armember_get_plan( );

    $results = array();

    // Parse tag results to match select2 results
    foreach ( $plans as $plan ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $plan['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }
        
        $results[] = array(
            'id' => $plan['id'],
            'text' => $plan['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_armember_get_plans', 'automatorwp_armember_ajax_get_plans' );