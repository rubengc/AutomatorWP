<?php
/**
 * Plugin Name:           AutomatorWP - BuddyBoss integration
 * Plugin URI:            https://automatorwp.com/add-ons/buddyboss/
 * Description:           Connect AutomatorWP with BuddyBoss.
 * Version:               1.1.2
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-buddyboss-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\BuddyBoss
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_BuddyBoss {

    /**
     * @var         AutomatorWP_Integration_BuddyBoss $instance The one true AutomatorWP_Integration_BuddyBoss
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_BuddyBoss self::$instance The one true AutomatorWP_Integration_BuddyBoss
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_BuddyBoss();
            
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
        define( 'AUTOMATORWP_BUDDYBOSS_VER', '1.1.2' );

        // Plugin file
        define( 'AUTOMATORWP_BUDDYBOSS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_BUDDYBOSS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_BUDDYBOSS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/triggers/account-activation.php';
            // BuddyBoss Follow
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/triggers/start-following.php';
            // BuddyBoss Email Invites
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/triggers/send-email-invite.php';
            // BuddyBoss Profile
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/triggers/update-avatar.php';
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/triggers/update-cover.php';
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/triggers/update-profile.php';
            // BuddyBoss Activity
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/triggers/publish-activity.php';
            // BuddyBoss Groups
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/triggers/join-group.php';
            // BuddyBoss Forums
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/triggers/create-forum.php';
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/triggers/create-topic.php';

            // Actions
            // BuddyBoss Profile
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/actions/set-user-member-type.php';
            // BuddyBoss Activity
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/actions/add-user-activity.php';
            // BuddyBoss Groups
            require_once AUTOMATORWP_BUDDYBOSS_DIR . 'includes/actions/add-user-group.php';

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

        automatorwp_register_integration( 'buddyboss', array(
            'label' => 'BuddyBoss',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/buddyboss.svg',
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

        if ( ! defined( 'BP_PLATFORM_VERSION' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_BuddyBoss' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_BuddyBoss instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_BuddyBoss The one true AutomatorWP_Integration_BuddyBoss
 */
function AutomatorWP_Integration_BuddyBoss() {
    return AutomatorWP_Integration_BuddyBoss::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_BuddyBoss' );
