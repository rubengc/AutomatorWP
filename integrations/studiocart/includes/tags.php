<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Studiocart\Tags
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Order tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_studiocart_order_tags() {

    return array(
        'order_id' => array(
            'label'     => __( 'Order ID', 'automatorwp' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'order_amount' => array(
            'label'     => __( 'Order amount', 'automatorwp' ),
            'type'      => 'float',
            'preview'   => '123.45',
        ),
        'order_status' => array(
            'label'     => __( 'Order status', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'completed',
        ),
    );

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
function automatorwp_studiocart_get_trigger_order_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'studiocart' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'order_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'order_id', true );
            break;
        case 'order_amount':
            $replacement = automatorwp_get_log_meta( $log->id, 'order_amount', true );
            break;      
        case 'order_status':
            $replacement = automatorwp_get_log_meta( $log->id, 'order_status', true );
            break;       
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_studiocart_get_trigger_order_tag_replacement', 10, 6 );

/**
 * Order tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_studiocart_product_tags() {

    return array(
        'product_id' => array(
            'label'     => __( 'Product ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => '123',
        ),
        'product_name' => array(
            'label'     => __( 'Product Name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'Product name',
        ),
    );

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
function automatorwp_studiocart_get_trigger_product_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'studiocart' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'product_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'product_id', true );
            break;
        case 'product_name':
            $replacement = automatorwp_get_log_meta( $log->id, 'product_name', true );
            break;           
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_studiocart_get_trigger_product_tag_replacement', 10, 6 );

