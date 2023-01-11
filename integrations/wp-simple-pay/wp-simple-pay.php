<?php
/**
 * Plugin Name:           AutomatorWP - WP Simple Pay integration
 * Plugin URI:            https://automatorwp.com/add-ons/wp-simple-pay/
 * Description:           Connect AutomatorWP with WP Simple Pay.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wp-simple-pay-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WP_Simple_Pay
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_WP_Simple_Pay {

    /**
     * @var         AutomatorWP_Integration_WP_Simple_Pay $instance The one true AutomatorWP_Integration_WP_Simple_Pay
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_WP_Simple_Pay self::$instance The one true AutomatorWP_Integration_WP_Simple_Pay
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_WP_Simple_Pay();

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
        define( 'AUTOMATORWP_WP_SIMPLE_PAY_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_WP_SIMPLE_PAY_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WP_SIMPLE_PAY_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WP_SIMPLE_PAY_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() && ! $this->pro_installed()  ) {

            // Includes
            require_once AUTOMATORWP_WP_SIMPLE_PAY_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_WP_SIMPLE_PAY_DIR . 'includes/ajax-functions.php';
            
            if( SIMPLE_PAY_PLUGIN_NAME === 'WP Simple Pay Pro' ) {

                // Triggers
                require_once AUTOMATORWP_WP_SIMPLE_PAY_DIR . 'includes/triggers/complete-purchase.php';

            } else {

                // Lite Triggers
                require_once AUTOMATORWP_WP_SIMPLE_PAY_DIR . 'includes/triggers/complete-purchase-lite.php';

            }

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

        automatorwp_register_integration( 'wp_simple_pay', array(
            'label' => 'WP Simple Pay',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/wp-simple-pay.svg',
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

        if ( ! defined( 'SIMPLE_PAY_PLUGIN_NAME' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_WP_Simple_Pay' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_WP_Simple_Pay instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_WP_Simple_Pay The one true AutomatorWP_Integration_WP_Simple_Pay
 */
function AutomatorWP_Integration_WP_Simple_Pay() {
    return AutomatorWP_Integration_WP_Simple_Pay::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_WP_Simple_Pay' );
