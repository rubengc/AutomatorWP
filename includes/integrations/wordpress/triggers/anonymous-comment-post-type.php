<?php
/**
 * Anonymous Comment Post Type
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Anonymous_Comment_Post_Type
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Anonymous_Comment_Post_Type extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_anonymous_comment_post_type';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'anonymous'         => true,
            'label'             => __( 'Guest comments on a post of a type', 'automatorwp' ),
            'select_option'     => __( 'Guest comments on <strong>a post of a type</strong>', 'automatorwp' ),
            /* translators: %1$s: Post type. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'Guest comments on %1$s %2$s time(s)', 'automatorwp' ), '{post_type}', '{times}' ),
            /* translators: %1$s: Post type. */
            'log_label'         => sprintf( __( 'Guest comments on %1$s', 'automatorwp' ), '{post_type}' ),
            'action'            => 'comment_post',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'post_type' => array(
                    'from' => 'post_type',
                    'fields' => array(
                        'post_type' => array(
                            'name' => __( 'Post type:', 'automatorwp' ),
                            'type' => 'select',
                            'classes' => 'automatorwp-selector',
                            'options_cb' => 'automatorwp_options_cb_post_types',
                            'option_none' => true,
                            'default' => 'any'
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
     * @param int        $comment_ID        The comment ID.
     * @param int|string $comment_approved  1 if the comment is approved, 0 if not, 'spam' if spam.
     * @param array      $comment           Comment data.
     */
    public function listener( $comment_ID, $comment_approved, $comment ) {

        // Bail if comments is not approved
        if( $comment_approved !== 1 ) {
            return;
        }

        $post = get_post( $comment[ 'comment_post_ID' ] );

        // Bail if not post instanced
        if( ! $post instanceof WP_Post ) {
            return;
        }

        $user_id = (int) $comment['user_id'];

        // Bail if comment assigned to a user
        if( $user_id !== 0 ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'comment_id'    => $comment_ID,
            'post_id'       => $post->ID,
        ) );

    }

    /**
     * Anonymous deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if anonymous deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                         True if anonymous deserves trigger, false otherwise
     */
    public function anonymous_deserves_trigger( $deserves_trigger, $trigger, $event, $trigger_options, $automation ) {

        // Don't deserve if post is not received
        if( ! isset( $event['post_id'] ) ) {
            return false;
        }

        $post = get_post( absint( $event['post_id'] ) );

        // Don't deserve if post doesn't exists
        if( ! $post ) {
            return false;
        }

        $post_type = $trigger_options['post_type'];

        // Don't deserve if post doesn't match with the trigger option
        if( $post_type !== 'any' && $post->post_type !== $post_type ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WordPress_Anonymous_Comment_Post_Type();