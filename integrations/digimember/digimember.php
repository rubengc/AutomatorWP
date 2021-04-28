<?php
/**
 * Plugin Name:           AutomatorWP - Digimember integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-digimember-integration/
 * Description:           Connect AutomatorWP with Digimember.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-digimember-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Digimember
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Digimember_Integration {

    /**
     * @var         AutomatorWP_Digimember_Integration $instance The one true AutomatorWP_Digimember_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Digimember_Integration self::$instance The one true AutomatorWP_Digimember_Integration
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Digimember_Integration();
            
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
        define( 'AUTOMATORWP_DIGIMEMBER_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_DIGIMEMBER_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_DIGIMEMBER_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_DIGIMEMBER_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_DIGIMEMBER_DIR . 'includes/triggers/purchase-product.php';

            // Includes
            require_once AUTOMATORWP_DIGIMEMBER_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_DIGIMEMBER_DIR . 'includes/functions.php';

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

        automatorwp_register_integration( 'digimember', array(
            'label' => 'Digimember',
            'icon'  => AUTOMATORWP_DIGIMEMBER_URL . 'assets/digimember.svg',
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

        if ( ! function_exists( 'digimember_init' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Digimember' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Digimember_Integration instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Digimember_Integration The one true AutomatorWP_Digimember_Integration
 */
function AutomatorWP_Digimember_Integration() {
    return AutomatorWP_Digimember_Integration::instance();
}
add_action( 'plugins_loaded', 'AutomatorWP_Digimember_Integration' );
