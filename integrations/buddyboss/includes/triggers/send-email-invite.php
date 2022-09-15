<?php
/**
 * Send Email Invite
 *
 * @package     AutomatorWP\Integrations\BuddyBoss\Triggers\Send_Email_Invite
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_BuddyBoss_Send_Email_Invite extends AutomatorWP_Integration_Trigger {

    public $integration = 'buddyboss';
    public $trigger = 'buddyboss_send_email_invite';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User sends an email invite', 'automatorwp' ),
            'select_option'     => __( 'User sends an <strong>email invite</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User sends an email invite %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User sends an email invite', 'automatorwp' ),
            'action'            => 'bp_member_invite_submit',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_times_tag(),
                automatorwp_buddyboss_get_invitation_tags()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int $user_id Inviter user id.
     * @param int $post_id Invitation post id.
     */
    public function listener( $user_id, $post_id ) {

        // Trigger the send email invite
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'post_id'       => $user_id,
            'inviter_id'    => $user_id,
        ) );

    }

    /**
     * Register the required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 6 );

        parent::hooks();
    }

    /**
     * Trigger custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    function log_meta( $log_meta, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Bail if action type don't match this action
        if( $trigger->type !== $this->trigger ) {
            return $log_meta;
        }

        $log_meta['inviter_id'] = ( isset( $event['inviter_id'] ) ? $event['inviter_id'] : '' );
        
        return $log_meta;

    }        
}

new AutomatorWP_BuddyBoss_Send_Email_Invite();