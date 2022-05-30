<?php
/**
 * User Achievement
 *
 * @package     AutomatorWP\Integrations\GamiPress\Actions\User_Achievement
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_GamiPress_User_Achievement extends AutomatorWP_Integration_Action {

    public $integration = 'gamipress';
    public $action = 'gamipress_user_achievement';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Award achievement to user', 'automatorwp' ),
            'select_option'     => __( 'Award <strong>achievement</strong> to user', 'automatorwp' ),
            /* translators: %1$s: Post title.  %2$s: User. */
            'edit_label'        => sprintf( __( 'Award the achievement %1$s to %2$s', 'automatorwp' ), '{post}', '{user}' ),
            /* translators: %1$s: Post title. %2$s: User. */
            'log_label'         => sprintf( __( 'Award the achievement %1$s to %2$s', 'automatorwp' ), '{post}', '{user}' ),
            'options'           => array(
                'post' => automatorwp_gamipress_utilities_post_option( array(
                    'name' => __( 'Achievement:', 'automatorwp' ),
                    'option_default' => __( 'Select an achievement', 'automatorwp' ),
                    'option_none' => false,
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Achievement ID', 'automatorwp' ),
                    'post_type_cb' => 'gamipress_get_achievement_types_slugs'
                ) ),
                'user' => array(
                    'from' => 'user',
                    'default' => __( 'user', 'automatorwp' ),
                    'fields' => array(
                        'user' => array(
                            'name' => __( 'User ID:', 'automatorwp' ),
                            'desc' => __( 'User ID that will receive this achievement. Leave blank to award the achievement to the user that completes the automation.', 'automatorwp' ),
                            'type' => 'input',
                            'default' => ''
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
        $post_id = absint( $action_options['post'] );
        $user_id_to_award = absint( $action_options['user'] );

        if( $user_id_to_award === 0 ) {
            $user_id_to_award = $user_id;
        }

        $user = get_userdata( $user_id_to_award );

        // Bail if user does not exists
        if( ! $user ) {
            return;
        }

        $post = get_post( $post_id );

        // Bail if post doesn't exists
        if( ! $post ) {
            return;
        }

        // Bail if post is not an achievement
        if( ! in_array( $post->post_type, gamipress_get_achievement_types_slugs() ) ) {
            return;
        }

        // Award the achievement
        gamipress_award_achievement_to_user( $post_id, $user_id_to_award );

    }

}

new AutomatorWP_GamiPress_User_Achievement();