<?php
/**
 * Mark Course
 *
 * @package     AutomatorWP\Integrations\LearnDash\Actions\Mark_Course
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_LearnDash_Mark_Course extends AutomatorWP_Integration_Action {

    public $integration = 'learndash';
    public $action = 'learndash_mark_course';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Mark course as completed', 'automatorwp' ),
            'select_option'     => __( 'Mark course as <strong>completed</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. */
            'edit_label'        => sprintf( __( 'Mark %1$s as completed', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'Mark %1$s as completed', 'automatorwp' ), '{post}' ),
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name'              => __( 'Course:', 'automatorwp' ),
                    'option_none_label' => __( 'all courses', 'automatorwp' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Course ID', 'automatorwp' ),
                    'post_type'         => 'sfwd-courses',
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
        $course_id = $action_options['post'];

        // Check specific course
        if( $course_id !== 'any' ) {

            $course = get_post( $course_id );

            // Bail if course doesn't exists
            if( ! $course ) {
                return;
            }

            $courses = array( $course_id );

        } else {

            // Get all user courses
            $user_courses = get_user_meta( $user_id, '_sfwd-course_progress', true );

            // Courses IDs are the keys of user courses meta array
            $courses = array_keys( $user_courses );

        }

        // Mark courses as completed
        foreach( $courses as $course_id ) {
            automatorwp_learndash_mark_course_as_completed( $user_id, $course_id );
        }

    }

}

new AutomatorWP_LearnDash_Mark_Course();