<?php
/**
 * Set Role From/To
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Set_Role_From_To
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Set_Role_From_To extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_set_role_from_to';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User role changes from role to role', 'automatorwp' ),
            'select_option'     => __( 'User role <strong>changes</strong> from role to role', 'automatorwp' ),
            /* translators: %1$s: Role From. %2$s: Role To. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User role changes from %1$s to %2$s %3$s time(s)', 'automatorwp' ), '{role_from}', '{role_to}', '{times}' ),
            /* translators: %1$s: Role From. %2$s: Role To. */
            'log_label'         => sprintf( __( 'User role changes from %1$s to %2$s', 'automatorwp' ), '{role_from}', '{role_to}' ),
            'action'            => 'set_user_role',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'role_from' => array(
                    'from' => 'role_from',
                    'default' => 'any',
                    'fields' => array(
                        'role_from' => automatorwp_utilities_role_field( array(
                            'option_custom' => true,
                        ) ),
                        'role_from_custom' => automatorwp_utilities_custom_field( array(
                            'option_custom_desc' => __( 'Role name.', 'automatorwp' )
                        ) ),
                    )
                ),
                'role_to' => array(
                    'from' => 'role_to',
                    'default' => 'any',
                    'fields' => array(
                        'role_to' => automatorwp_utilities_role_field( array(
                            'option_custom' => true,
                        ) ),
                        'role_to_custom' => automatorwp_utilities_custom_field( array(
                            'option_custom_desc' => __( 'Role name.', 'automatorwp' )
                        ) ),
                    )
                ),
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
     * @param string   $role      The new role.
     * @param string[] $old_roles An array of the user's previous roles.
     */
    public function listener( $user_id, $role, $old_roles ) {

        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
            'role_from' => $old_roles[0],
            'role_to'   => $role,
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
        if( ! isset( $event['role_from'] ) && ! isset( $event['role_to'] ) ) {
            return false;
        }

        // Don't deserve if role doesn't match with the trigger option
        if( $trigger_options['role_from'] !== 'any' && $trigger_options['role_from'] !== $event['role_from'] ) {
            return false;
        }

        // Don't deserve if role to change doesn't match with the trigger option
        if( $trigger_options['role_to'] !== 'any' && $trigger_options['role_to'] !== $event['role_to'] ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WordPress_Set_Role_From_To();