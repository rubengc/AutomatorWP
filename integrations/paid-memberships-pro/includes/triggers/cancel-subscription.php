<?php
/**
 * Cancel Subscription
 *
 * @package     AutomatorWP\Integrations\Paid_Memberships_Pro\Triggers\Cancel_Subscription
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Paid_Memberships_Pro_Cancel_Subscription extends AutomatorWP_Integration_Trigger {

    public $integration = 'paid_memberships_pro';
    public $trigger = 'paid_memberships_pro_cancel_subscription';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User cancels a subscription of a membership level', 'automatorwp' ),
            'select_option'     => __( 'User <strong>cancels a subscription</strong> of a membership level', 'automatorwp' ),
            /* translators: %1$s: Content title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User cancels a subscription of %1$s %2$s time(s)', 'automatorwp' ), '{membership}', '{times}' ),
            /* translators: %1$s: Content title. */
            'log_label'         => sprintf( __( 'User cancels a subscription of %1$s', 'automatorwp' ), '{membership}' ),
            'action'            => 'pmpro_before_change_membership_level',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 4,
            'options'           => array(
                'membership' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'membership',
                    'name'              => __( 'Membership Level:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any membership level', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_paid_memberships_pro_get_memberships',
                    'options_cb'        => 'automatorwp_paid_memberships_pro_options_cb_membership',
                    'default'           => 'any'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int $level_id ID of the level changed to.
	 * @param int $user_id ID of the user changed.
	 * @param array $old_levels array of prior levels the user belonged to.
	 * @param int $cancel_level ID of the level being cancelled if specified
     */
    public function listener( $level_id, $user_id, $old_levels, $cancel_level ) {

        // Bail if empty cancel
        if ( empty( $cancel_level ) ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'membership_id' => $cancel_level,
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

        // Don't deserve if membership is not received
        if( ! isset( $event['membership_id'] ) ) {
            return false;
        }

        $membership_id = absint( $event['membership_id'] );

        // Don't deserve if membership doesn't exists
        if( $membership_id === 0 ) {
            return false;
        }

        $required_membership_id = absint( $trigger_options['membership'] );

        // Don't deserve if membership doesn't match with the trigger option
        if( $trigger_options['membership'] !== 'any' && $membership_id !== $required_membership_id ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_Paid_Memberships_Pro_Cancel_Subscription();