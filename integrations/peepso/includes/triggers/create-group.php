<?php
/**
 * Create Group
 *
 * @package     AutomatorWP\Integrations\PeepSo\Triggers\Create_Group
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_PeepSo_Create_Group extends AutomatorWP_Integration_Trigger {

    public $integration = 'peepso';
    public $trigger = 'peepso_create_group';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User creates a group', 'automatorwp' ),
            'select_option'     => __( 'User <strong>creates</strong> a group', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User creates a group %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User creates a group', 'automatorwp' ),
            'action'            => 'peepso_action_group_create',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param PeepSoGroup $group The group object
     */
    public function listener( $group ) {

        // Trigger create group
        automatorwp_trigger_event( array(
            'trigger'           => $this->trigger,
            'user_id'           => $group->owner_id,
            'post_id'           => $group->id,
        ) );

    }

}

new AutomatorWP_PeepSo_Create_Group();