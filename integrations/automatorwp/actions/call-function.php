<?php
/**
 * Call Function
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\Call_Function
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Call_Function extends AutomatorWP_Integration_Action {

    public $integration = 'automatorwp';
    public $action = 'automatorwp_call_function';

    /**
     * The action result
     *
     * @since 1.0.0
     *
     * @var string $result
     */
    public $result = '';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Call a function', 'automatorwp' ),
            'select_option'     => __( 'Call a <strong>function</strong>', 'automatorwp' ),
            /* translators: %1$s: Function name. */
            'edit_label'        => sprintf( __( 'Call %1$s', 'automatorwp' ), '{function}' ),
            /* translators: %1$s: Function name */
            'log_label'         => sprintf( __( 'Call %1$s', 'automatorwp' ), '{function}' ),
            'options'           => array(
                'function' => array(
                    'from' => 'function_name',
                    'default' => __( 'a function', 'automatorwp' ),
                    'fields' => array(
                        'function_name' => array(
                            'name' => __( 'Function name:', 'automatorwp' ),
                            'desc' => __( 'The function to call.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'function_args' => array(
                            'name' => __( 'Variables:', 'automatorwp' ),
                            'desc' => __( 'The variables to pass to the function.', 'automatorwp' ),
                            'type' => 'group',
                            'classes' => 'automatorwp-fields-table automatorwp-fields-table-col-1',
                            'options'     => array(
                                'add_button'        => __( 'Add variable', 'automatorwp' ),
                                'remove_button'     => '<span class="dashicons dashicons-no-alt"></span>',
                            ),
                            'fields' => array(
                                'value' => array(
                                    'name' => __( 'Value:', 'automatorwp' ),
                                    'type' => 'text',
                                    'default' => ''
                                ),
                            ),
                        ),
                    )
                )
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

        // Shorthand
        $function_name = $action_options['function_name'];
        $function_args = $action_options['function_args'];

        // Bail if empty meta key
        if( empty( $function_name ) ) {
            $this->result = __( 'Empty function name.', 'automatorwp' );
            return;
        }

        // Bail if empty meta key
        if( ! function_exists( $function_name ) ) {
            $this->result = sprintf( __( 'Function "%s" not found.', 'automatorwp' ), $function_name );
            return;
        }

        if( ! is_array( $function_args ) ) {
            $function_args = array();
        }

        // Parse the function args option
        $function_args = automatorwp_parse_function_args_option( $function_args, $action, $user_id, $action_options, $automation );

        try {
            // Try to call to the function
            $function_result = call_user_func_array( $function_name, $function_args );

            $function_result = automatorwp_parse_function_arg_value( $function_result );

            $this->result = sprintf( __( 'Function "%s" called successfully.', 'automatorwp' ), $function_name );

            // Store the function result
            if( ! empty( $function_result ) ) {
                $this->result .= ' ' . sprintf( __( 'The function returned: %s', 'automatorwp' ), $function_result );
            }
        } catch ( Error $e ) {
            // Notify about any error handled
            $this->result = sprintf( __( 'Function "%s" throw the error: %s', 'automatorwp' ), $function_name, $e->getMessage() );
        }

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

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;
    }

}

new AutomatorWP_WordPress_Call_Function();