<?php
/**
 * Actions
 *
 * @package     AutomatorWP\Actions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Registers a new action
 *
 * @since 1.0.0
 *
 * @param string    $action The action key
 * @param array     $args   The action arguments
 */
function automatorwp_register_action( $action, $args ) {

    $args = wp_parse_args( $args, array(
        'integration'       => '',
        'label'             => '',
        'select_option'     => '',
        'edit_label'        => '',
        'options'           => array(),
    ) );

    /**
     * Filter to extend registered action arguments
     *
     * @since 1.0.0
     *
     * @param string    $action The action key
     * @param array     $args   The action arguments
     *
     * @return array
     */
    $args = apply_filters( 'automatorwp_register_action_args', $args, $action );

    // Sanitize options setup
    foreach( $args['options'] as $option => $option_args ) {

        if( in_array( $option, array( 'action', 'nonce', 'id', 'item_type', 'option_name' ) ) ) {
            _doing_it_wrong( __FUNCTION__, sprintf( __( 'Action "%s" has the option key "%s" that is not allowed', 'automatorwp' ), $action, $option ), null );
            return;
        }

    }

    AutomatorWP()->actions[$action] = $args;

}

/**
 * Get registered actions
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_get_actions() {

    return AutomatorWP()->actions;

}

/**
 * Get an action
 *
 * @since 1.0.0
 *
 * @param string $action
 *
 * @return array|false
 */
function automatorwp_get_action( $action ) {

    return ( isset( AutomatorWP()->actions[$action] ) ? AutomatorWP()->actions[$action] : false );

}

/**
 * Get an integration actions
 *
 * @since 1.0.0
 *
 * @param string $integration
 *
 * @return array
 */
function automatorwp_get_integration_actions( $integration ) {

    $actions = array();

    foreach( AutomatorWP()->actions as $action => $args ) {

        if( $args['integration'] === $integration ) {
            $actions[$action] = $args;
        }

    }

    return $actions;

}

/**
 * Get the action object data
 *
 * @param int       $action_id      The action ID
 * @param string    $output         Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which correspond to
 *                                  a object, an associative array, or a numeric array, respectively. Default OBJECT.
 * @return array|stdClass|null
 */
function automatorwp_get_action_object( $action_id, $output = OBJECT ) {

    ct_setup_table( 'automatorwp_actions' );

    $action = ct_get_object( $action_id );

    ct_reset_setup_table();

    return $action;

}

/**
 * Get the action object data
 *
 * @param int       $action_id      The action ID
 * @param string    $meta_key       Optional. The meta key to retrieve. By default, returns
 *                                  data for all keys. Default empty.
 * @param bool      $single         Optional. Whether to return a single value. Default false.
 *
 * @return mixed                    Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function automatorwp_get_action_meta( $action_id, $meta_key = '', $single = false ) {

    ct_setup_table( 'automatorwp_actions' );

    $meta_value = ct_get_object_meta( $action_id, $meta_key, $single );

    ct_reset_setup_table();

    return $meta_value;

}

/**
 * Get the action's automation object
 *
 * @since 1.0.0
 *
 * @param int $action_id    The action ID
 *
 * @return stdClass|false   The automation object or false
 */
function automatorwp_get_action_automation( $action_id ) {

    $action = automatorwp_get_action_object( $action_id );

    if( ! $action ) {
        return false;
    }

    $automation = automatorwp_get_automation_object( $action->automation_id );

    return $automation;

}

/**
 * Get the action's stored options
 *
 * @since 1.0.0
 *
 * @param int   $action_id      The action ID
 * @param bool  $single_level   The level of options array, if is set to try, only option fields will be returned
 *
 * @return array                The action options.
 *                              Stored options format wit $single_level=true:
 *                              array(
 *                                  'field_id' => 'value'
 *                              )
 *                              Stored options format wit $single_level=false:
 *                              array(
 *                                  'option' => array(
 *                                      'field_id' => 'value'
 *                                  )
 *                              )
 */
function automatorwp_get_action_stored_options( $action_id, $single_level = true ) {

    $object = automatorwp_get_action_object( $action_id );

    if( ! $object ) {
        return array();
    }

    $action = automatorwp_get_action( $object->type );

    if( ! $action ) {
        return array();
    }

    ct_setup_table( 'automatorwp_actions' );

    $options = array();

    foreach( $action['options'] as $option => $option_args ) {

        if( ! isset( $option_args['fields'] ) ) {
            continue;
        }

        if( ! $single_level ) {
            $options[$option] = array();
        }

        foreach( $option_args['fields'] as $field_id => $field ) {

            $value = ct_get_object_meta( $object->id, $field_id, true );

            if( empty( $value ) && isset( $field['default'] ) ) {
                $value = $field['default'];
            }

            if( $single_level ) {
                $options[$field_id] = $value;
            } else {
                $options[$option][$field_id] = $value;
            }


        }

    }

    ct_reset_setup_table();

    return $options;

}