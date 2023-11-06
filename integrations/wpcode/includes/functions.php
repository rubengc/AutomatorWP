<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Integrations\WPCode\Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get snippets from WPCode
 *
 * @since 1.0.0
 * 
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_wpcode_options_cb_snippet( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any snippet', 'automatorwp-wpcode' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );
    
    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $snippet_id ) {

            // Skip option none
            if( $snippet_id === $none_value ) {
                continue;
            }
            
            $options[$snippet_id] = automatorwp_wpcode_get_snippet_name( $snippet_id );
        }
    }

    return $options;

}

/**
* Get snippets from WPCode
*
* @since 1.0.0
*
*
* @return array
*/
function automatorwp_wpcode_get_snippets( ) {

    $snippets = array();

    $query = new WP_Query( array(
        'post_type'		=> 'wpcode',
        'post_status'	=> 'draft,publish',
        'fields'        => 'ids',
        'posts_per_page'      => -1,
    ) );

    $all_snippets = $query->get_posts();

    foreach ( $all_snippets as $snippet_id ){

        $snippets[] = array(
            'id' => $snippet_id,
            'name' => get_the_title( $snippet_id ),
        );

    }

    return $snippets;

}

/**
* Get snippet name
*
* @since 1.0.0
*
* @param int    $snippet_id         ID snippet
* 
*/
function automatorwp_wpcode_get_snippet_name( $snippet_id ) {

    if( ! $snippet_id ) {
        return '';
    }

    return get_the_title( $snippet_id );

}