<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\ClickUp\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * AJAX handler for the authorize action
 *
 * @since 1.0.0
 */
function automatorwp_clickup_ajax_authorize() {
    // Security check
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    $prefix = 'automatorwp_clickup_';

    $url = automatorwp_clickup_get_url();
    $token = sanitize_text_field( $_POST['token'] );
   
    // Check parameters given
    if( empty( $token ) ) {
        wp_send_json_error( array( 'message' => __( 'API Token is required to connect with ClickUp', 'automatorwp' ) ) );
        return;
    }

    // To get first answer and check the connection
    $response = wp_remote_get( $url . '/team', array(
        'headers' => array(
            'Authorization' => $token,
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        )
    ) );

    // Incorrect API token
    if ( isset( $response['response']['code'] ) && $response['response']['code'] !== 200 ){
        wp_send_json_error (array( 'message' => __( 'Please, check your credentials', 'automatorwp' ) ) );
        return;
    }

    $settings = get_option( 'automatorwp_settings' );

    // Save client url and API key
    $settings[$prefix . 'token'] = $token;

    // Update settings
    update_option( 'automatorwp_settings', $settings );
    $admin_url = str_replace( 'http://', 'http://', get_admin_url() )  . 'admin.php?page=automatorwp_settings&tab=opt-tab-clickup';
   
    wp_send_json_success( array(
        'message' => __( 'Correct data to connect with ClickUp', 'automatorwp' ),
        'redirect_url' => $admin_url
    ) );

}
add_action( 'wp_ajax_automatorwp_clickup_authorize',  'automatorwp_clickup_ajax_authorize' );


/**
 * Ajax function for selecting teams
 *
 * @since 1.0.0
 */
function automatorwp_clickup_ajax_get_teams() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    $teams = automatorwp_clickup_get_teams();
    
    $results = array();

    // Parse teams results to match select2 results
    foreach ( $teams as $team ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $team['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        $results[] = array(
            'id'   => $team['id'],
            'text' => $team['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_clickup_get_teams', 'automatorwp_clickup_ajax_get_teams' );

/**
 * Ajax function for selecting spaces
 *
 * @since 1.0.0
 */
function automatorwp_clickup_ajax_get_spaces() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    // Get Team ID
    $team_id = isset( $_REQUEST['table'] ) ? sanitize_text_field( $_REQUEST['table'] ) : '';

    $spaces = automatorwp_clickup_get_spaces( $team_id );
    
    $results = array();

    // Parse spaces results to match select2 results
    foreach ( $spaces as $space ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $space['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        $results[] = array(
            'id'   => strval($space['id']),
            'text' => $space['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_clickup_get_spaces', 'automatorwp_clickup_ajax_get_spaces' );

/**
 * Ajax function for selecting folders
 *
 * @since 1.0.0
 */
function automatorwp_clickup_ajax_get_folders() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    // Get Space ID
    $space_id = isset( $_REQUEST['table'] ) ? sanitize_text_field( $_REQUEST['table'] ) : '';

    $folders = automatorwp_clickup_get_folders( $space_id );
    
    $results = array();

    // Parse folders results to match select2 results
    foreach ( $folders as $folder ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $folder['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        $results[] = array(
            'id'   => strval($folder['id']),
            'text' => $folder['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_clickup_get_folders', 'automatorwp_clickup_ajax_get_folders' );

/**
 * Ajax function for selecting lists
 *
 * @since 1.0.0
 */
function automatorwp_clickup_ajax_get_lists() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    // Get Folder ID
    $folder_id = isset( $_REQUEST['table'] ) ? sanitize_text_field( $_REQUEST['table'] ) : '';

    $lists = automatorwp_clickup_get_lists( $folder_id );
    
    $results = array();

    // Parse lists results to match select2 results
    foreach ( $lists as $list ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $list['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        $results[] = array(
            'id'   => strval($list['id']),
            'text' => $list['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_clickup_get_lists', 'automatorwp_clickup_ajax_get_lists' );

/**
 * Ajax function for selecting tasks
 *
 * @since 1.0.0
 */
function automatorwp_clickup_ajax_get_tasks() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    // Get List ID
    $list_id = isset( $_REQUEST['table'] ) ? sanitize_text_field( $_REQUEST['table'] ) : '';

    $tasks = automatorwp_clickup_get_tasks( $list_id );
    
    $results = array();

    // Parse tasks results to match select2 results
    foreach ( $tasks as $task ) {

        if( ! empty( $search ) ) {
            if( strpos( strtolower( $task['name'] ), strtolower( $search ) ) === false ) {
                continue;
            }
        }

        $results[] = array(
            'id'   => strval($task['id']),
            'text' => $task['name']
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_clickup_get_tasks', 'automatorwp_clickup_ajax_get_tasks' );