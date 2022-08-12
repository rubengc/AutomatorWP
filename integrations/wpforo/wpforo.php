<?php
/**
 * Plugin Name:           AutomatorWP - wpForo integration
 * Plugin URI:            https://automatorwp.com/add-ons/wpforo/
 * Description:           Connect AutomatorWP with wpForo.
 * Version:               1.0.1
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wpforo-integration-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.0
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\wpForo
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_wpForo {

    /**
     * @var         AutomatorWP_Integration_wpForo $instance The one true AutomatorWP_Integration_wpForo
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_wpForo self::$instance The one true AutomatorWP_Integration_wpForo
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_wpForo();

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
        define( 'AUTOMATORWP_WPFORO_VER', '1.0.1' );

        // Plugin file
        define( 'AUTOMATORWP_WPFORO_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WPFORO_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WPFORO_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_WPFORO_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_WPFORO_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_WPFORO_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_WPFORO_DIR . 'includes/triggers/create-reply.php';
            require_once AUTOMATORWP_WPFORO_DIR . 'includes/triggers/create-topic.php';

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

        automatorwp_register_integration( 'wpforo', array(
            'label' => 'wpForo',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/wpforo.svg',
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

        if ( ! class_exists( 'wpforo\\wpforo' ) ) { // > 2.0.0
            if ( ! class_exists ( 'wpForo' ) ) { // < 2.0.0
                return false;
            }
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

        if ( ! class_exists( 'AutomatorWP_wpForo' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_wpForo instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_wpForo The one true AutomatorWP_Integration_wpForo
 */
function AutomatorWP_Integration_wpForo() {
    return AutomatorWP_Integration_wpForo::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_wpForo' );
