<?php
/**
 * User Add Membership
 *
 * @package     AutomatorWP\Integrations\ARMember\Triggers\User_Add_Membership
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_ARMember_User_Add_Membership_Trigger extends AutomatorWP_Integration_Trigger {

    public $integration = 'armember';
    public $trigger = 'armember_user_add_membership';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User added to a membership plan', 'automatorwp' ),
            'select_option'     => __( 'User added to <strong>a membership</strong> plan', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User added to %1$s %2$s time(s)', 'automatorwp' ), '{plan}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User added to %1$s', 'automatorwp' ), '{plan}' ),
            'action'            => array( 'arm_after_user_plan_change_by_admin', 'arm_after_user_plan_change' ),
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'plan' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'plan',
                    'name'              => __( 'Plan:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any plan', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_armember_get_plans',
                    'options_cb'        => 'automatorwp_armember_options_cb_plan',
                    'default'           => 'any'
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
     * @param int $user_id    User ID.
     * @param int $plan_id    Membership plan ID.
     */
    public function listener( $user_id, $plan_id ) {

        // Trigger the cancel membership
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'plan_id'       => $plan_id,
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
        
        // Don't deserve if plan is not received
        if( ! isset( $event['plan_id'] ) ) {
            return false;
        }

        // Don't deserve if plan doesn't match with the trigger option
        if( $trigger_options['plan'] !== 'any' && absint( $event['plan_id'] ) !== absint( $trigger_options['plan'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_ARMember_User_Add_Membership_Trigger();