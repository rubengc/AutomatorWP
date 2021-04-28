<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Integrations\Zoom\Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the request parameters
 *
 * @since 1.0.0
 *
 * @param string $platform
 *
 * @return array|false
 */
function automatorwp_zoom_get_request_parameters( $platform ) {

    if( empty( $platform ) ) {
        return false;
    }

    $auth = get_option( 'automatorwp_zoom_' . $platform . '_auth' );

    if( ! is_array( $auth ) ) {
        return false;
    }

    return array(
        'user-agent'  => 'AutomatorWP; ' . home_url(),
        'timeout'     => 120,
        'httpversion' => '1.1',
        'headers'     => array(
            'Authorization' => 'Bearer ' . $auth['access_token'],
            'Content-Type'  => 'application/json',
            'Accept'  		=> 'application/json'
        )
    );
}

/**
 * Get the request parameters
 *
 * @since 1.0.0
 *
 * @param string $platform
 *
 * @return string|false|WP_Error
 */
function automatorwp_zoom_refresh_token( $platform ) {

    $client_id = automatorwp_zoom_get_option( $platform . '_client_id', '' );
    $client_secret = automatorwp_zoom_get_option( $platform . '_client_secret', '' );

    if( empty( $client_id ) || empty( $client_secret ) ) {
        return false;
    }

    $auth = get_option( 'automatorwp_zoom_' . $platform . '_auth', false );

    if( ! is_array( $auth ) ) {
        return false;
    }

    $params = array(
        'headers' => array(
            'Content-Type'  => 'application/x-www-form-urlencoded; charset=utf-8',
            'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $client_secret ),
            'Accept'        => 'application/json',
        ),
        'body'	=> array(
            'refresh_token' => $auth['refresh_token'],
            'grant_type'    => 'refresh_token',
        )
    );

    $response = wp_remote_post( 'https://zoom.us/oauth/token', $params );

    if( is_wp_error( $response ) ) {
        return $response;
    }

    $response_code = wp_remote_retrieve_response_code( $response );

    if ( $response_code !== 200 ) {
        return false;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ) );

    $auth = array(
        'access_token'  => $body->access_token,
        'refresh_token' => $body->refresh_token,
        'token_type'    => $body->token_type,
        'expires_in'    => $body->expires_in,
        'scope'         => $body->scope,
    );

    // Update the access and refresh tokens
    update_option( 'automatorwp_zoom_' . $platform . '_auth', $auth );

    return $body->access_token;

}

/**
 * Filters the HTTP API response immediately before the response is returned.
 *
 * @since 1.0.0
 *
 * @param array  $response    HTTP response.
 * @param array  $parsed_args HTTP request arguments.
 * @param string $url         The request URL.
 *
 * @return array
 */
function automatorwp_zoom_maybe_refresh_token( $response, $args, $url ) {

    // Ensure to only run this check to on Zoom request
    if( strpos( $url, 'api.zoom' ) !== false ) {

        $code = wp_remote_retrieve_response_code( $response );

        if( $code === 401 ) {

            $access_token = automatorwp_zoom_refresh_token( 'meetings' );

            // Send again the request if token gets refreshed successfully
            if( $access_token ) {

                $args['headers']['Authorization'] = 'Bearer ' . $access_token;

                $response = wp_remote_request( $url, $args );

            }

        }

    }

    return $response;

}
add_filter( 'http_response', 'automatorwp_zoom_maybe_refresh_token', 10, 3 );

/**
 * Get all meetings
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_zoom_get_meetings() {

    $meetings = array();

    $transient = get_transient( 'automatorwp_zoom_meetings' );

    if ( $transient !== false ) {
        return $transient;
    }

    $params = automatorwp_zoom_get_request_parameters( 'meetings' );

    // Bail if the authorization has not been setup from settings
    if( $params === false ) {
        return $meetings;
    }

    $url  = 'https://api.zoom.us/v2/users/me/meetings?page_number=1&page_size=300&type=upcoming';
    $response = wp_remote_get( $url, $params );

    $response = json_decode( wp_remote_retrieve_body( $response ), true );

    if( isset( $response['meetings'] ) && is_array( $response['meetings'] ) ) {
        foreach( $response['meetings'] as $meeting ) {
            $meetings[] = array(
                'id' => $meeting['id'],
                'name' => $meeting['topic'],
            );
        }
    }

    if( count( $meetings ) ) {
        // Set a transient for 10 mins with the meetings
        set_transient( 'automatorwp_zoom_meetings', $meetings, 10 * 60 );
    }

    return $meetings;

}

/**
 * Get all meetings
 *
 * @since 1.0.0
 *
 * @param string $meeting_id
 *
 * @return array
 */
function automatorwp_zoom_get_meeting_registrants( $meeting_id ) {

    $registrants = array();

    $params = automatorwp_zoom_get_request_parameters( 'meetings' );

    // Bail if the authorization has not been setup from settings
    if( $params === false ) {
        return $registrants;
    }

    $url  = 'https://api.zoom.us/v2/meetings/' . $meeting_id . '/registrants?page_number=1&page_size=300&status=approved';
    $response = wp_remote_get( $url, $params );

    $response = json_decode( wp_remote_retrieve_body( $response ), true );

    if( isset( $response['registrants'] ) && is_array( $response['registrants'] ) ) {
        return $response['registrants'];
    }

    return $registrants;

}

/**
 * Get all meetings
 *
 * @since 1.0.0
 *
 * @param string $meeting_id
 * @param string $email
 *
 * @return string|false
 */
function automatorwp_zoom_get_meeting_registrant_id( $meeting_id, $email ) {

    $registrants = automatorwp_zoom_get_meeting_registrants( $meeting_id );

    foreach( $registrants as $registrant ) {
        if( $registrant['email'] === $email ) {
            return $registrant['id'];
        }
    }

    return false;

}

/**
 * Options callback for select2 fields assigned to meetings
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_zoom_options_cb_meetings( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any meeting', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $meeting_id ) {

            // Skip option none
            if( $meeting_id === $none_value ) {
                continue;
            }

            $options[$meeting_id] = automatorwp_zoom_get_meeting_title( $meeting_id );
        }
    }

    return $options;

}

/**
 * Get the meeting title
 *
 * @since 1.0.0
 *
 * @param int $meeting_id
 *
 * @return string|null
 */
function automatorwp_zoom_get_meeting_title( $meeting_id ) {

    // Empty title if no ID provided
    if( absint( $meeting_id ) === 0 ) {
        return '';
    }

    $meetings = automatorwp_zoom_get_meetings();
    $meeting_name = '';

    foreach( $meetings as $meeting ) {

        if( absint( $meeting['id'] ) === absint( $meeting_id ) ) {
            $meeting_name = $meeting['name'];
            break;
        }

    }

    return $meeting_name;

}