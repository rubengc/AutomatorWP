<?php
/**
 * Add Membership
 *
 * @package     AutomatorWP\Integrations\Restrict_Content_Pro\Actions\Add_Membership
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Restrict_Content_Pro_Add_Membership extends AutomatorWP_Integration_Action {

    public $integration = 'restrict_content_pro';
    public $action = 'restrict_content_pro_add_membership';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add membership level to user', 'automatorwp' ),
            'select_option'     => __( 'Add <strong>membership level</strong> to user', 'automatorwp' ),
            /* translators: %1$s: Membership. */
            'edit_label'        => sprintf( __( 'Add %1$s to user', 'automatorwp' ), '{membership}' ),
            /* translators: %1$s: Membership. */
            'log_label'         => sprintf( __( 'Add %1$s to user', 'automatorwp' ), '{membership}' ),
            'options'           => array(
                'membership' => array(
                    'from' => 'membership',
                    'fields' => array(
                        'membership' => array(
                            'name' => __( 'Membership Level:', 'automatorwp' ),
                            'type' => 'select',
                            'classes' => 'automatorwp-ajax-selector',
                            'option_none' => true,
                            'option_none_value' => 'any',
                            'option_none_label' => __( 'a membership level', 'automatorwp' ),
                            'option_custom'         => true,
                            'option_custom_value'   => 'custom',
                            'option_custom_label'   => __( 'Use a custom value', 'automatorwp' ),
                            'option_custom_desc'    => '',
                            'attributes' => array(
                                'data-action' => 'automatorwp_restrict_content_pro_get_memberships',
                                'data-option-none' => true,
                                'data-option-none-value' => 'any',
                                'data-option-none-label' => __( 'a membership level', 'automatorwp' ),
                                'data-option-custom'        => true,
                                'data-option-custom-value'  => 'custom',
                                'data-option-custom-label'  => __( 'Use a custom value', 'automatorwp' ),
                                'data-placeholder' => '',
                            ),
                            'options_cb' => 'automatorwp_restrict_content_pro_options_cb_membership',
                            'default' => 'any'
                        ),
                        'membership_custom' => automatorwp_utilities_custom_field( array(
                            'option_custom_desc'    => __( 'Membership ID', 'automatorwp' ),
                        ) ),
                        'status' => array(
                            'name' => __( 'Status:', 'automatorwp' ),
                            'type' => 'select',
                            'options' => array(
                                'active' => __( 'Active', 'automatorwp' ),
                                'pending' => __( 'Pending', 'automatorwp' ),
                                'expired' => __( 'Expired', 'automatorwp' ),
                                'cancelled' => __( 'Cancelled', 'automatorwp' ),
                            ),
                            'default' => 'active'
                        ),
                        'expiration' => array(
                            'name' => __( 'Expiration Date:', 'automatorwp' ),
                            'desc' => __( 'Enter the membership expiration date in format YYYY-MM-DD. Leave empty for lifetime.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                    )
                )
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
        $level_id   = $action_options['membership'];
        $status     = $action_options['status'];

        // Bail if not membership level has been configured
        if( empty( $level_id ) || $level_id === 'any' ) {
            return;
        }

        $customer = rcp_get_customer_by_user_id( $user_id );

        // Create a new customer record if one does not exist
        if ( empty( $customer ) ) {
            $customer_id = rcp_add_customer( array(
                'user_id'         => absint( $user_id ),
                'date_registered' => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) )
            ) );
        } else {
            $customer_id = $customer->get_id();
        }

        $membership_args = array(
            'customer_id'      => absint( $customer_id ),
            'user_id'          => $user_id,
            'object_id'        => $level_id,
            'status'           => $status,
            'created_date'     => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
            'gateway'          => 'manual',
            'subscription_key' => rcp_generate_subscription_key()
        );

        switch ( $status ) {
            case 'expired' :
                // Set the expiration date if expired
                $membership_args['expiration_date'] = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
                break;
            case 'cancelled' :
                // Set the cancellation date if cancelled
                $membership_args['cancellation_date'] = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
                break;
        }

        $membership_id = rcp_add_membership( $membership_args );

        // Add membership meta to designate this as a generated record from this action
        rcp_add_membership_meta( $membership_id, 'automatorwp_restrict_content_action_id', $action->id );

        $membership = rcp_get_membership( $membership_id );

        // Generate a transaction ID
        $auth_key       = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
        $transaction_id = strtolower( md5( $membership_args['subscription_key'] . date( 'Y-m-d H:i:s' ) . $auth_key . uniqid( 'rcp', true ) ) );

        // Create a corresponding payment record.
        $payment_args = array(
            'subscription'     => rcp_get_subscription_name( $membership_args['object_id'] ),
            'object_id'        => $membership_args['object_id'],
            'date'             => $membership_args['created_date'],
            'amount'           => $membership->get_initial_amount(),
            'subtotal'         => $membership->get_initial_amount(),
            'user_id'          => $user_id,
            'subscription_key' => $membership_args['subscription_key'],
            'transaction_id'   => $transaction_id,
            'status'           => 'pending' == $membership_args['status'] ? 'pending' : 'complete',
            'gateway'          => 'manual',
            'customer_id'      => $customer_id,
            'membership_id'    => $membership_id
        );

        $rcp_payments = new RCP_Payments();
        $payment_id   = $rcp_payments->insert( $payment_args );

        // Add payment meta to designate this as a generated record from this action
        $rcp_payments->add_meta( $payment_id, 'automatorwp_restrict_content_action_id', $action->id );

    }

}

new AutomatorWP_Restrict_Content_Pro_Add_Membership();