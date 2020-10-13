<?php
/**
 * Filters
 *
 * @package     AutomatorWP\Filters
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Filters the taxonomy option value for replacement on labels
 *
 * @since 1.0.0
 *
 * @param string    $value      The option value
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $option     The option name
 * @param string    $context    The context this function is executed
 *
 * @return string
 */
function automatorwp_dynamic_taxonomy_option_replacement( $value, $object, $item_type, $option, $context ) {

    // Check type args
    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        return $value;
    }

    // Bail if this type hasn't any option
    if( ! isset( $type_args['options'][$option] ) ) {
        return $value;
    }

    $option_args = $type_args['options'][$option];

    $field_id = ( isset( $option_args['from'] ) ? $option_args['from'] : '' );

    // Check if field id is not from term
    if( $field_id !== 'term' ) {
        return $value;
    }

    // Check if taxonomy field exists
    if( ! isset( $option_args['fields'] ) && ! isset( $option_args['fields']['taxonomy'] ) ) {
        return $value;
    }

    ct_setup_table( "automatorwp_{$item_type}s" );

    // Get the custom taxonomy
    $taxonomy = ct_get_object_meta( $object->id, 'taxonomy', true );

    if( $taxonomy !== '' && $taxonomy !== 'any' ) {

        $term_id = ct_get_object_meta( $object->id, 'term', true );

        // Get the term using the taxonomy
        $term = get_term( $term_id, $taxonomy );

        if( $term ) {
            $value = $term->name;
        }

    } else {
        $value = __( 'any taxonomy', 'automatorwp' );
    }

    ct_reset_setup_table();

    return $value;
}
add_filter( 'automatorwp_get_automation_item_option_replacement', 'automatorwp_dynamic_taxonomy_option_replacement', 10, 5 );