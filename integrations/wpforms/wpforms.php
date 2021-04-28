<?php
/**
 * Plugin Name:           AutomatorWP - WPForms integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-wpforms-integration/
 * Description:           Connect AutomatorWP with WPForms.
 * Version:               1.0.5
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wpforms-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WPForms
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_WPForms_Integration {

    /**
     * @var         AutomatorWP_WPForms_Integration $instance The one true AutomatorWP_WPForms_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_WPForms_Integration self::$instance The one true AutomatorWP_WPForms_Integration
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_WPForms_Integration();
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
        define( 'AUTOMATORWP_WPFORMS_VER', '1.0.5' );

        // Plugin file
        define( 'AUTOMATORWP_WPFORMS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WPFORMS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WPFORMS_URL', plugin_dir_url( __FILE__ ) );
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

            // Triggers
            require_once AUTOMATORWP_WPFORMS_DIR . 'includes/triggers/submit-form.php';
            // Anonymous Triggers
            require_once AUTOMATORWP_WPFORMS_DIR . 'includes/triggers/anonymous-submit-form.php';

            // Includes
            require_once AUTOMATORWP_WPFORMS_DIR . 'includes/functions.php';

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

        automatorwp_register_integration( 'wpforms', array(
            'label' => 'WPForms',
            'icon'  => AUTOMATORWP_WPFORMS_URL . 'assets/wpforms.svg',
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

        if ( ! function_exists( 'wpforms' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_WPForms' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_WPForms_Integration instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_WPForms_Integration The one true AutomatorWP_WPForms_Integration
 */
function AutomatorWP_WPForms_Integration() {
    return AutomatorWP_WPForms_Integration::instance();
}
add_action( 'plugins_loaded', 'AutomatorWP_WPForms_Integration' );
