<?php
/**
 * Create Topic
 *
 * @package     AutomatorWP\Integrations\bbPress\Triggers\Create_Topic
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_bbPress_Create_Topic extends AutomatorWP_Integration_Trigger {

    public $integration = 'bbpress';
    public $trigger = 'bbpress_create_topic';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User creates a topic', 'automatorwp' ),
            'select_option'     => __( 'User creates <strong>a topic</strong>', 'automatorwp' ),
            /* translators: %1$s: Forum title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User creates a topic in %1$s %2$s time(s)', 'automatorwp' ), '{forum}', '{times}' ),
            /* translators: %1$s: Forum title. */
            'log_label'         => sprintf( __( 'User creates a topic in %1$s', 'automatorwp' ), '{forum}' ),
            'action'            => 'bbp_new_topic',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 4,
            'options'           => array(
                'forum' => array(
                    'from' => 'forum',
                    'fields' => array(
                        'forum' => automatorwp_utilities_post_field( array(
                            'name' => __( 'Forum:', 'automatorwp' ),
                            'option_none_label' => __( 'any forum', 'automatorwp' ),
                            'post_type_cb' => 'bbp_get_forum_post_type'
                        ) )
                    )
                ),
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
     * @param int $topic_id
     * @param int $forum_id
     * @param array $anonymous_data
     * @param int $topic_author
     */
    public function listener( $topic_id, $forum_id, $anonymous_data, $topic_author ) {

        $user_id = $topic_author;

        // Bail if not user provided
        if( $user_id === 0 ) {
            return;
        }

        // Trigger the create topic
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'post_id'       => $topic_id,
            'forum_id'      => $forum_id,
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
        if( ! isset( $event['post_id'] ) && ! isset( $event['forum_id'] ) ) {
            return false;
        }

        // Don't deserve if forum doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['forum_id'], $trigger_options['forum'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_bbPress_Create_Topic();