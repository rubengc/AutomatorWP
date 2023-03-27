<?php
/**
 * Functions
 *
 * @package     AutomatorWP\JetEngine\Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get JetEngine post type
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_jetengine_options_cb_post_type( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any type', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );
    
    $post_types_obj = new Jet_Engine_CPT;
    $post_types = $post_types_obj->get_items();

    foreach ( $post_types as $post_type ) {
        
        if ( ! empty( $post_type['id'] ) && ! empty( $post_type['slug'] ) ) {

            $options[$post_type['slug']] = $post_type['labels']['name'];

        }
    }

    return $options;

}

/**
 * Check JetEngine post type
 *
 * @since 1.0.0
 * 
 * @param object $post  Post data
 *
 * @return bool
 */
function automatorwp_jetengine_check_type( $post ) {

    $array_types = array();

    // Get JetEngine post types
    $post_types_obj = new Jet_Engine_CPT;
    $post_types = $post_types_obj->get_items();

    foreach( $post_types as $post_type ) {
        $array_types[] = $post_type['slug'];
    }

    // Bail if the post is not a JetEngine type
    if ( !in_array( $post->post_type, $array_types ) ){
        return false;
    }

    return true;
}