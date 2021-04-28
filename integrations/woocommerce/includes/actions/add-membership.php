<?php
/**
 * Add Membership
 *
 * @package     AutomatorWP\Integrations\WooCommerce\Actions\Add_Membership
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WooCommerce_Add_Membership extends AutomatorWP_Integration_Action {

    public $integration = 'woocommerce';
    public $action = 'woocommerce_add_membership';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add user to membership', 'automatorwp' ),
            'select_option'     => __( 'Add user to <strong>membership</strong>', 'automatorwp' ),
            /* translators: %1$s: Membership. */
            'edit_label'        => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Membership. */
            'log_label'         => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{post}' ),
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name'              => __( 'Membership:', 'automatorwp' ),
                    'option_default'    => __( 'membership', 'automatorwp' ),
                    'placeholder'       => __( 'Select a membership', 'automatorwp' ),
                    'option_none'       => false,
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Membership ID', 'automatorwp' ),
                    'post_type'         => 'wc_membership_plan',
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
        $membership_id = absint( $action_options['post'] );

        // Bail if not membership provided
        if( $membership_id === 0 ) {
            return;
        }

        $is_user_member = wc_memberships_is_user_member( $user_id, $membership_id );

        // Bail if user is already on this membership
        if ( $is_user_member ) {
            return;
        }

        // Add user to this membership
        wc_memberships_create_user_membership( array(
            'plan_id' => $membership_id,
            'user_id' => $user_id
        ) );

    }

}

new AutomatorWP_WooCommerce_Add_Membership();