<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Advance_Custom_Fields\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get acf fields related to posts
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_advanced_custom_fields_options_cb_fields_posts() {

    $options = array(
        'any' => __( 'any field', 'automatorwp-advanced-custom-fields' ),
    );
    
    // Get all post types
    $all_post_types = acf_get_post_types( );

    foreach ( $all_post_types as $post_type ) {

        $args_acf = array(
            'post_type' => $post_type,
        );

        // Get groups related to posts
        $all_post_groups = acf_get_field_groups( $args_acf );
        
        foreach( $all_post_groups as $group ) {
        
            // Get fields from group
            $all_acf_fields = acf_get_fields( $group['ID'] );

            foreach ( $all_acf_fields as $acf_fields ){

                $options[$acf_fields['name']] = $acf_fields['label'];
            }

        }

    }
    
    return $options;

}

/**
 * Get acf fields related to users
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_advanced_custom_fields_options_cb_fields_users() {

    $options = array(
        'any' => __( 'any field', 'automatorwp' ),
    );
    
    $args = array(
        'user_form' => 'all',
    );
    
    // Get groups related to users
    $all_user_groups = acf_get_field_groups( $args );
    
    foreach( $all_user_groups as $group ) {
    
        // Get fields from group
        $all_acf_fields = acf_get_fields( $group['ID'] );

        foreach ( $all_acf_fields as $acf_fields ){

            $options[$acf_fields['name']] = $acf_fields['label'];
        }

    }
    
    return $options;

}