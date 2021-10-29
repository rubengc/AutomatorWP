<?php
/**
 * Complete Topic
 *
 * @package     AutomatorWP\Integrations\LearnDash\Triggers\Complete_Topic
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_LearnDash_Complete_Topic extends AutomatorWP_Integration_Trigger {

    public $integration = 'learndash';
    public $trigger = 'learndash_complete_topic';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User completes a topic', 'automatorwp' ),
            'select_option'     => __( 'User completes <strong>a topic</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User completes %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User completes %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'learndash_topic_completed',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Topic:', 'automatorwp' ),
                    'option_none_label' => __( 'any topic', 'automatorwp' ),
                    'post_type' => 'sfwd-topic'
                ) ),
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
     * @param array $args array(
     *      'user' => WP_User,
     *      'course' => WP_Post,
     *      'lesson' => WP_Post,
     *      'topic' => WP_Post,
     *      'progress' => array,
     * )
     */
    public function listener( $args ) {

        $user_id = $args['user']->ID;
        $topic_id = automatorwp_learndash_get_post_id( $args['topic'] );
        $lesson_id = automatorwp_learndash_get_post_id( $args['lesson'] );
        $course_id = automatorwp_learndash_get_post_id( $args['course'] );

        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
            'post_id'   => $topic_id,
            'lesson_id' => $lesson_id,
            'course_id' => $course_id,
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
        if( ! isset( $event['post_id'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['post_id'], $trigger_options['post'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_LearnDash_Complete_Topic();