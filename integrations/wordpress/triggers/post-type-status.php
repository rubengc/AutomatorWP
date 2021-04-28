<?php
/**
 * Post Type Status
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Post_Type_Status
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Post_Type_Status extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_post_type_status';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User post of a type status changes', 'automatorwp' ),
            'select_option'     => __( 'User post of a type <strong>status changes</strong>', 'automatorwp' ),
            /* translators: %1$s: Post type. %2$s: Post Status. %3$s: Number of times. */
            'edit_label'        => sprintf( __( 'User %1$s status changes to %2$s %3$s time(s)', 'automatorwp' ), '{post_type}', '{post_status}', '{times}' ),
            /* translators: %1$s: Post type. %2$s: Post Status. */
            'log_label'         => sprintf( __( 'User %1$s status changes to %2$s', 'automatorwp' ), '{post_type}', '{post_status}' ),
            'action'            => 'transition_post_status',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'post_type' => array(
                    'from' => 'post_type',
                    'fields' => array(
                        'post_type' => array(
                            'name' => __( 'Post type:', 'automatorwp' ),
                            'type' => 'select',
                            'classes' => 'automatorwp-selector',
                            'options_cb' => 'automatorwp_options_cb_post_types',
                            'option_none' => true,
                            'option_none_label' => __( 'post of any type', 'automatorwp' ),
                            'default' => 'any'
                        ),
                    )
                ),
                'post_status' => array(
                    'from' => 'post_status',
                    'fields' => array(
                        'post_status' => array(
                            'name' => __( 'Status:', 'automatorwp' ),
                            'type' => 'select',
                            'options_cb' => 'automatorwp_options_cb_post_status',
                            'option_none' => true,
                            'default' => 'any'
                        )
                    )
                ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags(),
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

        // Bail if post status hasn't changed
        if( $old_status === $new_status ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $post->post_author,
            'post_id'       => $post->ID,
            'post_status'   => $new_status,
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
        if( ! isset( $event['post_id'] ) && ! isset( $event['post_status'] ) ) {
            return false;
        }

        $post = get_post( absint( $event['post_id'] ) );

        // Don't deserve if post doesn't exists
        if( ! $post ) {
            return false;
        }

        $post_type = $trigger_options['post_type'];

        // Don't deserve if post doesn't match with the trigger option
        if( $post_type !== 'any' && $post->post_type !== $post_type ) {
            return false;
        }

        $post_status = $trigger_options['post_status'];

        // Don't deserve if post doesn't match with the trigger option
        if( $post_status !== 'any' && $event['post_status'] !== $post_status ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WordPress_Post_Type_Status();