<?php
/**
 * Plugin Name:           AutomatorWP - JetEngine
 * Plugin URI:            https://automatorwp.com/add-ons/jetengine/
 * Description:           Connect AutomatorWP with JetEngine.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-jetengine
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.1
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\JetEngine
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_JetEngine {

    /**
     * @var         AutomatorWP_Integration_JetEngine $instance The one true AutomatorWP_Integration_JetEngine
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_JetEngine self::$instance The one true AutomatorWP_Integration_JetEngine
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_JetEngine();

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
        define( 'AUTOMATORWP_JETENGINE_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_JETENGINE_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_JETENGINE_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_JETENGINE_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_JETENGINE_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_JETENGINE_DIR . 'includes/triggers/publish-post-type.php';

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

        automatorwp_register_integration( 'jetengine', array(
            'label' => 'JetEngine',
            'icon'  => AUTOMATORWP_JETENGINE_URL . 'assets/jetengine.svg',
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

        if ( ! class_exists( '\Jet_Engine' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_JetEngine' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_JetEngine instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_JetEngine The one true AutomatorWP_Integration_JetEngine
 */
function AutomatorWP_Integration_JetEngine() {
    return AutomatorWP_Integration_JetEngine::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_JetEngine' );
