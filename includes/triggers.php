<?php
/**
 * Triggers
 *
 * @package     AutomatorWP\Triggers
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Registers a new trigger
 *
 * @since 1.0.0
 *
 * @param string    $trigger    The trigger key
 * @param array     $args       The trigger arguments
 */
function automatorwp_register_trigger( $trigger, $args ) {

    $args = wp_parse_args( $args, array(
        'integration'       => '',
        'anonymous'         => false,
        'label'             => '',
        'select_option'     => '',
        'edit_label'        => '',
        'log_label'         => '',
        'action'            => '',
        'filter'            => '',
        'function'          => '',
        'priority'          => 10,
        'accepted_args'     => 1,
        'options'           => array(),
        'tags'              => array(),
    ) );

    /**
     * Filter to extend registered trigger arguments
     *
     * @since 1.0.0
     *
     * @param array     $args       The trigger arguments
     * @param string    $trigger    The trigger key
     *
     * @return array
     */
    $args = apply_filters( 'automatorwp_register_trigger_args', $args, $trigger );

    // Sanitize options setup
    foreach( $args['options'] as $option => $option_args ) {

        if( in_array( $option, array( 'action', 'nonce', 'id', 'item_type', 'option_name' ) ) ) {
            _doing_it_wrong( __FUNCTION__, sprintf( __( 'Trigger "%s" has the option key "%s" that is not allowed', 'automatorwp' ), $trigger, $option ), null );
            return;
        }

    }

    if( isset( AutomatorWP()->triggers[$trigger] ) ) {
        error_log( sprintf( __( 'Possible trigger duplication with the key "%s"', 'automatorwp' ), $trigger ) );
    }

    AutomatorWP()->triggers[$trigger] = $args;

}

/**
 * Get registered triggers
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_get_triggers() {

    return AutomatorWP()->triggers;

}

/**
 * Get a trigger
 *
 * @since 1.0.0
 *
 * @param string $trigger
 *
 * @return array|false
 */
function automatorwp_get_trigger( $trigger ) {

    $trigger_args = ( isset( AutomatorWP()->triggers[$trigger] ) ? AutomatorWP()->triggers[$trigger] : false );

    /**
     * Available filter to override the trigger args
     *
     * @since 1.0.0
     *
     * @param array|false $trigger_args
     * @param string $trigger
     *
     * @return array|false
     */
    return apply_filters( 'automatorwp_get_trigger', $trigger_args, $trigger );

}

/**
 * Get an integration triggers
 *
 * @since 1.0.0
 *
 * @param string    $integration    The integration key
 * @param array     $filters        Filters to filter triggers by args
 *
 * @return array
 */
function automatorwp_get_integration_triggers( $integration, $filters = array() ) {

    $triggers = array();

    foreach( AutomatorWP()->triggers as $trigger => $args ) {

        if( $args['integration'] !== $integration ) {
            continue;
        }

        // If filters defined, apply them
        if( is_array( $filters ) && ! empty( $filters )  ) {

            $pass_filters = true;

            foreach( $filters as $filter_key => $filter_value ) {

                // Check if argument exists
                if( ! isset( $args[$filter_key] ) ) {
                    $pass_filters = false;
                }

                // Check if argument value matches
                if( $args[$filter_key] !== $filter_value ) {
                    $pass_filters = false;
                }

            }

            // Skip this item if filters not passed
            if( ! $pass_filters ) {
                continue;
            }
        }

        $triggers[$trigger] = $args;

    }

    /**
     * Available filter to extend integration triggers
     *
     * @since 1.0.0
     *
     * @param array     $triggers
     * @param string    $integration
     *
     * @return array
     */
    return apply_filters( 'automatorwp_get_integration_triggers', $triggers, $integration );

}

/**
 * Get the trigger object data
 *
 * @param int       $trigger_id     The trigger ID
 * @param string    $output         Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which correspond to
 *                                  a object, an associative array, or a numeric array, respectively. Default OBJECT.
 * @return array|stdClass|null
 */
