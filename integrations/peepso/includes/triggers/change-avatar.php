<?php
/**
 * Change Avatar
 *
 * @package     AutomatorWP\Integrations\PeepSo\Triggers\Change_Avatar
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_PeepSo_Change_Avatar extends AutomatorWP_Integration_Trigger {

    public $integration = 'peepso';
    public $trigger = 'peepso_change_avatar';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User changes his profile avatar', 'automatorwp' ),
            'select_option'     => __( 'User changes his <strong>profile avatar</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User changes his profile avatar %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User changes his profile avatar', 'automatorwp' ),
            'action'            => 'peepso_user_after_change_avatar',
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
     * @param int $user_id
     */
    public function listener( $user_id ) {

        // Trigger profile avatar change
        automatorwp_trigger_event( array(
            'trigger'           => $this->trigger,
            'user_id'           => $user_id,
        ) );

    }

}

new AutomatorWP_PeepSo_Change_Avatar();