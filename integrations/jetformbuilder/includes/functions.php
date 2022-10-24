<?php
/**
 * Functions
 *
 * @package     AutomatorWP\JetFormBuilder\Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get form fields values
 *
 * @since 1.0.0
 *
 * @param Jet_Form_Builder\Form_Handler $form_handler
 *
 * @return array
 */
function automatorwp_jetformbuilder_get_form_fields_values( $form_handler ) {

    $form_fields = array();

    $all_fields = $form_handler->action_handler->request_data;

    foreach ( $all_fields as $field_key => $field_value ) {
        if ( empty( $field_value ) ) {
            continue;
        }

        if ( substr( $field_key, 0, 2 ) === '__' ) {
            continue;
        }
        
        $form_fields[$field_key] = $field_value;
    }

    return $form_fields;

}

/**
 * Custom tags replacements
 *
 * @since 1.0.0
 *
 * @param string    $parsed_content     Content parsed
 * @param array     $replacements       Automation replacements
 * @param int       $automation_id      The automation ID
 * @param int       $user_id            The user ID
 * @param string    $content            The content to parse
 *
 * @return string
 */
function automatorwp_jetformbuilder_parse_automation_tags( $parsed_content, $replacements, $automation_id, $user_id, $content ) {

    $new_replacements = array();

    // Get automation triggers to pass their tags
    $triggers = automatorwp_get_automation_triggers( $automation_id );

    foreach( $triggers as $trigger ) {

        $trigger_args = automatorwp_get_trigger( $trigger->type );

        // Skip if trigger is not from this integration
        if( $trigger_args['integration'] !== 'jetformbuilder' ) {
            continue;
        }

        // Get the last trigger log (where data for tags replacement will be get
        $log = automatorwp_get_user_last_completion( $trigger->id, $user_id, 'trigger' );

        if( ! $log ) {
            continue;
        }

        ct_setup_table( 'automatorwp_logs' );
        $form_fields = ct_get_object_meta( $log->id, 'form_fields', true );
        ct_reset_setup_table();

        // Skip if not form fields
        if( ! is_array( $form_fields ) ) {
            continue;
        }

        // Look for form field tags
        preg_match_all( "/\{" . $trigger->id . ":form_field:\s*(.*?)\s*\}/", $parsed_content, $matches );

        if( is_array( $matches ) && isset( $matches[1] ) ) {

            foreach( $matches[1] as $field_name ) {
                // Replace {ID:form_field:NAME} by the field value
                if( isset( $form_fields[$field_name] ) ) {
                    $new_replacements['{' . $trigger->id . ':form_field:' . $field_name . '}'] = $form_fields[$field_name];
                }
            }

        }

    }

    if( count( $new_replacements ) ) {

        $tags = array_keys( $new_replacements );

        // Replace all tags by their replacements
        $parsed_content = str_replace( $tags, $new_replacements, $parsed_content );

    }

    return $parsed_content;

}
add_filter( 'automatorwp_parse_automation_tags', 'automatorwp_jetformbuilder_parse_automation_tags', 10, 5 );