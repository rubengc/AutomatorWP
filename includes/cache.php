<?php
/**
 * AutomatorWP Cache
 *
 * Used to store commonly used query results
 *
 * @package     AutomatorWP\Cache
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get a cached element.
 *
 * @since 1.0.0
 *
 * @param string    $key        Cache key
 * @param mixed     $default    Default value in case the cache is not found
 * @param bool      $stored     Whatever if the cache has been stored previously in the database or not
 *
 * @return mixed
 */
function automatorwp_get_cache( $key = '', $default = null, $stored = true ) {

    if( isset( AutomatorWP()->cache[$key] ) ) {
        return AutomatorWP()->cache[$key];
    } else if( $stored ) {

        $cached = get_option( 'automatorwp_cache_' . $key );

        // If has been cached on options, then return the cached value
        if( $cached !== false ) {

            AutomatorWP()->cache[$key] = $cached;

            return AutomatorWP()->cache[$key];

        }

    }

    return $default;

}

/**
 * Set a cached element.
 *
 * @since 1.0.0
 *
 * @param string    $key
 * @param mixed     $value
 * @param bool      $save
 *
 * @return bool
 */
function automatorwp_set_cache( $key = '', $value = '', $save = false ) {

    // Just keep value on a floating cache
    // To make it persistent pass $save as true or use automatorwp_save_cache() function
    AutomatorWP()->cache[$key] = $value;

    if( $save === true ) {
        return automatorwp_save_cache( $key, $value );
    }

    return true;

}

/**
 * Save a cached element.
 *
 * @since 1.6.1
 *
 * @param string    $key
 * @param mixed     $value
 *
 * @return bool
 */
function automatorwp_save_cache( $key = '', $value = '' ) {

    // Allow to make value optional but just if element has been already cached
    if( empty( $value ) && isset( AutomatorWP()->cache[$key] ) ) {
        $value = AutomatorWP()->cache[$key];
    }

    // Update the floating cache
    AutomatorWP()->cache[$key] = $value;

    return update_option( 'automatorwp_cache_' . $key, $value, false );

}

/**
 * Delete a cached element.
 *
 * @since 1.6.1
 *
 * @param string    $key
 *
 * @return bool
 */
function automatorwp_delete_cache( $key = '' ) {

    return delete_option( 'automatorwp_cache_' . $key );

}