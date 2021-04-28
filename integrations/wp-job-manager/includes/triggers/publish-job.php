<?php
/**
 * Publish Job
 *
 * @package     AutomatorWP\Integrations\WP_Job_Manager\Triggers\Publish_Job
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WP_Job_Manager_Publish_Job extends AutomatorWP_Integration_Trigger {

    public $integration = 'wp_job_manager';
    public $trigger = 'wp_job_manager_publish_job';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User publishes a job of a type', 'automatorwp' ),
            'select_option'     => __( 'User publishes <strong>a job</strong> of a type', 'automatorwp' ),
            /* translators: %1$s: Term title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User publishes a job of %1$s %2$s time(s)', 'automatorwp' ), '{term}', '{times}' ),
            /* translators: %1$s: Term title. */
            'log_label'         => sprintf( __( 'User publishes a job of %1$s', 'automatorwp' ), '{term}' ),
            'action'            => 'transition_post_status',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'term' => automatorwp_utilities_term_option( array(
                    'name'              => __( 'Type:', 'automatorwp' ),
                    'option_none_label' => __( 'any type', 'automatorwp' ),
                    'taxonomy'          => 'job_listing_type',
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags( __( 'Job', 'automatorwp' ) ),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param  string   $new_status The new post status
     * @param  string   $old_status The old post status
     * @param  WP_Post  $post       The post
     */
    public function listener( $new_status, $old_status, $post ) {

        // Bail if post type is not a job
        if( $post->post_type !== 'job_listing' ) {
            return;
        }

        // Bail if post is already published
        if( $old_status === 'publish' ) {
            return;
        }

        // Bail if post is not published
        if( $new_status !== 'publish' ) {
            return;
        }

        $terms_ids = automatorwp_get_term_ids( $post->ID, 'job_listing_type' );

        $user_id = get_current_user_id();

        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
            'post_id'   => $post->ID,
            'terms_ids' => $terms_ids,
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

        // Don't deserve if post and terms IDs are not received
        if( ! isset( $event['post_id'] ) && ! isset( $event['terms_ids'] ) ) {
            return false;
        }

        // Don't deserve if term doesn't match with the trigger option
        if( ! automatorwp_terms_matches( $event['terms_ids'], $trigger_options['term'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WP_Job_Manager_Publish_Job();