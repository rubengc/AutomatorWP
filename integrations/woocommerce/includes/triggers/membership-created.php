<?php
/**
 * Membership Created
 *
 * @package     AutomatorWP\Integrations\WooCommerce\Triggers\Membership_Created
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WooCommerce_Membership_Created extends AutomatorWP_Integration_Trigger {

    public $integration = 'woocommerce';
    public $trigger = 'woocommerce_membership_created';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User is added to a membership', 'automatorwp' ),
            'select_option'     => __( 'User is <strong>added</strong> to a <strong>membership</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User is added to %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User is added to %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'wc_memberships_user_membership_saved',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Membership:', 'automatorwp' ),
                    'option_none_label' => __( 'any membership', 'automatorwp' ),
                    'post_type' => 'wc_membership_plan'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags( __( 'Membership', 'automatorwp' ) ),
                automatorwp_woocommerce_order_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param $membership_plan
     * @param array $data
     */
    public function listener( $membership_plan, $data ) {

        $user_id = absint( $data['user_id'] );

        // Bail if not user provided
        if( $user_id === 0 ) {
            return;
        }

        // Get the order ID
        $order_id = 0;
        $access_method = get_post_meta( $membership_plan->id, '_access_method', true );

        if ( $access_method === 'purchase' ) {
            $order_id = get_post_meta( $data['user_membership_id'], '_order_id', true );
        }

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'post_id'       => $membership_plan->id,
            'order_id'      => $order_id,
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

        return $log_meta;

    }

}

new AutomatorWP_WooCommerce_Membership_Created();