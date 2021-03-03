<?php
/**
 * Integration Trigger
 *
 * @package     AutomatorWP\Classes\Integration_Trigger
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Integration_Trigger {

    /**
     * Integration
     *
     * @since 1.0.0
     *
     * @var string $integration
     */
    public $integration = '';

    /**
     * Trigger
     *
     * @since 1.0.0
     *
     * @var string $trigger
     */
    public $trigger = '';

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

        if ( ! did_action( 'automatorwp_post_init' ) ) {
            // Default hook to register listener hook
            add_action('automatorwp_post_init', array( $this, 'register_listener_hook' ) );
        } else {
            // Hook for triggers registered from the theme's functions
            add_action( 'after_setup_theme', array( $this, 'register_listener_hook' ) );
        }

        // Guest deserves trigger hook
        add_filter( 'automatorwp_anonymous_deserves_trigger', array( $this, 'maybe_anonymous_deserves_trigger' ), 10, 5 );

        // User deserves trigger hook
        add_filter( 'automatorwp_user_deserves_trigger', array( $this, 'maybe_user_deserves_trigger' ), 10, 6 );
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
     * Register the trigger listener hook
     *
     * @since 1.0.0
     */
    public function register_listener_hook() {

        // Bail if this trigger is not in use
        if( ! automatorwp_is_trigger_in_use( $this->trigger ) ) {
            return;
        }

        // Get the trigger args
        $trigger = automatorwp_get_trigger( $this->trigger );

        // Bail if trigger not registered
        if( ! $trigger ) {
            return;
        }

        // Bail if not action or filter is provided
        if( empty( $trigger['action'] ) && empty( $trigger['filter'] ) ) {
            return;
        }

        // Bail if not callback function is provided
        if( empty( $trigger['function'] ) ) {
            return;
        }

        // Register the trigger hook (action or filter)
        if( ! empty( $trigger['action'] ) ) {

            // Ensure that is an array
            if( ! is_array( $trigger['action'] ) ) {
                $trigger['action'] = array( $trigger['action'] );
            }

            // Add all the actions
            foreach( $trigger['action'] as $action ) {
                add_action( $action, $trigger['function'], $trigger['priority'], $trigger['accepted_args'] );
            }

        } else if( ! empty( $trigger['filter'] ) ) {

            // Ensure that is an array
            if( ! is_array( $trigger['filter'] ) ) {
                $trigger['filter'] = array( $trigger['filter'] );
            }

            // Add all triggers
            foreach( $trigger['filter'] as $filter ) {
                add_filter( $filter, $trigger['function'], $trigger['priority'], $trigger['accepted_args'] );
            }

        }

    }

    /**
     * Checks if maybe should call or not to the user_deserves_trigger() function
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if user deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    public function maybe_user_deserves_trigger( $deserves_trigger, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Bail if trigger has not be deserved
        if( ! $deserves_trigger ) {
            return $deserves_trigger;
        }

        // Bail if trigger type don't match this trigger
        if( $trigger->type !== $this->trigger ) {
            return $deserves_trigger;
        }

        return $this->user_deserves_trigger( $deserves_trigger, $trigger, $user_id, $event, $trigger_options, $automation );

    }

    /**
     * User deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if user deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    public function user_deserves_trigger( $deserves_trigger, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Override
        return $deserves_trigger;

    }

    /**
     * Checks if maybe should call or not to the anonymous_deserves_trigger() function
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if anonymous deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if anonymous deserves trigger, false otherwise
     */
    public function maybe_anonymous_deserves_trigger( $deserves_trigger, $trigger, $event, $trigger_options, $automation ) {

        // Bail if event has not be deserved
        if( ! $deserves_trigger ) {
            return $deserves_trigger;
        }

        // Bail if trigger type don't match this trigger
        if( $trigger->type !== $this->trigger ) {
            return $deserves_trigger;
        }

        return $this->anonymous_deserves_trigger( $deserves_trigger, $trigger, $event, $trigger_options, $automation );

    }

    /**
     * Guest deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if anonymous deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                         True if anonymous deserves trigger, false otherwise
     */
    public function anonymous_deserves_trigger( $deserves_trigger, $trigger, $event, $trigger_options, $automation ) {

        // Override
        return $deserves_trigger;

    }

}