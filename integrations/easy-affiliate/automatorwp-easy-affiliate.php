<?php
/**
 * Plugin Name:           AutomatorWP - Easy Affiliate
 * Plugin URI:            https://automatorwp.com/add-ons/easy-affiliate/
 * Description:           Connect AutomatorWP with Easy Affiliate.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-easy-affiliate
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.1
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Easy_Affiliate
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Easy_Affiliate {

    /**
     * @var         AutomatorWP_Integration_Easy_Affiliate $instance The one true AutomatorWP_Integration_Easy_Affiliate
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Easy_Affiliate self::$instance The one true AutomatorWP_Integration_Easy_Affiliate
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Easy_Affiliate();
            
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
        define( 'AUTOMATORWP_EASY_AFFILIATE_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_EASY_AFFILIATE_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_EASY_AFFILIATE_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_EASY_AFFILIATE_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_EASY_AFFILIATE_DIR . 'includes/triggers/become-affiliate.php';
            require_once AUTOMATORWP_EASY_AFFILIATE_DIR . 'includes/triggers/earn-referral.php';

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

        automatorwp_register_integration( 'easy_affiliate', array(
            'label' => 'Easy Affiliate',
            'icon'  => AUTOMATORWP_EASY_AFFILIATE_URL . 'assets/easy-affiliate.svg',
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

        if ( ! defined( 'ESAF_PLUGIN_SLUG' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Easy_Affiliate' ) ) {
            return false;
        }

        return true;

    }    

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Easy_Affiliate instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Easy_Affiliate The one true AutomatorWP_Integration_Easy_Affiliate
 */
function AutomatorWP_Integration_Easy_Affiliate() {
    return AutomatorWP_Integration_Easy_Affiliate::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Easy_Affiliate' );
