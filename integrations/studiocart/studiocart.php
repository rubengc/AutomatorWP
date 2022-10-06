<?php
/**
 * Plugin Name:           AutomatorWP - Studiocart integration
 * Plugin URI:            https://automatorwp.com/add-ons/studiocart/
 * Description:           Connect AutomatorWP with Studiocart.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.8
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Studiocart
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Studiocart {

    /**
     * @var         AutomatorWP_Integration_Studiocart $instance The one true AutomatorWP_Integration_Studiocart
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Studiocart self::$instance The one true AutomatorWP_Integration_Studiocart
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Studiocart();

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
        define( 'AUTOMATORWP_STUDIOCART_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_STUDIOCART_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_STUDIOCART_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_STUDIOCART_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_STUDIOCART_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_STUDIOCART_DIR . 'includes/triggers/purchase-product.php';

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

        automatorwp_register_integration( 'studiocart', array(
            'label' => 'Studiocart',
            'icon'  => AUTOMATORWP_STUDIOCART_URL . 'assets/studiocart.svg',
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

        if ( ! class_exists( 'NCS_Cart' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Studiocart' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Studiocart instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Studiocart The one true AutomatorWP_Integration_Studiocart
 */
function AutomatorWP_Integration_Studiocart() {
    return AutomatorWP_Integration_Studiocart::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Studiocart' );
