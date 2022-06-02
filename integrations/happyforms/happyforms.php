<?php
/**
 * Plugin Name:           AutomatorWP - HappyForms integration
 * Plugin URI:            https://automatorwp.com/add-ons/happyforms/
 * Description:           Connect AutomatorWP with HappyForms.
 * Version:               1.0.4
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-happyforms-integration-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\HappyForms
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_HappyForms {

    /**
     * @var         AutomatorWP_Integration_HappyForms $instance The one true AutomatorWP_Integration_HappyForms
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_HappyForms self::$instance The one true AutomatorWP_Integration_HappyForms
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_HappyForms();
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
        define( 'AUTOMATORWP_HAPPYFORMS_VER', '1.0.4' );

        // Plugin file
        define( 'AUTOMATORWP_HAPPYFORMS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_HAPPYFORMS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_HAPPYFORMS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_HAPPYFORMS_DIR . 'includes/triggers/submit-form.php';
            // Anonymous Triggers
            require_once AUTOMATORWP_HAPPYFORMS_DIR . 'includes/triggers/anonymous-submit-form.php';

            // Includes
            require_once AUTOMATORWP_HAPPYFORMS_DIR . 'includes/functions.php';

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

        automatorwp_register_integration( 'happyforms', array(
            'label' => 'HappyForms',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/happyforms.svg',
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

        if ( ! function_exists( 'HappyForms' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_HappyForms' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_HappyForms instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_HappyForms The one true AutomatorWP_Integration_HappyForms
 */
function AutomatorWP_Integration_HappyForms() {
    return AutomatorWP_Integration_HappyForms::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_HappyForms' );
