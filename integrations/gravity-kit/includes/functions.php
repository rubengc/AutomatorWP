<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Gravity_Kit\Functions
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
function automatorwp_gravity_kit_options_cb_form( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any form', 'automatorwp-gravity-kit' );
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

            $options[$form_id] = automatorwp_gravity_kit_get_form_title( $form_id );
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
function automatorwp_gravity_kit_get_form_title( $form_id ) {

    // Empty title if no ID provided
    if( absint( $form_id ) === 0 ) {
        return '';
    }

    $form = RGFormsModel::get_form( $form_id );

    return $form->title;

}

/**
 * Get entry form
 *
 * @since 1.0.0
 *
 * @param int       $entry_id      The entry ID
 *
 * @return int
 */
function automatorwp_gravity_kit_get_entry_form ( $entry_id ) {

    global $wpdb;

	$form_id = $wpdb->get_var( $wpdb->prepare( "SELECT form_id from {$wpdb->prefix}gf_entry WHERE id=%d", $entry_id ) );

    return $form_id;

}

/**
 * Get entry user
 *
 * @since 1.0.0
 *
 * @param int       $entry_id      The entry ID
 *
 * @return int|NULL
 */
function automatorwp_gravity_kit_get_entry_user ( $entry_id ) {

    global $wpdb;

	$user_id = $wpdb->get_var( $wpdb->prepare( "SELECT created_by from {$wpdb->prefix}gf_entry WHERE id=%d", $entry_id ) );

    return $user_id;

}