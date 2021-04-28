<?php
/**
 * Favorite Post
 *
 * @package     AutomatorWP\Integrations\Favorites\Triggers\Favorite_Post
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Favorites_Favorite_Post extends AutomatorWP_Integration_Trigger {

    public $integration = 'favorites';
    public $trigger = 'favorites_favorite_post';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User favorites a post', 'automatorwp' ),
            'select_option'     => __( 'User <strong>favorites</strong> a post', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User favorites %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User favorites %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'favorites_after_favorite',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 4,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Post:', 'automatorwp' ),
                    'option_none_label' => __( 'any post', 'automatorwp' ),
                    'post_type_cb' => 'automatorwp_favorites_post_type_cb'
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
     * @param int       $post_id
     * @param string    $status
     * @param int       $site_id
     * @param int       $user_id
     */
    public function listener( $post_id, $status, $site_id, $user_id ) {

        // Just trigger active favorites
        if( $status !== 'active' ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
            'post_id'   => $post_id,
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

new AutomatorWP_Favorites_Favorite_Post();