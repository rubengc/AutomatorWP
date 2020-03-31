<?php
/**
 * Register
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Register
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Register extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_register';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User registers to the site', 'automatorwp' ),
            'select_option'     => __( 'User <strong>registers</strong> to the site', 'automatorwp' ),
            'edit_label'        => __( 'User registers to the site', 'automatorwp' ),
            'log_label'         => __( 'User registers', 'automatorwp' ),
            'action'            => 'user_register',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                // No options
            ),
            'tags' => array(
                // No tags
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int $user_id New registered user ID
     */
    public function listener( $user_id ) {

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $user_id,
        ) );

    }

}

new AutomatorWP_WordPress_Register();