<?php
/**
 * Plugin Name:           AutomatorWP - WP All Import
 * Plugin URI:            https://automatorwp.com/add-ons/wp-all-import/
 * Description:           Connect AutomatorWP with WP All Import.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wp-all-import
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.3
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WP_All_Import
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_WP_All_Import {

    /**
     * @var         AutomatorWP_Integration_WP_All_Import $instance The one true AutomatorWP_Integration_WP_All_Import
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_WP_All_Import self::$instance The one true AutomatorWP_Integration_WP_All_Import
     */
    public static function instance() {
        if( ! self::$instance ) {

            self::$instance = new AutomatorWP_Integration_WP_All_Import();

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
        define( 'AUTOMATORWP_WP_ALL_IMPORT_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_WP_ALL_IMPORT_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WP_ALL_IMPORT_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WP_ALL_IMPORT_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_WP_ALL_IMPORT_DIR . 'includes/functions.php'; 
            require_once AUTOMATORWP_WP_ALL_IMPORT_DIR . 'includes/tags.php'; 

            // Triggers
            require_once AUTOMATORWP_WP_ALL_IMPORT_DIR . 'includes/triggers/import-success.php';
            require_once AUTOMATORWP_WP_ALL_IMPORT_DIR . 'includes/triggers/import-post-type.php';
            
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

        automatorwp_register_integration( 'wp_all_import', array(
            'label' => 'WP All Import',
            'icon'  => AUTOMATORWP_WP_ALL_IMPORT_URL . 'assets/wp-all-import.svg',
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

        if ( ! class_exists( 'PMXI_Plugin' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_WP_All_Import' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_WP_All_Import instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_WP_All_Import The one true AutomatorWP_Integration_WP_All_Import
 */
function AutomatorWP_Integration_WP_All_Import() {
    return AutomatorWP_Integration_WP_All_Import::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_WP_All_Import' );
