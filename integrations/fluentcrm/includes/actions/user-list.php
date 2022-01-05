<?php
/**
 * User List
 *
 * @package     AutomatorWP\Integrations\FluentCRM\Actions\User_List
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly

if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_FluentCRM_User_List extends AutomatorWP_Integration_Action {

    public $integration = 'fluentcrm';
    public $action = 'fluentcrm_user_list';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add user to list', 'automatorwp' ),
            'select_option'     => __( 'Add user to <strong>list</strong>', 'automatorwp' ),
            /* translators: %1$s: Tag. */
            'edit_label'        => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{list}' ),
            /* translators: %1$s: Tag. */
            'log_label'         => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{list}' ),
            'options'           => array(
                'list' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'list',
                    'option_default'    => __( 'Select a list', 'automatorwp' ),
                    'name'              => __( 'List:', 'automatorwp' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'List ID', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_fluentcrm_get_lists',
                    'options_cb'        => 'automatorwp_fluentcrm_options_cb_list',
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

        global $wpdb;

        // Shorthand
        $list_id = $action_options['list'];

        // Bail if empty list to assign
        if( empty( $list_id ) ) {
            return;
        }

        $list = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}fc_lists WHERE id = %s",
            $list_id
        ) );

        // Bail if list not exists
        if( ! $list ) {
            return;
        }

        $subscriber = automatorwp_fluentcrm_get_subscriber( $user_id );

        // Bail if subscriber not exists
        if( ! $subscriber ) {
            return;
        }

        // Add user to list
        $subscriber->attachLists( array( $list_id ) );


    }

}

new AutomatorWP_FluentCRM_User_List();