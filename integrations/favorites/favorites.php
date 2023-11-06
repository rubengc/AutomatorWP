<?php
/**
 * Plugin Name:           AutomatorWP - Favorites integration
 * Plugin URI:            https://automatorwp.com/add-ons/favorites/
 * Description:           Connect AutomatorWP with Favorites.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-favorites-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Favorites_Integration
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Favorites {

    /**
     * @var         AutomatorWP_Integration_Favorites $instance The one true AutomatorWP_Integration_Favorites
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Favorites self::$instance The one true AutomatorWP_Integration_Favorites
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Favorites();
            
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
        define( 'AUTOMATORWP_FAVORITES_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_FAVORITES_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_FAVORITES_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_FAVORITES_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_FAVORITES_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_FAVORITES_DIR . 'includes/triggers/favorite-post.php';
            require_once AUTOMATORWP_FAVORITES_DIR . 'includes/triggers/get-favorite-post.php';

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

        automatorwp_register_integration( 'favorites', array(
            'label' => 'Favorites',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/favorites.svg',
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

        if ( ! class_exists( 'Favorites' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Favorites' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Favorites instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Favorites The one true AutomatorWP_Integration_Favorites
 */
function AutomatorWP_Integration_Favorites() {
    return AutomatorWP_Integration_Favorites::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Favorites' );
