<?php
/**
 * Plugin Name:           AutomatorWP - GamiPress integration
 * Plugin URI:            https://automatorwp.com/add-ons/gamipress/
 * Description:           Connect AutomatorWP with GamiPress.
 * Version:               1.0.8
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-gamipress-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\GamiPress
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_GamiPress {

    /**
     * @var         AutomatorWP_Integration_GamiPress $instance The one true AutomatorWP_Integration_GamiPress
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_GamiPress self::$instance The one true AutomatorWP_Integration_GamiPress
     */
    public static function instance() {

        if( ! self::$instance ) {

            self::$instance = new AutomatorWP_Integration_GamiPress();

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
        define( 'AUTOMATORWP_GAMIPRESS_VER', '1.0.8' );

        // Plugin file
        define( 'AUTOMATORWP_GAMIPRESS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_GAMIPRESS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_GAMIPRESS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_GAMIPRESS_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_GAMIPRESS_DIR . 'includes/triggers/earn-points.php';
            require_once AUTOMATORWP_GAMIPRESS_DIR . 'includes/triggers/earn-achievement.php';
            require_once AUTOMATORWP_GAMIPRESS_DIR . 'includes/triggers/reach-rank.php';

            // Actions
            require_once AUTOMATORWP_GAMIPRESS_DIR . 'includes/actions/user-points.php';
            require_once AUTOMATORWP_GAMIPRESS_DIR . 'includes/actions/user-achievement.php';
            require_once AUTOMATORWP_GAMIPRESS_DIR . 'includes/actions/user-rank.php';

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

        automatorwp_register_integration( 'gamipress', array(
            'label' => 'GamiPress',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/gamipress.svg',
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

        if ( ! class_exists( 'GamiPress' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_GamiPress' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_GamiPress instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_GamiPress The one true AutomatorWP_Integration_GamiPress
 */
function AutomatorWP_Integration_GamiPress() {
    return AutomatorWP_Integration_GamiPress::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_GamiPress' );
