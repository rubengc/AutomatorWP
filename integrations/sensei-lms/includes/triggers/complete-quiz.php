<?php
/**
 * Complete Quiz
 *
 * @package     AutomatorWP\Integrations\Sensei_LMS\Triggers\Complete_Quiz
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Sensei_LMS_Complete_Quiz extends AutomatorWP_Integration_Trigger {

    public $integration = 'sensei_lms';
    public $trigger = 'sensei_lms_complete_quiz';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User attempts a quiz', 'automatorwp' ),
            'select_option'     => __( 'User <strong>attempts</strong> a quiz', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User attempts %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User attempts %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'sensei_user_quiz_grade',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 5,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Quiz:', 'automatorwp' ),
                    'option_none_label' => __( 'any quiz', 'automatorwp' ),
                    'post_type' => 'quiz'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags( __( 'Quiz', 'automatorwp' ) ),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param  integer $user_id         ID of user being graded
     * @param  integer $quiz_id         ID of quiz
     * @param  integer $grade           Grade received
     * @param  integer $quiz_passmark   Quiz required pass mark
     * @param  string $quiz_grade_type  default 'auto'
     */
    public function listener( $user_id, $quiz_id, $grade, $quiz_passmark, $quiz_grade_type ) {

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'post_id'       => $quiz_id,
            'percentage'    => $grade,
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

new AutomatorWP_Sensei_LMS_Complete_Quiz();