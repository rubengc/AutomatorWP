<?php
/**
 * Like Comment
 *
 * @package     AutomatorWP\Integrations\WP_Ulike\Triggers\Like_Comment
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WP_Ulike_Like_Comment extends AutomatorWP_Integration_Trigger {

    public $integration = 'wp_ulike';
    public $trigger = 'wp_ulike_like_comment';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User likes a comment of a post', 'automatorwp' ),
            'select_option'     => __( 'User likes <strong>a comment</strong> of a post', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User likes a comment of %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User likes a comment of %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'wp_ulike_after_process',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 4,
            'options'           => array(
                'post' => automatorwp_utilities_post_option(),
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
     * @param int       $id         Liked element ID
     * @param string    $key        Like key
     * @param int       $user_id    User ID
     * @param string    $status     like|unlike
     */
    public function listener( $id, $key, $user_id, $status ) {

        // Bail if not is a comment like
        if( $key !== '_commentliked' ) {
            return;
        }

        $comment = get_comment( $id );

        // Bail if can't find the comment
        if( ! $comment ) {
            return;
        }

        // Bail if not is a like
        if( $status !== 'like' ) {
            return;
        }

        // Trigger the like or unlike
        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
            'post_id'   => $comment->comment_post_ID,
            'status'    => $status,
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

        // Don't deserve if post and status are not received
        if( ! isset( $event['post_id'] ) && ! isset( $event['status'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['post_id'], $trigger_options['post'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WP_Ulike_Like_Comment();