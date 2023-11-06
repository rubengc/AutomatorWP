<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Integrations\WooCommerce\Tags
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Custom tags
 *
 * @since 1.0.0
 *
 * @param array $tags The global tags
 *
 * @return array
 */
function automatorwp_woocommerce_get_tags( $tags ) {

    // Billing address
    $tags['woocommerce_billing'] = array(
        'label' => __( 'Customer Billing Address', 'automatorwp' ),
        'tags'  => array(),
        'icon'  => AUTOMATORWP_WOOCOMMERCE_URL . 'assets/woocommerce.svg',
    );

    foreach( automatorwp_woocommerce_get_address_fields() as $field ) {

        $tags['woocommerce_billing']['tags']['woocommerce_billing_' . $field] = array(
            'label'     => automatorwp_woocommerce_get_address_field_label( $field ),
            'type'      => 'text',
            'preview'   => automatorwp_woocommerce_get_address_field_preview( $field ),
        );

    }

    $tags['woocommerce_shipping'] = array(
        'label' => __( 'Customer Shipping Address', 'automatorwp' ),
        'tags'  => array(),
        'icon'  => AUTOMATORWP_WOOCOMMERCE_URL . 'assets/woocommerce.svg',
    );

    foreach( automatorwp_woocommerce_get_address_fields() as $field ) {

        $tags['woocommerce_shipping']['tags']['woocommerce_shipping_' . $field] = array(
            'label'     => automatorwp_woocommerce_get_address_field_label( $field ),
            'type'      => 'text',
            'preview'   => automatorwp_woocommerce_get_address_field_preview( $field ),
        );

    }

    return $tags;

}
add_filter( 'automatorwp_get_tags', 'automatorwp_woocommerce_get_tags' );

/**
 * Filter tags displayed on the tag selector
 *
 * @since 1.0.0
 *
 * @param array     $tags       The tags
 * @param stdClass  $automation The automation object
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 *
 * @return array
 */
function automatorwp_woocommerce_tags_selector_html_tags( $tags, $automation, $object, $item_type ) {

    // Remove tags on anonymous user action
    if( $automation->type === 'anonymous' && $object->type === 'automatorwp_anonymous_user' ) {
        if( isset( $tags['woocommerce_billing'] ) ) {
            unset( $tags['woocommerce_billing'] );
        }

        if( isset( $tags['woocommerce_shipping'] ) ) {
            unset( $tags['woocommerce_shipping'] );
        }
    }

    return $tags;

}
add_filter( 'automatorwp_tags_selector_html_tags', 'automatorwp_woocommerce_tags_selector_html_tags', 10, 4 );

/**
 * Custom tags replacement
 *
 * @since 1.0.0
 *
 * @param string    $replacement    The tag replacement
 * @param string    $tag_name       The tag name (without "{}")
 * @param int       $automation_id  The automation ID
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 *
 * @return string
 */
