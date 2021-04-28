<?php
/**
 * User Course
 *
 * @package     AutomatorWP\Integrations\MasterStudy_LMS\Actions\User_Course
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_MasterStudy_LMS_User_Course extends AutomatorWP_Integration_Action {

    public $integration = 'masterstudy_lms';
    public $action = 'masterstudy_lms_user_course';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Enroll user from a course', 'automatorwp' ),
            'select_option'     => __( 'Enroll user from <strong>a course</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. */
            'edit_label'        => sprintf( __( 'Enroll user to %1$s', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'Enroll user to %1$s', 'automatorwp' ), '{post}' ),
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name'              => __( 'Course:', 'automatorwp' ),
                    'option_none_label' => __( 'all courses', 'automatorwp' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Course ID', 'automatorwp' ),
                    'post_type'         => 'stm-courses',
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

        $courses = array();

        // Check specific course
        if( $course_id !== 'any' ) {

            $course = get_post( $course_id );

            // Bail if course doesn't exists
            if( ! $course ) {
                return;
            }

            $courses = array( $course_id );

        }

        // If enrolling to all courses, get all courses
        if( $course_id === 'any' ) {

            $query = new WP_Query( array(
                'post_type'		=> 'stm-courses',
                'post_status'	=> 'publish',
                'fields'        => 'ids',
                'nopaging'      => true,
            ) );

            $courses = $query->get_posts();
        }

        // Enroll user in courses
        foreach( $courses as $course_id ) {
            STM_LMS_Course::add_user_course( $course_id, $user_id, 0, 0 );
            STM_LMS_Course::add_student( $course_id );
        }

    }

}

new AutomatorWP_MasterStudy_LMS_User_Course();