<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Autonami\Tags
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Contact tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_autonami_contact_tags() {

    return array(
        'tag_id' => array(
            'label'     => __( 'Tag ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The tag ID',
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
function automatorwp_autonami_get_trigger_contact_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'autonami' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'tag_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'tag_id', true );
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_autonami_get_trigger_contact_tag_replacement', 10, 6 );

/**
 * Email tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_autonami_email_tags() {

    return array(
        'contact_email' => array(
            'label'     => __( 'Email', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The contact email',
        ),
    );
}


/**
 * Custom trigger email tag replacement
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
function automatorwp_autonami_get_trigger_email_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'autonami' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'contact_email':
            $replacement = automatorwp_get_log_meta( $log->id, 'contact_email', true );
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_autonami_get_trigger_email_tag_replacement', 10, 6 );

/**
 * List tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_autonami_list_tags() {

    return array(
        'list_id' => array(
            'label'     => __( 'List ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The list ID',
        ),
    );
}


/**
 * Custom trigger list tag replacement
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
function automatorwp_autonami_get_trigger_list_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'autonami' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'list_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'list_id', true );
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_autonami_get_trigger_list_tag_replacement', 10, 6 );
