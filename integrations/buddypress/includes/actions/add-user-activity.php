<?php
/**
 * Add User Activity
 *
 * @package     AutomatorWP\Integrations\BuddyPress\Actions\Add_User_Activity
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_BuddyPress_Add_User_Activity extends AutomatorWP_Integration_Action {

    public $integration = 'buddypress';
    public $action = 'buddypress_add_user_activity';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add an activity to the user', 'automatorwp' ),
            'select_option'     => __( 'Add an <strong>activity</strong> to the user', 'automatorwp' ),
            /* translators: %1$s: Activity. */
            'edit_label'        => sprintf( __( 'Add an %1$s to the user', 'automatorwp' ), '{activity}' ),
            /* translators: %1$s: Activity. */
            'log_label'         => sprintf( __( 'Add an %1$s to the user', 'automatorwp' ), '{activity}' ),
            'options'           => array(
                'activity' => array(
                    'default' => __( 'activity', 'automatorwp' ),
                    'fields' => array(
                        // Note: "action" key is reserved
                        'activity_action' => array(
                            'name' => __( 'Action:', 'automatorwp' ),
                            'desc' => __( 'Activity\'s action.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'content' => array(
                            'name' => __( 'Content:', 'automatorwp' ),
                            'desc' => __( 'Activity\'s content.', 'automatorwp' ),
                            'type' => 'wysiwyg',
                            'default' => ''
                        ),
                        'link' => array(
                            'name' => __( 'Link:', 'automatorwp' ),
                            'desc' => __( 'URL link associated to the activity.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'hide_sitewide' => array(
                            'name' => __( 'Hide on the sitewide activity stream:', 'automatorwp' ),
                            'desc' => __( 'Check this option to hide this activity from the sitewide activity stream.', 'automatorwp' ),
                            'type' => 'checkbox',
                            'classes' => 'cmb2-switch'
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
        $activity_action    = $action_options['activity_action'];
        $content            = $action_options['content'];
        $link               = $action_options['link'];
        $hide               = $action_options['hide_sitewide'];

        // Add the activity to the user
        bp_activity_add( array(
            'action'            => $activity_action,
            'content'           => $content,
            'component'         => 'automatorwp',
            'type'              => 'activity_update',
            'primary_link'      => $link,
            'user_id'           => $user_id,
            'item_id'           => false,
            'secondary_item_id' => false,
            'hide_sitewide'     => (bool) $hide,
        ) );

    }

}

new AutomatorWP_BuddyPress_Add_User_Activity();