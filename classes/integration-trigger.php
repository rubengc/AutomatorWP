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

        add_action( 'automatorwp_init', array( $this, 'register' ) );

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

        // Bail if event has not be deserved
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

}