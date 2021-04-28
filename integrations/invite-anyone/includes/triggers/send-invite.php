<?php
/**
 * Send Invite
 *
 * @package     AutomatorWP\Integrations\Invite_Anyone\Triggers\Send_Invite
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Invite_Anyone_Send_Invite extends AutomatorWP_Integration_Trigger {

    public $integration = 'invite_anyone';
    public $trigger = 'invite_anyone_send_invite';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User sends an invitation', 'automatorwp' ),
            'select_option'     => __( 'User <strong>sends</strong> an invitation', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User sends an invitation %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User sends an invitation', 'automatorwp' ),
            'action'            => 'sent_email_invite',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
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
     * @param int       $user_id
     * @param string    $email
     * @param array     $group
     */
    public function listener( $user_id, $email, $group ) {

        // Bail if can't find the user ID
        if( $user_id === 0 ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
        ) );

    }

}

new AutomatorWP_Invite_Anyone_Send_Invite();