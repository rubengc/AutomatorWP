<?php
/**
 * Plugin Name:           AutomatorWP - WPCode
 * Plugin URI:            https://automatorwp.com/add-ons/wpcode/
 * Description:           Connect AutomatorWP with WPCode.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wpcode
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WPCode
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_WPCode {

    /**
     * @var         AutomatorWP_Integration_WPCode $instance The one true AutomatorWP_Integration_WPCode
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_WPCode self::$instance The one true AutomatorWP_Integration_WPCode
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_WPCode();

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
        define( 'AUTOMATORWP_WPCODE_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_WPCODE_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WPCODE_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WPCODE_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_WPCODE_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_WPCODE_DIR . 'includes/functions.php';

            // Actions
            require_once AUTOMATORWP_WPCODE_DIR . 'includes/actions/activate-snippet.php';
            require_once AUTOMATORWP_WPCODE_DIR . 'includes/actions/deactivate-snippet.php';

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

        automatorwp_register_integration( 'wpcode', array(
            'label' => 'WPCode',
            'icon'  => AUTOMATORWP_WPCODE_URL . 'assets/wpcode.svg',
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

        if ( ! class_exists( 'WPCode' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_WPCode' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_WPCode instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_WPCode The one true AutomatorWP_Integration_WPCode
 */
function AutomatorWP_Integration_WPCode() {
    return AutomatorWP_Integration_WPCode::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_WPCode' );
