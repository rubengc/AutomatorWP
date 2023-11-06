<?php
/**
 * Plugin Name:           AutomatorWP - Thrive Leads
 * Plugin URI:            https://automatorwp.com/add-ons/thrive-leads/
 * Description:           Connect AutomatorWP with Thrive Leads.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-thrive-leads
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Thrive_Leads
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Thrive_Leads {

    /**
     * @var         AutomatorWP_Integration_Thrive_Leads $instance The one true AutomatorWP_Integration_Thrive_Leads
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Thrive_Leads self::$instance The one true AutomatorWP_Integration_Thrive_Leads
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Thrive_Leads();
            
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
        define( 'AUTOMATORWP_THRIVE_LEADS_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_THRIVE_LEADS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_THRIVE_LEADS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_THRIVE_LEADS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_THRIVE_LEADS_DIR . 'includes/triggers/submit-form.php';
            
            // Anonymous Triggers
            require_once AUTOMATORWP_THRIVE_LEADS_DIR . 'includes/triggers/anonymous-submit-form.php';

            // Includes
            require_once AUTOMATORWP_THRIVE_LEADS_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_THRIVE_LEADS_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_THRIVE_LEADS_DIR . 'includes/tags.php';

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

        automatorwp_register_integration( 'thrive_leads', array(
            'label' => 'Thrive Leads',
            'icon'  => AUTOMATORWP_THRIVE_LEADS_URL . 'assets/thrive-leads.svg',
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

        if ( ! defined( 'TVE_LEADS_PATH' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Thrive_Leads' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Thrive_Leads instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Thrive_Leads The one true AutomatorWP_Integration_Thrive_Leads
 */
function AutomatorWP_Integration_Thrive_Leads() {
    return AutomatorWP_Integration_Thrive_Leads::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Thrive_Leads' );
