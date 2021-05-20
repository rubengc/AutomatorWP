<?php
/**
 * User Password Reset
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\User_Password_Reset
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_User_Password_Reset extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_user_password_reset';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User resets their password', 'automatorwp' ),
            'select_option'     => __( 'User resets their <strong>password</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User resets their password %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User resets their password', 'automatorwp' ),
            'action'            => array(
                'after_password_reset',
                'woocommerce_customer_reset_password'
            ),
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
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
     * @param WP_User $user     The user.
     * @param string  $new_pass New user password.
     */
    public function listener( $user, $new_pass ) {

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user->ID,
        ) );

    }

}

new AutomatorWP_WordPress_User_Password_Reset();