<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Meta_Box\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get meta box fields related to posts
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_meta_box_options_cb_post_fields( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any field', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );
    
    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $field_id ) {

            // Skip option none
            if( $field_id === $none_value ) {
                continue;
            }
            
            $options[$field_id] = automatorwp_meta_box_get_field_name( $field_id );
        }
    }

    return $options;

}

/**
 * Get meta box fields related to posts
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_meta_box_get_post_fields( ) {
    
    if ( ! function_exists( 'rwmb_get_object_fields' ) ) {
        return array();
    }

    $options = array();
    $post_fields = rwmb_get_object_fields( 'post' );

    foreach ( $post_fields as $post_field ) {

        if ( ! empty( $post_field['id'] ) && ! empty( $post_field['name'] ) ) {

            $options[] = array(
                'id' => $post_field['id'],
                'title'  => $post_field['name'],
            );

        }
    }

    return $options;

}

/**
 * Get meta box field name related to posts
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_meta_box_get_field_name( $field_id ) {

    $post_fields = rwmb_get_object_fields( 'post' );

    foreach ( $post_fields as $post_field ) {

        if ( ! empty( $post_field['id'] ) && ! empty( $post_field['name'] ) ) {

            if ( $post_field['id'] === $field_id ){
                $field_name = $post_field['name'];
            }

        }
    }

    return $field_name;
}