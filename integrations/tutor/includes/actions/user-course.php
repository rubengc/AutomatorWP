<?php
/**
 * User Course
 *
 * @package     AutomatorWP\Integrations\Tutor_LMS\Actions\User_Course
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Tutor_LMS_User_Course extends AutomatorWP_Integration_Action {

    public $integration = 'tutor';
    public $action = 'tutor_user_course';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Enroll user to a course', 'automatorwp' ),
            'select_option'     => __( 'Enroll user to <strong>a course</strong>', 'automatorwp' ),
            /* translators: %1$s: Operation (add or remove). %2$s: Post title. */
            'edit_label'        => sprintf( __( 'Enroll user to %1$s', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Operation (add or remove). %2$s: Post title. */
            'log_label'         => sprintf( __( 'Enroll user to %1$s', 'automatorwp' ), '{post}' ),
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name'              => __( 'Course:', 'automatorwp' ),
                    'option_none_label' => __( 'all courses', 'automatorwp' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Course ID', 'automatorwp' ),
                    'post_type'         => apply_filters( 'tutor_course_post_type', 'courses' ),
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
                'post_type'		=> apply_filters( 'tutor_course_post_type', 'courses' ),
                'post_status'	=> 'publish',
                'fields'        => 'ids',
                'nopaging'      => true,
            ) );

            $courses = $query->get_posts();
        }

        // Enroll user in courses
        foreach( $courses as $course_id ) {

            // To know if is a paid course
            $is_purchasable = tutor_utils()->is_course_purchasable( $course_id );

            if ( ! $is_purchasable ){

                // Add a filter to force all enrollment to completed
                add_filter( 'tutor_enroll_data', array( $this, 'force_enrollment_status_to_completed' ) );

                // Add student in free course
                tutor_utils()->do_enroll( $course_id, $order_id = 0, $user_id );

            } else{

                do_action( 'tutor_before_enroll', $course_id );
                $title = __('Course Enrolled', 'tutor') . " &ndash; ".date_i18n( get_option( 'date_format' ) ) .' @ '.date_i18n( get_option( 'time_format' ) ) ;
                $enroll_data = apply_filters( 'tutor_enroll_data',
                    array(
                        'post_type'     => 'tutor_enrolled',
                        'post_title'    => $title,
                        'post_status'   => 'completed',
                        'post_author'   => $user_id,
                        'post_parent'   => $course_id,
                    )
                );

                // Get the students in the paid course
                $obj_student=new \TUTOR\Course_List();
                $list_students = $obj_student->course_enrollments_with_student_details( $course_id );
                $user_exists = FALSE;

                foreach( $list_students['enrollments'] as $student ){

                    if ($user_id == $student->ID){
                        $user_exists = TRUE;
                    }

                }

                if ( ! $user_exists ){
                    // Insert the post into the database
                    $isEnrolled = wp_insert_post( $enroll_data );

                    if ($isEnrolled) {

                        do_action('tutor_after_enroll', $course_id, $isEnrolled);

                        //Mark Current User as Students with user meta data
                        update_user_meta( $user_id, '_is_tutor_student', time() );
                        $product_id = tutor_utils()->get_course_product_id($course_id);
                        update_post_meta( $isEnrolled, '_tutor_enrolled_by_product_id', $product_id );

                    }
                }

            }

        }

        // Remove the filter added previously
        remove_filter( 'tutor_enroll_data', array( $this, 'force_enrollment_status_to_completed' ) );

    }

    /**
     * Forces the enrollment status to completed
     *
     * @param array $enrollment
     *
     * @return array
     */
    public function force_enrollment_status_to_completed( $enrollment ) {

        $enrollment['status'] = 'completed';

        return $enrollment;

    }

}

new AutomatorWP_Tutor_LMS_User_Course();