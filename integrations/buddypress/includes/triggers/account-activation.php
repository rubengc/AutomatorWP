<?php
/**
 * Account Activation
 *
 * @package     AutomatorWP\Integrations\BuddyPress\Triggers\Account_Activation
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_BuddyPress_Account_Activation extends AutomatorWP_Integration_Trigger {

    public $integration = 'buddypress';
    public $trigger = 'buddypress_account_activation';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User account gets activated', 'automatorwp' ),
            'select_option'     => __( 'User account <strong>gets activated</strong>', 'automatorwp' ),
            'edit_label'        => __( 'User account gets activated', 'automatorwp' ),
            'log_label'         => __( 'User account gets activated', 'automatorwp' ),
            'action'            => 'bp_core_activated_user',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(),
            'tags' => array()
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int $user_id
     * @param string $key
     * @param WP_User $user
     */
    public function listener( $user_id, $key, $user ) {

        // Trigger account activation
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
        ) );

    }

}

new AutomatorWP_BuddyPress_Account_Activation();