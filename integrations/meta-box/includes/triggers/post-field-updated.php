<?php
/**
 * Post Field Updated
 *
 * @package     AutomatorWP\Integrations\Meta_Box\Triggers\Post_Field_Updated
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Meta_Box_Post_Field_Updated extends AutomatorWP_Integration_Trigger {

    public $integration = 'meta_box';
    public $trigger = 'meta_box_post_field_updated';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User updates field on a post with a value', 'automatorwp' ),
            'select_option'     => __( 'User updates <strong>field</strong> on a post with a value', 'automatorwp' ),
            /* translators: %1$s: Field name. %2$s: Post title. %3$s: Field value. %4$s: Number of times. */
            'edit_label'        => sprintf( __( 'User updates field %1$s on %2$s with %3$s %4$s time(s)', 'automatorwp' ), '{field_name}', '{post}', '{field_value}', '{times}' ),
            /* translators: %1$s: Field name. %2$s: Post title. %3$s: Field value. */
            'log_label'         => sprintf( __( 'User updates field %1$s on %2$s with %3$s', 'automatorwp' ), '{field_name}', '{post}', '{field_value}' ),
            'action'            => array(
                'added_post_meta',
                'updated_post_meta'
            ),
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 4,
            'options'           => array(
                'field_name' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'field_name',
                    'name'              => __( 'Field:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any field', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_meta_box_get_post_fields',
                    'options_cb'        => 'automatorwp_meta_box_options_cb_post_fields',
                    'default'           => 'any'
                ) ),
                'field_value' => array(
                    'from' => 'field_value',
                    'default' => __( 'any value', 'automatorwp' ),
                    'fields' => array(
                        'field_value' => array(
                            'name' => __( 'Value:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                    )
                ),
                'post' => automatorwp_utilities_post_option( array(
                    'option_none_label' => __( 'any post', 'automatorwp' ),
                    'post_type' => 'any'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_meta_box_get_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }


    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int       $meta_id        ID of updated metadata entry
     * @param int       $object_id      ID of the object metadata is for
     * @param string    $meta_key       Metadata key
     * @param mixed     $_meta_value    Metadata value
     */
    public function listener( $meta_id, $object_id, $meta_key, $_meta_value ) {

        $user_id = get_current_user_id();

        // Bail if user is not logged
        if ($user_id === 0) {
            return;
        }

        // Trigger the tag removed
        
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'post_id'       => $object_id,
            'meta_key'      => $meta_key,
            'meta_value'    => $_meta_value,
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
        if( ! isset( $event['post_id'] ) ) {
            return false;
        }

        // Don't deserve if meta key is not received
        if( ! isset( $event['meta_key'] ) ) {
            return false;
        }

        // Ensure modified meta is a meta box
        $fields_allowed = array_keys( rwmb_get_object_fields( $event['post_id'] ) );

        // Don't deserve if meta_key is not allowed
        if ( ! in_array( $event['meta_key'], $fields_allowed, true ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( $trigger_options['post'] !== 'any' && absint( $trigger_options['post'] ) !== absint( $event['post_id'] ) ) {
            return false;
        }

        // Don't deserve if meta_key doesn't match with the trigger option
        if( $trigger_options['field_name'] !== 'any' && $trigger_options['field_name'] !== $event['meta_key'] ) {
            return false;
        }

        // Don't deserve if value doesn't matches with the trigger option
        if( $trigger_options['field_value'] !== '' && $trigger_options['field_value'] !== $event['meta_value'] ) {
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

        $log_meta['updated_meta_key'] = ( isset( $event['meta_key'] ) ? $event['meta_key'] : '' );
        $log_meta['updated_meta_value'] = ( isset( $event['meta_value'] ) ? $event['meta_value'] : '' );

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

        $log_fields['updated_meta_key'] = array(
            'name' => __( 'Updated field', 'automatorwp' ),
            'desc' => __( 'Key of the updated field.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['updated_meta_value'] = array(
            'name' => __( 'Updated value', 'automatorwp' ),
            'desc' => __( 'Value of the updated field.', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;

    }

}

new AutomatorWP_Meta_Box_Post_Field_Updated();