<?php
/**
 * View Page
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\View_Page
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_View_Page extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_view_page';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User views a page', 'automatorwp' ),
            'select_option'     => __( 'User views <strong>a page</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User views %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User views %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'template_redirect',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Page:', 'automatorwp' ),
                    'option_none_label' => __( 'any page', 'automatorwp' ),
                    'post_type' => 'page'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags( __( 'Page', 'automatorwp' ) ),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     */
    public function listener() {

        global $post;

        // Bail if in admin area
        if( is_admin() ) {
            return;
        }

        // Bail if not post instanced
        if( ! $post instanceof WP_Post ) {
            return;
        }

        // Bail if post type is not a page
        if( $post->post_type !== 'page' ) {
            return;
        }

        $user_id = get_current_user_id();

        // Bail if user is not logged in
        if( $user_id === 0 ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $user_id,
            'post_id' => $post->ID,
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

        // Don't deserve if post is not received
        if( ! isset( $event['post_id'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['post_id'], $trigger_options['post'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WordPress_View_Page();