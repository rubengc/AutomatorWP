<?php
/**
 * Add User To List
 *
 * @package     AutomatorWP\Integrations\MailPoet\Actions\Add_User_To_List
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_MailPoet_Add_User_To_List extends AutomatorWP_Integration_Action {

    public $integration = 'mailpoet';
    public $action = 'mailpoet_add_user_to_list';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add user to a list', 'automatorwp' ),
            'select_option'     => __( 'Add user to a <strong>list</strong>', 'automatorwp' ),
            /* translators: %1$s: List. */
            'edit_label'        => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{list}' ),
            /* translators: %1$s: List. */
            'log_label'         => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{list}' ),
            'options'           => array(
                'list' => array(
                    'from' => 'list',
                    'fields' => array(
                        'list' => array(
                            'name' => __( 'List:', 'automatorwp' ),
                            'type' => 'select',
                            'options_cb' => 'automatorwp_mailpoet_lists_options_cb',
                            'option_none' => true,
                            'default' => ''
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

        if ( ! class_exists( '\MailPoet\API\API' ) ) {
            return;
        }

        // Shorthand
        $list_id = $action_options['list'];

        // Bail if not list selected
        if( $list_id === '' ) {
            return;
        }

        // Get the MailPoet API
        $mailpoet = \MailPoet\API\API::MP( 'v1' );

        // Get the user to find its subscriber
        $user = get_userdata( $user_id );

        try {
            // Get the subscriber
            $subscriber = $mailpoet->getSubscriber( $user->user_email );

            // Subscribe him to the list
            $mailpoet->subscribeToList( $subscriber['id'], $list_id, array( 'send_confirmation_email' => true ) );
        } catch ( \MailPoet\API\MP\v1\APIException $e ) {

        }

    }

}

new AutomatorWP_MailPoet_Add_User_To_List();