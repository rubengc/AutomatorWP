<?php
/**
 * Complete Quiz Percentage
 *
 * @package     AutomatorWP\Integrations\LearnDash\Triggers\Complete_Quiz_Percentage
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_LearnDash_Complete_Quiz_Percentage extends AutomatorWP_Integration_Trigger {

    public $integration = 'learndash';
    public $trigger = 'learndash_complete_quiz_score';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User completes a quiz with a grade percentage greater than, less than or equal to a specific percentage', 'automatorwp' ),
            'select_option'     => __( 'User completes a quiz with a grade percentage <strong>greater than, less than or equal</strong> to a specific percentage', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Condition. %3$s: Percent. %4$s: Number of times. */
            'edit_label'        => sprintf( __( 'User completes %1$s with a grade percentage %2$s %3$s %4$s time(s)', 'automatorwp' ), '{post}', '{condition}', '{percentage}', '{times}' ),
            /* translators: %1$s: Post title. %2$s: Condition. %3$s: Percent. */
            'log_label'         => sprintf( __( 'User completes %1$s with a grade percentage %2$s %3$s', 'automatorwp' ), '{post}', '{condition}', '{percentage}' ),
            'action'            => ( defined( 'LEARNDASH_VERSION' ) && version_compare( LEARNDASH_VERSION, '3.0.0', '>=' ) ? 'learndash_quiz_submitted' : 'learndash_quiz_completed' ),
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Quiz:', 'automatorwp' ),
                    'option_none_label' => __( 'any quiz', 'automatorwp' ),
                    'post_type' => 'sfwd-quiz'
                ) ),
                'condition' => automatorwp_utilities_condition_option(),
                'percentage' => array(
                    'from' => 'percentage',
                    'fields' => array(
                        'percentage' => array(
                            'name' => __( 'Percentage:', 'automatorwp' ),
                            'desc' => __( 'The grade percentage required.', 'automatorwp' ),
                            'type' => 'text',
                            'attributes' => array(
                                'type' => 'number',
                                'min' => '0',
                            ),
                            'default' => 0
                        )
                    )
                ),
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
     * @param array $quiz_data array(
     *      'course' => WP_Post,
     *      'quiz' => WP_Post,
     *      'pass' => integer,
     *      'percentage' => integer,
     * )
     * @param WP_User $current_user
     */
    public function listener( $quiz_data, $current_user ) {

        $user_id = $current_user->ID;
        $quiz_id = automatorwp_learndash_get_post_id( $quiz_data['quiz'] );
        $course_id = automatorwp_learndash_get_post_id( $quiz_data['course'] );

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'post_id'       => $quiz_id,
            'course_id'     => $course_id,
            'percentage'    => $quiz_data['percentage'],
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
        if( ! isset( $event['post_id'] ) && ! isset( $event['percentage'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['post_id'], $trigger_options['post'] ) ) {
            return false;
        }

        $percentage = (float) $event['percentage'];
        $required_percentage =  (float) $trigger_options['percentage'];

        // Don't deserve if order total doesn't match with the trigger option
        if( ! automatorwp_number_condition_matches( $percentage, $required_percentage, $trigger_options['condition'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_LearnDash_Complete_Quiz_Percentage();