<?php
/**
 * Plugin Name:           AutomatorWP - PeepSo integration
 * Plugin URI:            https://automatorwp.com/add-ons/peepso/
 * Description:           Connect AutomatorWP with PeepSo.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-peepso-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\PeepSo
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_PeepSo {

    /**
     * @var         AutomatorWP_Integration_PeepSo $instance The one true AutomatorWP_Integration_PeepSo
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_PeepSo self::$instance The one true AutomatorWP_Integration_PeepSo
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_PeepSo();

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
        define( 'AUTOMATORWP_PEEPSO_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_PEEPSO_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_PEEPSO_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_PEEPSO_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_PEEPSO_DIR . 'includes/triggers/change-avatar.php';
            require_once AUTOMATORWP_PEEPSO_DIR . 'includes/triggers/write-activity-post.php';
            // PeepSo Groups
            if( class_exists( 'PeepSoGroupsPlugin' ) ) {
                require_once AUTOMATORWP_PEEPSO_DIR . 'includes/triggers/create-group.php';
            }

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

        automatorwp_register_integration( 'peepso', array(
            'label' => 'PeepSo',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/peepso.svg',
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

        if ( ! class_exists( 'PeepSo' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_PeepSo' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_PeepSo instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_PeepSo The one true AutomatorWP_Integration_PeepSo
 */
function AutomatorWP_Integration_PeepSo() {
    return AutomatorWP_Integration_PeepSo::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_PeepSo' );
