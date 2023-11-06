<?php
/**
 * Plugin Name:           AutomatorWP - Charitable
 * Plugin URI:            https://automatorwp.com/add-ons/charitable/
 * Description:           Connect AutomatorWP with Charitable.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-charitable
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Charitable
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Charitable {

    /**
     * @var         AutomatorWP_Integration_Charitable $instance The one true AutomatorWP_Integration_Charitable
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Charitable self::$instance The one true AutomatorWP_Integration_Charitable
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Charitable();
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
        define( 'AUTOMATORWP_CHARITABLE_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_CHARITABLE_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_CHARITABLE_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_CHARITABLE_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_CHARITABLE_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_CHARITABLE_DIR . 'includes/triggers/user-donation.php';

            // Anonymous triggers
            require_once AUTOMATORWP_CHARITABLE_DIR . 'includes/triggers/anonymous-donation.php';

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

        automatorwp_register_integration( 'charitable', array(
            'label' => 'Charitable',
            'icon'  => AUTOMATORWP_CHARITABLE_URL . 'assets/charitable.svg',
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

        if ( ! class_exists( 'Charitable' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Charitable' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Charitable instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Charitable The one true AutomatorWP_Integration_Charitable
 */
function AutomatorWP_Integration_Charitable() {
    return AutomatorWP_Integration_Charitable::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Charitable' );
