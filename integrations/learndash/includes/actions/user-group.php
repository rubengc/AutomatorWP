<?php
/**
 * User Group
 *
 * @package     AutomatorWP\Integrations\LearnDash\Actions\User_Group
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_LearnDash_User_Group extends AutomatorWP_Integration_Action {

    public $integration = 'learndash';
    public $action = 'learndash_user_group';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add user to a group', 'automatorwp' ),
            'select_option'     => __( 'Add user to <strong>a group</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. */
            'edit_label'        => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{post}' ),
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name'              => __( 'Group:', 'automatorwp' ),
                    'option_none_label' => __( 'all groups', 'automatorwp' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Group ID', 'automatorwp' ),
                    'post_type'         => 'groups',
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

        $groups = array();

        // Check specific group
        if( $group_id !== 'any' ) {

            $group = get_post( $group_id );

            // Bail if group doesn't exists
            if( ! $group ) {
                return;
            }

            $groups = array( $group_id );

        }

        // If adding to all groups, get all groups
        if( $group_id === 'any' ) {
            $groups = learndash_get_groups( true );
        }

        // Add or remove user from groups
        foreach( $groups as $group_id ) {
            ld_update_group_access( $user_id, $group_id, false );
        }

    }

}

new AutomatorWP_LearnDash_User_Group();