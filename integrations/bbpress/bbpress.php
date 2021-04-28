<?php
/**
 * Plugin Name:           AutomatorWP - bbPress integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-bbpress-integration/
 * Description:           Connect AutomatorWP with bbPress.
 * Version:               1.0.2
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-bbpress-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\bbPress
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_bbPress_Integration {

    /**
     * @var         AutomatorWP_bbPress_Integration $instance The one true AutomatorWP_bbPress_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_bbPress_Integration self::$instance The one true AutomatorWP_bbPress_Integration
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_bbPress_Integration();
            
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
        define( 'AUTOMATORWP_BBPRESS_VER', '1.0.2' );

        // Plugin file
        define( 'AUTOMATORWP_BBPRESS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_BBPRESS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_BBPRESS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_BBPRESS_DIR . 'includes/triggers/create-forum.php';
            require_once AUTOMATORWP_BBPRESS_DIR . 'includes/triggers/create-topic.php';


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

        automatorwp_register_integration( 'bbpress', array(
            'label' => 'bbPress',
            'icon'  => AUTOMATORWP_BBPRESS_URL . 'assets/bbpress.svg',
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

        if ( ! class_exists( 'bbPress' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_bbPress' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_bbPress_Integration instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_bbPress_Integration The one true AutomatorWP_bbPress_Integration
 */
function AutomatorWP_bbPress_Integration() {
    return AutomatorWP_bbPress_Integration::instance();
}
add_action( 'plugins_loaded', 'AutomatorWP_bbPress_Integration' );
