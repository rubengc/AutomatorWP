<?php
/**
 * Plugin Name:           AutomatorWP - ActiveMember360 integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-activemember360-integration/
 * Description:           Connect AutomatorWP with ActiveMember360.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-activemember360-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\ActiveMember360
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_ActiveMember360_Integration {

    /**
     * @var         AutomatorWP_ActiveMember360_Integration $instance The one true AutomatorWP_ActiveMember360_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_ActiveMember360_Integration self::$instance The one true AutomatorWP_ActiveMember360_Integration
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_ActiveMember360_Integration();
            
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
        define( 'AUTOMATORWP_ACTIVEMEMBER360_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_ACTIVEMEMBER360_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_ACTIVEMEMBER360_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_ACTIVEMEMBER360_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_ACTIVEMEMBER360_DIR . 'includes/triggers/contact-tag.php';

            // Actions
            require_once AUTOMATORWP_ACTIVEMEMBER360_DIR . 'includes/actions/user-tag.php';

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

        automatorwp_register_integration( 'activemember360', array(
            'label' => 'ActiveMember360',
            'icon'  => AUTOMATORWP_ACTIVEMEMBER360_URL . 'assets/activemember360.svg',
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

        if ( ! function_exists( 'activemember360' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_ActiveMember360' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_ActiveMember360_Integration instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_ActiveMember360_Integration The one true AutomatorWP_ActiveMember360_Integration
 */
function AutomatorWP_ActiveMember360_Integration() {
    return AutomatorWP_ActiveMember360_Integration::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_ActiveMember360_Integration' );
