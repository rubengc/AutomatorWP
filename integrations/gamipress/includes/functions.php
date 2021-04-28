<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Integrations\GamiPress\Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Options callback for select fields assigned to points types
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_gamipress_points_types_options_cb( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any points type', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    foreach( gamipress_get_points_types() as $points_type => $data ) {
        $options[$points_type] = $data['plural_name'];
    }

    return $options;

}

/**
 * Switch to main site if GamiPress is network wide active
 *
 * @since 1.0.0
 */
function automatorwp_gamipress_ajax_get_posts() {

    if( isset( $_REQUEST['post_type_cb'] ) ) {

        $post_type_cb = sanitize_text_field( $_REQUEST['post_type_cb'] );
        $gamipress_cbs = array( 'gamipress_get_points_types_slugs', 'gamipress_get_achievement_types_slugs', 'gamipress_get_rank_types_slugs' );

        // If is any of the GamiPress post types
        if( in_array( $post_type_cb, $gamipress_cbs ) ) {
            gamipress_switch_to_main_site_if_network_wide_active();
        }

    }

}
add_action( 'wp_ajax_automatorwp_get_posts', 'automatorwp_gamipress_ajax_get_posts', 1 );

/**
 * Utility function to get the post option parameter
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function automatorwp_gamipress_utilities_post_option( $args = array() ) {

    $post_options = automatorwp_utilities_post_option( $args );

    $post_options['fields']['post']['options_cb'] = 'automatorwp_gamipress_options_cb_posts';

    return $post_options;

}

/**
 * Options callback for select2 fields assigned to posts with the special condition that if is for GamiPress, requires a blog switch
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_gamipress_options_cb_posts( $field ) {

    if( gamipress_is_network_wide_active() ) {
        gamipress_switch_to_main_site();
    }

    $options = automatorwp_options_cb_posts( $field );

    if( gamipress_is_network_wide_active() ) {
        restore_current_blog();
    }

    return $options;

}
