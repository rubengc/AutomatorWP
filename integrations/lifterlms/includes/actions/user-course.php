<?php
/**
 * User Course
 *
 * @package     AutomatorWP\Integrations\LifterLMS\Actions\User_Course
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_LifterLMS_User_Course extends AutomatorWP_Integration_Action {

    public $integration = 'lifterlms';
    public $action = 'lifterlms_user_course';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Enroll user in a course', 'automatorwp' ),
            'select_option'     => __( 'Enroll user in <strong>a course</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. */
            'edit_label'        => sprintf( __( 'Enroll user in %1$s', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'Enroll user in %1$s', 'automatorwp' ), '{post}' ),
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name'              => __( 'Course:', 'automatorwp' ),
                    'option_none_label' => __( 'all courses', 'automatorwp' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Course ID', 'automatorwp' ),
                    'post_type'         => 'course',
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

        } else if( $course_id === 'any' ) {

            // Get all courses
            $query = new WP_Query( array(
                'post_type'		=> 'course',
                'post_status'	=> 'publish',
                'fields'        => 'ids',
                'nopaging'      => true,
            ) );

            $courses = $query->get_posts();
        }

        // Enroll user in courses
        foreach( $courses as $course_id ) {
            llms_enroll_student( $user_id, $course_id );
        }

    }

}

new AutomatorWP_LifterLMS_User_Course();