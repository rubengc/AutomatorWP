<?php
/**
 * User Tag
 *
 * @package     AutomatorWP\Integrations\FluentCRM\Actions\User_Tag
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly

if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_FluentCRM_User_Tag extends AutomatorWP_Integration_Action {

    public $integration = 'fluentcrm';
    public $action = 'fluentcrm_user_tag';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add tag to user', 'automatorwp' ),
            'select_option'     => __( 'Add <strong>tag</strong> to user', 'automatorwp' ),
            /* translators: %1$s: Tag. */
            'edit_label'        => sprintf( __( 'Add tag %1$s', 'automatorwp' ), '{tag}' ),
            /* translators: %1$s: Tag. */
            'log_label'         => sprintf( __( 'Add tag %1$s', 'automatorwp' ), '{tag}' ),
            'options'           => array(
                'tag' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'tag',
                    'option_default'    => __( 'Select a tag', 'automatorwp' ),
                    'name'              => __( 'Tag:', 'automatorwp' ),
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Tag ID', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_fluentcrm_get_tags',
                    'options_cb'        => 'automatorwp_fluentcrm_options_cb_tag',
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
        $tag_id = $action_options['tag'];

        // Bail if empty tag to assign
        if( empty( $tag_id ) ) {
            return;
        }

        $tag = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}fc_tags WHERE id = %s",
            $tag_id
        ) );

        // Bail if tag not exists
        if( ! $tag ) {
            return;
        }

        $subscriber = automatorwp_fluentcrm_get_subscriber( $user_id );

        // Bail if subscriber not exists
        if( ! $subscriber ) {
            return;
        }

        // Add tag to the user
        $subscriber->attachTags( array( $tag_id ) );

    }

}

new AutomatorWP_FluentCRM_User_Tag();