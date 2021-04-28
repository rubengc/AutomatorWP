<?php
/**
 * Purchase Product
 *
 * @package     AutomatorWP\Integrations\Digimember\Triggers\Purchase_Product
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Digimember_Purchase_Product extends AutomatorWP_Integration_Trigger {

    public $integration = 'digimember';
    public $trigger = 'digimember_purchase_product';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User purchases a product', 'automatorwp' ),
            'select_option'     => __( 'User purchases <strong>a product</strong>', 'automatorwp' ),
            /* translators: %1$s: Product title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User purchases %1$s %2$s time(s)', 'automatorwp' ), '{product}', '{times}' ),
            /* translators: %1$s: Product title. */
            'log_label'         => sprintf( __( 'User purchases %1$s', 'automatorwp' ), '{product}' ),
            'action'            => 'digimember_purchase',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 4,
            'options'           => array(
                'product' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'product',
                    'name'              => __( 'Product:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any product', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_digimember_get_products',
                    'options_cb'        => 'automatorwp_digimember_options_cb_product',
                    'default'           => 'any'
                ) ),
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
     * @param int       $user_id
     * @param int       $product_id
     * @param int       $order_id
     * @param string    $reason (order_paid|order_cancelled|payment_missing)
     */
    public function listener( $user_id, $product_id, $order_id, $reason ) {

        // Bail if not is a product purchase
        if( $reason !== 'order_paid' ) {
            return;
        }

        // Trigger the product purchase
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'product_id'    => $product_id,
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

        // Don't deserve if product is not received
        if( ! isset( $event['product_id'] ) ) {
            return false;
        }

        $product_id = absint( $event['product_id'] );

        // Don't deserve if product doesn't exists
        if( $product_id === 0 ) {
            return false;
        }

        $required_product_id = absint( $trigger_options['product'] );

        // Don't deserve if product doesn't match with the trigger option
        if( $required_product_id !== 0 && $product_id !== $required_product_id ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_Digimember_Purchase_Product();