<?php
/**
 * Create Reply
 *
 * @package     AutomatorWP\Integrations\wpForo\Triggers\Create_Reply
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_wpForo_Create_Reply extends AutomatorWP_Integration_Trigger {

    public $integration = 'wpforo';
    public $trigger = 'wpforo_create_reply';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User replies to a topic of a forum', 'automatorwp' ),
            'select_option'     => __( 'User <strong>replies</strong> to a topic of a forum', 'automatorwp' ),
            /* translators: %1$s: Topic title. %2$s: Forum title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User replies to %1$s of %2$s %3$s time(s)', 'automatorwp' ), '{topic}', '{forum}', '{times}' ),
            /* translators: %1$s: Topic title. %2$s: Forum title. */
            'log_label'         => sprintf( __( 'User replies to %1$s of %2$s', 'automatorwp' ), '{topic}', '{forum}' ),
            'action'            => 'wpforo_after_add_post',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'topic' => array(
                    'from' => 'topic',
                    'fields' => array(
                        'topic' => automatorwp_utilities_post_field( array(
                            'name' => __( 'Topic:', 'automatorwp' ),
                            'option_none_label' => __( 'any topic', 'automatorwp' ),
                            'post_type' => 'wpforo_topic'
                        ) )
                    )
                ),
                'forum' => array(
                    'from' => 'forum',
                    'fields' => array(
                        'forum' => automatorwp_utilities_post_field( array(
                            'name' => __( 'Forum:', 'automatorwp' ),
                            'option_none_label' => __( 'any forum', 'automatorwp' ),
                            'post_type' => 'wpforo_forum'
                        ) )
                    )
                ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param array $post
     * @param array $topic
     */
    public function listener( $post, $topic ) {

        $user_id = $post['userid'];

        // Bail if not user provided
        if( $user_id === 0 ) {
            return;
        }

        $reply_id = $post['postid'];
        $topic_id = $topic['topicid'];
        $forum_id = $post['forumid'];

        // Trigger the create a reply
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'post_id'       => $reply_id,
            'topic_id'      => $topic_id,
            'forum_id'      => $forum_id,
        ) );

    }

    /**
     * User deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if user deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    public function user_deserves_trigger( $deserves_trigger, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Don't deserve if post is not received
        if( ! isset( $event['post_id'] ) && ! isset( $event['topic_id'] ) && ! isset( $event['forum_id'] ) ) {
            return false;
        }

        // Don't deserve if topic doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['topic_id'], $trigger_options['topic'] ) ) {
            return false;
        }

        // Don't deserve if forum doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['forum_id'], $trigger_options['forum'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_wpForo_Create_Reply();