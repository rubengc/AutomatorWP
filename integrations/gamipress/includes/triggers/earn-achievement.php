<?php
/**
 * Earn Achievement
 *
 * @package     AutomatorWP\Integrations\GamiPress\Triggers\Earn_Achievement
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_GamiPress_Earn_Achievement extends AutomatorWP_Integration_Trigger {

    public $integration = 'gamipress';
    public $trigger = 'gamipress_earn_achievement';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User earns an achievement', 'automatorwp' ),
            'select_option'     => __( 'User earns <strong>an achievement</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User earns %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User earns %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'gamipress_award_achievement',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 5,
            'options'           => array(
                'post' => automatorwp_gamipress_utilities_post_option( array(
                    'name' => __( 'Achievement:', 'automatorwp' ),
                    'option_none_label' => __( 'any achievement', 'automatorwp' ),
                    'post_type_cb' => 'gamipress_get_achievement_types_slugs'
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
     * @param int       $user_id        The user ID
     * @param int       $achievement_id The achievement ID
     * @param string    $trigger        The trigger
     * @param int       $site_id        Site ID
     * @param array     $args           Event arguments
     */
    public function listener( $user_id, $achievement_id, $trigger, $site_id, $args ) {

        $post = get_post( $achievement_id );

        // Bail if not post instanced
        if( ! $post instanceof WP_Post ) {
            return;
        }

        // Bail if post type is not an achievement
        if( ! in_array(  $post->post_type, gamipress_get_achievement_types_slugs() ) ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $user_id,
            'post_id' => $post->ID,
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

new AutomatorWP_GamiPress_Earn_Achievement();