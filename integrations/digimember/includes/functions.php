<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Digimember\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Options callback for select2 fields assigned to products
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_digimember_options_cb_product( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any product', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $product_id ) {

            // Skip option none
            if( $product_id === $none_value ) {
                continue;
            }

            $options[$product_id] = automatorwp_digimember_get_product_title( $product_id );
        }
    }

    return $options;

}

/**
 * Get the product title
 *
 * @since 1.0.0
 *
 * @param int $product_id
 *
 * @return string|null
 */
function automatorwp_digimember_get_product_title( $product_id ) {

    // Empty title if no ID provided
    if( absint( $product_id ) === 0 ) {
        return '';
    }

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT p.name FROM {$wpdb->prefix}digimember_product AS p WHERE p.id = %d",
        $product_id
    ) );

}