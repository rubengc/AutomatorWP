<?php
/**
 * Functions
 *
 * @package     AutomatorWP\wpForo\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Options callback for select2 fields assigned to forums
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_wpforo_options_cb_forum( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any forum', 'automatorwp-wpforo' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $forum_id ) {

            // Skip option none
            if( $forum_id === $none_value ) {
                continue;
            }

            $options[$forum_id] = automatorwp_wpforo_get_forum_title( $forum_id );
        }
    }

    return $options;

}

/**
 * Get the forum title
 *
 * @since 1.0.0
 *
 * @param int $forum_id
 *
 * @return string|null
 */
function automatorwp_wpforo_get_forum_title( $forum_id ) {

    // Empty title if no ID provided
    if( $forum_id === 0 ) {
        return '';
    }

    global $wpdb;

    if ( strpos( $forum_id, '-' ) ) {
        $board = explode('-', $forum_id)[0];
        $forum_id = explode('-', $forum_id)[1];
        $table = $table = $wpdb->prefix . 'wpforo_' . $board . '_forums';

    } else {
        $table = WPF()->tables->forums;
    }
    
    return $wpdb->get_var( $wpdb->prepare(
        "SELECT f.title FROM {$table} AS f WHERE f.forumid = %d",
        $forum_id
    ) );

}

/**
 * Options callback for select2 fields assigned to topics
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_wpforo_options_cb_topic( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any topic', 'automatorwp-wpforo' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $topic_id ) {

            // Skip option none
            if( $topic_id === $none_value ) {
                continue;
            }

            $options[$topic_id] = automatorwp_wpforo_get_topic_title( $topic_id );
        }
    }

    return $options;

}

/**
 * Get the topic title
 *
 * @since 1.0.0
 *
 * @param int $topic_id
 *
 * @return string|null
 */
function automatorwp_wpforo_get_topic_title( $topic_id ) {

    // Empty title if no ID provided
    if( absint( $topic_id ) === 0 ) {
        return '';
    }

    global $wpdb;

    if ( strpos( $topic_id, '-' ) ) {
        $board = explode('-', $topic_id)[0];
        $topic_id = explode('-', $topic_id)[1];
        $table = $table = $wpdb->prefix . 'wpforo_' . $board . '_topics';

    } else {
        $table = WPF()->tables->topics;
    }

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT f.title FROM {$table} AS f WHERE f.topicid = %d",
        $topic_id
    ) );

}