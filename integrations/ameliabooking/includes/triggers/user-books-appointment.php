<?php
/**
 * User Books Appointment
 *
 * @package     AutomatorWP\Integrations\Amelia\Triggers\User_Books_Appointment
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Amelia_User_Books_Appointment extends AutomatorWP_Integration_Trigger {

    public $integration = 'ameliabooking';
    public $trigger = 'ameliabooking_user_books_appointment';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User books an appointment', 'automatorwp' ),
            'select_option'     => __( 'User books an <strong>appointment</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User books an appointment %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User books an appointment', 'automatorwp' ),
            'action'            => 'AmeliaBookingAddedBeforeNotify',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_ameliabooking_get_appointment_tags(),
                automatorwp_ameliabooking_get_customer_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param array       $args    Appointment data
     */
    public function listener( $args ) {

        // Bail if can not find any data
        if ( empty( $args ) ) {
            return;
        }

        // Bail if the booking is not for an appointment
        if ( $args['type'] !== 'appointment' ){
            return;
        }
        
        $user_id = get_current_user_id( );

        // Bail if user is not logged
        if ( $user_id === 0 ){
            return;
        }

        // Appointment tags
        $appointment_id = absint( $args['appointment']['id'] );
        $booking_start = $args['appointment']['bookingStart'];
        $booking_end = $args['appointment']['bookingEnd'];
        $status = $args['appointment']['status'];

        // Customer tags
        $customer_id = absint( $args['customer']['id'] );
        $customer_first_name = $args['customer']['firstName'];
        $customer_last_name = $args['customer']['lastName'];
        $customer_email = $args['customer']['email'];
        $customer_phone = $args['customer']['phone'];


        // Trigger user appointment
        automatorwp_trigger_event( array(
            'trigger'                       => $this->trigger,
            'user_id'                       => $user_id,
            'appointment_id'                => $appointment_id,
            'appointment_booking_start'     => $booking_start,
            'appointment_booking_end'       => $booking_end,
            'appointment_status'            => $status,
            'customer_id'                   => $customer_id,
            'customer_first_name'           => $customer_first_name,
            'customer_last_name'            => $customer_last_name,
            'customer_email'                => $customer_email,
            'customer_phone'                => $customer_phone
        ) );
       
    }

    /**
     * Register the required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 6 );

        parent::hooks();
    }

    /**
     * Trigger custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    function log_meta( $log_meta, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Bail if action type don't match this action
        if( $trigger->type !== $this->trigger ) {
            return $log_meta;
        }

        $log_meta['appointment_id'] = ( isset( $event['appointment_id'] ) ? $event['appointment_id'] : '' );
        $log_meta['appointment_booking_start'] = ( isset( $event['appointment_booking_start'] ) ? $event['appointment_booking_start'] : '' );
        $log_meta['appointment_booking_end'] = ( isset( $event['appointment_booking_end'] ) ? $event['appointment_booking_end'] : '' );
        $log_meta['appointment_status'] = ( isset( $event['appointment_status'] ) ? $event['appointment_status'] : '' );
        $log_meta['customer_id'] = ( isset( $event['customer_id'] ) ? $event['customer_id'] : '' );
        $log_meta['customer_first_name'] = ( isset( $event['customer_first_name'] ) ? $event['customer_first_name'] : '' );
        $log_meta['customer_last_name'] = ( isset( $event['customer_last_name'] ) ? $event['customer_last_name'] : '' );
        $log_meta['customer_email'] = ( isset( $event['customer_email'] ) ? $event['customer_email'] : '' );
        $log_meta['customer_phone'] = ( isset( $event['customer_phone'] ) ? $event['customer_phone'] : '' );

        return $log_meta;

    }

}

new AutomatorWP_Amelia_User_Books_Appointment();