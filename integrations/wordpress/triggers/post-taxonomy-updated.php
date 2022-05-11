<?php
/**
 * Post Taxonomy Updated
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Post_Taxonomy_Updated
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Post_Taxonomy_Updated extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_post_taxonomy_updated';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User updates a post of a taxonomy', 'automatorwp' ),
            'select_option'     => __( 'User updates a post of <strong>a taxonomy</strong>', 'automatorwp' ),
            /* translators: %1$s: Term title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User updates a post of %1$s %2$s time(s)', 'automatorwp' ), '{term}', '{times}' ),
            /* translators: %1$s: Term title. */
            'log_label'         => sprintf( __( 'User updates a post of %1$s', 'automatorwp' ), '{term}' ),
            'action'            => 'post_updated',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
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
     *
     * @param WP_Post    $post_after    Post object following the update.
     * @param WP_Post    $post_before   Post object before the update.
     * @param int        $post_ID       The post ID
     */
    public function listener( $post_ID, $post_after, $post_before ) {

        // Check if it is an autosave or a revision.
        if ( wp_is_post_autosave( $post_ID ) || wp_is_post_revision( $post_ID ) ) {
            return;
        }

        // Bail if is a new post
        if( $post_before->post_status === 'auto-draft' ) {
            return;
        } 
        
        $taxonomies = get_object_taxonomies( $post_after->post_type );

        foreach( $taxonomies as $taxonomy ) {

            $terms_ids = automatorwp_get_term_ids( $post_after->ID, $taxonomy );

            // Bail if post isn't assigned to any category
            if( empty( $terms_ids ) ) {
                continue;
            }

            automatorwp_trigger_event( array(
                'trigger'   => $this->trigger,
                'user_id'   => $post_after->post_author,
                'post_id'   => $post_ID,
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

new AutomatorWP_WordPress_Post_Taxonomy_Updated();