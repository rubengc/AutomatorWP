<?php
/**
 * Vote Comment
 *
 * @package     AutomatorWP\Integrations\wpDiscuz\Triggers\Vote_Comment
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_wpDiscuz_Vote_Comment extends AutomatorWP_Integration_Trigger {

    public $integration = 'wpdiscuz';
    public $trigger = 'wpdiscuz_vote_comment';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User votes up/down a comment', 'automatorwp' ),
            'select_option'     => __( 'User <strong>votes up/down</strong> a comment', 'automatorwp' ),
            /* translators: %1$s: Vote Up/Down. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User %1$s a comment %2$s time(s)', 'automatorwp' ), '{vote}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User %1$s a comment', 'automatorwp' ), '{vote}' ),
            'action'            => 'automatorwp_wpdiscuz_add_vote',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'vote' => array(
                    'from' => 'vote',
                    'fields' => array(
                        'vote' => array(
                            'name' => __( 'Vote:', 'automatorwp' ),
                            'type' => 'select',
                            'options' => array(
                                'vote_up' => __( 'votes up', 'automatorwp' ),
                                'vote_down' => __( 'votes down', 'automatorwp' ),
                            ),
                            'default' => 'vote_up'
                        )
                    )
                ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_comment_tags(),
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
     * @param int           $vote           1 on vote up, -1 on vote down
     * @param WP_Comment    $comment        Comment object
     */
    public function listener( $vote, $comment ) {

        // Bail if this trigger is not in use
        if( ! automatorwp_is_trigger_in_use( $this->trigger ) ) {
            return;
        }

        $comment_id = $comment->comment_ID;
        $user_id = get_current_user_id();
        $comment_author_id = automatorwp_wpdiscuz_get_commment_user_id( $comment );
        $post_id = absint( $comment->comment_post_ID );

        // Bail if voter is not logged in
        if( $user_id === 0 ) {
            return;
        }

        // Bail if user has voted himself
        if( $user_id === $comment_author_id ) {
            return;
        }

        // Trigger the vote
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'comment_id'    => $comment_id,
            'post_id'       => $post_id,
            'vote'          => $vote,
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
        if( ! isset( $event['vote'] ) ) {
            return false;
        }

        // Don't deserve if vote doesn't match with the trigger option
        switch ( $trigger_options['vote'] ) {
            case 'vote_up':
                if( $event['vote'] < 0 ) {
                    return false;
                }
                break;
            case 'vote_down':
                if( $event['vote'] > 0 ) {
                    return false;
                }
                break;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_wpDiscuz_Vote_Comment();