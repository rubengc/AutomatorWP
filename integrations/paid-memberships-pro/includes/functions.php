<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Paid_Memberships_Pro\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Options callback for select2 fields assigned to memberships
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_paid_memberships_pro_options_cb_membership( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any membership level', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $membership_id ) {

            // Skip option none
            if( $membership_id === $none_value ) {
                continue;
            }

            $options[$membership_id] = automatorwp_paid_memberships_pro_get_membership_title( $membership_id );
        }
    }

    return $options;

}

/**
 * Get the membership title
 *
 * @since 1.0.0
 *
 * @param int $membership_id
 *
 * @return string|null
 */
function automatorwp_paid_memberships_pro_get_membership_title( $membership_id ) {

    // Empty title if no ID provided
    if( absint( $membership_id ) === 0 ) {
        return '';
    }

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT m.name FROM {$wpdb->pmpro_membership_levels} AS m WHERE m.id = %d",
        $membership_id
    ) );

}