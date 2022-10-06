<?php
/**
 * Plugin Name:           AutomatorWP - JetFormBuilder integration
 * Plugin URI:            https://automatorwp.com/add-ons/jetformbuilder/
 * Description:           Connect AutomatorWP with JetFormBuilder.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.8
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\JetFormBuilder
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_JetFormBuilder {

    /**
     * @var         AutomatorWP_Integration_JetFormBuilder $instance The one true AutomatorWP_Integration_JetFormBuilder
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_JetFormBuilder self::$instance The one true AutomatorWP_Integration_JetFormBuilder
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_JetFormBuilder();

            if( ! self::$instance->pro_installed() ) {

                self::$instance->constants();
                self::$instance->includes();

            }

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
        define( 'AUTOMATORWP_JETFORMBUILDER_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_JETFORMBUILDER_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_JETFORMBUILDER_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_JETFORMBUILDER_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() ) {

            // Includes
            require_once AUTOMATORWP_JETFORMBUILDER_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_JETFORMBUILDER_DIR . 'includes/triggers/submit-form.php';
            require_once AUTOMATORWP_JETFORMBUILDER_DIR . 'includes/triggers/anonymous-submit-form.php';

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

        automatorwp_register_integration( 'jetformbuilder', array(
            'label' => 'JetFormBuilder',
            'icon'  => AUTOMATORWP_JETFORMBUILDER_URL . 'assets/jetformbuilder.svg',
        ) );

    }

    /**
     * Activation hook for the plugin.
     *
     * @since  1.0.0
     */
    function activate() {

        if( $this->meets_requirements() ) {

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

        if ( ! function_exists( 'jet_form_builder_init' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_JetFormBuilder' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_JetFormBuilder instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_JetFormBuilder The one true AutomatorWP_Integration_JetFormBuilder
 */
function AutomatorWP_Integration_JetFormBuilder() {
    return AutomatorWP_Integration_JetFormBuilder::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_JetFormBuilder' );
