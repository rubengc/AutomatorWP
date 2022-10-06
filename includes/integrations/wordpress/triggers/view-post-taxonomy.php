<?php
/**
 * View Post Taxonomy
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\View_Post_Taxonomy
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_View_Post_Taxonomy extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_view_post_taxonomy';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User views a post of a taxonomy', 'automatorwp' ),
            'select_option'     => __( 'User views a post of <strong>a taxonomy</strong>', 'automatorwp' ),
            /* translators: %1$s: Term title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User views a post of %1$s %2$s time(s)', 'automatorwp' ), '{term}', '{times}' ),
            /* translators: %1$s: Term title. */
            'log_label'         => sprintf( __( 'User views a post of %1$s', 'automatorwp' ), '{term}' ),
            'action'            => 'template_redirect',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'term'  => automatorwp_utilities_taxonomy_option(),
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

        $taxonomies = get_object_taxonomies( $post->post_type );

        foreach( $taxonomies as $taxonomy ) {

            $terms_ids = automatorwp_get_term_ids( $post->ID, $taxonomy );

            // Bail if post isn't assigned to any category
            if( empty( $terms_ids ) ) {
                continue;
            }

            $user_id = get_current_user_id();

            // Bail if user is not logged in
            if( $user_id === 0 ) {
                return;
            }

            automatorwp_trigger_event( array(
                'trigger'   => $this->trigger,
                'user_id'   => $user_id,
                'post_id'   => $post->ID,
                'taxonomy'  => $taxonomy,
                'terms_ids' => $terms_ids,
            ) );
        }

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

        // Don't deserve if post, taxonomy and terms IDs are not received
        if( ! isset( $event['post_id'] ) && ! isset( $event['taxonomy'] ) && ! isset( $event['terms_ids'] ) ) {
            return false;
        }

        // Don't deserve if taxonomy doesn't match with the trigger option
        if( $trigger_options['taxonomy'] !== 'any' && $trigger_options['taxonomy'] !== $event['taxonomy'] ) {
            return false;
        }

        // Don't deserve if term doesn't match with the trigger option
        if( ! automatorwp_terms_matches( $event['terms_ids'], $trigger_options['term'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WordPress_View_Post_Taxonomy();