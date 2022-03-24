<?php
/**
 * Tags
 *
 * @package     AutomatorWP\ActiveCampaign\Tags
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_activecampaign_get_webhook_tags() {

    return array(
        'webhook_url' => array(
            'label'     => __( 'Webhook URL', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'Webhook URL',
        ),
        'action_type' => array(
            'label'     => __( 'Action type', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The action type performed',
        ),
        'date_time' => array(
            'label'     => __( 'Datetime', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The execution datetime',
        ),
        'email' => array(
            'label'     => __( 'Email', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The contact email',
        ),
        'first_name' => array(
            'label'     => __( 'First name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The contact first name',
        ),
        'last_name' => array(
            'label'     => __( 'Last name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The contact last name',
        ),
        'tag' => array(
            'label'     => __( 'Tag', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The tag related to type action',
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
function automatorwp_activecampaign_get_trigger_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'activecampaign' ) {
        return $replacement;
    }

    $replacement = automatorwp_get_log_meta( $log->id, $tag_name, true );

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_activecampaign_get_trigger_tag_replacement', 10, 6 );

