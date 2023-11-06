<?php
/**
 * Order Shipped
 *
 * @package     AutomatorWP\Integrations\WooCommerce_ShipStation\Triggers\Order_Shipped
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WooCommerce_ShipStation_Order_Shipped extends AutomatorWP_Integration_Trigger {

    public $integration = 'woocommerce_shipstation';
    public $trigger = 'woocommerce_order_shipped';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User\'s order gets shipped', 'automatorwp' ),
            'select_option'     => __( 'User\'s <strong>order</strong> gets shipped', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User\'s order gets shipped %1$s time(s)', 'automatorwp' ), '{times}' ),
            /* translators: %1$s: Order status. */
            'log_label'         => __( 'User\'s order gets shipped', 'automatorwp' ), 
            'action'            => 'woocommerce_shipstation_shipnotify',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_woocommerce_shipstation_order_tags(),
                automatorwp_woocommerce_shipstation_get_shipping_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param WC_Order  $order  Order object
     * @param array     $args   Additional data
     */
    public function listener( $order, $args ) {

        // Bail if not order
        if ( ! $order ) {
			return;
		}

        $user_id = $order->get_user_id();

        // Bail if not user
        if ( $user_id === 0 ) {
            return;
        }
        
        $order_id = $order->get_id();

        // Shipping tags
        $tracking_number = $args['tracking_number'];
        $carrier = $args['carrier'];
        $ship_date = date( 'Y-m-d', $args['ship_date'] );

        // Trigger the order status change
        automatorwp_trigger_event( array(
            'trigger'           => $this->trigger,
            'user_id'           => $user_id,
            'order_id'          => $order_id,
            'tracking_number'   => $tracking_number,
            'carrier'           => $carrier,
            'ship_date'         => $ship_date
        ) );

    }

    /**
     * Register the required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 6 );

        parent::hooks();
    }

    /**
     * Trigger custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    function log_meta( $log_meta, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Bail if action type don't match this action
        if( $trigger->type !== $this->trigger ) {
            return $log_meta;
        }

        $log_meta['order_id'] = ( isset( $event['order_id'] ) ? $event['order_id'] : 0 );
        $log_meta['tracking_number'] = ( isset( $event['tracking_number'] ) ? $event['tracking_number'] : '' );
        $log_meta['carrier'] = ( isset( $event['carrier'] ) ? $event['carrier'] : '' );
        $log_meta['ship_date'] = ( isset( $event['ship_date'] ) ? $event['ship_date'] : '' );

        return $log_meta;

    }

}

new AutomatorWP_WooCommerce_ShipStation_Order_Shipped();