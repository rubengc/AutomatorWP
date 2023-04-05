<?php
/**
 * Update Profile
 *
 * @package     AutomatorWP\Integrations\BuddyBoss\Triggers\Update_Profile
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_BuddyBoss_Update_Profile extends AutomatorWP_Integration_Trigger {

    public $integration = 'buddyboss';
    public $trigger = 'buddyboss_update_profile';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User updates profile information', 'automatorwp' ),
            'select_option'     => __( 'User updates <strong>profile information</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User updates profile information %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User updates profile information', 'automatorwp' ),
            'action'            => 'xprofile_updated_profile',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 5,
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
     * @param array $posted_field_ids
     * @param array $errors
     * @param array $old_values
     * @param array $new_values
     */
    public function listener( $user_id, $posted_field_ids, $errors, $old_values, $new_values ) {

        // Bail if profile information did not change
        if ( $old_values === $new_values ) {
            return;
        }
        
        // Trigger the update profile information
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
        ) );

    }

}

new AutomatorWP_BuddyBoss_Update_Profile();