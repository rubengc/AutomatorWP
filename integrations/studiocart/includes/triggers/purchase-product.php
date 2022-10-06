<?php
/**
 * Purchase Product
 *
 * @package     AutomatorWP\Integrations\Studiocart\Triggers\Purchase_Product
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Studiocart_Purchase_Product extends AutomatorWP_Integration_Trigger {

    public $integration = 'studiocart';
    public $trigger = 'studiocart_purchase_product';

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
            'edit_label'        => sprintf( __( 'User purchases %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Product title. */
            'log_label'         => sprintf( __( 'User purchases %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'sc_order_complete',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Product:', 'automatorwp' ),
                    'option_none_label' => __( 'any product', 'automatorwp' ),
                    'post_type' => 'sc_product'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags( __( 'Product', 'automatorwp' ) ),
                automatorwp_studiocart_order_tags(),
                automatorwp_studiocart_product_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param string    $status
     * @param array     $order_data
     * @param string    $order_type
     * 
     */
    public function listener( $status, $order_data, $order_type ) {

        // Bail if can not find any data
        if ( $status !== 'paid' ) {
            return;
        }
        
        $user_id = get_current_user_id();

        // Bail if user is not logged
        if ($user_id === 0) {
            return;
        }

        // Trigger user product purchased
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'post_id'       => $order_data['product_id'],
            'order_id'      => $order_data['ID'],
            'order_amount'  => $order_data['amount'],
            'product_id'    => $order_data['product_id'],
            'product_name'  => $order_data['product_name'],
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
        $log_meta['order_amount'] = ( isset( $event['order_amount'] ) ? $event['order_amount'] : 0.00 );
        $log_meta['product_id'] = ( isset( $event['product_id'] ) ? $event['product_id'] : 0 );
        $log_meta['product_name'] = ( isset( $event['product_name'] ) ? $event['product_name'] : '' );

        return $log_meta;

    }

}


new AutomatorWP_Studiocart_Purchase_Product();