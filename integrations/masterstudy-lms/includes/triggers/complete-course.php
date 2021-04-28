<?php
/**
 * Complete Course
 *
 * @package     AutomatorWP\Integrations\MasterStudy_LMS\Triggers\Complete_Course
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_MasterStudy_LMS_Complete_Course extends AutomatorWP_Integration_Trigger {

    public $integration = 'masterstudy_lms';
    public $trigger = 'masterstudy_lms_complete_course';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User completes a course', 'automatorwp' ),
            'select_option'     => __( 'User completes <strong>a course</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User completes %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User completes %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'stm_lms_progress_updated',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Course:', 'automatorwp' ),
                    'option_none_label' => __( 'any course', 'automatorwp' ),
                    'post_type' => 'stm-courses'
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
     * @param int $user_id
     * @param int $course_id
     * @param int $progress Is a percent, 100 means complete
     */
    public function listener( $course_id, $user_id, $progress ) {

        // Bail if course not completed
        if( $progress < 100 ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
            'post_id'   => $course_id,
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

new AutomatorWP_MasterStudy_LMS_Complete_Course();