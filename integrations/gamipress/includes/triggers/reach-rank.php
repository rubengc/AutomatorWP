<?php
/**
 * Reach Rank
 *
 * @package     AutomatorWP\Integrations\GamiPress\Triggers\Reach_Rank
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_GamiPress_Reach_Rank extends AutomatorWP_Integration_Trigger {

    public $integration = 'gamipress';
    public $trigger = 'gamipress_reach_rank';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User reaches a rank', 'automatorwp' ),
            'select_option'     => __( 'User reaches <strong>a rank</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User reaches %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User reaches %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'gamipress_update_user_rank',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 5,
            'options'           => array(
                'post' => automatorwp_gamipress_utilities_post_option( array(
                    'name' => __( 'Rank:', 'automatorwp' ),
                    'option_none_label' => __( 'any rank', 'automatorwp' ),
                    'post_type_cb' => 'gamipress_get_rank_types_slugs'
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
     * @param WP_Post   $new_rank       The new rank object
     * @param WP_Post   $old_rank       The old rank object
     * @param int       $admin_id       The admin that awarded this rank
     * @param int       $achievement_id The achievement ID
     */
    public function listener( $user_id, $new_rank, $old_rank, $admin_id, $achievement_id ) {

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $user_id,
            'post_id' => $new_rank->ID,
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

new AutomatorWP_GamiPress_Reach_Rank();