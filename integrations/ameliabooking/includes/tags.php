<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Amelia\Tags
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Appointment tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_ameliabooking_get_appointment_tags() {

    return array(
        'appointment_id' => array(
            'label'     => __( 'Appointment ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The appointment ID',
        ),
        'appointment_booking_start' => array(
            'label'     => __( 'Appointment booking start', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The appointment booking start date',
        ),
        'appointment_booking_end' => array(
            'label'     => __( 'Appointment booking end', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The appointment booking end date',
        ),
        'appointment_status' => array(
            'label'     => __( 'Appointment status', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The appointment status',
        ),
        'service_id' => array(
            'label'     => __( 'Appointment service ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The appointment service ID',
        ),
    );

}

/**
 * Custom trigger tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $replacement    The tag replacement
 * @param string    $tag_name       The tag name (without "{}")
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 * @param stdClass  $log            The last trigger log object
 *
 * @return string
 */
function automatorwp_ameliabooking_get_trigger_appointment_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'ameliabooking' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'appointment_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'appointment_id', true );
            break;
        case 'appointment_booking_start':
            $replacement = automatorwp_get_log_meta( $log->id, 'appointment_booking_start', true );
            break;
        case 'appointment_booking_end':
            $replacement = automatorwp_get_log_meta( $log->id, 'appointment_booking_end', true );
            break;
        case 'appointment_status':
            $replacement = automatorwp_get_log_meta( $log->id, 'appointment_status', true );
            break;
        case 'service_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'service_id', true );
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_ameliabooking_get_trigger_appointment_tag_replacement', 10, 6 );

/**
 * Booking tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_ameliabooking_get_booking_tags() {

    return array(
        'booking_id' => array(
            'label'     => __( 'Booking ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The booking ID',
        ),
        'booking_status' => array(
            'label'     => __( 'Booking status', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The booking status',
        ),
        'booking_persons' => array(
            'label'     => __( 'Booking persons', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The number of persons',
        ),
        'booking_price' => array(
            'label'     => __( 'Booking price', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The booking price',
        ),
    );

}

/**
 * Custom trigger tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $replacement    The tag replacement
 * @param string    $tag_name       The tag name (without "{}")
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 * @param stdClass  $log            The last trigger log object
 *
 * @return string
 */
function automatorwp_ameliabooking_get_trigger_booking_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'ameliabooking' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'booking_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'booking_id', true );
            break;
        case 'booking_status':
            $replacement = automatorwp_get_log_meta( $log->id, 'booking_status', true );
            break;
        case 'booking_persons':
            $replacement = automatorwp_get_log_meta( $log->id, 'booking_persons', true );
            break;
        case 'booking_price':
            $replacement = automatorwp_get_log_meta( $log->id, 'booking_price', true );
            break;     
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_ameliabooking_get_trigger_booking_tag_replacement', 10, 6 );

/**
 * Customer tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_ameliabooking_get_customer_tags() {

    return array(
        'customer_id' => array(
            'label'     => __( 'Customer ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The customer ID',
        ),
        'customer_first_name' => array(
            'label'     => __( 'Customer first name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The customer first name',
        ),
        'customer_last_name' => array(
            'label'     => __( 'Customer last name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The customer last name',
        ),
        'customer_email' => array(
            'label'     => __( 'Customer email', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The customer email',
        ),
        'customer_phone' => array(
            'label'     => __( 'Customer phone', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The customer phone',
        ),
    );

}

/**
 * Custom trigger tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $replacement    The tag replacement
 * @param string    $tag_name       The tag name (without "{}")
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 * @param stdClass  $log            The last trigger log object
 *
 * @return string
 */
function automatorwp_ameliabooking_get_trigger_customer_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'ameliabooking' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'customer_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'customer_id', true );
            break;
        case 'customer_first_name':
            $replacement = automatorwp_get_log_meta( $log->id, 'customer_first_name', true );
            break;
        case 'customer_last_name':
            $replacement = automatorwp_get_log_meta( $log->id, 'customer_last_name', true );
            break;
        case 'customer_email':
            $replacement = automatorwp_get_log_meta( $log->id, 'customer_email', true );
            break;  
        case 'customer_phone':
            $replacement = automatorwp_get_log_meta( $log->id, 'customer_phone', true );
            break;   
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_ameliabooking_get_trigger_customer_tag_replacement', 10, 6 );
