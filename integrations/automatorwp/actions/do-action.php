<?php
/**
 * Do Action
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\Do_Action
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Do_Action extends AutomatorWP_Integration_Action {

    public $integration = 'automatorwp';
    public $action = 'automatorwp_do_action';

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
            'label'             => __( 'Run a WordPress hook', 'automatorwp' ),
            'select_option'     => __( 'Run a <strong>WordPress hook</strong>', 'automatorwp' ),
            /* translators: %1$s: Hook name. */
            'edit_label'        => sprintf( __( 'Run %1$s', 'automatorwp' ), '{hook}' ),
            /* translators: %1$s: Hook name */
            'log_label'         => sprintf( __( 'Run %1$s', 'automatorwp' ), '{hook}' ),
            'options'           => array(
                'hook' => array(
                    'from' => 'hook_name',
                    'default' => __( 'a WordPress hook', 'automatorwp' ),
                    'fields' => array(
                        'hook_name' => array(
                            'name' => __( 'Hook:', 'automatorwp' ),
                            'desc' => __( 'The hook to run.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'hook_args' => array(
                            'name' => __( 'Variables:', 'automatorwp' ),
                            'desc' => __( 'The variables to pass to the hook.', 'automatorwp' ),
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
        $hook_name = $action_options['hook_name'];
        $hook_args = $action_options['hook_args'];

        // Bail if empty meta key
        if( empty( $hook_name ) ) {
            $this->result = __( 'Empty hook name.', 'automatorwp' );
            return;
        }

        if( ! is_array( $hook_args ) ) {
            $hook_args = array();
        }

        // Parse the hook args option
        $hook_args = automatorwp_parse_function_args_option( $hook_args, $action, $user_id, $action_options, $automation );

        // Place the hook name at start of the hook args
        array_unshift( $hook_args, $hook_name );

        try {
            // Try to call to the hook
            call_user_func_array( 'do_action', $hook_args );

            $this->result = sprintf( __( 'Hook "%s" run successfully.', 'automatorwp' ), $hook_name );
        } catch ( Error $e ) {
            // Notify about any error handled
            $this->result = sprintf( __( 'Hook "%s" throw the error: %s', 'automatorwp' ), $hook_name, $e->getMessage() );
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

new AutomatorWP_WordPress_Do_Action();