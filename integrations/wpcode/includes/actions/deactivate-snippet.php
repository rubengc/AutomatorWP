<?php
/**
 * Deactive Snippet
 *
 * @package     AutomatorWP\Integrations\WPCode\Actions\Deactive_Snippet
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WPCode_Deactive_Snippet extends AutomatorWP_Integration_Action {

    public $integration = 'wpcode';
    public $action = 'wpcode_deactive_snippet';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Deactivate a snippet', 'automatorwp' ),
            'select_option'     => __( '<strong>Deactivate</strong> a snippet', 'automatorwp' ),
            /* translators: %1$s: Title snippet. */
            'edit_label'        => sprintf( __( 'Deactivate %1$s', 'automatorwp' ), '{snippet}' ),
            /* translators: %1$s: Title snippet. */
            'log_label'         => sprintf( __( 'Deactivate %1$s', 'automatorwp' ), '{snippet}' ),
            'options'           => array(
                'snippet' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'snippet',
                    'option_default'    => __( 'Snippet', 'automatorwp' ),
                    'name'              => __( 'Snippet:', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_wpcode_get_snippets',
                    'options_cb'        => 'automatorwp_wpcode_options_cb_snippet',
                    'placeholder'       => 'Select a snippet',
                    'default'           => ''
                ) ),
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
        $this->result = '';
        $snippet_id    = $action_options['snippet'];

        if ( empty ( $snippet_id ) ) {
            $this->result = __( 'Please, select a snippet to deactivate', 'automatorwp' );
            return;
        }

        $snippet    = new \WPCode_Snippet( absint( $snippet_id ) );   
        
        if ( ! $snippet->is_active() ) {
            $this->result = sprintf( __( 'The snippet %s is already inactive', 'automatorwp' ), get_the_title( $snippet_id ) );
            return;
        }
            
        $snippet->deactivate();
        $this->result = sprintf( __( 'The snippet %s has been deactivated', 'automatorwp' ), get_the_title( $snippet_id ) );
        
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

        // Store the action's result
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

new AutomatorWP_WPCode_Deactive_Snippet();