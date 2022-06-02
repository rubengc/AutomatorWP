<?php
/**
 * Plugin Name:           AutomatorWP - WooCommerce integration
 * Plugin URI:            https://automatorwp.com/add-ons/woocommerce/
 * Description:           Connect AutomatorWP with WooCommerce.
 * Version:               1.1.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-woocommerce-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WooCommerce
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_WooCommerce {

    /**
     * @var         AutomatorWP_Integration_WooCommerce $instance The one true AutomatorWP_Integration_WooCommerce
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_WooCommerce self::$instance The one true AutomatorWP_Integration_WooCommerce
     */
    public static function instance() {

        if( ! self::$instance ) {

            self::$instance = new AutomatorWP_Integration_WooCommerce();

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
        define( 'AUTOMATORWP_WOOCOMMERCE_VER', '1.1.0' );

        // Plugin file
        define( 'AUTOMATORWP_WOOCOMMERCE_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WOOCOMMERCE_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WOOCOMMERCE_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/filters.php';
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/triggers/view-product.php';
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/triggers/purchase-product.php';
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/triggers/purchase-product-category.php';
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/triggers/purchase-product-tag.php';
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/triggers/complete-purchase.php';
            // WooCommerce Memberships
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/triggers/membership-created.php';
            // WooCommerce Subscriptions
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/triggers/purchase-subscription.php';

            // Anonymous Triggers
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/triggers/anonymous-purchase-product.php';
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/triggers/anonymous-purchase-product-category.php';
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/triggers/anonymous-purchase-product-tag.php';
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/triggers/anonymous-complete-purchase.php';

            // Actions
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/actions/add-user-to-coupon.php';
            // WooCommerce Memberships
            require_once AUTOMATORWP_WOOCOMMERCE_DIR . 'includes/actions/add-membership.php';

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

        automatorwp_register_integration( 'woocommerce', array(
            'label' => 'WooCommerce',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/woocommerce.svg',
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

        if ( ! class_exists( 'WooCommerce' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_WooCommerce' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_WooCommerce instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_WooCommerce The one true AutomatorWP_Integration_WooCommerce
 */
function AutomatorWP_Integration_WooCommerce() {
    return AutomatorWP_Integration_WooCommerce::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_WooCommerce' );
