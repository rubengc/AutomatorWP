<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Autonami\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_autonami_get_tags( ){

    $tags = array();

    $all_tags = BWFCRM_Tag::get_tags();
    
	foreach ( $all_tags as $tag ) {
		$tags[] = array(
			'id' => $tag['ID'],
			'name'  => $tag['name'],
		);
	}

	return $tags;

}

/**
 * Options callback for select2 fields assigned to tags
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_autonami_options_cb_tag( $field ) {

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

            $options[$tag_id] = automatorwp_autonami_get_tag_title( $tag_id );
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
function automatorwp_autonami_get_tag_title( $tag_id ) {

    // Empty title if no ID provided
    if( absint( $tag_id ) === 0 ) {
        return '';
    }

    $all_tags = BWFCRM_Tag::get_tags();

	foreach ( $all_tags as $tag ) {

        if ( absint( $tag_id ) === absint( $tag['ID'] ) ){
            $tag_name = $tag['name'];
        }
		
	}

    return $tag_name;

}

/**
 * Get the lists
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_autonami_get_lists( ){

    $lists = array();

    $all_lists = BWFCRM_Lists::get_lists();
    
	foreach ( $all_lists as $list ) {
		$lists[] = array(
			'id' => $list['ID'],
			'name'  => $list['name'],
		);
	}

	return $lists;

}

/**
 * Options callback for select2 fields assigned to lists
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_autonami_options_cb_list( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any list', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $list_id ) {

            // Skip option none
            if( $list_id === $none_value ) {
                continue;
            }

            $options[$list_id] = automatorwp_autonami_get_list_title( $list_id );
        }
    }

    return $options;

}

/**
 * Get the list title
 *
 * @since 1.0.0
 *
 * @param int $list_id
 *
 * @return string|null
 */
function automatorwp_autonami_get_list_title( $list_id ) {

    // Empty title if no ID provided
    if( absint( $list_id ) === 0 ) {
        return '';
    }

    $all_lists = BWFCRM_Lists::get_lists();

	foreach ( $all_lists as $list ) {

        if ( absint( $list_id ) === absint( $list['ID'] ) ){
            $list_name = $list['name'];
        }
		
	}

    return $list_name;

}