<?php
/**
 * User Role
 *
 * @package     AutomatorWP\Integrations\Popup_Maker\Actions\Show_Popup
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Popup_Maker_Show_Popup extends AutomatorWP_Integration_Action {

    public $integration = 'popup_maker';
    public $action = 'popup_maker_show_popup';
    public $meta_key = 'automatorwp_popup_maker_popups';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Show a popup', 'automatorwp' ),
            'select_option'     => __( 'Show a <strong>popup</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. */
            'edit_label'        => sprintf( __( 'Show %1$s', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'Show %1$s', 'automatorwp' ), '{post}' ),
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Popup:', 'automatorwp' ),
                    'option_default' => __( 'a popup', 'automatorwp' ),
                    'option_none' => false,
                    'option_custom'         => true,
                    'option_custom_desc'    => __( 'Popup ID', 'automatorwp' ),
                    'post_type' => 'popup'
                ) ),
            ),
        ) );

    }

    /**
     * Register required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        add_filter( 'pum_popup_is_loadable', array( $this, 'maybe_load_popup' ), 10, 2 );

        parent::hooks();

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
        $post_id = absint( $action_options['post'] );

        $post = get_post( $post_id );

        // Bail if post doesn't exists
        if( ! $post ) {
            return;
        }

        // Get popups to show
        $popups_to_show = get_user_meta( $user_id, $this->meta_key, true );

        if( ! is_array( $popups_to_show ) ) {
            $popups_to_show = array();
        }

        $popups_to_show[] = $post_id;

        // Update popups to show
        update_user_meta( $user_id, $this->meta_key, $popups_to_show );

    }

    /**
     * Determine if popup should be displayed to the user
     *
     * @since 1.0.0
     *
     * @param bool $loadable
     * @param int $post_id
     *
     * @return bool
     */
    public function maybe_load_popup( $loadable, $post_id ) {

        global $wpdb;

        // Ensure post ID as integer
        $post_id = absint( $post_id );

        $actions_meta = AutomatorWP()->db->actions_meta;

        // Check if any action is using this popup
        $in_use = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$actions_meta} AS am WHERE am.meta_key = 'post' AND am.meta_value = {$post_id}" ) );

        // Bail if none action is using this
        if( $in_use === 0 ) {
            return $loadable;
        }

        $user_id = get_current_user_id();

        // Get popups to show
        $popups_to_show = get_user_meta( $user_id, $this->meta_key, true );

        if( ! is_array( $popups_to_show ) ) {
            $popups_to_show = array();
        }

        // If popup is on popups to show, then make it loadable
        return in_array( $post_id, $popups_to_show );

    }

}

new AutomatorWP_Popup_Maker_Show_Popup();