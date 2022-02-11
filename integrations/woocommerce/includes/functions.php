<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Integrations\WooCommerce\Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Helper function to get the address fields
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_woocommerce_get_address_fields() {

    return array(
        'first_name',
        'last_name',
        'company',
        'address_1',
        'address_2',
        'city',
        'postcode',
        'country',
        'state',
        'phone',
        'email',
    );

}

/**
 * Helper function to get an address field label
 *
 * @since 1.0.0
 *
 * @param string $field
 *
 * @return string
 */
function automatorwp_woocommerce_get_address_field_label( $field ) {

    $labels = array(
        'first_name' => __( 'First name', 'automatorwp' ),
        'last_name' => __( 'Last name', 'automatorwp' ),
        'company' => __( 'Company', 'automatorwp' ),
        'address_1' => __( 'Address line 1', 'automatorwp' ),
        'address_2' => __( 'Address line 2', 'automatorwp' ),
        'city' => __( 'City', 'automatorwp' ),
        'postcode' => __( 'Postcode / ZIP', 'automatorwp' ),
        'country' => __( 'Country / Region', 'automatorwp' ),
        'state' => __( 'State / County', 'automatorwp' ),
        'phone' => __( 'Phone', 'automatorwp' ),
        'email' => __( 'Email', 'automatorwp' ),
    );

    return isset( $labels[$field] ) ? $labels[$field] : '';

}

/**
 * Helper function to get an address field preview
 *
 * @since 1.0.0
 *
 * @param string $field
 *
 * @return string
 */
function automatorwp_woocommerce_get_address_field_preview( $field ) {

    $previews = array(
        'first_name' => 'AutomatorWP',
        'last_name' => __( 'Plugin', 'automatorwp' ),
        'company' => __( 'AutomatorWP Ltd.', 'automatorwp' ),
        'address_1' => __( 'False Street, 123', 'automatorwp' ),
        'address_2' => __( 'First floor, door 2', 'automatorwp' ),
        'city' => __( 'Brooklyn', 'automatorwp' ),
        'postcode' => __( '12345', 'automatorwp' ),
        'country' => __( 'United States', 'automatorwp' ),
        'state' => __( 'New York', 'automatorwp' ),
        'phone' => __( '202-555-1234', 'automatorwp' ),
        'email' => __( 'contact@automatorwp.com', 'automatorwp' ),
    );

    return isset( $previews[$field] ) ? $previews[$field] : '';

}