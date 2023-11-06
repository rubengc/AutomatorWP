<?php
/**
 * Add Membership
 *
 * @package     AutomatorWP\Integrations\ARMember\Actions\Add_Membership
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_ARMember_Add_Membership extends AutomatorWP_Integration_Action {

    public $integration = 'armember';
    public $action = 'armember_add_membership';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add user to membership plan', 'automatorwp' ),
            'select_option'     => __( 'Add user to <strong>membership</strong> plan', 'automatorwp' ),
            /* translators: %1$s: Referral. */
            'edit_label'        => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{plan}' ),
            /* translators: %1$s: Referral. */
            'log_label'         => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{plan}' ),
            'options'           => array(
                'plan' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'plan',
                    'option_default'    => __( 'Plan', 'automatorwp' ),
                    'name'              => __( 'Plan:', 'automatorwp' ),                    
                    'action_cb'         => 'automatorwp_armember_get_plans',
                    'options_cb'        => 'automatorwp_armember_options_cb_plan',
                    'placeholder'       => 'Select a plan',
                    'default'           => ''
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

        global $arm_subscription_plans;

        // Shorthand
        $plan_id = $action_options['plan'];

        // Bail if no plan
        if ( empty ( $plan_id ) ) {
            return;
        }
  
		$membership_data = array(
            'arm_user_plan'                 => $plan_id,
            'payment_gateway'               => 'manual',
            'arm_selected_payment_mode'     => 'manual_subscription',
            'arm_primary_status'            => 1,
            'arm_secondary_status'          => 0,
            'arm_subscription_start_date'   => date( 'm/d/Y' ),
            'arm_user_import'               => true,
        );

        $admin_save_flag = 1;
        do_action( 'arm_member_update_meta', $user_id, $membership_data, $admin_save_flag );

    }

}

new AutomatorWP_ARMember_Add_Membership();