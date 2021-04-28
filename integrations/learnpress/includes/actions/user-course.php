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

            $course = learn_press_get_course( $course_id );
            $order_id = 0;

            // Skip if user is already on this course
            if( $user->has_course_status( $course_id, 'enrolled' ) ) {
                continue;
            }

            // Skip if course not exists
            if( ! $course ) {
                continue;
            }

            // Skip if course not exists
            if( ! $course->exists() ) {
                continue;
            }

            if ( ! $course->is_free() ) {
                // Create a new order
                $order = new LP_Order();
                $order->set_customer_note( __( 'Order created by AutomatorWP', 'automatorwp' ) );
                $order->set_status( 'lp-completed' );
                $order->set_subtotal( 0 );
                $order->set_total( 0 );
                $order->set_user_id( $user_id );
                $order->set_user_ip_address( learn_press_get_ip() );
                $order->set_user_agent( learn_press_get_user_agent() );
                $order->set_created_via( 'AutomatorWP' );


                // Save the order
                $order_id = $order->save();

                // Add the course as order item
                $order_item                     = [];
                $order_item['order_item_name']  = $course->get_title();
                $order_item['item_id']          = $course_id;
                $order_item['quantity']         = 1;
                $order_item['subtotal']         = 0;
                $order_item['total']            = 0;
                $order->add_item( $order_item, 1 );

                learn_press_update_user_item_field( array(
                    'user_id'    => $user->get_id(),
                    'item_id'    => $course->get_id(),
                    'start_time' => current_time( 'mysql' ),
                    'status'     => 'enrolled',
                    'end_time'   => '0000-00-00 00:00:00',
                    'ref_id'     => $order->get_id(),
                    'item_type'  => 'lp_course',
                    'ref_type'   => 'lp_order',
                    'parent_id'  => $user->get_course_history_id( $course->get_id() )
                ) );
            }

            $user->enroll( $course_id, $order_id, true );
            delete_transient( 'checkout_enroll_course_id' );
                    
        }

    }

}

new AutomatorWP_LearnPress_User_Course();