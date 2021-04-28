<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Groundhogg\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Options callback for select2 fields assigned to tags
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_groundhogg_options_cb_tag( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any tag', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $tag_id ) {

            // Skip option none
            if( $tag_id === $none_value ) {
                continue;
            }

            $options[$tag_id] = automatorwp_groundhogg_get_tag_title( $tag_id );
        }
    }

    return $options;

}

/**
 * Get the tag title
 *
 * @since 1.0.0
 *
 * @param int $tag_id
 *
 * @return string|null
 */
function automatorwp_groundhogg_get_tag_title( $tag_id ) {

    // Empty title if no ID provided
    if( absint( $tag_id ) === 0 ) {
        return '';
    }

    $tag = Groundhogg\Plugin::$instance->dbs->get_db( 'tags' )->get( $tag_id );

    return ( $tag ? $tag->tag_name : '' );

}