<?php
/**
 * Functions
 *
 * @package     AutomatorWP\BuddyPress\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Options callback for select2 fields assigned to groups
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_buddypress_options_cb_group( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any group', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $group_id ) {

            // Skip option none
            if( $group_id === $none_value ) {
                continue;
            }

            $options[$group_id] = automatorwp_buddypress_get_group_title( $group_id );
        }
    }

    return $options;

}

/**
 * Get the group title
 *
 * @since 1.0.0
 *
 * @param int $group_id
 *
 * @return string|null
 */
function automatorwp_buddypress_get_group_title( $group_id ) {

    // Empty title if no ID provided
    if( absint( $group_id ) === 0 ) {
        return '';
    }

    $group = groups_get_group( $group_id );

    return $group->name;

}

/**
 * Options callback for select fields assigned to member types
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_buddypress_member_types_options_cb( $field ) {

    $none_value = 'any';
    $none_label = __( 'any profile type', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    $member_types = bp_get_member_types( array(), 'objects' );

    foreach( $member_types as $member_type => $member_type_obj ) {
        $options[$member_type] = $member_type_obj->labels['singular_name'];
    }

    return $options;

}