<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\Integrations\Jetpack_CRM\Includes\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting contact tags
 *
 * @since 1.0.0
 */

function automatorwp_jetpack_crm_ajax_get_contact_tags() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );
    
    // Predefined jetpack crm type
    $type_id = ZBS_TYPE_CONTACT;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    $tags = automatorwp_jetpack_crm_get_tags( $type_id );

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
add_action( 'wp_ajax_automatorwp_jetpack_crm_get_contact_tags', 'automatorwp_jetpack_crm_ajax_get_contact_tags' );

/**
 * Ajax function for selecting company tags
 *
 * @since 1.0.0
 */

function automatorwp_jetpack_crm_ajax_get_company_tags() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );
    
    // Predefined jetpack crm type
    $type_id = ZBS_TYPE_COMPANY;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( sanitize_text_field( $_REQUEST['q'] ) ) : '';

    $tags = automatorwp_jetpack_crm_get_tags( $type_id );

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
add_action( 'wp_ajax_automatorwp_jetpack_crm_get_company_tags', 'automatorwp_jetpack_crm_ajax_get_company_tags' );


