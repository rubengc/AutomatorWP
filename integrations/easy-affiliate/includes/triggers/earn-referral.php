<?php
/**
 * Earn Referral
 *
 * @package     AutomatorWP\Integrations\Easy_Affiliate\Triggers\Earn_REferral
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Easy_Affiliate_Earn_Referral extends AutomatorWP_Integration_Trigger {

    public $integration = 'easy_affiliate';
    public $trigger = 'easy_affiliate_earn_referral';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User earns a referral', 'automatorwp' ),
            'select_option'     => __( 'User earns a <strong>referral</strong', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User earns a referral %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User earns a referral', 'automatorwp' ),
            'action'            => 'esaf_event_transaction-recorded',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
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
     * @param array $args       Args from Easy Affiliate event
     */
    public function listener( $args ) {

        $user_id = get_current_user_id();

        // Bail if no user
        if ( absint( $user_id ) === 0 ) {
            return;
        }
        
        // Trigger the user added as affiliate
        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
        ) );

    }

}

new AutomatorWP_Easy_Affiliate_Earn_Referral();