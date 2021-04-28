<?php
/**
 * Change Profile Description
 *
 * @package     AutomatorWP\Integrations\WP_User_Manager\Triggers\Change_Profile_Description
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WP_User_Manager_Change_Profile_Description extends AutomatorWP_Integration_Trigger {

    public $integration = 'wp_user_manager';
    public $trigger = 'wp_user_manager_change_profile_description';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User changes profile description', 'automatorwp' ),
            'select_option'     => __( 'User changes <strong>profile description</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User changes profile description %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User changes profile description', 'automatorwp' ),
            'action'            => 'wpum_before_user_update',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
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
     * @param WPUM_Form_Profile $form
     * @param array $values
     * @param int $user_id
     */
    public function listener( $form, $values, $user_id ) {

        // Bail if description doesn't gets updated
        if ( ! isset( $values['account']['user_description'] ) ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $user_id,
        ) );

    }

}

new AutomatorWP_WP_User_Manager_Change_Profile_Description();