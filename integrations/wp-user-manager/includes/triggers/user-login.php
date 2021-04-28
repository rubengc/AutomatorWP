<?php
/**
 * User Login
 *
 * @package     AutomatorWP\Integrations\WP_User_Manager\Triggers\User_Login
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WP_User_Manager_User_Login extends AutomatorWP_Integration_Trigger {

    public $integration = 'wp_user_manager';
    public $trigger = 'wp_user_manager_user_login';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User logs in to the site', 'automatorwp' ),
            'select_option'     => __( 'User <strong>logs in</strong> to the site', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User logs in %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User logs in', 'automatorwp' ),
            'action'            => 'wpum_after_login',
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
     * @param WP_User $user
     */
    public function listener( $user_id, $user ) {

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $user_id,
        ) );

    }

}

new AutomatorWP_WP_User_Manager_User_Login();