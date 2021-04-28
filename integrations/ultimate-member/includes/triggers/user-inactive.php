<?php
/**
 * User Inactive
 *
 * @package     AutomatorWP\Integrations\Ultimate_Member\Triggers\User_Inactive
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Ultimate_Member_User_Inactive extends AutomatorWP_Integration_Trigger {

    public $integration = 'ultimate_member';
    public $trigger = 'ultimate_member_user_inactive';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User account gets marked as inactive', 'automatorwp' ),
            'select_option'     => __( 'User account gets marked as <strong>inactive</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User account gets marked as inactive %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User account gets marked as inactive', 'automatorwp' ),
            'action'            => 'um_after_user_is_inactive',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int $user_id The user ID
     */
    public function listener( $user_id ) {

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
        ) );

    }

}

new AutomatorWP_Ultimate_Member_User_Inactive();