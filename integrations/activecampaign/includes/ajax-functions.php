<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\ActiveCampaign\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * AJAX handler for the authorize action
 *
 * @since 1.0.0
 */
function automatorwp_activecampaign_ajax_authorize() {
    // Security check
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    $prefix = 'automatorwp_activecampaign_';

    $url = sanitize_text_field( $_POST['url'] );
    $key = sanitize_text_field( $_POST['key'] );
   
    // Check parameters given
    if( empty( $url ) || empty( $key ) ) {
        wp_send_json_error( array( 'message' => __( 'All fields are required to connect with ActiveCampaign', 'automatorwp' ) ) );
        return;
    }

    // To get first answer and check the connection
    $response = wp_remote_get( $url . '/api/3', array(
        'headers' => array(
            'Accept' => 'application/json',
            'Api-Token' => $key,
            'Content-Type'  => 'application/json'
        ),
        'sslverify' => false
    ) );

    // Incorrect URL or API key
    if ( isset( $response->errors ) ){
        wp_send_json_error (array( 'message' => __( 'Please, check your credentials', 'automatorwp' ) ) );
        return;
    }

    $settings = get_option( 'automatorwp_settings' );

    // Save client url and API key
    $settings[$prefix . 'url'] = $url;
    $settings[$prefix . 'key'] = $key;

    // Update settings
    update_option( 'automatorwp_settings', $settings );
    $admin_url = str_replace( 'http://', 'http://', get_admin_url() )  . 'admin.php?page=automatorwp_settings&tab=opt-tab-activecampaign';
   
    wp_send_json_success( array(
        'message' => __( 'Correct data to connect with ActiveCampaign', 'automatorwp' ),
        'redirect_url' => $admin_url
    ) );

}
add_action( 'wp_ajax_automatorwp_activecampaign_authorize',  'automatorwp_activecampaign_ajax_authorize' );


/**
 * Set the default URL value
 *
 * @since 1.0.0
 *
 * @return string
 */
function automatorwp_activecampaign_ajax_refresh( ) {
    // Security check
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    $prefix = 'automatorwp_activecampaign_';

    // Get random characters for slug
    $slug = strtolower( wp_generate_password( 8, false ) );

    $settings = get_option( 'automatorwp_settings' );

    $settings[$prefix . 'webhook'] = get_rest_url() . 'activecampaign/webhooks/' . $slug;
    $settings[$prefix . 'slug'] = $slug;
    update_option( 'automatorwp_settings', $settings);

    $admin_url = str_replace( 'http://', 'http://', get_admin_url() )  . 'admin.php?page=automatorwp_settings&tab=opt-tab-activecampaign';
   
    wp_send_json_success( array(
        'message' => __( 'Webhook URL refreshed', 'automatorwp' ),
        'redirect_url' => $admin_url
    ) );
    
}

add_action( 'wp_ajax_automatorwp_activecampaign_refresh',  'automatorwp_activecampaign_ajax_refresh' );

/**
 * Ajax function for selecting tags
 *
 * @since 1.0.0
 */
function automatorwp_activecampaign_ajax_get_tags() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );
    
    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';
    $page = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;

    $tags = automatorwp_activecampaign_get_tags( $search, $page );

    $results = array();

    // Parse tag results to match select2 results
    foreach ( $tags as $tag ) {
        
        $results[] = array(
            'id' => $tag['id'],
            'text' => $tag['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    $response = array(
        'results' => $results,
        'more_results' => count( $results ),
    );

    // Return our results
    wp_send_json_success( $response );
    die;

}
add_action( 'wp_ajax_automatorwp_activecampaign_get_tags', 'automatorwp_activecampaign_ajax_get_tags' );