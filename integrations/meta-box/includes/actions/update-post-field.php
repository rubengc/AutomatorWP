<?php
/**
 * Update Post Field
 *
 * @package     AutomatorWP\Integrations\Meta_Box\Actions\Update_Post_Field
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Meta_Box_Update_Post_Field extends AutomatorWP_Integration_Action {

    public $integration = 'meta_box';
    public $action = 'meta_box_update_post_field';

    /**
     * The action result
     *
     * @since 1.0.0
     *
     * @var array $result
     */
    public $result = array();

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Update post field with a value', 'automatorwp' ),
            'select_option'     => __( 'Update <strong>post field</strong> with a value', 'automatorwp' ),
            /* translators: %1$s: Post. %2$s: Field name. %1$s: Field value.*/
            'edit_label'        => sprintf( __( 'Update %1$s %2$s with %3$s', 'automatorwp' ), '{post}', '{field_name}', '{field_value}' ),
            /* translators: %1$s: Post. %2$s: Field name. %1$s: Field value.*/
            'log_label'         => sprintf( __( 'Update %1$s %2$s with %3$s', 'automatorwp' ), '{post}', '{field_name}', '{field_value}' ),
            'options'           => array(
                'post' => array(
                    'default' => __( 'any post', 'automatorwp' ),
                    'from' => 'post_id',
                    'fields' => array(
                        'post_id' => automatorwp_utilities_post_field( array(
                            'name' => __( 'Post:', 'automatorwp' ),
                            'post_type' => 'post',
                            'placeholder'           => __( 'Select a post', 'automatorwp' ),
                            'option_none_label'     => __( 'Select a post', 'automatorwp' ),
                            'option_custom'         => true,
                            'option_custom_desc'    => __( 'Post ID', 'automatorwp' ),
                        ) ),
                        'post_id_custom' => automatorwp_utilities_custom_field( array(
                            'option_custom_desc'    => __( 'Post ID', 'automatorwp' ),
                        ) ),
                    )),

                    'field_name' => automatorwp_utilities_ajax_selector_option( array(
                        'field'             => 'field_name',
                        'option_default'    => __( 'Select a field', 'automatorwp' ),
                        'name'              => __( 'Field:', 'automatorwp' ),
                        'action_cb'         => 'automatorwp_meta_box_get_post_fields',
                        'options_cb'        => 'automatorwp_meta_box_options_cb_post_fields',
                        'default'           => ''
                    ) ),

                    'field_value' => array(
                        'from' => 'field_value',
                        'default' => __( 'value', 'automatorwp' ),
                        'fields' => array(
                            'field_value' => array(
                                'name' => __( 'Value:', 'automatorwp' ),
                                'type' => 'text',
                                'default' => ''
                            ),
                        )
                    ),
            ),
        ) );

    }

    /**
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    public function execute( $action, $user_id, $action_options, $automation ) {

        global $wpdb;

        // Shorthand
        $field_name = $action_options['field_name'];
        $field_value = $action_options['field_value'];
        $post_id = absint( $action_options['post_id'] );

        // Bail if field_name is empty
        if ( empty ( $field_name ) ){
            return;
        }

        $this->result = array();

        // Bail if no post
        if( empty( $action_options['post_id'] ) ) {
            $this->result[] = sprintf( __( 'Post not found by the ID %1$s.', 'automatorwp' ), $post_id );
            return;
        }

        // Update post meta
        update_post_meta( $post_id, $field_name, $field_value );
        
        $this->result[] = __( 'Post field updated successfully.', 'automatorwp' );

    }

    /**
     * Register required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_action_log_meta', array( $this, 'log_meta' ), 10, 5 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

        parent::hooks();
    }

    /**
     * Action custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     *
     * @return array
     */
    public function log_meta( $log_meta, $action, $user_id, $action_options, $automation ) {

        // Bail if action type don't match this action
        if( $action->type !== $this->action ) {
            return $log_meta;
        }

        // Store post ID
        $log_meta['post_id'] = $action_options['post_id'];

        // Store post field
        $log_meta['field_name'] = $action_options['field_name'];

        // Store post value field
        $log_meta['field_value'] = $action_options['field_value'];

        // Store result
        $log_meta['result'] = $this->result;

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

        // Bail if log is not assigned to an action
        if( $log->type !== 'action' ) {
            return $log_fields;
        }

        // Bail if action type don't match this action
        if( $object->type !== $this->action ) {
            return $log_fields;
        }

        $log_fields['post_title'] = array(
            'name' => __( 'Post Information', 'automatorwp' ),
            'desc' => __( 'Information about the post updated.', 'automatorwp' ),
            'type' => 'title',
        );

        $log_fields['post_id'] = array(
            'name' => __( 'Post ID:', 'automatorwp' ),
            'desc' => __( 'The post\'s ID updated.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['field_name'] = array(
            'name' => __( 'Post Field:', 'automatorwp' ),
            'desc' => __( 'The updated field.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['field_value'] = array(
            'name' => __( 'Post Field Value:', 'automatorwp' ),
            'desc' => __( 'The updated field value.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;
    }

}

new AutomatorWP_Meta_Box_Update_Post_Field();