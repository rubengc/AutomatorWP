<?php
/**
 * Plugin Name:           AutomatorWP - Mailchimp integration
 * Plugin URI:            https://automatorwp.com/add-ons/mailchimp/
 * Description:           Connect AutomatorWP with Mailchimp.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-mailchimp-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.9
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Mailchimp
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Mailchimp {

    /**
     * @var         AutomatorWP_Integration_Mailchimp $instance The one true AutomatorWP_Integration_Mailchimp
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Mailchimp self::$instance The one true AutomatorWP_Integration_Mailchimp
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Mailchimp();

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
        define( 'AUTOMATORWP_MAILCHIMP_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_MAILCHIMP_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_MAILCHIMP_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_MAILCHIMP_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_MAILCHIMP_DIR . 'includes/admin.php';
            require_once AUTOMATORWP_MAILCHIMP_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_MAILCHIMP_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_MAILCHIMP_DIR . 'includes/scripts.php';

            // Actions
            require_once AUTOMATORWP_MAILCHIMP_DIR . 'includes/actions/user-add-tag.php';
            require_once AUTOMATORWP_MAILCHIMP_DIR . 'includes/actions/user-subscribe.php';
            require_once AUTOMATORWP_MAILCHIMP_DIR . 'includes/actions/user-add-note.php';

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

        automatorwp_register_integration( 'mailchimp', array(
            'label' => 'Mailchimp',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/mailchimp.svg',
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

        if ( ! class_exists( 'AutomatorWP_Mailchimp' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Mailchimp instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Mailchimp The one true AutomatorWP_Integration_Mailchimp
 */
function AutomatorWP_Integration_Mailchimp() {
    return AutomatorWP_Integration_Mailchimp::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Mailchimp' );
