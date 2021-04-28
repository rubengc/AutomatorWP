<?php
/**
 * Functions
 *
 * @package     AutomatorWP\MailPoet\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Options callback for lists
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_mailpoet_lists_options_cb( $field ) {

    $none_value = '';
    $none_label = __( 'a list', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if ( ! class_exists( '\MailPoet\API\API' ) ) {
        return $options;
    }

    $mailpoet  = \MailPoet\API\API::MP( 'v1' );
    $lists = $mailpoet->getLists();

    if( is_array( $lists ) ) {
        foreach( $lists as $list ) {
            $options[$list['id']] = $list['name'];
        }
    }

    return $options;

}