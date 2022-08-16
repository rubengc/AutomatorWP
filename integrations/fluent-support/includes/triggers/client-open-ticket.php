<?php
/**
 * Client Open Ticket
 *
 * @package     AutomatorWP\Integrations\Fluent_Support\Triggers\Client_Open_Ticket
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Fluent_Support_Client_Open_Ticket extends AutomatorWP_Integration_Trigger {

    public $integration = 'fluent_support';
    public $trigger = 'fluent_support_client_open_ticket';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'Client opens a ticket', 'automatorwp' ),
            'select_option'     => __( 'Client <strong>opens</strong> a ticket', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'Client opens a ticket %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'Client opens a ticket', 'automatorwp' ),
            'action'            => 'fluent_support/ticket_created',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'times' => automatorwp_utilities_times_option()
            ),
            'tags' => array(
                
                'times' => automatorwp_utilities_times_tag( true )
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param array $ticket Array with information related to Ticket
     */
    public function listener( $ticket ) {

        if ( $ticket['source'] == NULL ){
            return;
        }
        
        $ticket_id = absint( $ticket['id'] );
        $user = get_user_by( 'email', $ticket['customer']['email']);

        // Bail if user not found
        if( ! $user ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'post_id' => $ticket_id,
            'user_id' => $user->ID,
        ) );

    }

}

new AutomatorWP_Fluent_Support_Client_Open_Ticket();