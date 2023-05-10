<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\MailerLite\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * AJAX handler for the authorize action
 *
 * @since 1.0.0
 */
function automatorwp_mailerlite_ajax_authorize() {
    // Security check
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    $prefix = 'automatorwp_mailerlite_';

    $url = automatorwp_mailerlite_get_url();
    $token = sanitize_text_field( $_POST['token'] );
   
    // Check parameters given
    if( empty( $token ) ) {
        wp_send_json_error( array( 'message' => __( 'API Token is required to connect with MailerLite', 'automatorwp-pro' ) ) );
        return;
    }

    // To get first answer and check the connection
    $response = wp_remote_get( $url . '/api/subscribers', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        ),
        'body' => array(
            'limit' => 1,
        )
    ) );

    // Incorrect API token
    if ( isset( $response['response']['code'] ) && $response['response']['code'] !== 200 ){
        wp_send_json_error (array( 'message' => __( 'Please, check your credentials', 'automatorwp-pro' ) ) );
        return;
    }

    $settings = get_option( 'automatorwp_settings' );

    // Save client url and API key
    $settings[$prefix . 'token'] = $token;

    // Update settings
    update_option( 'automatorwp_settings', $settings );
    $admin_url = str_replace( 'http://', 'https://', get_admin_url() )  . 'admin.php?page=automatorwp_settings&tab=opt-tab-mailerlite';
   
    wp_send_json_success( array(
        'message' => __( 'Correct data to connect with MailerLite', 'automatorwp-pro' ),
        'redirect_url' => $admin_url
    ) );

}
add_action( 'wp_ajax_automatorwp_mailerlite_authorize',  'automatorwp_mailerlite_ajax_authorize' );


/**
 * Ajax function for selecting groups
 *
 * @since 1.0.0
 */
function automatorwp_mailerlite_ajax_get_groups() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    $groups = automatorwp_mailerlite_get_groups();
    
    $results = array();

    // Parse groups results to match select2 results
    foreach ( $groups as $group ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $group['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        $results[] = array(
            'id'   => strval($group['id']),
            'text' => $group['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_mailerlite_get_groups', 'automatorwp_mailerlite_ajax_get_groups' );