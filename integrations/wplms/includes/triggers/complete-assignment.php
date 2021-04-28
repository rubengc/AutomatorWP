<?php
/**
 * Complete Assignment
 *
 * @package     AutomatorWP\Integrations\WPLMS\Triggers\Complete_Assignment
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WPLMS_Complete_Assignment extends AutomatorWP_Integration_Trigger {

    public $integration = 'wplms';
    public $trigger = 'wplms_complete_assignment';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User completes a assignment', 'automatorwp' ),
            'select_option'     => __( 'User <strong>completes</strong> a assignment', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User completes %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User completes %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'wplms_submit_assignment',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Assignment:', 'automatorwp' ),
                    'option_none_label' => __( 'any assignment', 'automatorwp' ),
                    'post_type' => 'assignment'
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
     * @param integer $assignment_id
     * @param integer $user_id
     */
    public function listener( $assignment_id, $user_id ) {

        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
            'post_id'   => $assignment_id,
            'operation' => 'complete',
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
            $operation = 'complete';
        }

        // Don't deserve if post is not received
        if( ! isset( $event['post_id'] ) && ! isset( $event['operation'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['post_id'], $trigger_options['post'] ) ) {
            return false;
        }

        // Don't deserve if operation doesn't match with the trigger option
        if( $operation !== $event['operation'] ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WPLMS_Complete_Assignment();