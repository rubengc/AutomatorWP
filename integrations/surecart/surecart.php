<?php
/**
 * Plugin Name:           AutomatorWP - SureCart integration
 * Plugin URI:            https://automatorwp.com/add-ons/surecart/
 * Description:           Connect AutomatorWP with SureCart.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.8
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\SureCart
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_SureCart {

    /**
     * @var         AutomatorWP_Integration_SureCart $instance The one true AutomatorWP_Integration_SureCart
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_SureCart self::$instance The one true AutomatorWP_Integration_SureCart
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_SureCart();

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
        define( 'AUTOMATORWP_SURECART_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_SURECART_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_SURECART_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_SURECART_URL', plugin_dir_url( __FILE__ ) );
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

            // Triggers
            require_once AUTOMATORWP_SURECART_DIR . 'includes/triggers/make-purchase.php';

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

        automatorwp_register_integration( 'surecart', array(
            'label' => 'SureCart',
            'icon'  => AUTOMATORWP_SURECART_URL . 'assets/surecart.svg',
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

        if ( ! class_exists( 'SureCart' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_SureCart' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_SureCart instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_SureCart The one true AutomatorWP_Integration_SureCart
 */
function AutomatorWP_Integration_SureCart() {
    return AutomatorWP_Integration_SureCart::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_SureCart' );
