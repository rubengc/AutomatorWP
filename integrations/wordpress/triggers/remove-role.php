<?php
/**
 * Remove Role
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Remove_Role
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Remove_Role extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_remove_role';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User gets removed from role', 'automatorwp' ),
            'select_option'     => __( 'User gets <strong>removed</strong> from role', 'automatorwp' ),
            /* translators: %1$s: Role. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User gets removed from %1$s %2$s time(s)', 'automatorwp' ), '{role}', '{times}' ),
            /* translators: %1$s: Role. */
            'log_label'         => sprintf( __( 'User gets removed from %1$s', 'automatorwp' ), '{role}' ),
            'action'            => 'remove_user_role',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'role' => automatorwp_utilities_role_option(),
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
     * @param int      $user_id   The user ID.
     * @param string   $role      The role.
     */
    public function listener( $user_id, $role ) {

        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
            'role'      => $role,
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

        // Don't deserve if role is not received
        if( ! isset( $event['role'] ) ) {
            return false;
        }

        // Don't deserve if role doesn't match with the trigger option
        if( $trigger_options['role'] !== 'any' && $trigger_options['role'] !== $event['role'] ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WordPress_Remove_Role();