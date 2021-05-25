<?php
/**
 * Booking Completed
 *
 * @package     AutomatorWP\Integrations\Modern_Events_Calendar\Triggers\Booking_Completed
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Modern_Events_Calendar_Booking_Completed extends AutomatorWP_Integration_Trigger {

    public $integration = 'modern_events_calendar';
    public $trigger = 'modern_events_calendar_booking_completed';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User booking for an event is completed', 'automatorwp' ),
            'select_option'     => __( 'User booking for an event is <strong>completed</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User booking for %1$s is completed %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User booking for %1$s is completed', 'automatorwp' ), '{post}' ),
            'action'            => 'mec_booking_completed',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Event:', 'automatorwp' ),
                    'option_none_label' => __( 'any event', 'automatorwp' ),
                    'post_type' => 'mec-events'
                ) ),
                'times' => automatorwp_utilities_times_option()
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags( __( 'Event', 'automatorwp' ) ),
                automatorwp_modern_events_calendar_event_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int $book_id ID of the booking
     */
    public function listener( $book_id ) {

        $event_id = absint( get_post_meta( $book_id, 'mec_event_id', true ) );
        $attendees = get_post_meta( $book_id, 'mec_attendees', true );

        foreach( $attendees as $attendee ) {

            $user = get_user_by( 'email', $attendee['email'] );

            // Skip if user not registered
            if( ! $user ) {
                continue;
            }

            automatorwp_trigger_event( array(
                'trigger' => $this->trigger,
                'post_id' => $event_id,
                'user_id' => $user->ID,
            ) );

        }

    }

}

new AutomatorWP_Modern_Events_Calendar_Booking_Completed();