<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Advance_Custom_Fields\Tags
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Appointment tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_advanced_custom_fields_get_tags() {

    return array(
        'meta_key' => array(
            'label'     => __( 'Updated field', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => __( 'Key of the updated field', 'automatorwp' ),
        ),
        'meta_value' => array(
            'label'     => __( 'Updated value', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => __( 'Value of the updated field', 'automatorwp' ),
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
function automatorwp_advanced_custom_fields_get_trigger_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'advanced_custom_fields' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'meta_key':
            $replacement = automatorwp_get_log_meta( $log->id, 'updated_meta_key', true );
            break;
        case 'meta_value':
            $replacement = automatorwp_get_log_meta( $log->id, 'updated_meta_value', true );
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_advanced_custom_fields_get_trigger_tag_replacement', 10, 6 );
