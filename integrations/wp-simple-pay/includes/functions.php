<?php
/**
 * Functions
 *
 * @package     AutomatorWP\WP_Simple_Pay\Includes\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get forms from WP Simple Pay
 *
 * @since 1.0.0
 * 
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_simple_pay_options_cb_form( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any form', 'automatorwp-wp-simple-pay' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );
    
    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $form_id ) {

            // Skip option none
            if( $form_id === $none_value ) {
                continue;
            }
            
            $options[$form_id] = automatorwp_simple_pay_get_form_name( $form_id );
        }
    }

    return $options;

}

/**
 * Get WP Simple Pay forms
 *
 * @since 1.0.0
 *
 * @return array|false
 */
function automatorwp_simple_pay_get_forms() {

    $forms = array();

    $args = array( 
        'numberposts' => -1,
        'post_type' => 'simple-pay',
    );

    $wp_simple_pay_posts = get_posts( $args );
    
    foreach ( $wp_simple_pay_posts as $post ){   

        $forms[] = array(
            'id'    => $post->ID,
            'name'  => get_post_meta( $post->ID, '_company_name', true ),
        );         

    }

    return $forms;

}

/**
 * Get WP Simple Pay form name
 *
 * @since 1.0.0
 *
 * @param int    $form_id         ID form
 * 
 */
function automatorwp_simple_pay_get_form_name( $form_id ) {
    
    return get_post_meta( $form_id, '_company_name', true );

}