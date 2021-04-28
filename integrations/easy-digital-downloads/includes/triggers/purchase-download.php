<?php
/**
 * Purchase Download
 *
 * @package     AutomatorWP\Integrations\Easy_Digital_Downloads\Triggers\Purchase_Download
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Easy_Digital_Downloads_Purchase_Download extends AutomatorWP_Integration_Trigger {

    public $integration = 'easy_digital_downloads';
    public $trigger = 'easy_digital_downloads_purchase_download';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User purchases a download', 'automatorwp' ),
            'select_option'     => __( 'User purchases <strong>a download</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User purchases %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User purchases %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'edd_after_payment_actions',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Download:', 'automatorwp' ),
                    'option_none_label' => __( 'any download', 'automatorwp' ),
                    'post_type' => 'download'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags( __( 'Download', 'automatorwp' ) ),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int $payment_id The payment ID
     */
    public function listener( $payment_id ) {

        $payment = edd_get_payment( $payment_id );

        // Bail if payment object not exists
        if( ! $payment ) {
            return;
        }

        $cart_details = $payment->cart_details;

        // Bail if cart is not well setup
        if ( ! is_array( $cart_details ) ) {
            return;
        }

        $payment_total = $payment->total;
        $user_id = $payment->user_id;

        // Loop all items to trigger events on each one purchased
        foreach ( $cart_details as $index => $item ) {

            // Setup vars
            $download_id = $item['id'];
            $download = get_post( $download_id );
            $quantity = isset( $item['quantity'] ) ? absint( $item['quantity'] ) : 1;

            // Skip items not assigned to a product
            if( ! $download ) {
                continue;
            }

            // Trigger events same times as item quantity
            for ( $i = 0; $i < $quantity; $i++ ) {

                // Trigger the download purchase
                automatorwp_trigger_event( array(
                    'trigger'       => $this->trigger,
                    'user_id'       => $user_id,
                    'post_id'       => $download_id,
                    'payment_id'    => $payment_id,
                    'payment_total' => $payment_total,
                ) );

            } // End for of quantities

        } // End foreach of cart details

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

new AutomatorWP_Easy_Digital_Downloads_Purchase_Download();