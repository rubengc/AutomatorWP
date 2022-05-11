<?php
/**
 * Post Updated
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Post_Updated
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Post_Updated extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_post_updated';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User updates a post', 'automatorwp' ),
            'select_option'     => __( 'User updates <strong>a post</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User updates %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User updates %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'post_updated',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'post_type'             => 'any',
                    'option_none_label'     => __( 'any post', 'automatorwp' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Post ID', 'automatorwp' ),
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
     * @param WP_Post    $post_after    Post object following the update.
     * @param WP_Post    $post_before   Post object before the update.
     * @param int        $post_ID       The post ID
     */
    public function listener( $post_ID, $post_after, $post_before ) {

        // Check if it is an autosave or a revision.
        if ( wp_is_post_autosave( $post_ID ) || wp_is_post_revision( $post_ID ) ) {
            return;
        }

        // Bail if is a new post
        if( $post_before->post_status === 'auto-draft' ) {
            return;
        }
    
        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $post_after->post_author,
            'post_id' => $post_ID,
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

new AutomatorWP_WordPress_Post_Updated();