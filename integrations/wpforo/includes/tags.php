<?php
/**
 * Tags
 *
 * @package     AutomatorWP\wpForo\Tags
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Forum tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_wpforo_forum_tags() {

    $forum_tags = array(
        'wpforo_forum_id' => array(
            'label'     => __( 'Forum ID', 'automatorwp-wpforo' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'wpforo_forum_title' => array(
            'label'     => __( 'Forum Title', 'automatorwp-wpforo' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'wpforo_forum_url' => array(
            'label'     => __( 'Forum URL', 'automatorwp-wpforo' ),
            'type'      => 'text',
            'preview'   => get_option( 'home' ) . '/sample-forum',
        ),
        'wpforo_forum_link' => array(
            'label'     => __( 'Forum Link', 'automatorwp-wpforo' ),
            'type'      => 'text',
            'preview'   => '<a href="' . get_option( 'home' ) . '/sample-forum">' . __( 'Forum Title', 'automatorwp-wpforo' ) . '</a>',
        ),
    );

    /**
     * Filter forum tags
     *
     * @since 1.0.0
     *
     * @param array $tags
     *
     * @return array
     */
    return apply_filters( 'automatorwp_wpforo_forum_tags', $forum_tags );

}

/**
 * Topic tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_wpforo_topic_tags() {

    $topic_tags = array(
        'wpforo_topic_id' => array(
            'label'     => __( 'Topic ID', 'automatorwp-wpforo' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'wpforo_topic_title' => array(
            'label'     => __( 'Topic Title', 'automatorwp-wpforo' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'wpforo_topic_url' => array(
            'label'     => __( 'Topic URL', 'automatorwp-wpforo' ),
            'type'      => 'text',
            'preview'   => get_option( 'home' ) . '/sample-topic',
        ),
        'wpforo_topic_link' => array(
            'label'     => __( 'Topic Link', 'automatorwp-wpforo' ),
            'type'      => 'text',
            'preview'   => '<a href="' . get_option( 'home' ) . '/sample-topic">' . __( 'Topic Title', 'automatorwp-wpforo' ) . '</a>',
        ),
    );

    /**
     * Filter topic tags
     *
     * @since 1.0.0
     *
     * @param array $tags
     *
     * @return array
     */
    return apply_filters( 'automatorwp_wpforo_topic_tags', $topic_tags );

}

/**
 * Forum tag replacement
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
function automatorwp_wpforo_get_trigger_forum_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {

    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Bail if no order ID attached
    if( ! $trigger_args ) {
        return $replacement;
    }

    // Bail if trigger is not from this integration
    if( $trigger_args['integration'] !== 'wpforo' ) {
        return $replacement;
    }

    $tags = array_keys( automatorwp_wpforo_forum_tags() );

    // Bail if not order tags found
    if( ! in_array( $tag_name, $tags ) ) {
        return $replacement;
    }

    $forum_id = (int) automatorwp_get_log_meta( $log->id, 'forum_id', true );

    // Bail if no order ID attached
    if( $forum_id === 0 ) {
        return $replacement;
    }

    // Format values for some tags
    switch( $tag_name ) {
        case 'wpforo_forum_id':
            $replacement = $forum_id;
            break;
        case 'wpforo_forum_title':
            $replacement = wpforo_forum( $forum_id, 'title' );
            break;
        case 'wpforo_forum_url':
        case 'wpforo_forum_link':
            $url = wpforo_forum( $forum_id, 'url' );

            if( $tag_name === 'wpforo_forum_url' ) {
                $replacement = $url;
            } else {
                $replacement = '<a href="' . $url . '">' . wpforo_forum( $forum_id, 'title' ) . '</a>';
            }
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_wpforo_get_trigger_forum_tag_replacement', 10, 6 );

/**
 * Topic tag replacement
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
function automatorwp_wpforo_get_trigger_topic_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {

    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Bail if no order ID attached
    if( ! $trigger_args ) {
        return $replacement;
    }

    // Bail if trigger is not from this integration
    if( $trigger_args['integration'] !== 'wpforo' ) {
        return $replacement;
    }

    $tags = array_keys( automatorwp_wpforo_topic_tags() );

    // Bail if not order tags found
    if( ! in_array( $tag_name, $tags ) ) {
        return $replacement;
    }

    $topic_id = (int) automatorwp_get_log_meta( $log->id, 'topic_id', true );

    // Bail if no order ID attached
    if( $topic_id === 0 ) {
        return $replacement;
    }

    // Format values for some tags
    switch( $tag_name ) {
        case 'wpforo_topic_id':
            $replacement = $topic_id;
            break;
        case 'wpforo_topic_title':
            $replacement = wpforo_topic( $topic_id, 'title' );
            break;
        case 'wpforo_topic_url':
        case 'wpforo_topic_link':
            $url = wpforo_topic( $topic_id, 'url' );

            if( $tag_name === 'wpforo_topic_url' ) {
                $replacement = $url;
            } else {
                $replacement = '<a href="' . $url . '">' . wpforo_topic( $topic_id, 'title' ) . '</a>';
            }
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_wpforo_get_trigger_topic_tag_replacement', 10, 6 );