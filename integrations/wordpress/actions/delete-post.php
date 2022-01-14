<?php
/**
 * Delete Post
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\Delete_Post_Action
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Delete_Post_Action extends AutomatorWP_Integration_Action {

    public $integration = 'wordpress';
    public $action = 'wordpress_delete_post_action';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Delete a post', 'automatorwp' ),
            'select_option'     => __( 'Delete <strong>a post</strong>', 'automatorwp' ),
            /* translators: %1$s: Post. */
            'edit_label'        => sprintf( __( 'Delete %1$s', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Post. */
            'log_label'         => sprintf( __( 'Delete %1$s', 'automatorwp' ), '{post}' ),
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'post_type'             => 'any',
                    'option_none_label'     => __( 'any post', 'automatorwp' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Post ID', 'automatorwp' ),
                ) )
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

        $post_id = absint( $action_options['post'] );

        // Bail if not post ID provided
        if( $post_id === 0 ) {
            return;
        }

        wp_delete_post( $post_id, true );

    }

}

new AutomatorWP_WordPress_Delete_Post_Action();