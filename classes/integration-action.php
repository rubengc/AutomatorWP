<?php
/**
 * Integration Action
 *
 * @package     AutomatorWP\Classes\Integration_Action
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Integration_Action {

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
     * @var string $action
     */
    public $action = '';

    public function __construct() {

        $this->hooks();

    }

    /**
     * Register required hooks
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

        // Execute action hook
        add_action( 'automatorwp_execute_action', array( $this, 'maybe_execute' ), 10, 5 );

    }

    /**
     * Register the action
     *
     * @since 1.0.0
     */
    public function register() {
        // Override
    }

    /**
     * Checks if maybe should call or not to the execute() function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    public function maybe_execute( $action, $user_id, $event, $action_options, $automation ) {

        // Bail if action type don't match this action
        if( $action->type !== $this->action ) {
            return;
        }

        $this->execute( $action, $user_id, $action_options, $automation );

    }

    /**
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    public function execute( $action, $user_id, $action_options, $automation ) {
        // Override
    }

}