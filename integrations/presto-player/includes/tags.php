<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Presto_Player\Tags
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Video tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_presto_player_video_tags() {

    $video_tags = array(
        'presto_player_video_id' => array(
            'label'     => __( 'Video ID', 'automatorwp' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'presto_player_video_title' => array(
            'label'     => __( 'Video Title', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => __( 'The title', 'automatorwp' ),
        ),
        'presto_player_video_src' => array(
            'label'     => __( 'Video Source', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => get_option( 'home' ) . '/sample-video',
        ),
    );

    /**
     * Filter video tags
     *
     * @since 1.0.0
     *
     * @param array $tags
     *
     * @return array
     */
    return apply_filters( 'automatorwp_presto_player_video_tags', $video_tags );

}

/**
 * Custom trigger tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $replacement    The tag replacement
 * @param string    $tag_name       The tag name (without "{}")
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 * @param stdClass  $log            The last trigger log object
 *
 * @return string
 */
function automatorwp_presto_player_get_trigger_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {

    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Bail if no video ID attached
    if( ! $trigger_args ) {
        return $replacement;
    }

    // Bail if trigger is not from this integration
    if( $trigger_args['integration'] !== 'presto_player' ) {
        return $replacement;
    }

    $tags = array_keys( automatorwp_presto_player_video_tags() );

    // Bail if not video tags found
    if( ! in_array( $tag_name, $tags ) ) {
        return $replacement;
    }

    $video_id = (int) automatorwp_get_log_meta( $log->id, 'video_id', true );

    // Bail if no video ID attached
    if( $video_id === 0 ) {
        return $replacement;
    }

    $model = new \PrestoPlayer\Models\Video();

    if( ! $model ) {
        return $replacement;
    }

    $video = $model->get( $video_id );

    if( ! $video ) {
        return $replacement;
    }

    // Get the tag replacement
    switch( $tag_name ) {
        case 'presto_player_video_id':
            $replacement = $video_id;
            break;
        case 'presto_player_video_title':
            $replacement = $video->__get( 'title' );
            break;
        case 'presto_player_video_src':
            $replacement = $video->__get( 'src' );
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_presto_player_get_trigger_tag_replacement', 10, 6 );