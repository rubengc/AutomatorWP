<?php
/**
 * Integration Filter
 *
 * @package     AutomatorWP\Classes\Integration_Filter
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Integration_Filter {

    /**
     * Integration
     *
     * @since 1.0.0
     *
     * @var string $integration
     */
    public $integration = '';

    /**
     * Filter
     *
     * @since 1.0.0
     *
     * @var string $filter
     */
    public $filter = '';

    /**
     * Filter result
     *
     * @since 1.0.0
     *
     * @var string $result
     */
    public $result = '';

    public function __construct() {

        $this->hooks();

    }

    /**
     * Register the required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        if ( ! did_action( 'automatorwp_init' ) ) {
            // Default hook to register
            add_action('automatorwp_init', array( $this, 'register' ) );
        } else {
            // Hook for triggers registered from the theme's functions
            add_action( 'after_setup_theme', array( $this, 'register' ) );
        }

        // Type args
        add_filter( 'automatorwp_automation_item_type_args', array( $this, 'override_type_args' ), 5, 3 );

        // Deserves filter
        add_filter( 'automatorwp_user_deserves_trigger_filter', array( $this, 'maybe_user_deserves_filter' ), 10, 6 );
        add_filter( 'automatorwp_user_deserves_action_filter', array( $this, 'maybe_user_deserves_filter' ), 10, 6 );

        // Filter log meta data
        add_filter( 'automatorwp_user_not_passed_filter_log_meta', array( $this, 'maybe_log_meta' ), 15, 6 );
        add_filter( 'automatorwp_user_completed_trigger_log_meta', array( $this, 'maybe_log_meta' ), 15, 6 );
        add_filter( 'automatorwp_user_completed_action_log_meta', array( $this, 'maybe_action_log_meta' ), 15, 5 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'maybe_log_fields' ), 10, 5 );

    }

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {
        // Override
    }

    /**
     * Check if object is a filter object
     *
     * @since 1.0.0
     *
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The item type (trigger|action)
     *
     * @return bool
     */
    public function is_filter_object( $object, $item_type ) {

        // Bail if not object provided
        if( ! is_object( $object ) ) {
            return false;
        }

        // Bail if not is a filter
        if( $object->type !== 'filter' ) {
            return false;
        }

        // Bail if not correct item type provided
        if( ! in_array( $item_type, array( 'trigger', 'action' ) ) ) {
            return false;
        }

        ct_setup_table( "automatorwp_{$item_type}s" );
        $filter = ct_get_object_meta( $object->id, 'filter', true );
        ct_reset_setup_table();

        // Bail if filter does not matches
        if( $filter !== $this->filter ) {
            return false;
        }

        return true;

    }

    /**
     * Get the object type args
     *
     * @since 1.0.0
     *
     * @param array     $type_args  The trigger/action args
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The item type (trigger|action)
     *
     * @return array
     */
    public function override_type_args( $type_args, $object, $item_type = '' ) {

        // Bail if not is a filter
        if( ! $this->is_filter_object( $object, $item_type ) ) {
            return $type_args;
        }

        $filter_args = automatorwp_get_filter( $this->filter );

        // Bail if filter not registered
        if( ! $filter_args ) {
            return $type_args;
        }

        $type_args['edit_label'] .= ' ' . $filter_args['edit_label'];
        $type_args['log_label'] .= ' ' . $filter_args['log_label'];
        $type_args['options'] = array_merge( $type_args['options'], $filter_args['options'] );

        return $type_args;

    }

    /**
     * Checks if maybe should call or not to the user_deserves_filter() function
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_filter    True if user deserves filter, false otherwise
     * @param stdClass  $filter             The filter object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $filter_options     The filter's stored options
     * @param stdClass  $automation         The filter's automation object
     *
     * @return bool                         True if user deserves filter, false otherwise
     */
    public function maybe_user_deserves_filter( $deserves_filter, $filter, $user_id, $event, $filter_options, $automation ) {

        // Bail if filter has not be deserved
        if( ! $deserves_filter ) {
            return $deserves_filter;
        }

        $item_type = ( current_filter() === 'automatorwp_user_deserves_trigger_filter' ? 'trigger' : 'action' );

        // Bail if trigger type don't match this trigger
        if( ! $this->is_filter_object( $filter, $item_type ) ) {
            return $deserves_filter;
        }

        // Initialize the result
        $this->result = '';

        return $this->user_deserves_filter( $deserves_filter, $filter, $user_id, $event, $filter_options, $automation );

    }

    /**
     * User deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_filter    True if user deserves filter, false otherwise
     * @param stdClass  $filter             The filter object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $filter_options     The filter's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    public function user_deserves_filter( $deserves_filter, $filter, $user_id, $event, $filter_options, $automation ) {

        // Override
        return $deserves_filter;

    }

    /**
     * Checks if should add custom filter log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored option
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    public function maybe_log_meta( $log_meta, $trigger, $user_id, $event, $trigger_options, $automation ) {

        $item_type = '';

        if( current_filter() === 'automatorwp_user_completed_trigger_log_meta' ) {
            $item_type = 'trigger';
        } else if( current_filter() === 'automatorwp_user_completed_action_log_meta' ) {
            $item_type = 'action';
        } else if( isset( $log_meta['item_type'] ) ) {
            $item_type = $log_meta['item_type'];
        }

        // Bail if not is a filter
        if( ! $this->is_filter_object( $trigger, $item_type ) ) {
            return $log_meta;
        }

        $log_meta['result'] = $this->result;

        return $this->log_meta( $log_meta, $trigger, $user_id, $event, $trigger_options, $automation );

    }

    /**
     * Checks if should add custom filter log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored option
     * @param stdClass  $automation         The action's automation object
     *
     * @return array
     */
    public function maybe_action_log_meta( $log_meta, $action, $user_id, $action_options, $automation ) {

        global $automatorwp_event;

        $event = ( is_array( $automatorwp_event ) ? $automatorwp_event : array() );
        $item_type = 'action';

        // Bail if not is a filter
        if( ! $this->is_filter_object( $action, $item_type ) ) {
            return $log_meta;
        }

        $log_meta['result'] = $this->result;

        return $this->log_meta( $log_meta, $action, $user_id, $event, $action_options, $automation );

    }

    /**
     * Filter custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored option
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    public function log_meta( $log_meta, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Override
        return $log_meta;

    }

    /**
     * Filter custom log fields
     *
     * @since 1.0.0
     *
     * @param array     $log_fields The log fields
     * @param stdClass  $log        The log object
     * @param stdClass  $object     The trigger/action/automation object attached to the log
     *
     * @return array
     */
    public function maybe_log_fields( $log_fields, $log, $object ) {

        // Bail if log is not assigned to an action
        if( ! in_array( $log->type, array( 'trigger', 'action', 'filter' ) ) ) {
            return $log_fields;
        }

        if( $log->type === 'filter' ) {
            $item_type = automatorwp_get_log_meta( $log->id, 'item_type', true );
        } else {
            $item_type = $log->type;
        }

        // Bail if not is a filter
        if( ! $this->is_filter_object( $object, $item_type ) ) {
            return $log_fields;
        }

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $this->log_fields( $log_fields, $log, $object );
    }

    /**
     * Filter custom log fields
     *
     * @since 1.0.0
     *
     * @param array     $log_fields The log fields
     * @param stdClass  $log        The log object
     * @param stdClass  $object     The trigger/action/automation object attached to the log
     *
     * @return array
     */
    public function log_fields( $log_fields, $log, $object ) {

        // Override
        return $log_fields;

    }

}