<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Gravity_Forms\Functions
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
function automatorwp_gravity_forms_options_cb_form( $field ) {

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

            $options[$form_id] = automatorwp_gravity_forms_get_form_title( $form_id );
        }
    }

    return $options;

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
function automatorwp_gravity_forms_get_form_title( $form_id ) {

    // Empty title if no ID provided
    if( absint( $form_id ) === 0 ) {
        return '';
    }

    $form = RGFormsModel::get_form( $form_id );

    return $form->title;

}

/**
 * Get form fields values
 *
 * @since 1.0.0
 *
 * @param array $lead
 * @param array $form
 *
 * @return array
 */
function automatorwp_gravity_forms_get_form_fields_values( $lead, $form ) {

    $form_fields = array();

    $fields = $form['fields'];

    // Loop all fields to trigger events per field value
    foreach ( $fields as $field ) {

        // Excluded fields
        if( in_array( $field->type, array( 'captcha', 'section' ) ) ) {
            continue;
        }

        $field_name = $field->id;
        $field_value = GFFormsModel::get_lead_field_value( $lead, $field );

        // Turn serialized values into array
        if ( $field->type === 'list' ) {

            // On list fields is required to unserialize
            $field_value = maybe_unserialize( $field_value );

        } else if ( $field->type === 'name' ) {

            $new_value = array();

            foreach( $field_value as $value ) {
                if( ! empty( $value ) ) {
                    $new_value[] = $value;
                }
            }

            $field_value = $new_value;

        } else if ( $field->type === 'multiselect' ) {

            // Multiselect value is a json
            $field_value = json_decode( $field_value, true );

        }  else if ( $field->type === 'checkbox' ) {

            // On checkboxes, values are stored on {field_id}.{choice_number}
            $field_value = array();
            $choice_number = 1;

            foreach ( $field->choices as $choice ) {
                $value = ( isset( $lead[$field->id . '.' . $choice_number] ) ? $lead[$field->id . '.' . $choice_number] : '' );

                // Not checked options are empty
                if( ! empty( $value ) )
                    $field_value[] = $value;

                $choice_number++;
            }

        }

        // Turn indexes like 4.1, into 1
        if( is_array( $field_value ) ) {
            $new_value = array();

            // Remove the main keys
            foreach( $field_value as $key => $value ) {
                if( strpos( $key, '.' ) !== false ) {
                    $keys = explode( '.', $key );
                    unset( $keys[0] );

                    $new_value[implode('/', $keys)] = $value;
                } else {
                    $new_value[$key] = $value;
                }
            }

            $field_value = $new_value;
        }

        $form_fields[$field_name] = $field_value;

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
function automatorwp_gravity_forms_parse_automation_tags( $parsed_content, $replacements, $automation_id, $user_id, $content ) {

    $new_replacements = array();

    // Get automation triggers to pass their tags
    $triggers = automatorwp_get_automation_triggers( $automation_id );

    foreach( $triggers as $trigger ) {

        $trigger_args = automatorwp_get_trigger( $trigger->type );

        // Skip if trigger is not from this integration
        if( $trigger_args['integration'] !== 'gravity_forms' ) {
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
add_filter( 'automatorwp_parse_automation_tags', 'automatorwp_gravity_forms_parse_automation_tags', 10, 5 );