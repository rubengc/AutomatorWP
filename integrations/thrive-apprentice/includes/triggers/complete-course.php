<?php
/**
 * Complete Course
 *
 * @package     AutomatorWP\Integrations\Thrive_Apprentice\Triggers\Complete_Course
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Thrive_Apprentice_Complete_Course extends AutomatorWP_Integration_Trigger {

    public $integration = 'thrive_apprentice';
    public $trigger = 'thrive_apprentice_complete_course';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User completes a course', 'automatorwp' ),
            'select_option'     => __( 'User completes <strong>a course</strong>', 'automatorwp' ),
            /* translators: %1$s: Course title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User completes %1$s %2$s time(s)', 'automatorwp' ), '{term}', '{times}' ),
            /* translators: %1$s: Course title. */
            'log_label'         => sprintf( __( 'User completes %1$s', 'automatorwp' ), '{term}' ),
            'action'            => 'thrive_apprentice_course_finish',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'term' => automatorwp_utilities_term_option( array(
                    'name' => __( 'Course:', 'automatorwp' ),
                    'option_none_label' => __( 'any course', 'automatorwp' ),
                    'taxonomy' => 'tva_courses'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_thrive_apprentice_get_course_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param  array  	$course_details
     * @param  array  	$user_details
     */
    public function listener( $course_details, $user_details ) {

        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_details['user_id'],
            'course_id'   => $course_details['course_id'],
            'course_title'   => $course_details['course_title']
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
        if( ! isset( $event['course_id'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( $trigger_options['term'] != 'any' && $trigger_options['term'] != $event['course_id'] ) {
            return false;
        }

        return $deserves_trigger;

    }

    /**
     * Register the required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 6 );

        parent::hooks();
    }

    /**
     * Trigger custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    function log_meta( $log_meta, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Bail if action type don't match this action
        if( $trigger->type !== $this->trigger ) {
            return $log_meta;
        }

        $log_meta['course_id'] = ( isset( $event['course_id'] ) ? $event['course_id'] : '' );
        $log_meta['course_title'] = ( isset( $event['course_title'] ) ? $event['course_title'] : '' );
        
        return $log_meta;

    }

}

new AutomatorWP_Thrive_Apprentice_Complete_Course();