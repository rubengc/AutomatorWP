<?php
/**
 * File Downloaded
 *
 * @package     AutomatorWP\Integrations\WP_All_Import\Triggers\Import_Post_Type
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WP_All_Import_Post_Type extends AutomatorWP_Integration_Trigger {

    public $integration = 'wp_all_import';
    public $trigger = 'wp_all_import_post_type';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User imports posts of a type', 'automatorwp' ),
            'select_option'     => __( 'User imports posts of a <strong>type</strong>', 'automatorwp' ),
            /* translators: %1$s: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User imports posts of %1$s type %2$s time(s)', 'automatorwp' ), '{post_type}','{times}' ),
            'log_label'         => sprintf( __( 'User imports posts of %1$s type', 'automatorwp' ), '{post_type}' ),
            'action'            => 'pmxi_saved_post',
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
     * @param int       $post_id
     * @param object    $node
     * @param bool      $is_update
     * 
     */
    public function listener( $post_id, $node, $is_update ) {
        
        $user_id = get_current_user_id();

        // Bail if user is not logged
        if ($user_id === 0) {
            return;
        }

        if ( empty( $post_id ) ) {
			return false;
		}

        $post_type = get_post_type( $post_id );

        // Trigger import post type
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'post_type'     => $post_type,
            'post_id'       => $post_id,
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

        // Don't deserve if post type is not received
        if( ! isset( $event['post_type'] ) ) {
            return false;
        }

        $post_type = $trigger_options['post_type'];
        
        // Don't deserve if post doesn't match with the trigger option
        if( $post_type !== 'any' && $event['post_type'] !== $trigger_options['post_type'] ) {
            return false;
        }

        return $deserves_trigger;

    }

}


new AutomatorWP_WP_All_Import_Post_Type();