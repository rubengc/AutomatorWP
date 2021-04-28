<?php
/**
 * Update Cover
 *
 * @package     AutomatorWP\Integrations\BuddyPress\Triggers\Update_Cover
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_BuddyPress_Update_Cover extends AutomatorWP_Integration_Trigger {

    public $integration = 'buddypress';
    public $trigger = 'buddypress_update_cover';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User updates cover image', 'automatorwp' ),
            'select_option'     => __( 'User updates <strong>cover image</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User updates cover image %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User updates cover image', 'automatorwp' ),
            'action'            => 'xprofile_cover_image_uploaded',
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

        // Trigger the update cover image
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
        ) );

    }

}

new AutomatorWP_BuddyPress_Update_Cover();