<?php
/**
 * Vote Poll
 *
 * @package     AutomatorWP\Integrations\WP_Polls\Triggers\Vote_Polls
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WP_Polls_Vote_Polls extends AutomatorWP_Integration_Trigger {

    public $integration = 'wp_polls';
    public $trigger = 'wp_polls_vote_poll';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User votes on a poll', 'automatorwp' ),
            'select_option'     => __( 'User <strong>votes</strong> on a poll', 'automatorwp' ),
            /* translators: %1$s: Post Title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User votes on %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post Title. */
            'log_label'         => sprintf( __( 'User votes on %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'wp_polls_vote_poll_success',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Poll:', 'automatorwp' ),
                    'option_none_label' => __( 'any poll', 'automatorwp' ),
                    'post_type' => 'wp_polls'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags( __( 'Poll', 'automatorwp' ) ),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     */
    public function listener() {

        // Get the poll ID
        $poll_id = ( isset($_REQUEST['poll_id'] ) ? (int) sanitize_key( $_REQUEST['poll_id'] ) : 0 );

        if( $poll_id === 0 ) {
            return;
        }

        $user_id = get_current_user_id();

        // Guests not allowed yet
        if( $user_id === 0 ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $user_id,
            'post_id' => $poll_id,
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

new AutomatorWP_WP_Polls_Vote_Polls();