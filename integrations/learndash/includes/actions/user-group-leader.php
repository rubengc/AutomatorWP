<?php
/**
 * User Group Leader
 *
 * @package     AutomatorWP\Integrations\LearnDash\Actions\User_Group_Leader
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_LearnDash_User_Group_Leader extends AutomatorWP_Integration_Action {

    public $integration = 'learndash';
    public $action = 'learndash_user_group_leader';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Make user the leader of a group', 'automatorwp' ),
            'select_option'     => __( 'Make user the <strong>leader</strong> of a group', 'automatorwp' ),
            /* translators: %1$s: Group. */
            'edit_label'        => sprintf( __( 'Make user the leader of a %1$s', 'automatorwp' ), '{group}' ),
            /* translators: %1$s: Group. */
            'log_label'         => sprintf( __( 'Make user the leader of a %1$s', 'automatorwp' ), '{group}' ),
            'options'           => array(
                'group' => array(
                    'default' => __( 'group', 'automatorwp' ),
                    'from' => 'post',
                    'fields' => array(
                        'post' => automatorwp_utilities_post_field( array(
                            'name'              => __( 'Group:', 'automatorwp' ),
                            'option_none'       => false,
                            'option_custom'         => true,
                            'option_custom_desc'    => __( 'Group ID', 'automatorwp' ),
                            'post_type'         => 'groups',
                            'placeholder'       => __( 'Select a group', 'automatorwp' ),
                            'default'           => ''
                        ) ),
                        'post_custom' => automatorwp_utilities_custom_field( array(
                            'option_custom_desc'    => __( 'Group ID', 'automatorwp' ),
                        ) ),
                        'leader_role_assignment' => array(
                            'name' => __( 'If user doesn\'t have the "Group Leader" role:', 'automatorwp' ),
                            'type' => 'select',
                            'options' => array(
                                'nothing'   => __( 'Do nothing', 'automatorwp' ),
                                'add'       => __( 'Add the role to their existing roles', 'automatorwp' ),
                                'set'       => __( 'Replace their existing roles with the "Group Leader" role', 'automatorwp' ),
                            ),
                            'default' => 'nothing'
                        ),
                    )
                ),
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
        $leader_role_assignment = $action_options['leader_role_assignment'];

        $user = get_user_by( 'ID', $user_id );

        // Bail if user doesn't exists
        if ( is_wp_error( $user_id ) ) {
            return;
        }

        // Bail if action is configured to don't continue if the user hasn't the role
        if ( ! user_can( $user, 'group_leader' ) && $leader_role_assignment === 'nothing' ) {
            return;
        }

        // If user hasn't the role, add or assign it
        if ( ! user_can( $user, 'group_leader' ) ) {
            switch ( $leader_role_assignment ) {
                case 'add':
                    $user->add_role( 'group_leader' );
                    break;
                case 'set':
                    $user->set_role( 'group_leader' );
                    break;
            }
        }

        // Set the user as leader
        ld_update_leader_group_access( $user_id, $group_id );

    }

}

new AutomatorWP_LearnDash_User_Group_Leader();