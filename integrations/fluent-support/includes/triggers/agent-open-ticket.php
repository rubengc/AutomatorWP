<?php
/**
 * Agent Open Ticket
 *
 * @package     AutomatorWP\Integrations\Fluent_Support\Triggers\Agent_Open_Ticket
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Fluent_Support_Agent_Open_Ticket extends AutomatorWP_Integration_Trigger {

    public $integration = 'fluent_support';
    public $trigger = 'fluent_support_agent_open_ticket';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'Agent opens a ticket', 'automatorwp' ),
            'select_option'     => __( 'Agent <strong>opens</strong> a ticket', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'Agent opens a ticket %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'Agent opens a ticket', 'automatorwp' ),
            'action'            => 'fluent_support/ticket_created',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'times' => automatorwp_utilities_times_option()
            ),
            'tags' => array(
                automatorwp_utilities_post_tags( __( 'Ticket', 'automatorwp' ) ),
                'times' => automatorwp_utilities_times_tag( true )
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param array $ticket     Data opened ticket
     */
    public function listener( $ticket ) {
        global $wpdb;

        $ticket_id = absint( $ticket['id'] );
        $user = wp_get_current_user();

        $agent = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}fs_persons WHERE email='{$user->user_email}'" );

        // Bail if agent not found
        if( ! $agent ) {
            return;
        }

        // Bail if person entry is not an agent
        if ( $agent->person_type !== 'agent' ){
            return;
        }
        
        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'post_id' => $ticket_id,
            'user_id' => $user->ID,
        ) );
    }

}

new AutomatorWP_Fluent_Support_Agent_Open_Ticket();