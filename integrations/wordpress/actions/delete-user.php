<?php
/**
 * Delete user
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\Delete_User
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>, Dionisio Sánchez <dionisio@automatorwp.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Delete_User extends AutomatorWP_Integration_Action {

    public $integration = 'wordpress';
    public $action = 'wordpress_delete_user';

    /**
     * Register the action
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Delete a user', 'automatorwp' ),
            'select_option'     => __( 'Delete <strong>a user</strong>', 'automatorwp' ),
            /* translators: %1$s: User. */
            'edit_label'        => sprintf( __( 'Delete a %1$s', 'automatorwp' ), '{user}' ),
            /* translators: %1$s: User. */
            'log_label'         => sprintf( __( 'Delete a %1$s', 'automatorwp' ), '{user}' ),
            'options'           => array(
                'user' => array(
                    'default' => __ ( 'user', 'automatorwp' ),
                    'fields' => array(
                        'user_id' => array(
                            'name' => __( 'User ID:', 'automatorwp' ),
                            'desc' => __( 'The user\'s ID to delete. Leave empty to assign the user that completes the automation.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'target_user_id' => array(
                            'name' => __( 'User ID to reassign the content:', 'automatorwp' ),
                            'desc' => __( 'The user\'s ID to reassign the content owned by the user that will get deleted. Leave empty to do not reassign the content.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                    ),
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

        $user_id_to_delete = absint( $action_options['user_id'] );
        $target_user_id = absint( $action_options['target_user_id'] );

        if( $user_id_to_delete === 0 ) {
            $user_id_to_delete = $user_id;
        }

        $user = get_userdata( $user_id_to_delete );

        // Bail if user to delete does not exists
        if ( ! $user ) {
            return;
        }

        // Ensure that wp_delete_user is available
        if( ! function_exists( 'wp_delete_user' ) ) {
            include_once( ABSPATH . 'wp-admin/includes/user.php' );
        }

        if ( $target_user_id !== 0 ) {

            $target_user = get_userdata( $target_user_id );

            // Bail if user to reassign does not exists
            if ( ! $target_user ) {
                return;
            }

            // Bail if user to reassign is equal to the user that will get deleted
            if( $target_user->ID === $user->ID ) {
                return;
            }

            // Delete the user and reassign its content
            wp_delete_user( $user->ID, $target_user->ID );

        } else {
            // Delete the user
            wp_delete_user( $user->ID );
        }

    }

}

new AutomatorWP_WordPress_Delete_User();