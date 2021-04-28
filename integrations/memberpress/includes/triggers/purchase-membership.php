<?php
/**
 * Purchase Membership
 *
 * @package     AutomatorWP\Integrations\MemberPress\Triggers\Purchase_Membership
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_MemberPress_Purchase_Membership extends AutomatorWP_Integration_Trigger {

    public $integration = 'memberpress';
    public $trigger = 'memberpress_purchase_membership';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User purchases a membership', 'automatorwp' ),
            'select_option'     => __( 'User purchases <strong>a membership</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User purchases %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User purchases %1$s', 'automatorwp' ), '{post}' ),
            'action'            => array(
                'mepr-event-non-recurring-transaction-completed',
                'mepr-event-recurring-transaction-completed'
            ),
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Membership:', 'automatorwp' ),
                    'option_none_label' => __( 'any membership', 'automatorwp' ),
                    'post_type' => 'memberpressproduct'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param object $event transaction object.
     */
    public function listener( $event ) {

        $subscription = $event->get_data();

        $product_id = intval( $subscription->rec->product_id );
        $user_id = intval( $subscription->rec->user_id );

        // Trigger the product purchase
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'post_id'       => $product_id,
            'subscription'  => $subscription,
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

new AutomatorWP_MemberPress_Purchase_Membership();