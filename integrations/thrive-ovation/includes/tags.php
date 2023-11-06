<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Integrations\Thrive_Ovation\Tags
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Testimonial tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_thrive_ovation_get_testimonial_tags() {

    return array(
        'testimonial_id' => array(
            'label'     => __( 'Testimonial ID', 'automatorwp' ),
            'type'      => 'integer',
            'preview'   => 'The testimonial ID',
        ),
        'testimonial_content' => array(
            'label'     => __( 'Testimonial content', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The testimonial content',
        ),
        'testimonial_author_email' => array(
            'label'     => __( 'Testimonial author email', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The testimonial author email',
        ),
        'testimonial_author_role' => array(
            'label'     => __( 'Testimonial author role', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The testimonial author role',
        ),
        'testimonial_author_website' => array(
            'label'     => __( 'Testimonial author website', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The testimonial author website',
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
function automatorwp_thrive_ovation_get_trigger_testimonial_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {

    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'thrive_ovation' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'testimonial_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'testimonial_id', true );
            break;
        case 'testimonial_content':
            $replacement = automatorwp_get_log_meta( $log->id, 'testimonial_content', true );
            break;
        case 'testimonial_author_email':
            $replacement = automatorwp_get_log_meta( $log->id, 'testimonial_author_email', true );
            break;
        case 'testimonial_author_role':
            $replacement = automatorwp_get_log_meta( $log->id, 'testimonial_author_role', true );
            break;
        case 'testimonial_author_website':
            $replacement = automatorwp_get_log_meta( $log->id, 'testimonial_author_website', true );
            break;          
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_thrive_ovation_get_trigger_testimonial_tag_replacement', 10, 6 );