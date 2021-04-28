<?php
/**
 * Purchase Membership
 *
 * @package     AutomatorWP\Integrations\Restrict_Content_Pro\Triggers\Purchase_Membership
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Restrict_Content_Pro_Purchase_Membership extends AutomatorWP_Integration_Trigger {

    public $integration = 'restrict_content_pro';
    public $trigger = 'restrict_content_pro_purchase_membership';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User purchases a membership level', 'automatorwp' ),
            'select_option'     => __( 'User purchases <strong>a membership level</strong>', 'automatorwp' ),
            /* translators: %1$s: Content title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User purchases %1$s %2$s time(s)', 'automatorwp' ), '{membership}', '{times}' ),
            /* translators: %1$s: Content title. */
            'log_label'         => sprintf( __( 'User purchases %1$s', 'automatorwp' ), '{membership}' ),
            'action'            => 'rcp_membership_post_activate',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'membership' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'membership',
                    'name'              => __( 'Membership Level:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any membership level', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_restrict_content_pro_get_memberships',
                    'options_cb'        => 'automatorwp_restrict_content_pro_options_cb_membership',
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
     * @param int            $membership_id ID of the membership.
     * @param RCP_Membership $membership    Membership object.
     */
    public function listener( $membership_id, $membership ) {

        // Bail if not is a paid membership
        if( ! $membership->is_paid() ) {
            return;
        }

        $user_id = $membership->get_user_id();
        $membership_id = $membership->get_object_id();

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'membership_id' => $membership_id,
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

new AutomatorWP_Restrict_Content_Pro_Purchase_Membership();