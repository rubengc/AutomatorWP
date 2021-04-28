<?php
/**
 * Rate Post
 *
 * @package     AutomatorWP\Integrations\WP_PostRatings\Triggers\Rate_Post
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WP_PostRatings_Rate_Post extends AutomatorWP_Integration_Trigger {

    public $integration = 'wp_postratings';
    public $trigger = 'wp_postratings_rate_post';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        // All public post types
        $post_types = get_post_types( array(
            'public' => true
        ) );

        // Remove keys
        $post_types = array_values( $post_types );

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User rates a post', 'automatorwp' ),
            'select_option'     => __( 'User <strong>rates</strong> a post', 'automatorwp' ),
            /* translators: %1$s: Post Title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User rates %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post Title. */
            'log_label'         => sprintf( __( 'User rates %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'rate_post',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'post_type' => $post_types
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
     * @param int $user_id
     * @param int $post_id
     * @param int $rating_value
     */
    public function listener( $user_id, $post_id, $rating_value ) {

        // Bail if can't find the user ID
        if( $user_id === 0 ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
            'post_id'   => $post_id,
            'rating'    => $rating_value,
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

new AutomatorWP_WP_PostRatings_Rate_Post();