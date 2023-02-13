<?php
/**
 * Become Affiliate
 *
 * @package     AutomatorWP\Integrations\AffiliateWP\Triggers\Become_Affiliate
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_AffiliateWP_Become_Affiliate extends AutomatorWP_Integration_Trigger {

    public $integration = 'affiliatewp';
    public $trigger = 'affiliatewp_become_affiliate';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User becomes an affiliate', 'automatorwp' ),
            'select_option'     => __( 'User <strong>becomes</strong> an affiliate', 'automatorwp' ),
            'edit_label'        => __( 'User becomes an affiliate', 'automatorwp' ),
            'log_label'         => __( 'User becomes an affiliate', 'automatorwp' ),
            'action'            => 'affwp_set_affiliate_status',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(),
            'tags'              => array()
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param integer $affiliate  Affiliate ID
     * @param  string $status     The new affiliate status. Optional.
     * @param  string $old_status The old affiliate status.
     */
    public function listener( $affiliate, $status, $old_status ) {

        // Bail if status is not active
        if ( $status !== 'active' ) {
            return;
        }

        // Get user id from affiliate id
        $user_id = affwp_get_affiliate_user_id( $affiliate );

        // Trigger the become an affiliate
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
        ) );

    }

}

new AutomatorWP_AffiliateWP_Become_Affiliate();