<?php
/**
 * Join Group
 *
 * @package     AutomatorWP\Integrations\BuddyBoss\Triggers\Join_Group
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_BuddyBoss_Join_Group extends AutomatorWP_Integration_Trigger {

    public $integration = 'buddyboss';
    public $trigger = 'buddyboss_join_group';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User joins a group', 'automatorwp' ),
            'select_option'     => __( 'User <strong>joins</strong> a group', 'automatorwp' ),
            /* translators: %1$s: Group. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User joins %1$s %2$s time(s)', 'automatorwp' ), '{group}', '{times}' ),
            /* translators: %1$s: Group. */
            'log_label'         => sprintf( __( 'User joins %1$s', 'automatorwp' ), '{group}' ),
            'action'            => 'groups_join_group',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'group' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'group',
                    'name'              => __( 'Group:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any group', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_buddyboss_get_groups',
                    'options_cb'        => 'automatorwp_buddyboss_options_cb_group',
                    'default'           => 'any'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_buddyboss_get_group_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int $group_id
     * @param int $user_id
     */
    public function listener( $group_id, $user_id ) {

        // Trigger the join group
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'group_id'      => $group_id,
            'post_id'       => $group_id,
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

        // Don't deserve if group is not received
        if( ! isset( $event['group_id'] ) ) {
            return false;
        }

        // Don't deserve if group doesn't match with the trigger option
        if( $trigger_options['group'] !== 'any' && absint( $event['group_id'] ) !== absint( $trigger_options['group'] ) ) {
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

        $log_meta['group_id'] = ( isset( $event['group_id'] ) ? $event['group_id'] : '' );

        return $log_meta;

    }

}

new AutomatorWP_BuddyBoss_Join_Group();