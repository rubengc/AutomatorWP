<?php
/**
 * Purchase Product
 *
 * @package     AutomatorWP\Integrations\Upsell_Plugin\Triggers\Purchase_Product
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Upsell_Plugin_Purchase_Product extends AutomatorWP_Integration_Trigger {

    public $integration = 'upsell_plugin';
    public $trigger = 'upsell_plugin_purchase_product';

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
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User purchases %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User purchases %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'upsell_order_status_completed',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Product:', 'automatorwp' ),
                    'option_none_label' => __( 'any product', 'automatorwp' ),
                    'post_type' => 'upsell_product'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags( __( 'Product', 'automatorwp' ) ),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param Upsell\Entities\Order $order The order
     */
    public function listener( $order ) {

        // Bail if order is not marked as completed
        if ( $order->getStatus() !== 'completed' ) {
            return;
        }

        $items = $order->getItems();

        // Bail if no items purchased
        if ( ! is_array( $items ) ) {
            return;
        }

        $order_id = $order->getId();
        $order_total = $order->getTotal();
        $user_id = $order->customer()->attribute('user_id');

        // Loop all items to trigger events on each one purchased
        foreach ( $items as $item ) {

            $product_id     = $item['id'];
            $quantity       = $item['quantity'];

            // Skip items not assigned to a product
            if( $product_id === 0 ) {
                continue;
            }

            // Trigger events same times as item quantity
            for ( $i = 0; $i < $quantity; $i++ ) {

                // Trigger the product purchase
                automatorwp_trigger_event( array(
                    'trigger'       => $this->trigger,
                    'user_id'       => $user_id,
                    'post_id'       => $product_id,
                    'order_id'      => $order_id,
                    'order_total'   => $order_total,
                ) );

            } // End for of quantities

        } // End foreach of items

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

        return $log_meta;

    }

}

new AutomatorWP_Upsell_Plugin_Purchase_Product();