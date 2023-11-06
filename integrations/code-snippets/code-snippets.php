<?php
/**
 * Plugin Name:           AutomatorWP - Code Snippets
 * Plugin URI:            https://automatorwp.com/add-ons/code-snippets/
 * Description:           Connect AutomatorWP with Code Snippets.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-code-snippets
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.3
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Code_Snippets
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Code_Snippets {

    /**
     * @var         AutomatorWP_Integration_Code_Snippets $instance The one true AutomatorWP_Integration_Code_Snippets
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Code_Snippets self::$instance The one true AutomatorWP_Integration_Code_Snippets
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Code_Snippets();
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
        define( 'AUTOMATORWP_CODE_SNIPPETS_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_CODE_SNIPPETS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_CODE_SNIPPETS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_CODE_SNIPPETS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_CODE_SNIPPETS_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_CODE_SNIPPETS_DIR . 'includes/functions.php';
            
            // ACTIONS
            require_once AUTOMATORWP_CODE_SNIPPETS_DIR . 'includes/actions/activate-snippet.php';
            require_once AUTOMATORWP_CODE_SNIPPETS_DIR . 'includes/actions/deactivate-snippet.php';

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

        automatorwp_register_integration( 'code_snippets', array(
            'label' => 'Code Snippets',
            'icon'  => AUTOMATORWP_CODE_SNIPPETS_URL . 'assets/code-snippets.svg',
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

        if ( ! defined( 'CODE_SNIPPETS_VERSION' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Code_Snippets' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Code_Snippets instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Code_Snippets The one true AutomatorWP_Integration_Code_Snippets
 */
function AutomatorWP_Integration_Code_Snippets() {
    return AutomatorWP_Integration_Code_Snippets::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Code_Snippets' );
