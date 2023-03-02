<?php
/**
 * Make Purchase
 *
 * @package     AutomatorWP\Integrations\SureCart\Triggers\Make_Purchase
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_SureCart_Make_Purchase extends AutomatorWP_Integration_Trigger {

    public $integration = 'surecart';
    public $trigger = 'surecart_make_purchase';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User makes a purchase', 'automatorwp-surecart' ),
            'select_option'     => __( 'User makes a <strong>purchase</strong>', 'automatorwp-surecart' ),
            /* translators: %1$s: Product title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User makes a purchase %1$s time(s)', 'automatorwp-surecart' ), '{times}' ),
            'log_label'         => __( 'User makes a purchase', 'automatorwp-surecart' ),
            'action'            => 'surecart/purchase_created',
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
     * @param array     $purchase
     * 
     */
    public function listener( $purchase ) {
        
        $user_id = get_current_user_id();

        // Bail if user is not logged
        if ($user_id === 0) {
            return;
        }

        // Trigger user product purchased
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'order_id'      => $purchase->initial_order,
        ) );
       
    }

}

new AutomatorWP_SureCart_Make_Purchase();