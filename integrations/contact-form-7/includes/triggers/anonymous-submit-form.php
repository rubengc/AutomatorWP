<?php
/**
 * Anonymous Submit Form
 *
 * @package     AutomatorWP\Integrations\Contact_Form_7\Triggers\Anonymous_Submit_Form
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Contact_Form_7_Anonymous_Submit_Form extends AutomatorWP_Integration_Trigger {

    public $integration = 'contact_form_7';
    public $trigger = 'contact_form_7_anonymous_submit_form';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'anonymous'         => true,
            'label'             => __( 'Guest submits a form', 'automatorwp' ),
            'select_option'     => __( 'Guest submits <strong>a form</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. */
            'edit_label'        => sprintf( __( 'Guest submits %1$s', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'Guest submits %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'wpcf7_submit',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Form:', 'automatorwp' ),
                    'option_none_label' => __( 'any form', 'automatorwp' ),
                    'post_type' => 'wpcf7_contact_form'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                array(
                    'form_field:FIELD_NAME' => array(
                        'label'     => __( 'Form field value', 'automatorwp' ),
                        'type'      => 'text',
                        'preview'   => __( 'Form field value, replace "FIELD_NAME" by the field name', 'automatorwp' ),
                    ),
                ),
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
     * @param WPCF7_ContactForm $form
     * @param array $result
     */
    public function listener( $form, $result ) {

        // Check if form was correctly filled
        if( ! in_array( $result['status'], array( 'mail_sent', 'mail_failed' ) ) ) {
            return;
        }

        $form_id = ( version_compare( WPCF7_VERSION, '4.8', '<' ) ) ? $form->id : $form->id();
        $user_id = get_current_user_id();

        // Bail if user is logged in
        if ( $user_id !== 0 ) {
            return;
        }

        // Check if form submission is correctly setup
        $submission = WPCF7_Submission::get_instance();

        if ( ! $submission ) {
            return;
        }

        // Check if posted data is correctly setup
        $posted_data = $submission->get_posted_data();

        if ( ! $posted_data ) {
            return;
        }

        $form_fields = automatorwp_contact_form_7_get_form_fields_values( $form, $submission );

        // Trigger submit form event
        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'post_id' => $form_id,
            'form_fields'   => $form_fields,
        ) );

    }

    /**
     * Anonymous deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if anonymous deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                         True if anonymous deserves trigger, false otherwise
     */
    public function anonymous_deserves_trigger( $deserves_trigger, $trigger, $event, $trigger_options, $automation ) {

        // Don't deserve if post is not received
        if( ! isset( $event['post_id'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['post_id'], $trigger_options['post'] ) ) {
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

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

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

        $log_meta['form_fields'] = ( isset( $event['form_fields'] ) ? $event['form_fields'] : array() );

        return $log_meta;

    }

    /**
     * Action custom log fields
     *
     * @since 1.0.0
     *
     * @param array     $log_fields The log fields
     * @param stdClass  $log        The log object
     * @param stdClass  $object     The trigger/action/automation object attached to the log
     *
     * @return array
     */
    public function log_fields( $log_fields, $log, $object ) {

        // Bail if log is not assigned to an trigger
        if( $log->type !== 'trigger' ) {
            return $log_fields;
        }

        // Bail if trigger type don't match this trigger
        if( $object->type !== $this->trigger ) {
            return $log_fields;
        }

        $log_fields['form_fields'] = array(
            'name' => __( 'Fields Submitted', 'automatorwp' ),
            'desc' => __( 'Information about the fields values sent on this form submission.', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;

    }

}

new AutomatorWP_Contact_Form_7_Anonymous_Submit_Form();