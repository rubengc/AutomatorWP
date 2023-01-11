<?php
/**
 * Complete Purchase Lite
 *
 * @package     AutomatorWP\Integrations\WP_Simple_Pay\Triggers\Complete_Purchase_Lite
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WP_Simple_Pay_Complete_Purchase_Lite extends AutomatorWP_Integration_Trigger {

    public $integration = 'wp_simple_pay';
    public $trigger = 'wp_simple_pay_complete_purchase_lite';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User completes a purchase through a form', 'automatorwp' ),
            'select_option'     => __( 'User completes a <strong>purchase</strong> through a form', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User completes a purchase through %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User completes a purchase through %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'init',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'post' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'post',
                    'name'              => __( 'Form:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any form', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_simple_pay_get_forms',
                    'options_cb'        => 'automatorwp_simple_pay_options_cb_form',
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags( __( 'Payment Form', 'automatorwp' ) ),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     */
    public function listener() {

        $data = \SimplePay\Core\Payments\Payment_Confirmation\get_confirmation_data();

        // Bail if not customer data
        if ( ! isset( $data['customer'] ) ) {
            return;
        }

        // Bail if no form assigned
        if( ! isset( $data['form'] ) ) {
            return;
        }

        $user = get_user_by( 'email', $data['customer']->email );

        // Bail if user can't be found
        if( ! $user ) {
            return;
        }

        $user_id = $user->ID;
        $post_id = $data['form']->id;

        // Trigger the complete purchase
        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
            'post_id'   => $post_id,
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
        if( ! isset( $event['post_id'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['post_id'], $trigger_options['post'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WP_Simple_Pay_Complete_Purchase_Lite();