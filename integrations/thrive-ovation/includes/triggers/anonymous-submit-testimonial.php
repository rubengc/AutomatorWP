<?php
/**
 * Anonymous Submits Testimonial
 *
 * @package     AutomatorWP\Integrations\Thrive_Ovation\Triggers\Anonymous_Submits_Testimonial
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Thrive_Ovation_Anonymous_Submits_Testimonial extends AutomatorWP_Integration_Trigger {

    public $integration = 'thrive_ovation';
    public $trigger = 'thrive_ovation_anonymous_submits_testimonial';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'anonymous'         => true,
            'label'             => __( 'Guest submits a testimonial', 'automatorwp' ),
            'select_option'     => __( 'Guest submits a <strong>testimonial</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'Guest submits a testimonial %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'Guest submits a testimonial', 'automatorwp' ),
            'action'            => 'thrive_ovation_testimonial_submit',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_thrive_ovation_get_testimonial_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param  array  	$testimonial_details
     * @param  array  	$user_details
     */
    public function listener( $testimonial_details, $user_details ) {
        
        // Bail if empty user details
        if ( !empty( $user_details ) ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'testimonial_id'   => $testimonial_details['testimonial_id'],
            'testimonial_author_email'   => $testimonial_details['testimonial_author_email'],
            'testimonial_content'   => $testimonial_details['testimonial_content'],
            'testimonial_author_role'   => $testimonial_details['testimonial_author_role'],
            'testimonial_author_website'   => $testimonial_details['testimonial_author_website'],
        ) );

    }

    /**
     * Register the required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_anonymous_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 5 );

        parent::hooks();
    }

    /**
     * Trigger custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    function log_meta( $log_meta, $trigger, $event, $trigger_options, $automation ) {

        // Bail if action type don't match this action
        if( $trigger->type !== $this->trigger ) {
            return $log_meta;
        }

        $log_meta['testimonial_id'] = ( isset( $event['testimonial_id'] ) ? $event['testimonial_id'] : '' );
        $log_meta['testimonial_author_email'] = ( isset( $event['testimonial_author_email'] ) ? $event['testimonial_author_email'] : '' );
        $log_meta['testimonial_content'] = ( isset( $event['testimonial_content'] ) ? $event['testimonial_content'] : '' );
        $log_meta['testimonial_author_role'] = ( isset( $event['testimonial_author_role'] ) ? $event['testimonial_author_role'] : '' );
        $log_meta['testimonial_author_website'] = ( isset( $event['testimonial_author_website'] ) ? $event['testimonial_author_website'] : '' );
        
        return $log_meta;

    }

}

new AutomatorWP_Thrive_Ovation_Anonymous_Submits_Testimonial();