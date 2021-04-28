<?php
/**
 * Comment Post Category
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Comment_Post_Category
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Comment_Post_Category extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_comment_post_category';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User comments on a post of a category', 'automatorwp' ),
            'select_option'     => __( 'User comments on a post of <strong>a category</strong>', 'automatorwp' ),
            /* translators: %1$s: Term title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User comments on a post of %1$s %2$s time(s)', 'automatorwp' ), '{term}', '{times}' ),
            /* translators: %1$s: Term title. */
            'log_label'         => sprintf( __( 'User comments on a post of %1$s', 'automatorwp' ), '{term}' ),
            'action'            => array(
                'comment_approved_',
                'comment_approved_comment',
                'wp_insert_comment',
            ),
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'term' => automatorwp_utilities_term_option(),
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

        // Bail if post type is not a post
        if( $post->post_type !== 'post' ) {
            return;
        }

        $terms_ids = automatorwp_get_term_ids( $post->ID, 'category' );

        // Bail if post isn't assigned to any category
        if( empty( $terms_ids ) ) {
            return;
        }

        $user_id = (int) $comment['user_id'];

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'comment_id'    => $comment_ID,
            'post_id'       => $post->ID,
            'terms_ids'     => $terms_ids,
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
        if( ! isset( $event['post_id'] ) && ! isset( $event['terms_ids'] ) ) {
            return false;
        }

        // Don't deserve if term doesn't match with the trigger option
        if( ! automatorwp_terms_matches( $event['terms_ids'], $trigger_options['term'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WordPress_Comment_Post_Category();