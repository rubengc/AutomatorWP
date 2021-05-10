<?php
/**
 * User Tag
 *
 * @package     AutomatorWP\Integrations\Groundhogg\Actions\User_Tag
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Groundhogg_User_Tag extends AutomatorWP_Integration_Action {

    public $integration = 'groundhogg';
    public $action = 'groundhogg_user_tag';

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
                    'action_cb'         => 'automatorwp_groundhogg_get_tags',
                    'options_cb'        => 'automatorwp_groundhogg_options_cb_tag',
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
        $tag_id = $action_options['tag'];

        // Bail if empty tag to assign
        if( empty( $tag_id ) ) {
            return;
        }

        $tag = Groundhogg\Plugin::$instance->dbs->get_db( 'tags' )->get( $tag_id );

        // Bail if tag not exists
        if( ! $tag ) {
            return;
        }

        $contact = new Groundhogg\Contact( $user_id, true );

        if( ! $contact ) {
            return;
        }

        // Add tag to the user
        $contact->add_tag( $tag->tag_id );

    }

}

new AutomatorWP_Groundhogg_User_Tag();