function automatorwp_woocommerce_get_tag_replacement( $replacement, $tag_name, $automation_id, $user_id, $content ) {

    $address_tags = array();

    // Setup an array with all address tags
    foreach( automatorwp_woocommerce_get_address_fields() as $field ) {
        $address_tags[] = 'woocommerce_billing_' . $field;
        $address_tags[] = 'woocommerce_shipping_' . $field;
    }

    // Bail if not is a custom tag
    if( ! in_array( $tag_name, $address_tags ) ) {
        return $replacement;
    }

    $meta_key = str_replace( 'woocommerce_', '', $tag_name );

    $replacement = get_user_meta( $user_id, $meta_key, true );

    // Format values for some tags
    switch( $meta_key ) {
        case 'billing_country':
        case 'shipping_country':
            $replacement = ( isset( WC()->countries->countries[$replacement] ) ) ? WC()->countries->countries[$replacement] : $replacement;
            break;
        case 'billing_state':
        case 'shipping_state':
            // For the state, is required the country
            if( $meta_key === 'billing_state' ) {
                $country = get_user_meta( $user_id, 'billing_country', true );
            } else {
                $country = get_user_meta( $user_id, 'shipping_country', true );
            }

            $replacement = ( isset( WC()->countries->states[$country][$replacement] ) ) ? WC()->countries->states[$country][$replacement] : $replacement;
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_tag_replacement', 'automatorwp_woocommerce_get_tag_replacement', 10, 5 );

/**
 * Order tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_woocommerce_order_tags() {

    $order_tags = array(
        'woocommerce_order_id' => array(
            'label'     => __( 'Order ID', 'automatorwp' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'woocommerce_order_number' => array(
            'label'     => __( 'Order Number', 'automatorwp' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'woocommerce_order_status' => array(
            'label'     => __( 'Order Status', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => __( 'Completed', 'automatorwp' ),
        ),
        'woocommerce_order_date_created' => array(
            'label'     => __( 'Order Date Created', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => date( 'Y-m-d H:i:s' ),
        ),
        'woocommerce_order_date_paid' => array(
            'label'     => __( 'Order Date Paid', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => date( 'Y-m-d H:i:s' ),
        ),
        'woocommerce_order_customer_ip_address' => array(
            'label'     => __( 'Order Customer IP Address', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => '255.255.255.255',
        ),
        'woocommerce_order_customer_note' => array(
            'label'     => __( 'Order Customer Note', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => __( 'The customer note entered at checkout', 'automatorwp' ),
        ),
        'woocommerce_order_coupon_codes' => array(
            'label'     => __( 'Order Coupon Codes', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'CODE01, CODE02',
        ),
        'woocommerce_order_currency' => array(
            'label'     => __( 'Order Currency', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'USD',
        ),
        'woocommerce_order_currency_symbol' => array(
            'label'     => __( 'Order Currency Symbol', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => '$',
        ),
        'woocommerce_order_subtotal' => array(
            'label'     => __( 'Order Subtotal', 'automatorwp' ),
            'type'      => 'float',
            'preview'   => '123.45',
        ),
        'woocommerce_order_discount_total' => array(
            'label'     => __( 'Order Discount', 'automatorwp' ),
            'type'      => 'float',
            'preview'   => '123.45',
        ),
        'woocommerce_order_total_tax' => array(
            'label'     => __( 'Order Tax', 'automatorwp' ),
            'type'      => 'float',
            'preview'   => '123.45',
        ),
        'woocommerce_order_total' => array(
            'label'     => __( 'Order Total', 'automatorwp' ),
            'type'      => 'float',
            'preview'   => '123.45',
        ),
        'woocommerce_order_meta:META_KEY' => array(
            'label'     => __( 'Order Meta', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => __( 'Order meta value, replace "META_KEY" by the order meta key', 'automatorwp' ),
        ),
    );

    foreach( automatorwp_woocommerce_get_address_fields() as $field ) {

        $order_tags['woocommerce_order_billing_' . $field] = array(
            'label'     => sprintf( __( 'Billing %s', 'automatorwp'  ), automatorwp_woocommerce_get_address_field_label( $field ) ),
            'type'      => 'text',
            'preview'   => automatorwp_woocommerce_get_address_field_preview( $field ),
        );

    }

    foreach( automatorwp_woocommerce_get_address_fields() as $field ) {

        $order_tags['woocommerce_order_shipping_' . $field] = array(
            'label'     => sprintf( __( 'Shipping %s', 'automatorwp'  ), automatorwp_woocommerce_get_address_field_label( $field ) ),
            'type'      => 'text',
            'preview'   => automatorwp_woocommerce_get_address_field_preview( $field ),
        );

    }

    /**
     * Filter order tags
     *
     * @since 1.0.0
     *
     * @param array $tags
     *
     * @return array
     */
    return apply_filters( 'automatorwp_woocommerce_order_tags', $order_tags );

}

/**
 * Custom trigger tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $replacement    The tag replacement
 * @param string    $tag_name       The tag name (without "{}")
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 * @param stdClass  $log            The last trigger log object
 *
 * @return string
 */
function automatorwp_woocommerce_get_trigger_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {

    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Bail if no order ID attached
    if( ! $trigger_args ) {
        return $replacement;
    }

    // Bail if trigger is not from this integration
    if( $trigger_args['integration'] !== 'woocommerce' ) {
        return $replacement;
    }

    $tags = array_keys( automatorwp_woocommerce_order_tags() );

    // Bail if not order tags found
    if( ! in_array( $tag_name, $tags ) ) {
        return $replacement;
    }

    $order_id = (int) automatorwp_get_log_meta( $log->id, 'order_id', true );

    // Bail if no order ID attached
    if( $order_id === 0 ) {
        return $replacement;
    }

    $order = wc_get_order( $order_id );

    // Bail if order can't be found
    if( ! $order ) {
        return $replacement;
    }

    $function_name = str_replace( 'woocommerce_order_', '', $tag_name );

    // Dynamically, call to a order function to retrieve the tag value
    if( method_exists( $order, 'get_' . $function_name ) ) {
        $replacement = call_user_func( array( $order, 'get_' . $function_name ) );
    }

    // Format values for some tags
    switch( $tag_name ) {
        case 'woocommerce_order_status':
            $replacement = wc_get_order_status_name( $replacement );
            break;
        case 'woocommerce_order_number':
            $replacement = $order->get_order_number();
            break;
        case 'woocommerce_order_date_created':
        case 'woocommerce_order_date_paid':
            $replacement = ( $replacement ? $replacement->format( 'Y-m-d H:i:s' ) : '' );
            break;
        case 'woocommerce_order_coupon_codes':
            $replacement = implode( ', ', $replacement );
            break;
        case 'woocommerce_order_currency_symbol':
            $replacement = get_woocommerce_currency_symbol( $order->get_currency() );
            break;
        case 'woocommerce_order_billing_country':
        case 'woocommerce_order_shipping_country':
            $replacement = ( isset( WC()->countries->countries[$replacement] ) ) ? WC()->countries->countries[$replacement] : $replacement;
            break;
        case 'woocommerce_order_billing_state':
        case 'woocommerce_order_shipping_state':
            // For the state, is required the country
            if( $tag_name === 'woocommerce_order_billing_state' ) {
                $country = $order->get_billing_country();
            } else {
                $country = $order->get_shipping_country();
            }

            $replacement = ( isset( WC()->countries->states[$country][$replacement] ) ) ? WC()->countries->states[$country][$replacement] : $replacement;
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_woocommerce_get_trigger_tag_replacement', 10, 6 );

/**
 * Order meta tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $replacement    The tag replacement
 * @param string    $tag_name       The tag name (without "{}")
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 * @param stdClass  $log            The last trigger log object
 *
 * @return string
 */
function automatorwp_woocommerce_get_order_meta_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {

    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Bail if no order ID attached
    if( ! $trigger_args ) {
        return $replacement;
    }

    // Bail if trigger is not from this integration
    if( $trigger_args['integration'] !== 'woocommerce' ) {
        return $replacement;
    }

    // Bail if not is the order meta tag
    if( substr( $tag_name, 0, 22 ) !== 'woocommerce_order_meta' ) {
        return $replacement;
    }

    $order_id = (int) automatorwp_get_log_meta( $log->id, 'order_id', true );

    // Bail if no order ID attached
    if( $order_id === 0 ) {
        return $replacement;
    }

    $meta_key = explode( ':', $tag_name );

    // Bail if meta key can't be found
    if( ! isset( $meta_key[1] ) ) {
        return $replacement;
    }

    $meta_key = $meta_key[1];

    $replacement = get_post_meta( $order_id, $meta_key, true );

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_woocommerce_get_order_meta_tag_replacement', 10, 6 );