function automatorwp_get_trigger_object( $trigger_id, $output = OBJECT ) {

    ct_setup_table( 'automatorwp_triggers' );

    $trigger = ct_get_object( $trigger_id );

    ct_reset_setup_table();

    return $trigger;

}

/**
 * Get the trigger object data
 *
 * @param int       $trigger_id     The trigger ID
 * @param string    $meta_key       Optional. The meta key to retrieve. By default, returns
 *                                  data for all keys. Default empty.
 * @param bool      $single         Optional. Whether to return a single value. Default false.
 *
 * @return mixed                    Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function automatorwp_get_trigger_meta( $trigger_id, $meta_key = '', $single = false ) {

    ct_setup_table( 'automatorwp_triggers' );

    $meta_value = ct_get_object_meta( $trigger_id, $meta_key, $single );

    ct_reset_setup_table();

    return $meta_value;

}

/**
 * Get the trigger's automation object
 *
 * @since 1.0.0
 *
 * @param int $trigger_id The trigger ID
 *
 * @return stdClass|false The automation object or false
 */
function automatorwp_get_trigger_automation( $trigger_id ) {

    $trigger = automatorwp_get_trigger_object( $trigger_id );

    if( ! $trigger ) {
        return false;
    }

    $automation = automatorwp_get_automation_object( $trigger->automation_id );

    return $automation;

}

/**
 * Get the trigger's stored options
 *
 * @since 1.0.0
 *
 * @param int   $trigger_id     The trigger ID
 * @param bool  $single_level   The level of options array, if is set to try, only option fields will be returned
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
function automatorwp_get_trigger_stored_options( $trigger_id, $single_level = true ) {

    $object = automatorwp_get_trigger_object( $trigger_id );

    if( ! $object ) {
        return array();
    }

    $trigger = automatorwp_get_trigger( $object->type );

    if( ! $trigger ) {
        return array();
    }

    ct_setup_table( 'automatorwp_triggers' );

    $options = array();

    foreach( $trigger['options'] as $option => $option_args ) {

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

/**
 * Check if trigger is in use
 *
 * @since 1.3.0
 *
 * @param string $trigger
 *
 * @return bool
 */
function automatorwp_is_trigger_in_use( $trigger ) {

    $triggers_in_user = automatorwp_get_triggers_in_use();

    // Check if this trigger is not in use
    return (bool) in_array( $trigger, $triggers_in_user );

}

/**
 * Get all triggers in use
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_get_triggers_in_use() {

    global $wpdb;

    $ct_table = ct_setup_table( 'automatorwp_triggers' );

    // Check if table exists, just to avoid issues on first install
    if( ! $ct_table->db->exists() ) {
        ct_reset_setup_table();
        return array();
    }

    $cache = automatorwp_get_cache( 'triggers_in_use', false, false );

    // If result already cached, return it
    if( is_array( $cache ) ) {
        ct_reset_setup_table();
        return $cache;
    }

    $triggers_in_use = array();
    $results = $wpdb->get_results( "SELECT t.type FROM {$ct_table->db->table_name} AS t GROUP BY t.type" );

    ct_reset_setup_table();

    if( is_array( $results ) && count( $results ) ) {
        $triggers_in_use = wp_list_pluck( $results, 'type' );
    }

    // Cache function result
    automatorwp_set_cache( 'triggers_in_use', $triggers_in_use );

    return $triggers_in_use;

}

/**
 * Get a trigger required times
 *
 * @since 1.0.0
 *
 * @param int $trigger_id The trigger ID
 *
 * @return int
 */
function automatorwp_get_trigger_required_times( $trigger_id ) {

    $trigger_id = absint( $trigger_id );

    // Check the trigger ID
    if( $trigger_id === 0 ) {
        return 0;
    }

    $times = absint( automatorwp_get_trigger_meta( $trigger_id, 'times', true ) );

    // Ensure to always require triggers at least 1 time
    if( $times === 0 ) {
        $times = 1;
    }

    return $times;

}