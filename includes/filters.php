<?php
/**
 * Filters
 *
 * @package     AutomatorWP\Filters
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Registers a new filter
 *
 * @since 1.0.0
 *
 * @param string    $filter The filter key
 * @param array     $args   The filter arguments
 */
function automatorwp_register_filter( $filter, $args ) {

    $args = wp_parse_args( $args, array(
        'integration'       => '',
        'label'             => '',
        'select_option'     => '',
        'edit_label'        => '',
        'log_label'         => '',
        'options'            => array(),
    ) );

    /**
     * Filter to extend registered filter arguments
     *
     * @since 1.0.0
     *
     * @param array     $args   The filter arguments
     * @param string    $filter The filter key
     *
     * @return array
     */
    $args = apply_filters( 'automatorwp_register_filter_args', $args, $filter );

    // Sanitize options setup
    foreach( $args['options'] as $option => $option_args ) {

        if( in_array( $option, array( 'trigger', 'action', 'filter', 'operator', 'nonce', 'id', 'item_type', 'option_name' ) ) ) {
            _doing_it_wrong( __FUNCTION__, sprintf( __( 'Filter "%s" has the option key "%s" that is not allowed', 'automatorwp' ), $filter, $option ), null );
            return;
        }

    }

    if( isset( AutomatorWP()->filters[$filter] ) ) {
        error_log( sprintf( __( 'Possible filter duplication with the key "%s"', 'automatorwp' ), $filter ) );
    }

    AutomatorWP()->filters[$filter] = $args;

}

/**
 * Get a filter
 *
 * @since 1.0.0
 *
 * @param string $filter
 *
 * @return array|false
 */
function automatorwp_get_filter( $filter ) {

    $filter_args = ( isset( AutomatorWP()->filters[$filter] ) ? AutomatorWP()->filters[$filter] : false );

    /**
     * Available filter to override the filter args
     *
     * @since 1.0.0
     *
     * @param array|false $filter_args
     * @param string $filter
     *
     * @return array|false
     */
    return apply_filters( 'automatorwp_get_filter', $filter_args, $filter );

}

/**
 * Get a trigger filters
 *
 * @since 1.0.0
 *
 * @param stdClass $trigger
 *
 * @return array
 */
function automatorwp_get_trigger_filters( $trigger ) {

    $triggers = automatorwp_get_automation_triggers( $trigger->automation_id );
    $filters = array();
    $found = false;

    foreach ( $triggers as $item ) {

        // Skip if we found the trigger
        if( absint( $item->id ) === absint( $trigger->id ) ) {
            $found = true;
            continue;
        }

        if( $found === false ) {
            continue;
        }

        // Skip if is not a filter
        if( $item->type !== 'filter' ) {
            $found = false;
            continue;
        }

        // Add to the filters list
        $filters[] = $item;

    }

    return $filters;

}

/**
 * Get an action filters
 *
 * @since 1.0.0
 *
 * @param stdClass $action
 *
 * @return array
 */
function automatorwp_get_action_filters( $action ) {

    $actions = automatorwp_get_automation_actions( $action->automation_id );
    $filters = array();
    $found = false;

    foreach ( $actions as $item ) {

        // Skip if we found the action
        if( absint( $item->id ) === absint( $action->id ) ) {
            $found = true;
            continue;
        }

        if( $found === false ) {
            continue;
        }

        // Skip if is not a filter
        if( $item->type !== 'filter' ) {
            $found = false;
            continue;
        }

        // Add to the filters list
        $filters[] = $item;

    }

    return $filters;

}

/**
 * Get the filter's stored options
 *
 * @since 1.0.0
 *
 * @param int       $filter_id      The filter ID
 * @param string    $item_type      The filter item type (trigger/action)
 * @param bool      $single_level   The level of options array, if is set to try, only option fields will be returned
 *
 * @return array                The trigger options.
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
function automatorwp_get_filter_stored_options( $filter_id, $item_type, $single_level = true ) {

    ct_setup_table( "automatorwp_{$item_type}s" );
    $object = ct_get_object( $filter_id );
    ct_reset_setup_table();

    if( ! $object ) {
        return array();
    }

    $filter = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $filter ) {
        return array();
    }

    ct_setup_table( "automatorwp_{$item_type}s" );

    $options = array();

    foreach( $filter['options'] as $option => $option_args ) {

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

            /**
             * Filters the option value for replacement on labels
             *
             * @since 1.0.0
             *
             * @param string    $value      The option value
             * @param stdClass  $object     The trigger/action object
             * @param string    $item_type  The item type (trigger|action)
             * @param string    $option     The option name
             * @param string    $context    The context this function is executed
             *
             * @return string
             */
            $value = apply_filters( 'automatorwp_get_automation_item_option_replacement', $value, $object, $item_type, $option, 'view' );

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

