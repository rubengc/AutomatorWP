<?php
/**
 * Add User Group
 *
 * @package     AutomatorWP\Integrations\WP_User_Manager\Actions\Add_User_Group
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WP_User_Manager_Add_User_Group extends AutomatorWP_Integration_Action {

    public $integration = 'wp_user_manager';
    public $action = 'wp_user_manager_add_user_group';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add user to group', 'automatorwp' ),
            'select_option'     => __( 'Add user to <strong>group</strong>', 'automatorwp' ),
            /* translators: %1$s: Group. */
            'edit_label'        => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Group. */
            'log_label'         => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{post}' ),
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Group:', 'automatorwp' ),
                    'option_none_label' => __( 'Select a group', 'automatorwp' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Group ID', 'automatorwp' ),
                    'post_type' => 'wpum_group'
                ) ),
            ),
        ) );

    }

    /**
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    public function execute( $action, $user_id, $action_options, $automation ) {

        // Shorthand
        $group_id = $action_options['post'];

        // Bail if group not provided
        if( absint( $group_id ) === 0 ) {
            return;
        }

        if( ! function_exists( 'wpumgp_join_group' ) ) {
            return;
        }

        // Add the user to the group
        do_action( 'wpumgp_user_join_group', $group_id, $user_id );

    }

}

new AutomatorWP_WP_User_Manager_Add_User_Group();