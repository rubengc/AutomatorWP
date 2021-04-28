<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\Zoom\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * AJAX handler for the authorize action
 *
 * @since 1.0.0
 */
function automatorwp_zoom_ajax_authorize() {
    // Security check
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    $prefix = 'automatorwp_zoom_';

    $client_id = sanitize_text_field( $_POST['client_id'] );
    $client_secret = sanitize_text_field( $_POST['client_secret'] );
    $platform = sanitize_text_field( $_POST['platform'] );

    // Check parameters given
    if( empty( $client_id ) || empty( $client_secret ) ) {
        wp_send_json_error( array( 'message' => __( 'All fields are required to connect with Zoom', 'automatorwp' ) ) );
    }

    $settings = get_option( 'automatorwp_settings' );

    // Save client id and secret
    $settings[$prefix . $platform . '_client_id'] = $client_id;
    $settings[$prefix . $platform . '_client_secret'] = $client_secret;

    // Update settings
    update_option( 'automatorwp_settings', $settings );

    $state = $prefix . $platform;
    $admin_url = str_replace( 'http://', 'https://', get_admin_url() )  . 'admin.php?page=automatorwp_settings&tab=opt-tab-zoom';
    $redirect_url = 'https://zoom.us/oauth/authorize?response_type=code&client_id=' . $client_id . '&state=' . urlencode( $state ) . '&redirect_uri=' . urlencode( $admin_url );

    // Return the redirect URL
    wp_send_json_success( array(
        'message' => __( 'Zoom settings saved successfully, redirecting to Zoom...', 'automatorwp' ),
        'redirect_url' => $redirect_url
    ) );

}
add_action( 'wp_ajax_automatorwp_zoom_authorize',  'automatorwp_zoom_ajax_authorize' );

/**
 * Ajax function for selecting meetings
 *
 * @since 1.0.0
 */
function automatorwp_zoom_ajax_get_meetings() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    $meetings = automatorwp_zoom_get_meetings();

    // Parse meetings results to match select2 results
    foreach ( $meetings as $meeting ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $meeting['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        $results[] = array(
            'id'   => $meeting['id'],
            'text' => $meeting['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_zoom_get_meetings', 'automatorwp_zoom_ajax_get_meetings' );