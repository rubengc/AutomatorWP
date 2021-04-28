<?php
/**
 * Functions
 *
 * @package     AutomatorWP\H5P\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Options callback for select2 fields assigned to contents
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_h5p_options_cb_content( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any content', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $content_id ) {

            // Skip option none
            if( $content_id === $none_value ) {
                continue;
            }

            $options[$content_id] = automatorwp_h5p_get_content_title( $content_id );
        }
    }

    return $options;

}

/**
 * Get the content title
 *
 * @since 1.0.0
 *
 * @param int $content_id
 *
 * @return string|null
 */
function automatorwp_h5p_get_content_title( $content_id ) {

    // Empty title if no ID provided
    if( absint( $content_id ) === 0 ) {
        return '';
    }

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT c.title FROM {$wpdb->prefix}h5p_contents c WHERE c.id = %d",
        $content_id
    ) );

}