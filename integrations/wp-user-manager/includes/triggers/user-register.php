<?php
/**
 * User Register
 *
 * @package     AutomatorWP\Integrations\WP_User_Manager\Triggers\User_Register
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WP_User_Manager_User_Register extends AutomatorWP_Integration_Trigger {

    public $integration = 'wp_user_manager';
    public $trigger = 'wp_user_manager_user_register';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User registers through a form', 'automatorwp' ),
            'select_option'     => __( 'User <strong>registers</strong> through a form', 'automatorwp' ),
            /* translators: %1$s: Form. */
            'edit_label'        => sprintf( __( 'User registers through %1$s', 'automatorwp' ), '{form}' ),
            'log_label'         => sprintf( __( 'User registers through %1$s', 'automatorwp' ), '{form}' ),
            'action'            => 'wpum_after_registration',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'form' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'form',
                    'name'              => __( 'Form:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any form', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_wp_user_manager_get_forms',
                    'options_cb'        => 'automatorwp_wp_user_manager_options_cb_form',
                    'default'           => 'any'
                ) ),
            ),
            'tags' => array()
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int $user_id
     * @param array $values
     * @param WPUM_Registration_Form $form
     */
    public function listener( $user_id, $values, $form ) {

        $form_id = $form->get_ID();

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'form_id'       => $form_id,
        ) );

    }

    /**
     * User deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if user deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    public function user_deserves_trigger( $deserves_trigger, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Don't deserve if post is not received
        if( ! isset( $event['form_id'] ) ) {
            return false;
        }

        // Bail if form doesn't match with the trigger option
        if( $trigger_options['form'] !== 'any' && absint( $event['form_id'] ) !== absint( $trigger_options['form'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WP_User_Manager_User_Register();