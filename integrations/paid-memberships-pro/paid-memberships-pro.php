<?php
/**
 * Plugin Name:           AutomatorWP - Paid Memberships Pro integration
 * Plugin URI:            https://automatorwp.com/add-ons/paid-memberships-pro/
 * Description:           Connect AutomatorWP with Paid Memberships Pro.
 * Version:               1.0.2
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-paid-memberships-pro-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Paid_Memberships_Pro
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Paid_Memberships_Pro {

    /**
     * @var         AutomatorWP_Integration_Paid_Memberships_Pro $instance The one true AutomatorWP_Integration_Paid_Memberships_Pro
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Paid_Memberships_Pro self::$instance The one true AutomatorWP_Integration_Paid_Memberships_Pro
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Paid_Memberships_Pro();
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
        define( 'AUTOMATORWP_PAID_MEMBERSHIPS_PRO_VER', '1.0.2' );

        // Plugin file
        define( 'AUTOMATORWP_PAID_MEMBERSHIPS_PRO_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_PAID_MEMBERSHIPS_PRO_URL', plugin_dir_url( __FILE__ ) );
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

            // Includes
            require_once AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR . 'includes/triggers/purchase-membership.php';
            require_once AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR . 'includes/triggers/cancel-subscription.php';
            require_once AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR . 'includes/triggers/subscription-expired.php';

            // Actions
            require_once AUTOMATORWP_PAID_MEMBERSHIPS_PRO_DIR . 'includes/actions/add-user-membership.php';

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

        automatorwp_register_integration( 'paid_memberships_pro', array(
            'label' => 'Paid Memberships Pro',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/paid-memberships-pro.svg',
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

        if ( ! function_exists( 'pmpro_init' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Paid_Memberships_Pro' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Paid_Memberships_Pro instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Paid_Memberships_Pro The one true AutomatorWP_Integration_Paid_Memberships_Pro
 */
function AutomatorWP_Integration_Paid_Memberships_Pro() {
    return AutomatorWP_Integration_Paid_Memberships_Pro::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Paid_Memberships_Pro' );
