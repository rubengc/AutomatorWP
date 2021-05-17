<?php
/**
 * Plugin Name:           AutomatorWP - WP Ulike integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-wp-ulike-integration/
 * Description:           Connect AutomatorWP with WP Ulike.
 * Version:               1.0.1
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wp-ulike-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WP_Ulike
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_WP_Ulike {

    /**
     * @var         AutomatorWP_Integration_WP_Ulike $instance The one true AutomatorWP_Integration_WP_Ulike
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_WP_Ulike self::$instance The one true AutomatorWP_Integration_WP_Ulike
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_WP_Ulike();
            
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
        define( 'AUTOMATORWP_WP_ULIKE_VER', '1.0.1' );

        // Plugin file
        define( 'AUTOMATORWP_WP_ULIKE_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WP_ULIKE_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WP_ULIKE_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_WP_ULIKE_DIR . 'includes/triggers/like-post.php';
            require_once AUTOMATORWP_WP_ULIKE_DIR . 'includes/triggers/like-comment.php';

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

        automatorwp_register_integration( 'wp_ulike', array(
            'label' => 'WP Ulike',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/wp-ulike.svg',
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

        if ( ! class_exists( 'WpUlikeInit' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_WP_Ulike' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_WP_Ulike instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_WP_Ulike The one true AutomatorWP_Integration_WP_Ulike
 */
function AutomatorWP_Integration_WP_Ulike() {
    return AutomatorWP_Integration_WP_Ulike::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_WP_Ulike' );
