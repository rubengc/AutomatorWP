<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Thrive_Leads\Tags
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Download tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_thrive_leads_tags() {

    return array(
        'form_id' => array(
            'label'     => __( 'Form ID', 'automatorwp' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'form_name' => array(
            'label'     => __( 'Form name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The form name',
        ),
        'group_id' => array(
            'label'     => __( 'Group ID', 'automatorwp' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'group_name' => array(
            'label'     => __( 'Group name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The group name',
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
function automatorwp_thrive_leads_get_trigger_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'thrive_leads' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'form_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'form_id', true );
            break;
        case 'form_name':
            $replacement = automatorwp_get_log_meta( $log->id, 'form_name', true );
            break;      
        case 'group_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'group_id', true );
            break; 
        case 'group_name':
            $replacement = automatorwp_get_log_meta( $log->id, 'group_name', true );
            break;      
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_thrive_leads_get_trigger_tag_replacement', 10, 6 );
