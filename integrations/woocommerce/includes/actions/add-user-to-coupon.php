<?php
/**
 * Add User To Coupon
 *
 * @package     AutomatorWP\Integrations\WooCommerce\Actions\Add_User_To_Coupon
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WooCommerce_Add_User_To_Coupon extends AutomatorWP_Integration_Action {

    public $integration = 'woocommerce';
    public $action = 'woocommerce_add_user_to_coupon';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add user to a coupon', 'automatorwp' ),
            'select_option'     => __( 'Add user to a <strong>coupon</strong>', 'automatorwp' ),
            /* translators: %1$s: Coupon. */
            'edit_label'        => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Coupon. */
            'log_label'         => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{post}' ),
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name'              => __( 'Coupon:', 'automatorwp' ),
                    'option_none_label' => __( 'Choose a coupon', 'automatorwp' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Coupon ID', 'automatorwp' ),
                    'post_type'         => 'shop_coupon',
                ) ),
            ),
        ) );

    }

    /**
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    public function execute( $action, $user_id, $action_options, $automation ) {

        // Shorthand
        $coupon_id = absint( $action_options['post'] );

        // Bail if not coupon selected
        if( $coupon_id === 0 ) {
            return;
        }

        $user = get_userdata( $user_id );

        // Get the coupon emails
        $customer_email = array_filter( (array) get_post_meta( $coupon_id, 'customer_email', true ) );

        if( ! in_array( $user->user_email, $customer_email ) ) {

            $customer_email[] = $user->user_email;

            // Add user to coupon
            update_post_meta( $coupon_id, 'customer_email', array_filter( array_map( 'sanitize_email', $customer_email ) ) );
        }

    }

}

new AutomatorWP_WooCommerce_Add_User_To_Coupon();