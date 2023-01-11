<?php
/**
 * Add Tag
 *
 * @package     AutomatorWP\Integrations\WP_Fusion\Triggers\Add_Tag
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WP_Fusion_Add_Tag extends AutomatorWP_Integration_Trigger {

    public $integration = 'wp_fusion';
    public $trigger = 'wp_fusion_add_tag';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'Tag added to user', 'automatorwp' ),
            'select_option'     => __( '<strong>Tag</strong> added to user', 'automatorwp' ),
            /* translators: %1$s: Tag. %2$s: Number of times. */
            'edit_label'        => sprintf( __( '%1$s added to user %2$s time(s)', 'automatorwp' ), '{tag}', '{times}' ),
            /* translators: %1$s: Tag. */
            'log_label'         => sprintf( __( '%1$s added to user', 'automatorwp' ), '{tag}' ),
            'action'            => 'wpf_tags_applied',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'tag' => array(
                    'from' => 'tag',
                    'fields' => array(
                        'tag' => array(
                            'name' => __( 'Tag:', 'automatorwp' ),
                            'type' => 'select',
                            'options_cb' => array( $this, 'options_cb_tags' ),
                            'default' => 'any'
                        )
                    )
                ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Options callback for tags options
     *
     * @since 1.0.0
     *
     * @param stdClass $field
     *
     * @return array
     */
    public function options_cb_tags( $field ) {

        $options = array(
            'any' => __( 'Any tag', 'automatorwp' ),
        );

        $tags = wp_fusion()->settings->get( 'available_tags' );

        if( is_array( $tags ) ) {
            $options = array_merge( $options, $tags );
        }

        return $options;

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int   $user_id    The user ID
     * @param array $tags       Tags added to the user
     */
    public function listener( $user_id, $tags ) {

        foreach( $tags as $tag ) {

            // Trigger the tag added
            automatorwp_trigger_event( array(
                'trigger'   => $this->trigger,
                'user_id'   => $user_id,
                'tag'       => $tag,
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

        // Don't deserve if post is not received
        if( ! isset( $event['tag'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( $trigger_options['tag'] !== 'any' && $event['tag'] !== $trigger_options['tag'] ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WP_Fusion_Add_Tag();