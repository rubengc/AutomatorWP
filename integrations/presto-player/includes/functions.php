<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Presto_Player\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Options callback for select2 fields assigned to videos
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_presto_player_options_cb_video( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any video', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $video_id ) {

            // Skip option none
            if( $video_id === $none_value ) {
                continue;
            }

            $options[$video_id] = automatorwp_presto_player_get_video_title( $video_id );
        }
    }

    return $options;

}

/**
 * Get the video title
 *
 * @since 1.0.0
 *
 * @param int $video_id
 *
 * @return string|null
 */
function automatorwp_presto_player_get_video_title( $video_id ) {

    // Empty title if no ID provided
    if( absint( $video_id ) === 0 ) {
        return '';
    }

    $model = new \PrestoPlayer\Models\Video();

    if( ! $model ) {
        return '';
    }

    $video = $model->get( $video_id );

    if( ! $video ) {
        return '';
    }

    return $video->__get( 'title' );

}