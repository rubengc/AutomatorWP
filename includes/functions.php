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