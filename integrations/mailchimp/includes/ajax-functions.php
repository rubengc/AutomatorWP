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
 * Handler to save OAuth credentials
 *
 * @since 1.0.0
 */
function automatorwp_mailchimp_ajax_save_oauth_credentials() {

    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    $prefix = "automatorwp_mailchimp_";

    /* sanitize incoming data */
    $api_key = sanitize_text_field( $_POST["api_key"] );
    $server_prefix = sanitize_text_field( $_POST["server_prefix"] );

    if( $api_key == '' && $server_prefix == '' ) {
        // return error one of the field is missing
        
        wp_send_json_error();
    } else {
        try {

            // Mailchimp API
            require_once AUTOMATORWP_MAILCHIMP_DIR . 'vendor/autoload.php';
            
            $mailchimp = new MailchimpMarketing\ApiClient();

            $mailchimp->setConfig([
              'apiKey' => $api_key,
              'server' => $server_prefix
            ]);
            
            $response = $mailchimp->ping->get();
        
            $credentials = get_option( 'automatorwp_settings' );

            $credentials[$prefix . 'api_key'] = $api_key;
            $credentials[$prefix . 'server_prefix'] = $server_prefix;
            $credentials[$prefix . 'access_valid'] = true; 
            $credentials = array_filter( $credentials );

            update_option( 'automatorwp_settings', $credentials );
            wp_send_json_success();

        } catch(Exception $e) {
            
            wp_send_json_error();
        }
    }

}
add_action( 'wp_ajax_automatorwp_mailchimp_save_oauth_credentials', 'automatorwp_mailchimp_ajax_save_oauth_credentials' );

/**
 * Handler to delete OAuth credentials
 *
 * @since 1.0.0
 */
function automatorwp_mailchimp_ajax_delete_oauth_credentials() {

    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    $prefix = "automatorwp_mailchimp_";
    $credentials = get_option( 'automatorwp_settings' );

    $credentials[$prefix . 'api_key'] = null;
    $credentials[$prefix . 'server_prefix'] = null;
    $credentials[$prefix . 'access_valid'] = false;
    $credentials = array_filter( $credentials );

    update_option( 'automatorwp_settings', $credentials );

    wp_send_json_success();

}
add_action( 'wp_ajax_automatorwp_mailchimp_delete_oauth_credentials', 'automatorwp_mailchimp_ajax_delete_oauth_credentials' );


/**
 * Ajax function for selecting lists
 *
 * @since 1.0.0
 */
function automatorwp_mailchimp_ajax_get_lists() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    $lists = automatorwp_mailchimp_get_lists();
    
    $results = array();

    // Parse lists results to match select2 results
    foreach ( $lists as $list ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $list['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        $results[] = array(
            'id'   => $list['id'],
            'text' => $list['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_mailchimp_get_lists', 'automatorwp_mailchimp_ajax_get_lists' );

/**
 * Ajax function for selecting tags
 *
 * @since 1.0.0
 */
function automatorwp_mailchimp_ajax_get_tags() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );
    
    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    // Get Audience ID
    $list_id = isset( $_REQUEST['table'] ) ? sanitize_text_field( $_REQUEST['table'] ) : '';
    
    $tags = automatorwp_mailchimp_get_tags( $list_id );

    $results = array();

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
add_action( 'wp_ajax_automatorwp_mailchimp_get_tags', 'automatorwp_mailchimp_ajax_get_tags' );

/**
 * Ajax function for selecting campaigns
 *
 * @since 1.0.0
 */
function automatorwp_mailchimp_ajax_get_campaigns() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    $campaigns = automatorwp_mailchimp_get_campaigns();
    
    $results = array();

    // Parse campaigns results to match select2 results
    foreach ( $campaigns as $campaign ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $campaign['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        $results[] = array(
            'id'   => $campaign['id'],
            'text' => $campaign['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_mailchimp_get_campaigns', 'automatorwp_mailchimp_ajax_get_campaigns' );


/**
 * Ajax function for selecting templates
 *
 * @since 1.0.0
 */
function automatorwp_mailchimp_ajax_get_templates() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    $templates = automatorwp_mailchimp_get_templates();
    
    $results = array();

    // Parse templates results to match select2 results
    foreach ( $templates as $template ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $template['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        $results[] = array(
            'id'   => $template['id'],
            'text' => $template['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_mailchimp_get_templates', 'automatorwp_mailchimp_ajax_get_templates' );