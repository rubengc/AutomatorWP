<?php
/**
 * Create Group
 *
 * @package     AutomatorWP\Integrations\LearnDash\Actions\Create_Group
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_LearnDash_Create_Group extends AutomatorWP_Integration_Action {

    public $integration = 'learndash';
    public $action = 'learndash_create_group';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Create a group', 'automatorwp' ),
            'select_option'     => __( 'Create a <strong>group</strong>', 'automatorwp' ),
            /* translators: %1$s: Group. */
            'edit_label'        => sprintf( __( 'Create a %1$s', 'automatorwp' ), '{group}' ),
            /* translators: %1$s: Group. */
            'log_label'         => sprintf( __( 'Create a %1$s', 'automatorwp' ), '{group}' ),
            'options'           => array(
                'group' => array(
                    'default' => __( 'group', 'automatorwp' ),
                    'fields' => array(
                        'post_title' => array(
                            'name' => __( 'Group name:', 'automatorwp' ),
                            'type' => 'text',
                        ),
                        'post' => automatorwp_utilities_post_field( array(
                            'name'              => __( 'Group course:', 'automatorwp' ),
                            'option_none'       => false,
                            'option_custom'         => true,
                            'option_custom_desc'    => __( 'Course ID', 'automatorwp' ),
                            'post_type'         => 'sfwd-courses',
                            'placeholder'       => __( 'Select a course', 'automatorwp' ),
                            'default'           => ''
                        ) ),
                        'post_custom' => automatorwp_utilities_custom_field( array(
                            'option_custom_desc'    => __( 'Course ID', 'automatorwp' ),
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
        $post_title = $action_options['post_title'];
        $course_id = $action_options['post'];
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

        // Create the group
        $group_id = wp_insert_post( array(
            'post_title'   => $post_title,
            'post_content' => '',
            'post_type'    => 'groups',
            'post_status'  => 'publish',
            'post_author'  => $user_id,
        ) );

        // Bail if can't create the group
        if ( is_wp_error( $group_id ) ) {
            return;
        }

        // Set the user as leader
        ld_update_leader_group_access( $user_id, $group_id );

        $course = get_post( $course_id );

        // Bail if course not found
        if( ! $course ) {
            return;
        }

        // Add the course to the group
        ld_update_course_group_access( (int) $course_id, (int) $group_id, false );

        // Delete the course groups transient
        delete_transient( 'learndash_course_groups_' . $course_id );

    }

}

new AutomatorWP_LearnDash_Create_Group();