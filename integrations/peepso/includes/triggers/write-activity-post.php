<?php
/**
 * Write Activity Post
 *
 * @package     AutomatorWP\Integrations\PeepSo\Triggers\Write_Activity_Post
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_PeepSo_Write_Activity_Post extends AutomatorWP_Integration_Trigger {

    public $integration = 'peepso';
    public $trigger = 'peepso_write_activity_post';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User writes an activity post', 'automatorwp' ),
            'select_option'     => __( 'User <strong>writes</strong> an activity post', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User writes an activity post %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User writes an activity post', 'automatorwp' ),
            'action'            => 'peepso_activity_after_add_post',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags( __( 'Activity', 'automatorwp' ) ),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int $post_id      Post ID
     * @param int $activity_id  Activity ID
     */
    public function listener( $post_id, $activity_id ) {

        $user_id = absint( get_post_field( 'post_author', $post_id ) );

        // Trigger write activity post
        automatorwp_trigger_event( array(
            'trigger'           => $this->trigger,
            'user_id'           => $user_id,
            'post_id'           => $post_id,
            'activity_id'       => $activity_id,
        ) );

    }

}

new AutomatorWP_PeepSo_Write_Activity_Post();