<?php
/**
 * Plugin Name:           AutomatorWP - Jetpack CRM integration
 * Plugin URI:            https://automatorwp.com/add-ons/jetpack-crm/
 * Description:           Connect AutomatorWP with Jetpack CRM.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-jetpack-crm-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.0
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Jetpack_CRM
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Jetpack_CRM {

    /**
     * @var         AutomatorWP_Integration_Jetpack_CRM $instance The one true AutomatorWP_Integration_Jetpack_CRM
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Jetpack_CRM self::$instance The one true AutomatorWP_Integration_Jetpack_CRM
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Jetpack_CRM();
            
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
        define( 'AUTOMATORWP_JETPACK_CRM_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_JETPACK_CRM_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_JETPACK_CRM_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_JETPACK_CRM_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_JETPACK_CRM_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_JETPACK_CRM_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_JETPACK_CRM_DIR . 'includes/triggers/company-added.php';
            require_once AUTOMATORWP_JETPACK_CRM_DIR . 'includes/triggers/contact-added.php';
            require_once AUTOMATORWP_JETPACK_CRM_DIR . 'includes/triggers/contact-tag-added.php';
            require_once AUTOMATORWP_JETPACK_CRM_DIR . 'includes/triggers/company-tag-added.php';

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

        automatorwp_register_integration( 'jetpack_crm', array(
            'label' => 'Jetpack CRM',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/jetpack-crm.svg',
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

        if ( ! class_exists( 'ZeroBSCRM' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Jetpack_CRM' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Jetpack_CRM instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Jetpack_CRM The one true AutomatorWP_Integration_Jetpack_CRM
 */
function AutomatorWP_Integration_Jetpack_CRM() {
    return AutomatorWP_Integration_Jetpack_CRM::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Jetpack_CRM' );
