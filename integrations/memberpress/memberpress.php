<?php
/**
 * Plugin Name:           AutomatorWP - MemberPress integration
 * Plugin URI:            https://automatorwp.com/add-ons/memberpress/
 * Description:           Connect AutomatorWP with MemberPress.
 * Version:               1.0.2
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-memberpress-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\MemberPress
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_MemberPress {

    /**
     * @var         AutomatorWP_Integration_MemberPress $instance The one true AutomatorWP_Integration_MemberPress
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_MemberPress self::$instance The one true AutomatorWP_Integration_MemberPress
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_MemberPress();

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
        define( 'AUTOMATORWP_MEMBERPRESS_VER', '1.0.2' );

        // Plugin file
        define( 'AUTOMATORWP_MEMBERPRESS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_MEMBERPRESS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_MEMBERPRESS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_MEMBERPRESS_DIR . 'includes/triggers/purchase-membership.php';

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

        automatorwp_register_integration( 'memberpress', array(
            'label' => 'MemberPress',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/memberpress.svg',
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

        if ( ! class_exists( 'MeprCtrlFactory' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_MemberPress' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_MemberPress instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_MemberPress The one true AutomatorWP_Integration_MemberPress
 */
function AutomatorWP_Integration_MemberPress() {
    return AutomatorWP_Integration_MemberPress::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_MemberPress' );
