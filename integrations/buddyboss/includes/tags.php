<?php
/**
 * Tags
 *
 * @package     AutomatorWP\BuddyBoss\Tags
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Activity tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_buddyboss_get_activity_tags() {

    return array(
        'activity_id' => array(
            'label'     => __( 'Activity ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The activity ID',
        ),
        'activity_url' => array(
            'label'     => __( 'Activity URL', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The activity URL',
        ),
        'activity_content' => array(
            'label'     => __( 'Activity content', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The activity content',
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
function automatorwp_buddyboss_get_trigger_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'buddyboss' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'activity_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'activity_id', true );
            break;
        case 'activity_url':
            $activity_id = absint( automatorwp_get_log_meta( $log->id, 'activity_id', true ) );

            $replacement = bp_activity_get_permalink( $activity_id );
            break;
        case 'activity_content':
            $replacement = automatorwp_get_log_meta( $log->id, 'activity_content', true );
            break;
        case 'activity_comment_content':
            $replacement = automatorwp_get_log_meta( $log->id, 'activity_comment_content', true );
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_buddyboss_get_trigger_tag_replacement', 10, 6 );