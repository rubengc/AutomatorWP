<?php
/**
 * Mark Lesson
 *
 * @package     AutomatorWP\Integrations\LearnDash\Actions\Mark_Lesson
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_LearnDash_Mark_Lesson extends AutomatorWP_Integration_Action {

    public $integration = 'learndash';
    public $action = 'learndash_mark_lesson';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Mark lesson as completed', 'automatorwp' ),
            'select_option'     => __( 'Mark lesson as <strong>completed</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. */
            'edit_label'        => sprintf( __( 'Mark %1$s as completed', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'Mark %1$s as completed', 'automatorwp' ), '{post}' ),
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name'              => __( 'Lesson:', 'automatorwp' ),
                    'option_none_label' => __( 'all lessons', 'automatorwp' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Lesson ID', 'automatorwp' ),
                    'post_type'         => 'sfwd-lessons',
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
        $lesson_id = $action_options['post'];

        // Courses have the following format: array( $course_id => array( $lesson_id ) )
        // This way has been used to reduce the number of queries needed at the end of this execution
        $courses = array();

        // Check specific lesson
        if( $lesson_id !== 'any' ) {

            $lesson = get_post( $lesson_id );

            // Bail if lesson doesn't exists
            if( ! $lesson ) {
                return;
            }

            $course_id = learndash_get_course_id( $lesson_id );

            $courses = array( $course_id => array( $lesson_id ) );

        } else {

            // Get all user courses
            $user_courses = get_user_meta( $user_id, '_sfwd-course_progress', true );

            foreach( $user_courses as $course_id => $user_course ) {

                // Loop all lessons completed
                foreach( $user_course['lessons'] as $lesson_id => $completed ) {

                    // Initialize course if not exists
                    if( ! isset( $courses[$course_id] ) ) {
                        $courses[$course_id] = array();
                    }

                    $courses[$course_id][] = $lesson_id;

                }
            }

        }

        // Loop courses
        foreach( $courses as $course_id => $course_lessons ) {

            // Mark lessons as completed
            foreach( $course_lessons as $lesson_id ) {
                automatorwp_learndash_mark_lesson_as_completed( $user_id, $lesson_id, $course_id );
            }

        }

    }

}

new AutomatorWP_LearnDash_Mark_Lesson();