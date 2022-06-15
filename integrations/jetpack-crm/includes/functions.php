<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Integrations\Jetpack_CRM\Includes\Functions
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
function automatorwp_jetpack_crm_options_cb_tag( $field ) {

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

            $options[$tag_id] = automatorwp_jetpack_crm_get_tag_title( $tag_id );
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
function automatorwp_jetpack_crm_get_tag_title( $tag_id ) {

    // Empty title if no ID provided
    if( absint( $tag_id ) === 0 ) {
        return '';
    }

    $class_tags = new zbsDAL;
    $tag = $class_tags->getTag( $tag_id );
    
    return $tag['name'];

}

/**
 * Get the tags by type
 *
 * @since 1.0.0
 * 
 * @param int $type_id   ID Type
 *
 * @return array
 */
function automatorwp_jetpack_crm_get_tags ( $type_id ) {

    $obj_tags = new zbsDAL;
    $all_tags = $obj_tags->getAllTags( );  

    foreach ( $all_tags as $tag ){

        if ( $tag['objtype'] === (string) $type_id ){

            $tags[] = array(
                'id'    => $tag['id'],
                'name'  => $tag['name']
            );
           
        }
        
    }

    return $tags;

}