/**
 * Filters the taxonomy option value for replacement on labels
 *
 * @since 1.0.0
 *
 * @param string    $value      The option value
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $option     The option name
 * @param string    $context    The context this function is executed
 *
 * @return string
 */
function automatorwp_dynamic_taxonomy_option_replacement( $value, $object, $item_type, $option, $context ) {

    // Check type args
    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        return $value;
    }

    // Bail if this type hasn't any option
    if( ! isset( $type_args['options'][$option] ) ) {
        return $value;
    }

    $option_args = $type_args['options'][$option];

    $field_id = ( isset( $option_args['from'] ) ? $option_args['from'] : '' );

    // Check if field id is term
    if( $field_id !== 'term' ) {
        return $value;
    }

    if( ! isset( $option_args['fields'] ) ) {
        return $value;
    }

    // Check if taxonomy field exists
    if( ! isset( $option_args['fields']['taxonomy'] ) ) {
        return $value;
    }

    ct_setup_table( "automatorwp_{$item_type}s" );

    // Get the custom taxonomy
    $taxonomy = ct_get_object_meta( $object->id, 'taxonomy', true );

    if( $taxonomy !== '' && $taxonomy !== 'any' ) {

        $term_id = ct_get_object_meta( $object->id, 'term', true );

        // Get the term using the taxonomy
        $term = get_term( $term_id, $taxonomy );

        if( $term ) {
            $value = $term->name;
        }

    } else {
        $value = __( 'any taxonomy', 'automatorwp' );
    }

    ct_reset_setup_table();

    return $value;
}
add_filter( 'automatorwp_get_automation_item_option_replacement', 'automatorwp_dynamic_taxonomy_option_replacement', 10, 5 );

/**
 * Filters the option custom for replacement on labels
 *
 * @since 1.0.0
 *
 * @param string    $value      The option value
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $option     The option name
 * @param string    $context    The context this function is executed
 *
 * @return string
 */
function automatorwp_option_custom_replacement( $value, $object, $item_type, $option, $context ) {

    // Check type args
    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        return $value;
    }

    // Bail if this type hasn't any option
    if( ! isset( $type_args['options'][$option] ) ) {
        return $value;
    }

    $option_args = $type_args['options'][$option];
    $field_id = ( isset( $option_args['from'] ) ? $option_args['from'] : '' );

    // Check if field exists
    if( ! isset( $option_args['fields'] ) ) {
        return $value;
    }

    if( ! isset( $option_args['fields'][$field_id] ) ) {
        return $value;
    }

    $field = $option_args['fields'][$field_id];

    // Bail if option_custom not enabled on this field
    if( ! isset( $field['option_custom'] ) ) {
        return $value;
    }

    if( $field['option_custom'] !== true ) {
        return $value;
    }

    // Check if custom field exists
    if( ! isset( $option_args['fields'][$field_id . '_custom'] ) ) {
        return $value;
    }

    // Get the real value
    ct_setup_table( "automatorwp_{$item_type}s" );
    $field_value = ct_get_object_meta( $object->id, $field_id, true );
    ct_reset_setup_table();

    // Bail if field is not setup to use the custom value
    if( $field_value !== $field['option_custom_value'] ) {
        return $value;
    }

    // Get the custom value instead
    ct_setup_table( "automatorwp_{$item_type}s" );
    $value = ct_get_object_meta( $object->id, $field_id . '_custom', true );
    ct_reset_setup_table();

    return $value;

}
add_filter( 'automatorwp_get_automation_item_option_replacement', 'automatorwp_option_custom_replacement', 10, 5 );