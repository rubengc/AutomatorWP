<?php
/**
 * Publish Activity
 *
 * @package     AutomatorWP\Integrations\BuddyBoss\Triggers\Publish_Activity
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_BuddyBoss_Publish_Activity extends AutomatorWP_Integration_Trigger {

    public $integration = 'buddyboss';
    public $trigger = 'buddyboss_publish_activity';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User writes an activity stream message', 'automatorwp' ),
            'select_option'     => __( 'User <strong>writes</strong> an activity stream message', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User writes an activity stream message %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User writes an activity stream message', 'automatorwp' ),
            'action'            => 'bp_activity_posted_update',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_buddyboss_get_activity_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param string $content
     * @param int $user_id
     * @param int $activity_id
     */
    public function listener( $content, $user_id, $activity_id ) {

        $activity = new BP_Activity_Activity( $activity_id );

        $media_types = array( 'document', 'video', 'media' );
        
        if ( in_array( $activity->privacy, $media_types )) {
            return;
        }

        $activity_edited = bp_activity_get_meta( $activity_id, '_is_edited', true );

        // Bail is a edited activity
        if ( !empty ( $activity_edited ) ) {
            return;
        }

        // Trigger the publish an activity
        automatorwp_trigger_event( array(
            'trigger'           => $this->trigger,
            'user_id'           => $user_id,
            'activity_id'       => $activity_id,
            'activity_content'  => $content,
            'activity_author_id'    => $activity->user_id,
        ) );

    }

    /**
     * Register the required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 6 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

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

        $log_meta['activity_id'] = ( isset( $event['activity_id'] ) ? $event['activity_id'] : '' );
        $log_meta['activity_content'] = ( isset( $event['activity_content'] ) ? $event['activity_content'] : '' );
        $log_meta['activity_author_id'] = ( isset( $event['activity_author_id'] ) ? $event['activity_author_id'] : '' );

        return $log_meta;

    }

    /**
     * Action custom log fields
     *
     * @since 1.0.0
     *
     * @param array     $log_fields The log fields
     * @param stdClass  $log        The log object
     * @param stdClass  $object     The trigger/action/automation object attached to the log
     *
     * @return array
     */
    public function log_fields( $log_fields, $log, $object ) {

        // Bail if log is not assigned to an trigger
        if( $log->type !== 'trigger' ) {
            return $log_fields;
        }

        // Bail if trigger type don't match this trigger
        if( $object->type !== $this->trigger ) {
            return $log_fields;
        }

        $log_fields['activity_content'] = array(
            'name' => __( 'Activity Content', 'automatorwp' ),
            'desc' => __( 'The activity content.', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;

    }

}

new AutomatorWP_BuddyBoss_Publish_Activity();