<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Integrations\Thrive_Apprentice\Tags
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Course tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_thrive_apprentice_get_course_tags() {

    return array(
        'course_id' => array(
            'label'     => __( 'Course ID', 'automatorwp' ),
            'type'      => 'integer',
            'preview'   => 'The course ID',
        ),
        'course_title' => array(
            'label'     => __( 'Course name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The course name',
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
function automatorwp_thrive_apprentice_get_trigger_course_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {

    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'thrive_apprentice' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'course_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'course_id', true );
            break;
        case 'course_title':
            $replacement = automatorwp_get_log_meta( $log->id, 'course_title', true );
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_thrive_apprentice_get_trigger_course_tag_replacement', 10, 6 );