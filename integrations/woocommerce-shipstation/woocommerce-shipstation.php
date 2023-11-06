<?php
/**
 * Plugin Name:           AutomatorWP - WooCommerce ShipStation
 * Plugin URI:            https://automatorwp.com/add-ons/woocommerce-shipstation/
 * Description:           Connect AutomatorWP with WooCommerce ShipStation.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-woocommerce-shipstation
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.3
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WooCommerce_ShipStation
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_WooCommerce_ShipStation {

    /**
     * @var         AutomatorWP_Integration_WooCommerce_ShipStation $instance The one true AutomatorWP_Integration_WooCommerce_ShipStation
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_WooCommerce_ShipStation self::$instance The one true AutomatorWP_Integration_WooCommerce_ShipStation
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_WooCommerce_ShipStation();

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
        define( 'AUTOMATORWP_WOOCOMMERCE_SHIPSTATION_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_WOOCOMMERCE_SHIPSTATION_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WOOCOMMERCE_SHIPSTATION_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WOOCOMMERCE_SHIPSTATION_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_WOOCOMMERCE_SHIPSTATION_DIR . 'includes/filters.php';
            require_once AUTOMATORWP_WOOCOMMERCE_SHIPSTATION_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_WOOCOMMERCE_SHIPSTATION_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_WOOCOMMERCE_SHIPSTATION_DIR . 'includes/triggers/order-shipped.php';

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

    }

    /**
     * Registers this integration
     *
     * @since 1.0.0
     */
    function register_integration() {

        automatorwp_register_integration( 'woocommerce_shipstation', array(
            'label' => 'WooCommerce ShipStation',
            'icon'  => AUTOMATORWP_WOOCOMMERCE_SHIPSTATION_URL . 'assets/woocommerce-shipstation.svg',
        ) );

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

        if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'woocommerce_shipstation_init' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_WooCommerce_ShipStation' ) ) {
            return false;
        }

        return true;

    }


}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_WooCommerce_ShipStation instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_WooCommerce_ShipStation The one true AutomatorWP_Integration_WooCommerce_ShipStation
 */
function AutomatorWP_Integration_WooCommerce_ShipStation() {
    return AutomatorWP_Integration_WooCommerce_ShipStation::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_WooCommerce_ShipStation' );
