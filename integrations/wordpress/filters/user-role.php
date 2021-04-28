<?php
/**
 * User Role
 *
 * @package     AutomatorWP\Integrations\WordPress\Filters\User_Role
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_User_Role_Filter extends AutomatorWP_Integration_Filter {

    public $integration = 'wordpress';
    public $filter = 'wordpress_user_role';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_filter( $this->filter, array(
            'integration'       => $this->integration,
            'label'             => __( 'User role', 'automatorwp' ),
            'select_option'     => __( 'User <strong>role</strong>', 'automatorwp' ),
            /* translators: %1$s: Condition. %2$s: Role. */
            'edit_label'        => sprintf( __( '%1$s %2$s', 'automatorwp' ), '{condition}', '{role}'  ),
            /* translators: %1$s: Condition. %2$s: Role. */
            'log_label'         => sprintf( __( '%1$s %2$s', 'automatorwp' ), '{condition}', '{role}' ),
            'options'           => array(
                'condition' => array(
                    'from' => 'condition',
                    'fields' => array(
                        'condition' => array(
                            'name' => __( 'Condition:', 'automatorwp' ),
                            'type' => 'select',
                            'options' => array(
                                'equal'             => __( 'is equal to', 'automatorwp' ),
                                'not_equal'         => __( 'is not equal to', 'automatorwp' ),
                                'contains'          => __( 'contains', 'automatorwp' ),
                                'not_contains'      => __( 'does not contains', 'automatorwp' ),
                            ),
                            'default' => 'equal'
                        )
                    )
                ),
                'role' => automatorwp_utilities_role_option( array(
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Role name:', 'automatorwp' ),
                ) ),
            ),
        ) );

    }

    /**
     * User deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_filter    True if user deserves filter, false otherwise
     * @param stdClass  $filter             The filter object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $filter_options     The filter's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    public function user_deserves_filter( $deserves_filter, $filter, $user_id, $event, $filter_options, $automation ) {

        // Shorthand
        $condition = $filter_options['condition'];
        $role = $filter_options['role'];

        // Bail if wrong configured
        if( empty( $role ) ) {
            $this->result = __( 'Filter not passed. Role option has not been configured.', 'automatorwp' );
            return false;
        }

        $user = get_userdata( $user_id );

        if( ! $user ) {
            $this->result = __( 'Filter not passed. Could not find the user.', 'automatorwp' );
            return false;
        }

        $user_roles = $user->roles;

        if( in_array( $condition, array( 'equal', 'no_equal' ) ) ) {
            // Don't deserve if user has more than 1 role
            if( count( $user_roles ) > 1 ) {
                $this->result = sprintf( __( 'Filter not passed. User role "%1$s" does not meets the condition %2$s "%3$s".', 'automatorwp' ),
                    implode( ', ', $user_roles ),
                    automatorwp_utilities_get_condition_label( $condition ),
                    $role
                );
                return false;
            }
        }

        switch( $condition ) {
            case 'equal':
            case 'contains':
                // Don't deserve if user is not in this role
                if( ! in_array( $role, $user_roles ) ) {
                    $this->result = sprintf( __( 'Filter not passed. User role "%1$s" does not meets the condition %2$s "%3$s".', 'automatorwp' ),
                        implode( ', ', $user_roles ),
                        automatorwp_utilities_get_condition_label( $condition ),
                        $role
                    );
                    return false;
                }
                break;
            case 'not_equal':
            case 'not_contains':
                // Don't deserve if user is in this role
                if( in_array( $role, $user_roles ) ) {
                    $this->result = sprintf( __( 'Filter not passed. User role "%1$s" does not meets the condition %2$s "%3$s".', 'automatorwp' ),
                        implode( ', ', $user_roles ),
                        automatorwp_utilities_get_condition_label( $condition ),
                        $role
                    );
                    return false;
                }
                break;
        }

        $this->result = sprintf( __( 'Filter passed. User role "%1$s" meets the condition %2$s "%3$s".', 'automatorwp' ),
            implode( ', ', $user_roles ),
            automatorwp_utilities_get_condition_label( $condition ),
            $role
        );

        return $deserves_filter;

    }

}

new AutomatorWP_WordPress_User_Role_Filter();