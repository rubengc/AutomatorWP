<?php
/**
 * Comment Post Type
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Comment_Post_Type
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Comment_Post_Type extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_comment_post_type';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User comments on a post of a type', 'automatorwp' ),
            'select_option'     => __( 'User comments on <strong>a post of a type</strong>', 'automatorwp' ),
            /* translators: %1$s: Post type. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User comments on %1$s %2$s time(s)', 'automatorwp' ), '{post_type}', '{times}' ),
            /* translators: %1$s: Post type. */
            'log_label'         => sprintf( __( 'User comments on %1$s', 'automatorwp' ), '{post_type}' ),
            'action'            => array(
                'comment_approved_',
                'comment_approved_comment',
                'wp_insert_comment',
            ),
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
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
     * @param int               $comment_ID        The comment ID.
     * @param array|WP_Comment  $comment           The Comment.
     */
    public function listener( $comment_ID, $comment ) {

        // Ensure comment as array (wp_insert_comment uses object, comment_{status}_comment uses array)
        if ( is_object( $comment ) ) {
            $comment = get_object_vars( $comment );
        }

        // Check if comment is approved
        if ( (int) $comment['comment_approved'] !== 1 ) {
            return;
        }

        $post = get_post( absint( $comment[ 'comment_post_ID' ] ) );

        // Bail if not post instanced
        if( ! $post instanceof WP_Post ) {
            return;
        }

        $user_id = (int) $comment['user_id'];

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'comment_id'    => $comment_ID,
            'post_id'       => $post->ID,
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

new AutomatorWP_WordPress_Comment_Post_Type();