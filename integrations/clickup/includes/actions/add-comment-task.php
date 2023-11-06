<?php
/**
 * Add Comment Task
 *
 * @package     AutomatorWP\Integrations\ClickUp\Actions\Add_Comment_Task
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_ClickUp_Add_Comment_Task extends AutomatorWP_Integration_Action {

    public $integration = 'clickup';
    public $action = 'clickup_add_comment_task';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add comment to a task', 'automatorwp' ),
            'select_option'     => __( 'Add comment to a <strong>task</strong>', 'automatorwp' ),
            /* translators: %1$s: Task. */
            'edit_label'        => sprintf( __( 'Add comment to a  %1$s', 'automatorwp' ), '{task}' ),
            /* translators: %1$s: Task. */
            'log_label'         => sprintf( __( 'Add comment to a  %1$s', 'automatorwp' ), '{task}' ),
            'options'           => array(
                'task' => array(
                    'from' => 'task',
                    'default' => __( 'task', 'automatorwp' ),
                    'fields' => array(
                        'team' => automatorwp_utilities_ajax_selector_field( array(
                            'name'              => __( 'Team:', 'automatorwp' ),
                            'option_none'       => false,
                            'action_cb'         => 'automatorwp_clickup_get_teams',
                            'options_cb'        => 'automatorwp_clickup_options_cb_team',
                            'placeholder'       => 'Select a team',
                        ) ),
                        'space' => automatorwp_utilities_ajax_selector_field( array(
                            'name'              => __( 'Space:', 'automatorwp' ),
                            'option_none'       => false,
                            'action_cb'         => 'automatorwp_clickup_get_spaces',
                            'options_cb'        => 'automatorwp_clickup_options_cb_space',
                            'placeholder'       => 'Select a space',
                            'default'           => ''
                        ) ),
                        'folder' => automatorwp_utilities_ajax_selector_field( array(
                            'name'              => __( 'Folder:', 'automatorwp' ),
                            'option_none'       => false,
                            'action_cb'         => 'automatorwp_clickup_get_folders',
                            'options_cb'        => 'automatorwp_clickup_options_cb_folder',
                            'placeholder'       => 'Select a folder',
                            'default'           => ''
                        ) ),
                        'list' => automatorwp_utilities_ajax_selector_field( array(
                            'name'              => __( 'List:', 'automatorwp' ),
                            'option_none'       => false,
                            'action_cb'         => 'automatorwp_clickup_get_lists',
                            'options_cb'        => 'automatorwp_clickup_options_cb_list',
                            'placeholder'       => 'Select a list',
                            'default'           => ''
                        ) ),
                        'task' => automatorwp_utilities_ajax_selector_field( array(
                            'name'              => __( 'Task:', 'automatorwp' ),
                            'option_none'       => false,
                            'action_cb'         => 'automatorwp_clickup_get_tasks',
                            'options_cb'        => 'automatorwp_clickup_options_cb_task',
                            'placeholder'       => 'Select a task',
                            'default'           => ''
                        ) ),
                        'comment' => array(
                            'name' => __( 'Comment:', 'automatorwp' ),
                            'desc' => __( 'The comment to add to the task', 'automatorwp' ),
                            'type' => 'wysiwyg',
                            'required'  => true,
                            'default' => ''
                        ),
                    ),
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

        // Shorthand
        $task_id = $action_options['task'];
        $comment_text = $action_options['comment'];

        // Bail if comment is empty
        if ( empty ( $comment_text ) ) {
            return;
        }

        $user = get_user_by ( 'ID', $user_id );

        $this->result = '';

        // Bail if ClickUp not configured
        if( ! automatorwp_clickup_get_api() ) {
            $this->result = __( 'ClickUp integration not configured in AutomatorWP settings.', 'automatorwp-clickup' );
            return;
        }

        $response = automatorwp_clickup_add_comment( $task_id, $comment_text );

        // Create list if not exist
        if ( $response === 200 ) {
            $this->result = __( 'Created comment in task ', 'automatorwp-clickup' );
        } else {
            $this->result = __( 'The comment could not be created', 'automatorwp-clickup' );
        }

    }

    /**
     * Register required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Configuration notice
        add_filter( 'automatorwp_automation_ui_after_item_label', array( $this, 'configuration_notice' ), 10, 2 );

        // Log meta data
        add_filter( 'automatorwp_user_completed_action_log_meta', array( $this, 'log_meta' ), 10, 5 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

        parent::hooks();

    }

    /**
     * Configuration notice
     *
     * @since 1.0.0
     *
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The object type (trigger|action)
     */
    public function configuration_notice( $object, $item_type ) {

        // Bail if action type don't match this action
        if( $item_type !== 'action' ) {
            return;
        }

        if( $object->type !== $this->action ) {
            return;
        }

        // Warn user if the authorization has not been setup from settings
        if( ! automatorwp_clickup_get_api() ) : ?>
            <div class="automatorwp-notice-warning" style="margin-top: 10px; margin-bottom: 0;">
                <?php echo sprintf(
                    __( 'You need to configure the <a href="%s" target="_blank">ClickUp settings</a> to get this action to work.', 'automatorwp-clickup' ),
                    get_admin_url() . 'admin.php?page=automatorwp_settings&tab=opt-tab-clickup'
                ); ?>
                <?php echo sprintf(
                    __( '<a href="%s" target="_blank">Documentation</a>', 'automatorwp-clickup' ),
                    'https://automatorwp.com/docs/clickup/'
                ); ?>
            </div>
        <?php endif;

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
            'name' => __( 'Result:', 'automatorwp-clickup' ),
            'type' => 'text',
        );

        return $log_fields;
    }

}

new AutomatorWP_ClickUp_Add_Comment_Task();