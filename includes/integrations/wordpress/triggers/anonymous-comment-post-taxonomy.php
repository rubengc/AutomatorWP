<?php
/**
 * Anonymous Comment Post Taxonomy
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Anonymous_Comment_Post_Taxonomy
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Anonymous_Comment_Post_Taxonomy extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_anonymous_comment_post_taxonomy';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'anonymous'         => true,
            'label'             => __( 'Guest comments on a post of a taxonomy', 'automatorwp' ),
            'select_option'     => __( 'Guest comments on a post of <strong>a taxonomy</strong>', 'automatorwp' ),
            /* translators: %1$s: Term title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'Guest comments on a post of %1$s %2$s time(s)', 'automatorwp' ), '{term}', '{times}' ),
            /* translators: %1$s: Term title. */
            'log_label'         => sprintf( __( 'Guest comments on a post of %1$s', 'automatorwp' ), '{term}' ),
            'action'            => array(
                'comment_approved_',
                'comment_approved_comment',
                'wp_insert_comment',
            ),
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'term'  => automatorwp_utilities_taxonomy_option(),
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

        $taxonomies = get_object_taxonomies( $post->post_type );

        foreach( $taxonomies as $taxonomy ) {

            $terms_ids = automatorwp_get_term_ids( $post->ID, $taxonomy );

            // Bail if post isn't assigned to any category
            if( empty( $terms_ids ) ) {
                continue;
            }

            $user_id = (int) $comment['user_id'];

            // Bail if comment assigned to a user
            if( $user_id !== 0 ) {
                return;
            }

            automatorwp_trigger_event( array(
                'trigger'       => $this->trigger,
                'post_id'       => $post->ID,
                'comment_id'    => $comment_ID,
                'taxonomy'      => $taxonomy,
                'terms_ids'     => $terms_ids,
            ) );

        }

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
        if( ! isset( $event['post_id'] ) && ! isset( $event['taxonomy'] ) && ! isset( $event['terms_ids'] ) ) {
            return false;
        }

        // Don't deserve if taxonomy doesn't match with the trigger option
        if( $trigger_options['taxonomy'] !== 'any' && $trigger_options['taxonomy'] !== $event['taxonomy'] ) {
            return false;
        }

        // Don't deserve if term doesn't match with the trigger option
        if( ! automatorwp_terms_matches( $event['terms_ids'], $trigger_options['term'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WordPress_Anonymous_Comment_Post_Taxonomy();