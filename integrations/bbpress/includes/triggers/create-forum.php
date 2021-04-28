<?php
/**
 * Create Forum
 *
 * @package     AutomatorWP\Integrations\bbPress\Triggers\Create_Forum
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_bbPress_Create_Forum extends AutomatorWP_Integration_Trigger {

    public $integration = 'bbpress';
    public $trigger = 'bbpress_create_forum';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User creates a forum', 'automatorwp' ),
            'select_option'     => __( 'User creates <strong>a forum</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User creates a forum %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User creates a forum', 'automatorwp' ),
            'action'            => 'bbp_new_forum',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
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
     * @param array $forum
     */
    public function listener( $forum ) {

        $forum_id = $forum['forum_id'];
        $user_id = $forum['forum_author'];

        // Bail if not user provided
        if( $user_id === 0 ) {
            return;
        }

        // Trigger the create forum
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'post_id'       => $forum_id,
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

        return $deserves_trigger;

    }

}

new AutomatorWP_bbPress_Create_Forum();