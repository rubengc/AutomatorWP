<?php
/**
 * Publish Advert
 *
 * @package     AutomatorWP\Integrations\WPAdverts\Triggers\Publish_Advert
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WPAdverts_Publish_Advert extends AutomatorWP_Integration_Trigger {

    public $integration = 'wpadverts';
    public $trigger = 'wpadverts_publish_advert';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User publishes an advert', 'automatorwp' ),
            'select_option'     => __( 'User publishes <strong>an advert</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User publishes an advert %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User publishes an advert', 'automatorwp' ),
            'action'            => 'transition_post_status',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags( __( 'Advert', 'automatorwp' ) ),
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

        // Bail if not is an advert
        if( $post->post_type !== 'advert' ) {
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

new AutomatorWP_WPAdverts_Publish_Advert();