<?php
/**
 * Plugin Name:           AutomatorWP - WS Form integration
 * Plugin URI:            https://automatorwp.com/add-ons/ws-form/
 * Description:           Connect AutomatorWP with WS Form.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-ws-form-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WS_Form
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_WS_Form {

    /**
     * @var         AutomatorWP_Integration_WS_Form $instance The one true AutomatorWP_Integration_WS_Form
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_WS_Form self::$instance The one true AutomatorWP_Integration_WS_Form
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_WS_Form();
            self::$instance->constants();
            self::$instance->includes();
            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'AUTOMATORWP_WS_FORM_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_WS_FORM_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WS_FORM_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WS_FORM_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() && ! $this->pro_installed() ) {

            // Triggers
            require_once AUTOMATORWP_WS_FORM_DIR . 'includes/triggers/submit-form.php';
            // Anonymous Triggers
            require_once AUTOMATORWP_WS_FORM_DIR . 'includes/triggers/anonymous-submit-form.php';

            // Includes
            require_once AUTOMATORWP_WS_FORM_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_WS_FORM_DIR . 'includes/functions.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {

        add_action( 'automatorwp_init', array( $this, 'register_integration' ) );
        // Setup our activation and deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        
    }

    /**
     * Registers this integration
     *
     * @since 1.0.0
     */
    function register_integration() {

        automatorwp_register_integration( 'ws_form', array(
            'label' => 'WS Form',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/ws-form.svg',
        ) );

    }

    /**
     * Activation hook for the plugin.
     *
     * @since  1.0.0
     */
    function activate() {

        if( $this->meets_requirements() && ! $this->pro_installed() ) {

        }

    }

    /**
     * Deactivation hook for the plugin.
     *
     * @since  1.0.0
     */
    function deactivate() {

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'AutomatorWP' ) ) {
            return false;
        }

        if ( ! class_exists( 'WS_Form' ) ) {
            return false;
        }

        if ( ! version_compare( WS_FORM_VERSION, '1.8.71' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Check if the pro version of this integration is installed
     *
     * @since  1.0.0
     *
     * @return bool True if pro version installed
     */
    private function pro_installed() {

        if ( ! class_exists( 'AutomatorWP_WS_Form' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_WS_Form instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_WS_Form The one true AutomatorWP_Integration_WS_Form
 */
function AutomatorWP_Integration_WS_Form() {
    return AutomatorWP_Integration_WS_Form::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_WS_Form' );
