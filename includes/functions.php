<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Generates the required HTML with the dashicon provided
 *
 * @since 1.0.0
 *
 * @param string $dashicon      Dashicon class
 * @param string $tag           Optional, tag used (recommended i or span)
 *
 * @return string
 */
function automatorwp_dashicon( $dashicon = 'automatorwp', $tag = 'i' ) {

    return '<' . $tag . ' class="dashicons dashicons-' . $dashicon . '"></' . $tag . '>';

}

/**
 * Helper function to send an email
 *
 * @since 1.4.0
 *
 * @param array $args   Arguments passed to this function.
 *                      Note: Pass additional args to be used in the function's filters
 *
 * @return bool         Whether the email contents were sent successfully.
 */
function automatorwp_send_email( $args = array() ) {

    // Parse the email required args
    $email = wp_parse_args( $args, array(
        'from'          => '',
        'to'            => '',
        'cc'            => '',
        'bcc'           => '',
        'subject'       => '',
        'message'       => '',
        'headers'       => array(),
        'attachments'   => array(),
    ) );

    /**
     * Filter available to override the email arguments before process them
     *
     * @since 1.4.0
     *
     * @param array     $email  The email arguments
     * @param array     $args   The original arguments received
     *
     * @return array
     */
    $email = apply_filters( 'automatorwp_pre_email_args', $email, $args );

    // Setup subject
    $email['subject'] = do_shortcode( $email['subject'] );

    // Setup message
    $email['message'] = wpautop( $email['message'] );
    $email['message'] = do_shortcode( $email['message'] );

    // Setup headers
    if( ! is_array( $email['headers'] ) ) {
        $email['headers'] = array();
    }

    if( ! empty( $email['from'] ) ) {
        $email['headers'][] = 'From: <' . $email['from'] . '>';
    }

    if ( ! empty( $email['cc'] ) ) {
        $email['headers'][] = 'Cc: ' . $email['cc'];
    }

    if ( ! empty( $email['bcc'] ) ) {
        $email['headers'][] = 'Bcc: ' . $email['bcc'];
    }

    $email['headers'][] = 'Content-Type: text/html; charset='  . get_option( 'blog_charset' );

    // Setup attachments
    if( ! is_array( $email['attachments'] ) ) {
        $email['attachments'] = array();
    }

    /**
     * Filter available to override the email arguments after process them
     *
     * @since 1.4.0
     *
     * @param array     $email  The email arguments
     * @param array     $args   The original arguments received
     *
     * @return array
     */
    $email = apply_filters( 'automatorwp_email_args', $email, $args );

    add_filter( 'wp_mail_content_type', 'automatorwp_set_html_content_type' );

    // Send the email
    $result = wp_mail( $email['to'], $email['subject'], $email['message'], $email['headers'], $email['attachments'] );

    remove_filter( 'wp_mail_content_type', 'automatorwp_set_html_content_type' );

    /**
     * Filter available to decide to log email errors or not
     *
     * @since 1.4.0
     *
     * @param bool      $log_errors Whatever to log email errors or not, by default true
     * @param array     $email      The email arguments
     * @param array     $args       The original arguments received
     *
     * @return bool
     */
    $log_errors = apply_filters( 'automatorwp_log_email_errors', true, $email, $args );

    if( ! $result && $log_errors === true) {
        $log_message = sprintf(
            __( "[AutomatorWP] Email failed to send to %s with subject: %s", 'autoamtorwp' ),
            ( is_array( $email['to'] ) ? implode( ',', $email['to'] ) : $email['to'] ),
            $email['subject']
        );

        error_log( $log_message );
    }

    return $result;

}

/**
 * Function to set the mail content type
 *
 * @since 1.0.0
 *
 * @param string $content_type
 *
 * @return string
 */
function automatorwp_set_html_content_type( $content_type = 'text/html' ) {
    return 'text/html';
}

/**
 * Helper function to get the date format
 *
 * @since 1.0.0
 *
 * @param array $allowed_formats
 *
 * @return string
 */
function automatorwp_get_date_format( $allowed_formats = array() ) {

    $format = 'Y-m-d';

    $option = get_option( 'date_format' );

    if( empty( $allowed_formats ) ) {
        $allowed_formats = array( 'Y-m-d', 'm/d/Y', 'd/m/Y' );
    }

    if( in_array( $option, $allowed_formats ) ) {
        $format = $option;
    }

    return $format;

}

/**
 * Helper function to get the time format
 *
 * @since 1.0.0
 *
 * @param array $allowed_formats
 *
 * @return string
 */
function automatorwp_get_time_format( $allowed_formats = array() ) {

    $format = 'H:i';

    $option = get_option( 'time_format' );

    if( empty( $allowed_formats ) ) {
        $allowed_formats = array( 'g:i a', 'g:i A', 'H:i' );
    }

    if( in_array( $option, $allowed_formats ) ) {
        $format = $option;
    }

    return $format;

}

/**
 * Get timestamp from text date
 *
 * @since  2.2.2
 *
 * @param  string $value    Date value.
 * @param  string $format   Expected date format.
 *
 * @return mixed            Unix timestamp representing the date.
 */
function automatorwp_get_timestamp_from_value( $value, $format = 'Y-m-d' ) {

    // Create a new date object from the given format
    $date_object = date_create_from_format( $format, $value );

    // get the timestamp from the date object
    $timestamp = ( $date_object ? $date_object->getTimeStamp() : strtotime( $value ) );

    // Ensure to make a valid timestamp
    if ( empty( $timestamp ) && CMB2_Utils::is_valid_date( $value ) ) {
        $timestamp = CMB2_Utils::make_valid_time_stamp( $value );
    }

    return $timestamp;
}