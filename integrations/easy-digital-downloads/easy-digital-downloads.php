<?php
/**
 * Plugin Name:           AutomatorWP - Easy Digital Downloads integration
 * Plugin URI:            https://automatorwp.com/add-ons/easy-digital-downloads/
 * Description:           Connect AutomatorWP with Easy Digital Downloads.
 * Version:               1.0.1
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-easy-digital-downloads-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Easy_Digital_Downloads_Integration
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Easy_Digital_Downloads {

    /**
     * @var         AutomatorWP_Integration_Easy_Digital_Downloads $instance The one true AutomatorWP_Integration_Easy_Digital_Downloads
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Easy_Digital_Downloads self::$instance The one true AutomatorWP_Integration_Easy_Digital_Downloads
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Easy_Digital_Downloads();
            
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
        define( 'AUTOMATORWP_EASY_DIGITAL_DOWNLOADS_VER', '1.0.1' );

        // Plugin file
        define( 'AUTOMATORWP_EASY_DIGITAL_DOWNLOADS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_EASY_DIGITAL_DOWNLOADS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_EASY_DIGITAL_DOWNLOADS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_EASY_DIGITAL_DOWNLOADS_DIR . 'includes/triggers/view-download.php';
            require_once AUTOMATORWP_EASY_DIGITAL_DOWNLOADS_DIR . 'includes/triggers/purchase-download.php';

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

        automatorwp_register_integration( 'easy_digital_downloads', array(
            'label' => 'Easy Digital Downloads',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/easy-digital-downloads.svg',
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

        if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Easy_Digital_Downloads' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Easy_Digital_Downloads instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Easy_Digital_Downloads The one true AutomatorWP_Integration_Easy_Digital_Downloads
 */
function AutomatorWP_Integration_Easy_Digital_Downloads() {
    return AutomatorWP_Integration_Easy_Digital_Downloads::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Easy_Digital_Downloads' );
