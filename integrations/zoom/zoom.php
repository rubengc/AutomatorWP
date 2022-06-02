<?php
/**
 * Plugin Name:           AutomatorWP - Zoom integration
 * Plugin URI:            https://automatorwp.com/add-ons/zoom/
 * Description:           Connect AutomatorWP with Zoom.
 * Version:               1.0.3
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-zoom
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Zoom
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Zoom {

    /**
     * @var         AutomatorWP_Integration_Zoom $instance The one true AutomatorWP_Integration_Zoom
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Zoom self::$instance The one true AutomatorWP_Integration_Zoom
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Zoom();

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
        define( 'AUTOMATORWP_ZOOM_VER', '1.0.3' );

        // Plugin file
        define( 'AUTOMATORWP_ZOOM_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_ZOOM_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_ZOOM_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_ZOOM_DIR . 'includes/admin.php';
            require_once AUTOMATORWP_ZOOM_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_ZOOM_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_ZOOM_DIR . 'includes/scripts.php';

            // Actions
            require_once AUTOMATORWP_ZOOM_DIR . 'includes/actions/add-user-meeting.php';

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

        automatorwp_register_integration( 'zoom', array(
            'label' => 'Zoom',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/zoom.svg',
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

        if ( ! class_exists( 'AutomatorWP_Zoom' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Zoom instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Zoom The one true AutomatorWP_Integration_Zoom
 */
function AutomatorWP_Integration_Zoom() {
    return AutomatorWP_Integration_Zoom::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Zoom' );
