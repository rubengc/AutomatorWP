<?php
/**
 * Create Topic
 *
 * @package     AutomatorWP\Integrations\wpForo\Triggers\Create_Topic
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_wpForo_Create_Topic extends AutomatorWP_Integration_Trigger {

    public $integration = 'wpforo';
    public $trigger = 'wpforo_create_topic';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User creates a topic on a forum', 'automatorwp' ),
            'select_option'     => __( 'User <strong>creates a topic</strong> on a forum', 'automatorwp' ),
            /* translators: %1$s: Forum title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User creates a topic on %1$s of %2$s time(s)', 'automatorwp' ), '{forum}', '{times}' ),
            /* translators: %1$s: Forum title. */
            'log_label'         => sprintf( __( 'User creates a topic on %1$s', 'automatorwp' ), '{forum}' ),
            'action'            => 'wpforo_after_add_topic',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'forum' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'forum',
                    'name'              => __( 'Forum:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any forum', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_wpforo_get_forums',
                    'options_cb'        => 'automatorwp_wpforo_options_cb_forum',
                    'default'           => 'any'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_wpforo_forum_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param array $topic
     */
    public function listener( $topic ) {

        $user_id = $topic['userid'];

        // Bail if not user provided
        if( $user_id === 0 ) {
            return;
        }

        $topic_id = $topic['topicid'];
        $forum_id = strval( $topic['forumid'] );

        // Get the current Board
        $board_id = WPF()->board->get_current( 'boardid' );

        // Boards other than the main one (ID=0) take data from other additional tables in the database
        if ( absint( $board_id ) !== 0 ){
            $forum_id = $board_id . '-' . $topic['forumid'];
        }

        // Trigger the create a reply
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
        if( $trigger_options['forum'] !== 'any' &&  $event['forum_id'] !==  $trigger_options['forum'] ) {
            return false;
        }   

        return $deserves_trigger;

    }

    /**
     * Register the required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 6 );

        parent::hooks();
    }

    /**
     * Trigger custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    function log_meta( $log_meta, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Bail if action type don't match this action
        if( $trigger->type !== $this->trigger ) {
            return $log_meta;
        }

        $log_meta['forum_id'] = ( isset( $event['forum_id'] ) ? $event['forum_id'] : 0 );

        return $log_meta;

    }

}

new AutomatorWP_wpForo_Create_Topic();