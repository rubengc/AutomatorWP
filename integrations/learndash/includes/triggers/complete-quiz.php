<?php
/**
 * Complete Quiz
 *
 * @package     AutomatorWP\Integrations\LearnDash\Triggers\Complete_Quiz
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_LearnDash_Complete_Quiz extends AutomatorWP_Integration_Trigger {

    public $integration = 'learndash';
    public $trigger = 'learndash_complete_quiz';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User attempts, passes or fails a quiz', 'automatorwp' ),
            'select_option'     => __( 'User <strong>attempts, passes or fails</strong> a quiz', 'automatorwp' ),
            /* translators: %1$s: Operation (attempts, passes or fails). %2$s: Post title. %3$s: Number of times. */
            'edit_label'        => sprintf( __( 'User %1$s %2$s %3$s time(s)', 'automatorwp' ), '{operation}', '{post}', '{times}' ),
            /* translators: %1$s: Operation (attempts, passes or fails). %2$s: Post title. */
            'log_label'         => sprintf( __( 'User %1$s %2$s', 'automatorwp' ), '{operation}', '{post}' ),
            'action'            => ( defined( 'LEARNDASH_VERSION' ) && version_compare( LEARNDASH_VERSION, '3.0.0', '>=' ) ? 'learndash_quiz_submitted' : 'learndash_quiz_completed' ),
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'operation' => array(
                    'from' => 'operation',
                    'fields' => array(
                        'operation' => array(
                            'name' => __( 'Operation:', 'automatorwp' ),
                            'type' => 'select',
                            'options' => array(
                                'attempt'     => __( 'attempts', 'automatorwp' ),
                                'pass'    => __( 'passes', 'automatorwp' ),
                                'fail'    => __( 'fails', 'automatorwp' ),
                            ),
                            'default' => 'attempt'
                        ),
                    )
                ),
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Quiz:', 'automatorwp' ),
                    'option_none_label' => __( 'any quiz', 'automatorwp' ),
                    'post_type' => 'sfwd-quiz'
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
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
            'post_id'   => $quiz_id,
            'course_id' => $course_id,
            'pass'      => $quiz_data['pass'],
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

        // Shorthand
        $operation = $trigger_options['operation'];

        // Ensure operation default value
        if( empty( $operation ) ) {
            $operation = 'attempt';
        }

        // Don't deserve if post is not received
        if( ! isset( $event['post_id'] ) && ! isset( $event['pass'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['post_id'], $trigger_options['post'] ) ) {
            return false;
        }

        switch ( $operation ) {
            case 'attempt':
                // Attempt doesn't requires any extra check
                break;
            case 'pass':
                // Don't deserve if user hasn't passed this quiz
                if( ! $event['pass'] ) {
                    return false;
                }
                break;
            case 'fail':
                // Don't deserve if user hasn't failed this quiz
                if( $event['pass'] ) {
                    return false;
                }
                break;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_LearnDash_Complete_Quiz();