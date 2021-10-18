<?php
/**
 * Become Affiliate
 *
 * @package     AutomatorWP\Integrations\SliceWP\Triggers\Become_Affiliate
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_SliceWP_Become_Affiliate extends AutomatorWP_Integration_Trigger {

    public $integration = 'slicewp';
    public $trigger = 'slicewp_become_affiliate';

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
            'action'            => array(
                'slicewp_insert_affiliate',
                'slicewp_update_affiliate'
            ),
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(),
            'tags'              => array()
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int       $affiliate_id
     * @param array     $affiliate_data
     */
    public function listener( $affiliate_id, $affiliate_data ) {

        if( $affiliate_data['status'] != 'active' ) {
            return;
        }

        // Get the affiliate user ID
        $affiliate = slicewp_get_affiliate( $affiliate_id );
        $user_id =  $affiliate->get( 'user_id' );

        // Trigger the become an affiliate
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
        ) );

    }

}

new AutomatorWP_SliceWP_Become_Affiliate();