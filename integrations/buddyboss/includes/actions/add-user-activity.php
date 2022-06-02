<?php
/**
 * Add User Activity
 *
 * @package     AutomatorWP\Integrations\BuddyBoss\Actions\Add_User_Activity
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_BuddyBoss_Add_User_Activity extends AutomatorWP_Integration_Action {

    public $integration = 'buddyboss';
    public $action = 'buddyboss_add_user_activity';

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
                        'link_preview' => array(
                            'name' => __( 'Link Preview (Optional):', 'automatorwp' ),
                            'desc' => __( 'URL link to show as preview with the activity.', 'automatorwp' ),
                            'type' => 'text',
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

        if( ! function_exists( 'bp_activity_add' ) ) {
            return;
        }

        // Shorthand
        $activity_action    = $action_options['activity_action'];
        $content            = $action_options['content'];
        $link_preview       = $action_options['link_preview'];
        $link               = $action_options['link'];
        $hide               = $action_options['hide_sitewide'];

        // Add the activity to the user
        $activity_id = bp_activity_add( array(
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

        // Activity link preview
        if( $activity_id && ! empty( $link_preview ) ) {

            $preview_data = automatorwp_buddyboss_get_link_preview( $link_preview );

            if( $preview_data !== false ) {
                bp_activity_update_meta( $activity_id, '_link_embed', '1' );
                bp_activity_update_meta( $activity_id, '_link_preview_data', $preview_data );
            }
        }

    }

}

new AutomatorWP_BuddyBoss_Add_User_Activity();