<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Thrive_Leads\Functions
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
function automatorwp_thrive_leads_options_cb_form( $field ) {

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

            $options[$form_id] = automatorwp_thrive_leads_get_form_title( $form_id );
        }
    }

    return $options;

}

/**
 * Get the form title
 *
 * @since 1.0.0
 *
 * @return string|null
 */
function automatorwp_thrive_leads_get_forms( ) {

    $type_forms = get_posts(
        array(
            'post_type'      => 'tve_form_type',
            'posts_per_page' => -1,
            'post_status'    => 'any',   
        )
    );

    $all_forms = array();
		
		foreach ( $type_forms as $parent ) {
			$forms = tve_leads_get_form_variations( $parent->ID );

			foreach ( $forms as $form ) {
				$all_forms[ ] = array(
                    'id' => $form['key'], 
                    'name' => $form['post_title']
                );
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
function automatorwp_thrive_leads_get_form_title( $form_id ) {

    // Empty title if no ID provided
    if( absint( $form_id ) === 0 ) {
        return '';
    }

    $form = tve_leads_get_form_variation( null, $form_id );

    return $form['post_title'];

}
