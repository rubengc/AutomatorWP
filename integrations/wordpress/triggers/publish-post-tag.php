<?php
/**
 * Publish Post Tag
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Publish_Post_Tag
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Publish_Post_Tag extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_publish_post_tag';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User publishes a post with a tag', 'automatorwp' ),
            'select_option'     => __( 'User publishes a post with <strong>a tag</strong>', 'automatorwp' ),
            /* translators: %1$s: Term title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User publishes a post with %1$s %2$s time(s)', 'automatorwp' ), '{term}', '{times}' ),
            /* translators: %1$s: Term title. */
            'log_label'         =>  sprintf( __( 'User publishes a post with %1$s', 'automatorwp' ), '{term}' ),
            'action'            => 'transition_post_status',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'term' => automatorwp_utilities_term_option( array(
                    'name'              => __( 'Tag:', 'automatorwp' ),
                    'option_none_label' => __( 'any tag', 'automatorwp' ),
                    'taxonomy'          => 'post_tag',
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
     * @param string    $new_status The new post status
     * @param string    $old_status The old post status
     * @param WP_Post   $post       The post
     */
    public function listener( $new_status, $old_status, $post ) {

        // Bail if not is a post
        if( $post->post_type !== 'post' ) {
            return;
        }

        // Bail if post has been already published
        if( $old_status === 'publish' ) {
            return;
        }

        // Bail if post is not published
        if( $new_status !== 'publish' ) {
            return;
        }

        // When inserting a post through the block editor, the post gets updated through rest API
        // This is required since on rest saving the post gets updated BEFORE get taxonomies applied
        if( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            // Hook the action 'rest_after_insert_{post_type}'
            add_action( 'rest_after_insert_post', array( $this, 'rest_listener' ),  10, 3 );
            return;
        }

        $terms_ids = automatorwp_get_term_ids( $post->ID, 'post_tag' );

        // Bail if post isn't assigned to any tag
        if( empty( $terms_ids ) ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $post->post_author,
            'post_id' => $post->ID,
            'terms_ids' => $terms_ids,
        ) );

    }

    /**
     * Trigger rest listener
     * This listener is required since on rest saving the post gets updated BEFORE get taxonomies applied
     *
     * @since 1.0.0
     *
     * @param WP_Post         $post     Inserted or updated post object.
     * @param WP_REST_Request $request  Request object.
     * @param bool            $creating True when creating a post, false when updating.
     */
    public function rest_listener( $post, $request, $creating ) {

        $terms_ids = automatorwp_get_term_ids( $post->ID, 'post_tag' );

        // Bail if post isn't assigned to any tag
        if( empty( $terms_ids ) ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $post->post_author,
            'post_id' => $post->ID,
            'terms_ids' => $terms_ids,
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

        // Don't deserve if post and terms IDs are not received
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

new AutomatorWP_WordPress_Publish_Post_Tag();