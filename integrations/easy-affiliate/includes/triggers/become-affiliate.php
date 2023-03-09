<?php
/**
 * Become Affiliate
 *
 * @package     AutomatorWP\Integrations\Easy_Affiliate\Triggers\Add_Affiliate
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Easy_Affiliate_Become_Affiliate extends AutomatorWP_Integration_Trigger {

    public $integration = 'easy_affiliate';
    public $trigger = 'easy_affiliate_add_user_affiliate';

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
            'action'            => 'esaf_event_affiliate-added',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(),
            'tags'              => array()
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

        $user_affiliate = get_user_meta( $user_id, 'wafp_is_affiliate', true );

        // Bail if user is affiliated
        if ( isset ( $user_affiliate ) && $user_affiliate === '1' ) {
            return;
        }
        
        // Trigger the user added as affiliate
        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
        ) );

    }

}

new AutomatorWP_Easy_Affiliate_Become_Affiliate();