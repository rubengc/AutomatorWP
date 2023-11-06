<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Kadence_Blocks\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Options callback for select2 fields assigned to forms
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_kadence_blocks_options_cb_form( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any form', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $form_id ) {

            // Skip option none
            if( $form_id === $none_value ) {
                continue;
            }

            $options[$form_id] = automatorwp_kadence_blocks_get_form_title( $form_id );
        }
    }

    return $options;

}

/**
 * Get all forms
 *
 * @since 1.0.0
 *
 * @return string|null
 */
function automatorwp_kadence_blocks_get_forms( ) {

    global $wpdb;

    $all_forms = array();

    $forms = $wpdb->get_results("select id,post_title,post_content from {$wpdb->posts} where post_content like '%<!-- wp:kadence/form%' and post_status = 'publish'");
    
    foreach ($forms as $post) {
        $form_id = $post->id;
        $form_title = $post->post_title;
        $form_content = $post->post_content;
        	
        $contentArray = explode('<!--', $form_content);
        $content = [];
        
        foreach ($contentArray as $key => $value) {
        	if (str_contains($value, ' wp:kadence/form')) {
        	    $temp = str_replace(' wp:kadence/form', '', $value);
        	    $temp1 = explode('-->', $temp, 2);
        	    $content[] = json_decode($temp1[0]);
        	}
        }
        	
        if (is_array($content)) {
        	foreach ($content as $form) {
        	    $parent_id = $form->postID;
        	    $unique_id = $form->uniqueID;
        	    $all_forms[] = array(
                    'id' => $unique_id,
                    'name' => $form_title . '_' . $unique_id,     
                );
        	    }
        }
    }

    return $all_forms;

}

/**
 * Get the form title
 *
 * @since 1.0.0
 *
 * @param int $form_id
 *
 * @return string|null
 */
function automatorwp_kadence_blocks_get_form_title( $form_id ) {

    // Empty title if no ID provided
    if( empty( $form_id ) ) {
        return '';
    }

    $complete_form_id = explode( '_', $form_id );
    $post_title = get_the_title( $complete_form_id[0] );

    return $post_title . '_' . $form_id;

}

/**
 * Get form fields values
 *
 * @since 1.0.0
 *
 * @param array $fields
 *
 * @return array
 */
function automatorwp_kadence_blocks_get_form_fields_values( $fields ) {

    $form_fields = array();

    // Loop all fields
    foreach ( $fields as $field_name => $field_value ) {
        
        if( is_array( $field_value ) ) {

            $field_name = $field_value['label'];
            $value = ( isset( $field_value['value'] ) ? $field_value['value'] : '' );

            $form_fields[$field_name] = $value;

        }
    }

    // Check for AutomatorWP 1.4.4
    if( function_exists( 'automatorwp_utilities_pull_array_values' ) ) {
        $form_fields = automatorwp_utilities_pull_array_values( $form_fields );
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
function automatorwp_kadence_blocks_parse_automation_tags( $parsed_content, $replacements, $automation_id, $user_id, $content ) {

    $new_replacements = array();

    // Get automation triggers to pass their tags
    $triggers = automatorwp_get_automation_triggers( $automation_id );

    foreach( $triggers as $trigger ) {

        $trigger_args = automatorwp_get_trigger( $trigger->type );

        // Skip if trigger is not from this integration
        if( $trigger_args['integration'] !== 'kadence_blocks' ) {
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
add_filter( 'automatorwp_parse_automation_tags', 'automatorwp_kadence_blocks_parse_automation_tags', 10, 5 );

