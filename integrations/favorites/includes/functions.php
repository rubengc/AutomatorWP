<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Integrations\Favorites\Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Post type options callback
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_favorites_post_type_cb() {

    $post_types = array();

    // Get favorites post type display options
    $types = get_option('simplefavorites_display');

    if ( ! empty( $types['posttypes'] ) && $types !== "" ) {

        foreach ( $types['posttypes'] as $key => $type ) {

            // If favorites display is active, then add the post type
            if ( isset( $type['display'] ) && $type['display'] == 'true' ) {
                $post_types[] = $key;
            }

        }

    }

    return $post_types;

}