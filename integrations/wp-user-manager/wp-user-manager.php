<?php
/**
 * Plugin Name:           AutomatorWP - WP User Manager integration
 * Plugin URI:            https://automatorwp.com/add-ons/wp-user-manager/
 * Description:           Connect AutomatorWP with WP User Manager.
 * Version:               1.0.1
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wp-user-manager-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WP_User_Manager
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_WP_User_Manager {

    /**
     * @var         AutomatorWP_Integration_WP_User_Manager $instance The one true AutomatorWP_Integration_WP_User_Manager
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_WP_User_Manager self::$instance The one true AutomatorWP_Integration_WP_User_Manager
     */
    public static function instance() {

        if( !self::$instance ) {

            self::$instance = new AutomatorWP_Integration_WP_User_Manager();

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
        define( 'AUTOMATORWP_WP_USER_MANAGER_VER', '1.0.1' );

        // Plugin file
        define( 'AUTOMATORWP_WP_USER_MANAGER_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WP_USER_MANAGER_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WP_USER_MANAGER_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_WP_USER_MANAGER_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_WP_USER_MANAGER_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_WP_USER_MANAGER_DIR . 'includes/triggers/user-login.php';
            require_once AUTOMATORWP_WP_USER_MANAGER_DIR . 'includes/triggers/user-register.php';
            require_once AUTOMATORWP_WP_USER_MANAGER_DIR . 'includes/triggers/change-profile-photo.php';
            require_once AUTOMATORWP_WP_USER_MANAGER_DIR . 'includes/triggers/change-profile-cover.php';
            require_once AUTOMATORWP_WP_USER_MANAGER_DIR . 'includes/triggers/change-profile-description.php';
            require_once AUTOMATORWP_WP_USER_MANAGER_DIR . 'includes/triggers/join-group.php';

            // Actions
            require_once AUTOMATORWP_WP_USER_MANAGER_DIR . 'includes/actions/add-user-group.php';

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

        automatorwp_register_integration( 'wp_user_manager', array(
            'label' => 'WP User Manager',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/wp-user-manager.svg',
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

        if ( ! class_exists( 'WP_User_Manager' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_WP_User_Manager' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_WP_User_Manager instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_WP_User_Manager The one true AutomatorWP_Integration_WP_User_Manager
 */
function AutomatorWP_Integration_WP_User_Manager() {
    return AutomatorWP_Integration_WP_User_Manager::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_WP_User_Manager' );
