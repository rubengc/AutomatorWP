<?php
/**
 * Confirm RSVP
 *
 * @package     AutomatorWP\Integrations\The_Events_Calendar\Triggers\Confirm_RSVP
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_The_Events_Calendar_Confirm_RSVP extends AutomatorWP_Integration_Trigger {

    public $integration = 'the_events_calendar';
    public $trigger = 'the_events_calendar_confirm_rsvp';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User confirms RSVP for an event', 'automatorwp' ),
            'select_option'     => __( 'User <strong>confirms RSVP</strong> for an event', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User confirms RSVP for %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User confirms RSVP for %1$s', 'automatorwp' ), '{post}' ),
            'action'            => array(
                'event_tickets_rsvp_tickets_generated_for_product',
                'event_tickets_woocommerce_tickets_generated_for_product',
                'event_tickets_tpp_tickets_generated_for_product',
            ),
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Event:', 'automatorwp' ),
                    'option_none_label' => __( 'any event', 'automatorwp' ),
                    'post_type' => Tribe__Events__Main::POSTTYPE
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
     * @param int    $product_id   RSVP ticket post ID
     * @param string $order_id     ID (hash) of the RSVP order
     * @param int    $qty          Quantity ordered
     */
    public function listener( $product_id, $order_id, $qty ) {

        $attendees = tribe_tickets_get_attendees( $order_id, 'rsvp_order' );

        if ( empty( $attendees ) ) {
            return;
        }

        foreach ( $attendees as $attendee ) {

            $user_id  = absint( $attendee['user_id'] );
            $event_id = absint( $attendee['event_id'] );
            $ticket_id = absint( $attendee['ticket_id'] );

            if( $attendee['order_status'] === 'yes' ) {

                // Trigger the RSVP confirmation
                automatorwp_trigger_event( array(
                    'trigger'       => $this->trigger,
                    'user_id'       => $user_id,
                    'post_id'       => $event_id,
                    'ticket_id'     => $ticket_id,
                ) );

            }
        }

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

        // Don't deserve if post is not received
        if( ! isset( $event['post_id'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['post_id'], $trigger_options['post'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_The_Events_Calendar_Confirm_RSVP();