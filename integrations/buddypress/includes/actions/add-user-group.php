<?php
/**
 * Add User Group
 *
 * @package     AutomatorWP\Integrations\BuddyPress\Actions\Add_User_Group
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_BuddyPress_Add_User_Group extends AutomatorWP_Integration_Action {

    public $integration = 'buddypress';
    public $action = 'buddypress_add_user_group';

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
            'label'             => __( 'Add user to group', 'automatorwp' ),
            'select_option'     => __( 'Add user to <strong>group</strong>', 'automatorwp' ),
            /* translators: %1$s: Group. */
            'edit_label'        => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{group}' ),
            /* translators: %1$s: Group. */
            'log_label'         => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{group}' ),
            'options'           => array(
                'group' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'group',
                    'option_default'    => __( 'group', 'automatorwp' ),
                    'name'              => __( 'Group:', 'automatorwp' ),
                    'option_none'       => false,
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Group ID', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_buddypress_get_groups',
                    'options_cb'        => 'automatorwp_buddypress_options_cb_group',
                    'placeholder'       => __( 'Select a group', 'automatorwp' ),
                    'default'           => '',
                ) )
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

        $this->result = '';

        // Bail if BuddyPress function does not exist
        if ( ! function_exists( 'groups_join_group' ) ) {
            $this->result = __( 'Groups component is not active.', 'automatorwp' );
            return;
        }

        // Shorthand
        $group_id = absint( $action_options['group'] );

        // Bail if group not provided
        if( $group_id === 0 ) {
            return;
        }

        // Add the user to the group
        groups_join_group( $group_id, $user_id );
        $this->result = __( 'User added to group successfully.', 'automatorwp' );

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

new AutomatorWP_BuddyPress_Add_User_Group();