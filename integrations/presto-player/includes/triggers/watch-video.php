<?php
/**
 * Watch Video
 *
 * @package     AutomatorWP\Integrations\Presto_Player\Triggers\Watch_Video
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Presto_Player_Watch_Video extends AutomatorWP_Integration_Trigger {

    public $integration = 'presto_player';
    public $trigger = 'presto_player_watch_video';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User fully watches a video', 'automatorwp' ),
            'select_option'     => __( 'User fully watches a <strong>video</strong>', 'automatorwp' ),
            /* translators: %1$s: Post Title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User fully watches %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post Title. */
            'log_label'         => sprintf( __( 'User fully watches %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'presto_player_progress',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Video:', 'automatorwp' ),
                    'option_none_label' => __( 'any video', 'automatorwp' ),
                    'post_type' => 'pp_video_block'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags( __( 'Video', 'automatorwp' ) ),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int $video_id
     * @param int $percent
     */
    public function listener( $video_id, $percent ) {

        $user_id = get_current_user_id();

        // Bail if user is not logged in
        if( $user_id === 0 ) {
            return;
        }

        // Bail if user not watched the video entirely
        if( $percent < 100 ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger'           => $this->trigger,
            'user_id'           => $user_id,
            'post_id'           => $video_id,
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

new AutomatorWP_Presto_Player_Watch_Video();