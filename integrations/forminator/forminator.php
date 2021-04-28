<?php
/**
 * Plugin Name:           AutomatorWP - Forminator integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-forminator-integration/
 * Description:           Connect AutomatorWP with Forminator.
 * Version:               1.0.7
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-forminator-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Forminator
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Forminator_Integration {

    /**
     * @var         AutomatorWP_Forminator_Integration $instance The one true AutomatorWP_Forminator_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Forminator_Integration self::$instance The one true AutomatorWP_Forminator_Integration
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Forminator_Integration();
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
        define( 'AUTOMATORWP_FORMINATOR_VER', '1.0.7' );

        // Plugin file
        define( 'AUTOMATORWP_FORMINATOR_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_FORMINATOR_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_FORMINATOR_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_FORMINATOR_DIR . 'includes/triggers/submit-form.php';
            // Anonymous Triggers
            require_once AUTOMATORWP_FORMINATOR_DIR . 'includes/triggers/anonymous-submit-form.php';

            // Includes
            require_once AUTOMATORWP_FORMINATOR_DIR . 'includes/functions.php';

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

        automatorwp_register_integration( 'forminator', array(
            'label' => 'Forminator',
            'icon'  => AUTOMATORWP_FORMINATOR_URL . 'assets/forminator.svg',
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

        if ( ! class_exists( 'Forminator' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Forminator' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Forminator_Integration instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Forminator_Integration The one true AutomatorWP_Forminator_Integration
 */
function AutomatorWP_Forminator_Integration() {
    return AutomatorWP_Forminator_Integration::instance();
}
add_action( 'plugins_loaded', 'AutomatorWP_Forminator_Integration' );
