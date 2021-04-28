<?php
/**
 * Set User Profile Type
 *
 * @package     AutomatorWP\Integrations\BuddyBoss\Actions\Set_User_Member_Type
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_BuddyBoss_Set_User_Member_Type extends AutomatorWP_Integration_Action {

    public $integration = 'buddyboss';
    public $action = 'buddyboss_set_user_member_type';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Set user profile type', 'automatorwp' ),
            'select_option'     => __( 'Set user <strong>profile type</strong>', 'automatorwp' ),
            /* translators: %1$s: Group. */
            'edit_label'        => sprintf( __( 'Set user the profile type %1$s', 'automatorwp' ), '{member_type}' ),
            /* translators: %1$s: Group. */
            'log_label'         => sprintf( __( 'Set user the profile type %1$s', 'automatorwp' ), '{member_type}' ),
            'options'           => array(
                'member_type' => array(
                    'from' => 'member_type',
                    'default' => __( 'choose a profile type', 'automatorwp' ),
                    'fields' => array(
                        'member_type' => array(
                            'name' => __( 'Profile Type:', 'automatorwp' ),
                            'type' => 'select',
                            'options_cb' => 'automatorwp_buddyboss_member_types_options_cb',
                            'option_none' => false,
                            'option_none_value' => '',
                            'option_none_label' => __( 'choose a profile type', 'automatorwp' ),
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
        $member_type = $action_options['member_type'];

        // Bail if member type not provided
        if( empty( $member_type ) ) {
            return;
        }

        // Set the user member type
        bp_set_member_type( $user_id, $member_type );

    }

}

new AutomatorWP_BuddyBoss_Set_User_Member_Type();