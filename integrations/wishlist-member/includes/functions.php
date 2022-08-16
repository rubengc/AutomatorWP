<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Integrations\WishList_Member\Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Options callback for levels options
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_wishlist_member_levels_options_cb( $field ) {

    $options = array(
        'any' => __( 'any level', 'automatorwp' ),
    );

    if( function_exists( 'wlmapi_get_levels' ) ) {

        // Get all registered levels
        $levels = wlmapi_get_levels();

        // Check that levels are correctly setup
        if ( is_array( $levels )
            && isset( $levels['levels'] )
            && isset( $levels['levels']['level'] )
            && ! empty( $levels['levels']['level'] ) ) {

            // Loop levels to add them as options
            foreach ( $levels['levels']['level'] as $level ) {
                $options[$level['id']] = $level['name'];
            }

        }

    }

    return $options;

}