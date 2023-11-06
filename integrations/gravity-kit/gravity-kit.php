<?php
/**
 * Plugin Name:           AutomatorWP - Gravity Kit
 * Plugin URI:            https://automatorwp.com/add-ons/gravity-kit/
 * Description:           Connect AutomatorWP with Gravity Kit.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-gravity-kit
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Gravity_Kit
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Gravity_Kit {

    /**
     * @var         AutomatorWP_Integration_Gravity_Kit $instance The one true AutomatorWP_Integration_Gravity_Kit
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Gravity_Kit self::$instance The one true AutomatorWP_Integration_Gravity_Kit
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Gravity_Kit();

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
        define( 'AUTOMATORWP_GRAVITY_KIT_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_GRAVITY_KIT_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_GRAVITY_KIT_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_GRAVITY_KIT_URL', plugin_dir_url( __FILE__ ) );
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
             require_once AUTOMATORWP_GRAVITY_KIT_DIR . 'includes/ajax-functions.php';
             require_once AUTOMATORWP_GRAVITY_KIT_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_GRAVITY_KIT_DIR . 'includes/triggers/user-entry-approved.php';

            // Anonymous Triggers
            require_once AUTOMATORWP_GRAVITY_KIT_DIR . 'includes/triggers/anonymous-entry-approved.php';

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

        automatorwp_register_integration( 'gravity_kit', array(
            'label' => 'Gravity Kit',
            'icon'  => AUTOMATORWP_GRAVITY_KIT_URL . 'assets/gravity-kit.svg',
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

        if ( ! class_exists( 'GravityView_Plugin' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Gravity_Kit' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Gravity_Kit instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Gravity_Kit The one true AutomatorWP_Integration_Gravity_Kit
 */
function AutomatorWP_Integration_Gravity_Kit() {
    return AutomatorWP_Integration_Gravity_Kit::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Gravity_Kit' );
