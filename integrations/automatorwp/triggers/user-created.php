<?php
/**
 * User Created
 *
 * @package     AutomatorWP\Integrations\AutomatorWP\Triggers\User_Created
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_AutomatorWP_User_Created extends AutomatorWP_Integration_Trigger {

    public $integration = 'automatorwp';
    public $trigger = 'automatorwp_user_created';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User gets created through an automation', 'automatorwp' ),
            'select_option'     => __( 'User gets created through <strong>an automation</strong>', 'automatorwp' ),
            /* translators: %1$s: Automation title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User gets created through %1$s %2$s time(s)', 'automatorwp' ), '{automation}', '{times}' ),
            /* translators: %1$s: Automation title. */
            'log_label'         => sprintf( __( 'User gets created through %1$s', 'automatorwp' ), '{automation}' ),
            'action'            => 'automatorwp_wordpress_create_user_executed',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 5,
            'options'           => array(
                'automation' => automatorwp_utilities_automation_option(),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Register the trigger listener hook
     *
     * @since 1.0.0
     */
    public function register_listener_hook() {

        parent::register_listener_hook();

        if( automatorwp_is_trigger_in_use( $this->trigger ) ) {
            // Support for anonymous users
            add_action( 'automatorwp_anonymous_user_created', array( $this, 'anonymous_listener' ), 10, 4 );
        }

    }
    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int       $new_user_id        The new user ID
     * @param stdClass  $action             The action object
     * @param array     $action_options     The action's stored options (with tags already passed, included on meta keys and values)
     * @param stdClass  $automation         The action's automation object
     */
    public function anonymous_listener( $new_user_id, $action, $action_options, $automation ) {

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $new_user_id,
            'automation_id' => $automation->id,
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int       $new_user_id        The new user ID
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID (user who triggered the automation)
     * @param array     $action_options     The action's stored options (with tags already passed, included on meta keys and values)
     * @param stdClass  $automation         The action's automation object
     */
    public function listener( $new_user_id, $action, $user_id, $action_options, $automation ) {

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $new_user_id,
            'automation_id' => $automation->id,
        ) );

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

        // Don't deserve if automation is not received
        if( ! isset( $event['automation_id'] ) ) {
            return false;
        }

        $automation = automatorwp_get_automation_object( absint( $event['automation_id'] ) );

        // Don't deserve if automation doesn't exists
        if( ! $automation ) {
            return false;
        }

        $automation_id = absint( $trigger_options['automation'] );

        // Don't deserve if automation doesn't match with the trigger option
        if( $automation_id !== 0 && absint( $automation->id ) !== $automation_id ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_AutomatorWP_User_Created();