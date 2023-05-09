<?php
/**
 * Functions
 *
 * @package     AutomatorWP\MailerLite\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Helper function to get the MailerLite url
 *
 * @since 1.0.0
 *
 * @return string
 */
function automatorwp_mailerlite_get_url() {

    return ' https://connect.mailerlite.com';

}

/**
 * Helper function to get the MailerLite API parameters
 *
 * @since 1.0.0
 *
 * @return array|false
 */
function automatorwp_mailerlite_get_api() {

    $url = automatorwp_mailerlite_get_url();
    $token = automatorwp_mailerlite_get_option( 'token', '' );

    if( empty( $token ) ) {
        return false;
    }

    return array(
        'url' => $url,
        'token' => $token,
    );

}


/**
 * Add subscriber to MailerLite
 *
 * @since 1.0.0
 * 
 * @param array     $subscriber     The subscriber data
 */
function automatorwp_mailerlite_add_subscriber( $subscriber ) {

    $api = automatorwp_mailerlite_get_api();

    if( ! $api ) {
        return;
    }

    $response = wp_remote_post( $api['url'] . '/api/subscribers', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api['token'],
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        ),
        'body' => json_encode( array(
            'email'     => $subscriber['email'],
            'fields'    => array(
                'name'      => $subscriber['first_name'],
                'last_name' => $subscriber['last_name'],
            )
        ) )
    ) );

    return $response['response']['code'];
}

/**
 * Get groups from MailerLite
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_mailerlite_get_groups( ) {

    $groups = array();

    $api = automatorwp_mailerlite_get_api();

    if( ! $api ) {
        return $options;
    }

    $response = wp_remote_get( 'https://api.mailerlite.com/api/v2/groups', array(
        'headers' => array(
            'X-MailerLite-ApiKey' => $api['token'],
        ),
    ) );
    
    $response = json_decode( wp_remote_retrieve_body( $response ), true  );
    
    foreach ( $response as $group ){

        $groups[] = array(
            'id'    => $group['id'],
            'name'  => $group['name'],
        );
        
    }

    return $groups;

}

/**
 * Get group from MailerLite
 *
 * @since 1.0.0
 * 
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_mailerlite_options_cb_group( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any group', 'automatorwp-pro' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );
    
    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }
    
        foreach( $value as $group_id ) {

            // Skip option none
            if( $group_id === $none_value ) {
                continue;
            }

            $options[$group_id] = automatorwp_mailerlite_get_group_name( $group_id );
        }
    }

    return $options;

}

/**
* Get the group name
*
* @since 1.0.0
* 
* @param string $group_id
*
* @return array
*/
function automatorwp_mailerlite_get_group_name( $group_id ) {

    $api = automatorwp_mailerlite_get_api();

    if( ! $api ) {
        return $options;
    }

    $response = wp_remote_get( 'https://api.mailerlite.com/api/v2/groups/' . $group_id, array(
        'headers' => array(
            'X-MailerLite-ApiKey' => $api['token'],
        ),
    ) );

    $response = json_decode( wp_remote_retrieve_body( $response ), true  );
    
    if ( isset ( $response['error']['code'] ) === 404 || !isset ( $response['name'] )  ){
        return;
    }
    
    return $response['name'];
}


/**
 * Add subscriber to MailerLite
 *
 * @since 1.0.0
 * 
 * @param array     $subscriber     The subscriber data
 * @param int       $group          The group ID
 */
function automatorwp_mailerlite_add_subscriber_group( $subscriber, $group ) {

    $api = automatorwp_mailerlite_get_api();

    if( ! $api ) {
        return;
    }

    $response = wp_remote_post( $api['url'] . '/api/subscribers', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api['token'],
            'Accept' => 'application/json',
            'Content-Type'  => 'application/json'
        ),
        'body' => json_encode( array(
            'email'     => $subscriber['email'],
            'groups' => array(
                $group,
            )
        ) )
    ) );

    return $response['response']['code'];
}

/**
 * Add subscriber to MailerLite
 *
 * @since 1.0.0
 * 
 * @param array     $subscriber     The subscriber data
 * @param int       $group          The group ID
 */
function automatorwp_mailerlite_remove_subscriber_group( $subscriber, $group ) {

    $api = automatorwp_mailerlite_get_api();

    if( ! $api ) {
        return;
    }

    $response = wp_remote_request( 'https://api.mailerlite.com/api/v2/groups/' . $group .'/subscribers/' . $subscriber['email'], array(
        'method' => 'DELETE',
        'headers' => array(
            'X-MailerLite-ApiKey' => $api['token'],
        ), 
    ) );

    return $response['response']['code'];

}

/**
 * Create group in MailerLite
 *
 * @since 1.0.0
 * 
 * @param string       $group          The group name
 */
function automatorwp_mailerlite_create_group( $group ) {

    $api = automatorwp_mailerlite_get_api();

    if( ! $api ) {
        return;
    }

    $response = wp_remote_post( 'https://api.mailerlite.com/api/v2/groups', array(
        'headers' => array(
            'X-MailerLite-ApiKey' => $api['token'],
            'Content-Type'  => 'application/json'
        ),
        'body' => json_encode( array(
            'name' => $group
        ))
    ) );

    return $response['response']['code'];
    
}

/**
 * Remove group from MailerLite
 *
 * @since 1.0.0
 * 
 * @param int       $group_id          The ID group
 */
function automatorwp_mailerlite_remove_group( $group_id ) {

    $api = automatorwp_mailerlite_get_api();

    if( ! $api ) {
        return;
    }

    $response = wp_remote_request( 'https://api.mailerlite.com/api/v2/groups/' . $group_id, array(
        'method' => 'DELETE',
        'headers' => array(
            'X-MailerLite-ApiKey' => $api['token'],
        ),
    ) );

    return $response['response']['code'];
    
}
