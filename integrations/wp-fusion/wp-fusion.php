<?php
/**
 * Plugin Name:           AutomatorWP - WP Fusion integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-wp-fusion-integration/
 * Description:           Connect AutomatorWP with WP Fusion.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wp-fusion-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WP_Fusion
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_WP_Fusion_Integration {

    /**
     * @var         AutomatorWP_WP_Fusion_Integration $instance The one true AutomatorWP_WP_Fusion_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_WP_Fusion_Integration self::$instance The one true AutomatorWP_WP_Fusion_Integration
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_WP_Fusion_Integration();
            
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
        define( 'AUTOMATORWP_WP_FUSION_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_WP_FUSION_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WP_FUSION_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WP_FUSION_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_WP_FUSION_DIR . 'includes/triggers/add-tag.php';

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

        automatorwp_register_integration( 'wp_fusion', array(
            'label' => 'WP Fusion',
            'icon'  => AUTOMATORWP_WP_FUSION_URL . 'assets/wp-fusion.svg',
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

        if ( ! function_exists( 'wp_fusion' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_WP_Fusion' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_WP_Fusion_Integration instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_WP_Fusion_Integration The one true AutomatorWP_WP_Fusion_Integration
 */
function AutomatorWP_WP_Fusion_Integration() {
    return AutomatorWP_WP_Fusion_Integration::instance();
}
add_action( 'plugins_loaded', 'AutomatorWP_WP_Fusion_Integration' );
