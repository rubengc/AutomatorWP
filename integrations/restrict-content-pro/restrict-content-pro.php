<?php
/**
 * Plugin Name:           AutomatorWP - Restrict Content Pro integration
 * Plugin URI:            https://automatorwp.com/add-ons/restrict-content-pro/
 * Description:           Connect AutomatorWP with Restrict Content Pro.
 * Version:               1.1.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-restrict-content-pro-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.3
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Restrict_Content_Pro
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Restrict_Content_Pro {

    /**
     * @var         AutomatorWP_Integration_Restrict_Content_Pro $instance The one true AutomatorWP_Integration_Restrict_Content_Pro
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Restrict_Content_Pro self::$instance The one true AutomatorWP_Integration_Restrict_Content_Pro
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Restrict_Content_Pro();
            
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
        define( 'AUTOMATORWP_RESTRICT_CONTENT_PRO_VER', '1.1.0' );

        // Plugin file
        define( 'AUTOMATORWP_RESTRICT_CONTENT_PRO_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_RESTRICT_CONTENT_PRO_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_RESTRICT_CONTENT_PRO_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_RESTRICT_CONTENT_PRO_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_RESTRICT_CONTENT_PRO_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_RESTRICT_CONTENT_PRO_DIR . 'includes/triggers/free-membership.php';
            require_once AUTOMATORWP_RESTRICT_CONTENT_PRO_DIR . 'includes/triggers/purchase-membership.php';
            require_once AUTOMATORWP_RESTRICT_CONTENT_PRO_DIR . 'includes/triggers/cancel-membership.php';

            // Actions
            require_once AUTOMATORWP_RESTRICT_CONTENT_PRO_DIR . 'includes/actions/add-membership.php';

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

        automatorwp_register_integration( 'restrict_content_pro', array(
            'label' => 'Restrict Content Pro',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/restrict-content-pro.svg',
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

        if ( ! function_exists( 'rcp_get_membership_levels' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Restrict_Content_Pro' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Restrict_Content_Pro instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Restrict_Content_Pro The one true AutomatorWP_Integration_Restrict_Content_Pro
 */
function AutomatorWP_Integration_Restrict_Content_Pro() {
    return AutomatorWP_Integration_Restrict_Content_Pro::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Restrict_Content_Pro' );
