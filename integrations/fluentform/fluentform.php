<?php
/**
 * Plugin Name:           AutomatorWP - WP Fluent Forms integration
 * Plugin URI:            https://automatorwp.com/add-ons/fluentform/
 * Description:           Connect AutomatorWP with WP Fluent Forms.
 * Version:               1.0.6
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-fluentform-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\FluentForm
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_FluentForm {

    /**
     * @var         AutomatorWP_Integration_FluentForm $instance The one true AutomatorWP_Integration_FluentForm
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_FluentForm self::$instance The one true AutomatorWP_Integration_FluentForm
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_FluentForm();
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
        define( 'AUTOMATORWP_FLUENTFORM_VER', '1.0.6' );

        // Plugin file
        define( 'AUTOMATORWP_FLUENTFORM_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_FLUENTFORM_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_FLUENTFORM_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_FLUENTFORM_DIR . 'includes/triggers/submit-form.php';
            // Anonymous Triggers
            require_once AUTOMATORWP_FLUENTFORM_DIR . 'includes/triggers/anonymous-submit-form.php';

            // Includes
            require_once AUTOMATORWP_FLUENTFORM_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_FLUENTFORM_DIR . 'includes/functions.php';

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

        automatorwp_register_integration( 'fluentform', array(
            'label' => 'WP Fluent Forms',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/fluentform.svg',
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

        if ( ! defined( 'FLUENTFORM' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_FluentForm' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_FluentForm instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_FluentForm The one true AutomatorWP_Integration_FluentForm
 */
function AutomatorWP_Integration_FluentForm() {
    return AutomatorWP_Integration_FluentForm::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_FluentForm' );
