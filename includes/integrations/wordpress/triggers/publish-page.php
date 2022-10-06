<?php
/**
 * Publish Page
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Publish_Page
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Publish_Page extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_publish_page';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User publishes a page', 'automatorwp' ),
            'select_option'     => __( 'User publishes <strong>a page</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User publishes a page %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User publishes a page', 'automatorwp' ),
            'action'            => 'transition_post_status',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
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
     *
     * @param string    $new_status The new post status
     * @param string    $old_status The old post status
     * @param WP_Post   $post       The post
     */
    public function listener( $new_status, $old_status, $post ) {

        // Bail if not is a page
        if( $post->post_type !== 'page' ) {
            return;
        }

        // Bail if post has been already published
        if( $old_status === 'publish' ) {
            return;
        }

        // Bail if post is not published
        if( $new_status !== 'publish' ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $post->post_author,
            'post_id' => $post->ID,
        ) );

    }

}

new AutomatorWP_WordPress_Publish_Page();