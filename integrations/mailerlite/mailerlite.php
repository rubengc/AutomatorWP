<?php
/**
 * Plugin Name:           AutomatorWP - MailerLite
 * Plugin URI:            https://automatorwp.com/add-ons/mailerlite/
 * Description:           Connect AutomatorWP with MailerLite.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-mailerlite
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\MailerLite
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_MailerLite {

    /**
     * @var         AutomatorWP_Integration_MailerLite $instance The one true AutomatorWP_Integration_MailerLite
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_MailerLite self::$instance The one true AutomatorWP_Integration_MailerLite
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_MailerLite();

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
        define( 'AUTOMATORWP_MAILERLITE_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_MAILERLITE_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_MAILERLITE_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_MAILERLITE_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_MAILERLITE_DIR . 'includes/admin.php';
            require_once AUTOMATORWP_MAILERLITE_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_MAILERLITE_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_MAILERLITE_DIR . 'includes/scripts.php';

            // Actions
            require_once AUTOMATORWP_MAILERLITE_DIR . 'includes/actions/add-user.php';
            require_once AUTOMATORWP_MAILERLITE_DIR . 'includes/actions/add-user-group.php';      

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

        automatorwp_register_integration( 'mailerlite', array(
            'label' => 'MailerLite',
            'icon'  => AUTOMATORWP_MAILERLITE_URL . 'assets/mailerlite.svg',
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

        if ( ! class_exists( 'AutomatorWP_MailerLite' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_MailerLite instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_MailerLite The one true AutomatorWP_Integration_MailerLite
 */
function AutomatorWP_Integration_MailerLite() {
    return AutomatorWP_Integration_MailerLite::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_MailerLite' );