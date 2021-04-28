<?php
/**
 * Plugin Name:           AutomatorWP - BuddyPress integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-buddypress-integration/
 * Description:           Connect AutomatorWP with BuddyPress.
 * Version:               1.1.1
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-buddypress-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\BuddyPress
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_BuddyPress_Integration {

    /**
     * @var         AutomatorWP_BuddyPress_Integration $instance The one true AutomatorWP_BuddyPress_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_BuddyPress_Integration self::$instance The one true AutomatorWP_BuddyPress_Integration
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_BuddyPress_Integration();
            
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
        define( 'AUTOMATORWP_BUDDYPRESS_VER', '1.1.1' );

        // Plugin file
        define( 'AUTOMATORWP_BUDDYPRESS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_BUDDYPRESS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_BUDDYPRESS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_BUDDYPRESS_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_BUDDYPRESS_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_BUDDYPRESS_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_BUDDYPRESS_DIR . 'includes/triggers/account-activation.php';
            // BuddyPress Profile
            require_once AUTOMATORWP_BUDDYPRESS_DIR . 'includes/triggers/update-avatar.php';
            require_once AUTOMATORWP_BUDDYPRESS_DIR . 'includes/triggers/update-cover.php';
            require_once AUTOMATORWP_BUDDYPRESS_DIR . 'includes/triggers/update-profile.php';
            // BuddyPress Activity
            require_once AUTOMATORWP_BUDDYPRESS_DIR . 'includes/triggers/publish-activity.php';
            // BuddyPress Groups
            require_once AUTOMATORWP_BUDDYPRESS_DIR . 'includes/triggers/join-group.php';

            // Actions
            // BuddyPress Profile
            require_once AUTOMATORWP_BUDDYPRESS_DIR . 'includes/actions/set-user-member-type.php';
            // BuddyPress Activity
            require_once AUTOMATORWP_BUDDYPRESS_DIR . 'includes/actions/add-user-activity.php';
            // BuddyPress Groups
            require_once AUTOMATORWP_BUDDYPRESS_DIR . 'includes/actions/add-user-group.php';

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

        automatorwp_register_integration( 'buddypress', array(
            'label' => 'BuddyPress',
            'icon'  => AUTOMATORWP_BUDDYPRESS_URL . 'assets/buddypress.svg',
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

        if ( ! class_exists( 'BuddyPress' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_BuddyPress' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_BuddyPress_Integration instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_BuddyPress_Integration The one true AutomatorWP_BuddyPress_Integration
 */
function AutomatorWP_BuddyPress_Integration() {
    return AutomatorWP_BuddyPress_Integration::instance();
}
add_action( 'plugins_loaded', 'AutomatorWP_BuddyPress_Integration' );
