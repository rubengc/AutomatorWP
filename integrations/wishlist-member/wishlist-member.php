<?php
/**
 * Plugin Name:           AutomatorWP - WishList Member integration
 * Plugin URI:            https://automatorwp.com/add-ons/wishlist-member/
 * Description:           Connect AutomatorWP with WishList Member.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wishlist-member-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WishList_Member
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_WishList_Member {

    /**
     * @var         AutomatorWP_Integration_WishList_Member $instance The one true AutomatorWP_Integration_WishList_Member
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_WishList_Member self::$instance The one true AutomatorWP_Integration_WishList_Member
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_WishList_Member();
            
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
        define( 'AUTOMATORWP_WISHLIST_MEMBER_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_WISHLIST_MEMBER_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WISHLIST_MEMBER_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WISHLIST_MEMBER_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_WISHLIST_MEMBER_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_WISHLIST_MEMBER_DIR . 'includes/triggers/add-level.php';
            require_once AUTOMATORWP_WISHLIST_MEMBER_DIR . 'includes/triggers/remove-level.php';

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

        automatorwp_register_integration( 'wishlist_member', array(
            'label' => 'WishList Member',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/wishlist-member.svg',
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

        if ( ! class_exists( 'WishListMember' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_WishList_Member' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_WishList_Member instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_WishList_Member The one true AutomatorWP_Integration_WishList_Member
 */
function AutomatorWP_Integration_WishList_Member() {
    return AutomatorWP_Integration_WishList_Member::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_WishList_Member' );
