<?php
/**
 * User Course
 *
 * @package     AutomatorWP\Integrations\LearnPress\Actions\User_Course
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_LearnPress_User_Course extends AutomatorWP_Integration_Action {

    public $integration = 'learnpress';
    public $action = 'learnpress_user_course';

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
                    'post_type'         => LP_COURSE_CPT,
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

        $user = learn_press_get_user( $user_id );

        // Bail if can't find the user
        if( ! $user ) {
            return;
        }

        $courses = array();

        // Check specific course
        if( $course_id !== 'any' ) {

            $course = get_post( $course_id );

            // Bail if course doesn't exists
            if( ! $course ) {
                return;
            }

            $courses = array( $course_id );

        } else {
            // If enrolling to all courses, get all courses

            $query = new WP_Query( array(
                'post_type'		=> LP_COURSE_CPT,
                'post_status'	=> 'publish',
                'fields'        => 'ids',
                'nopaging'      => true,
            ) );

            $courses = $query->get_posts();
        }

        // Enroll user in courses
        foreach( $courses as $course_id ) {

            // Skip if user is already on this course
            if( $user->has_enrolled_course( $course_id ) ) {
                continue;
            }

            $course = learn_press_get_course( $course_id );

            // Skip if course not exists
            if( ! $course ) {
                continue;
            }

            // Skip if course not exists
            if( ! $course->exists() ) {
                continue;
            }

            // Create a new order
            $order = new LP_Order();
            $order->set_customer_note( __( 'Order created by AutomatorWP', 'automatorwp' ) );
            $order->set_status( learn_press_default_order_status( 'lp-' ) );
            $order->set_user_id( $user_id );
            $order->set_user_ip_address( learn_press_get_ip() );
            $order->set_user_agent( learn_press_get_user_agent() );
            $order->set_created_via( 'AutomatorWP' );
            $order->set_subtotal( 0 );
            $order->set_total( 0 );

            // Save the order
            $order_id = $order->save();

            // Add the course as order item
            $order_item = array(
                'order_item_name'  => $course->get_title(),
                'item_id'          => $course_id,
                'quantity'         => 1,
                'subtotal'         => 0,
                'total'            => 0,
            );

            // Save the order item
            $item_id = $order->add_item( $order_item, 1 );

            // Force the order status update
            $order->update_status( 'completed' );

            // Create a new user item
            $user_item_data = array(
                'user_id' => $user->get_id(),
                'item_id' => $course_id,
                'ref_id'  => $order_id,
                'status'  => LP_COURSE_ENROLLED,
                'graduation' => LP_COURSE_GRADUATION_IN_PROGRESS,
            );

            $user_item_new = new LP_User_Item_Course( $user_item_data );

            // Save the user item
            $user_item_new->update();
                    
        }

    }

}

new AutomatorWP_LearnPress_User_Course();