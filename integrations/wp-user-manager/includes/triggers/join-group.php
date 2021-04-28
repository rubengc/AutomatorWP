<?php
/**
 * Join Group
 *
 * @package     AutomatorWP\Integrations\WP_User_Manager\Triggers\Join_Group
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WP_User_Manager_Join_Group extends AutomatorWP_Integration_Trigger {

    public $integration = 'wp_user_manager';
    public $trigger = 'wp_user_manager_join_group';

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
            'edit_label'        => sprintf( __( 'User joins %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Group. */
            'log_label'         => sprintf( __( 'User joins %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'wpumgp_after_member_join',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Group:', 'automatorwp' ),
                    'option_none_label' => __( 'any group', 'automatorwp' ),
                    'post_type' => 'wpum_group'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array(
                'times' => automatorwp_utilities_times_tag( true )
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
     * @param string $privacy_method
     */
    public function listener( $group_id, $user_id, $privacy_method ) {

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'group_id'      => $group_id,
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
        if( ! isset( $event['group_id'] ) ) {
            return false;
        }

        // Bail if group doesn't match with the trigger option
        if( $trigger_options['group'] !== 'any' && absint( $event['group_id'] ) !== absint( $trigger_options['group'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WP_User_Manager_Join_Group();