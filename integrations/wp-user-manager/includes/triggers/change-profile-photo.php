<?php
/**
 * Change Profile Photo
 *
 * @package     AutomatorWP\Integrations\WP_User_Manager\Triggers\Change_Profile_Photo
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WP_User_Manager_Change_Profile_Photo extends AutomatorWP_Integration_Trigger {

    public $integration = 'wp_user_manager';
    public $trigger = 'wp_user_manager_change_profile_photo';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User changes profile photo', 'automatorwp' ),
            'select_option'     => __( 'User changes <strong>profile photo</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User changes profile photo %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User changes profile photo', 'automatorwp' ),
            'action'            => 'wpum_user_update_change_avatar',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'times' => automatorwp_utilities_times_option()
            ),
            'tags' => array(
                'times' => automatorwp_utilities_times_tag( true )
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int $user_id
     * @param string $image_url
     */
    public function listener( $user_id, $image_url ) {

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $user_id,
        ) );

    }

}

new AutomatorWP_WP_User_Manager_Change_Profile_Photo();