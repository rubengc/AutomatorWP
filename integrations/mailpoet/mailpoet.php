<?php
/**
 * Plugin Name:           AutomatorWP - MailPoet integration
 * Plugin URI:            https://automatorwp.com/add-ons/mailpoet/
 * Description:           Connect AutomatorWP with MailPoet.
 * Version:               1.0.1
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-mailpoet-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\MailPoet
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_MailPoet {

    /**
     * @var         AutomatorWP_Integration_MailPoet $instance The one true AutomatorWP_Integration_MailPoet
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_MailPoet self::$instance The one true AutomatorWP_Integration_MailPoet
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_MailPoet();

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
        define( 'AUTOMATORWP_MAILPOET_VER', '1.0.1' );

        // Plugin file
        define( 'AUTOMATORWP_MAILPOET_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_MAILPOET_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_MAILPOET_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_MAILPOET_DIR . 'includes/functions.php';

            // Actions
            require_once AUTOMATORWP_MAILPOET_DIR . 'includes/actions/add-user-to-list.php';
            require_once AUTOMATORWP_MAILPOET_DIR . 'includes/actions/add-subscriber-to-list.php';

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

        automatorwp_register_integration( 'mailpoet', array(
            'label' => 'MailPoet',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/mailpoet.svg',
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

        if ( ! defined( 'MAILPOET_VERSION' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_MailPoet' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_MailPoet instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_MailPoet The one true AutomatorWP_Integration_MailPoet
 */
function AutomatorWP_Integration_MailPoet() {
    return AutomatorWP_Integration_MailPoet::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_MailPoet' );
