<?php
/**
 * Plugin Name:           AutomatorWP - Newsletter integration
 * Plugin URI:            https://automatorwp.com/add-ons/newsletter/
 * Description:           Connect AutomatorWP with Newsletter.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-newsletter-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Newsletter_Integration
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Newsletter {

    /**
     * @var         AutomatorWP_Integration_Newsletter $instance The one true AutomatorWP_Integration_Newsletter
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Newsletter self::$instance The one true AutomatorWP_Integration_Newsletter
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Newsletter();
            self::$instance->constants();
            self::$instance->includes();
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
        define( 'AUTOMATORWP_NEWSLETTER_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_NEWSLETTER_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_NEWSLETTER_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_NEWSLETTER_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() && ! $this->pro_installed() ) {

            // Actions
            require_once AUTOMATORWP_NEWSLETTER_DIR . 'includes/triggers/anonymous-subscribe-list.php';

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

        automatorwp_register_integration( 'newsletter', array(
            'label' => 'Newsletter',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/newsletter.svg',
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

        if ( ! defined( 'NEWSLETTER_VERSION' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Newsletter' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Newsletter instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Newsletter The one true AutomatorWP_Integration_Newsletter
 */
function AutomatorWP_Integration_Newsletter() {
    return AutomatorWP_Integration_Newsletter::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Newsletter' );
