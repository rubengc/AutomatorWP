<?php
/**
 * Plugin Name:           AutomatorWP - Ultimate Member integration
 * Plugin URI:            https://automatorwp.com/add-ons/ultimate-member/
 * Description:           Connect AutomatorWP with Ultimate Member.
 * Version:               1.0.1
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-ultimate-member-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Ultimate_Member
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Ultimate_Member {

    /**
     * @var         AutomatorWP_Integration_Ultimate_Member $instance The one true AutomatorWP_Integration_Ultimate_Member
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Ultimate_Member self::$instance The one true AutomatorWP_Integration_Ultimate_Member
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Ultimate_Member();
            
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
        define( 'AUTOMATORWP_ULTIMATE_MEMBER_VER', '1.0.1' );

        // Plugin file
        define( 'AUTOMATORWP_ULTIMATE_MEMBER_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_ULTIMATE_MEMBER_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_ULTIMATE_MEMBER_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_ULTIMATE_MEMBER_DIR . 'includes/triggers/user-approved.php';
            require_once AUTOMATORWP_ULTIMATE_MEMBER_DIR . 'includes/triggers/user-inactive.php';

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

        automatorwp_register_integration( 'ultimate_member', array(
            'label' => 'Ultimate Member',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/ultimate-member.svg',
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

        if ( ! class_exists( 'UM' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Ultimate_Member' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Ultimate_Member instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Ultimate_Member The one true AutomatorWP_Integration_Ultimate_Member
 */
function AutomatorWP_Integration_Ultimate_Member() {
    return AutomatorWP_Integration_Ultimate_Member::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Ultimate_Member' );
