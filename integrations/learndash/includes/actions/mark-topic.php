<?php
/**
 * Mark Topic
 *
 * @package     AutomatorWP\Integrations\LearnDash\Actions\Mark_Topic
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_LearnDash_Mark_Topic extends AutomatorWP_Integration_Action {

    public $integration = 'learndash';
    public $action = 'learndash_mark_topic';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Mark topic as completed', 'automatorwp' ),
            'select_option'     => __( 'Mark topic as <strong>completed</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. */
            'edit_label'        => sprintf( __( 'Mark %1$s as completed', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'Mark %1$s as completed', 'automatorwp' ), '{post}' ),
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name'              => __( 'Topic:', 'automatorwp' ),
                    'option_none_label' => __( 'all topics', 'automatorwp' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Yopic ID', 'automatorwp' ),
                    'post_type'         => 'sfwd-topic',
                ) ),
            ),
        ) );

    }

    /**
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    public function execute( $action, $user_id, $action_options, $automation ) {

        // Shorthand
        $topic_id = $action_options['post'];

        // Courses have the following format: array( $course_id => array( $topic_id ) )
        // This way has been used to reduce the number of queries needed at the end of this execution
        $courses = array();

        // Check specific topic
        if( $topic_id !== 'any' ) {

            $topic = get_post( $topic_id );

            // Bail if topic doesn't exists
            if( ! $topic ) {
                return;
            }

            $course_id = learndash_get_course_id( $topic_id );

            $courses = array( $course_id => array( $topic_id ) );

        } else {

            // Get all user courses
            $user_courses = get_user_meta( $user_id, '_sfwd-course_progress', true );

            foreach( $user_courses as $course_id => $user_course ) {

                // Loop all topics completed (topics are separated in lessons)
                foreach( $user_course['topics'] as $lesson_id => $user_topics ) {

                    // Loop all lesson topics completed
                    foreach( $user_topics as $topic_id => $completed ) {

                        // Initialize course if not exists
                        if( ! isset( $courses[$course_id] ) ) {
                            $courses[$course_id] = array();
                        }

                        $courses[$course_id][] = $topic_id;

                    }

                }
            }

        }

        // Loop courses
        foreach( $courses as $course_id => $course_topics ) {

            // Mark topics as completed
            foreach( $course_topics as $topic_id ) {
                automatorwp_learndash_mark_topic_as_completed( $user_id, $topic_id, $course_id );
            }

        }

    }

}

new AutomatorWP_LearnDash_Mark_Topic();