<?php
/**
 * Start Following
 *
 * @package     AutomatorWP\Integrations\BuddyBoss\Triggers\Start_Following
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_BuddyBoss_Start_Following extends AutomatorWP_Integration_Trigger {

    public $integration = 'buddyboss';
    public $trigger = 'buddyboss_start_following';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User starts following someone', 'automatorwp' ),
            'select_option'     => __( 'User <strong>starts following</strong> someone', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User starts following someone %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User starts following someone', 'automatorwp' ),
            'action'            => 'bp_start_following',
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
     * @param object $follow Follow object.
     */
    public function listener( $follow ) {

        $user_id = $follow->follower_id;

        // Trigger the start following
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
        ) );

    }

}

new AutomatorWP_BuddyBoss_Start_Following();