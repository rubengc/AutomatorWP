<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Integrations\Code_Snippets\Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get snippets from Code Snippets
 *
 * @since 1.0.0
 * 
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_code_snippets_options_cb_snippet( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any snippet', 'automatorwp' );
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
            
            $options[$snippet_id] = automatorwp_code_snippets_get_snippet_name( $snippet_id );
        }
    }

    return $options;

}

/**
* Get snippets from Code Snippets
*
* @since 1.0.0
*
*
* @return array
*/
function automatorwp_code_snippets_get_snippets( ) {

    $snippets = array();

    $all_snippets = Code_Snippets\get_snippets();

    foreach ( $all_snippets as $snippet ){

        $snippets[] = array(
            'id' => $snippet->id,
            'name' => $snippet->name,
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
function automatorwp_code_snippets_get_snippet_name( $snippet_id ) {

    if( ! $snippet_id ) {
        return '';
    }

    $snippet = Code_Snippets\get_snippet( $snippet_id );

    return $snippet->name;

}