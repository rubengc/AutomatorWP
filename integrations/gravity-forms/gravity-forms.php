<?php
/**
 * Plugin Name:           AutomatorWP - Gravity Forms integration
 * Plugin URI:            https://automatorwp.com/add-ons/gravity-forms/
 * Description:           Connect AutomatorWP with Gravity Forms.
 * Version:               1.0.7
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-gravity-forms-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Gravity_Forms
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Gravity_Forms {

    /**
     * @var         AutomatorWP_Integration_Gravity_Forms $instance The one true AutomatorWP_Integration_Gravity_Forms
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Gravity_Forms self::$instance The one true AutomatorWP_Integration_Gravity_Forms
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Gravity_Forms();
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
        define( 'AUTOMATORWP_GRAVITY_FORMS_VER', '1.0.7' );

        // Plugin file
        define( 'AUTOMATORWP_GRAVITY_FORMS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_GRAVITY_FORMS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_GRAVITY_FORMS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_GRAVITY_FORMS_DIR . 'includes/triggers/submit-form.php';
            // Anonymous Triggers
            require_once AUTOMATORWP_GRAVITY_FORMS_DIR . 'includes/triggers/anonymous-submit-form.php';

            // Includes
            require_once AUTOMATORWP_GRAVITY_FORMS_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_GRAVITY_FORMS_DIR . 'includes/functions.php';

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
        // Setup our activation and deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        
    }

    /**
     * Registers this integration
     *
     * @since 1.0.0
     */
    function register_integration() {

        automatorwp_register_integration( 'gravity_forms', array(
            'label' => 'Gravity Forms',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/gravity-forms.svg',
        ) );

    }

    /**
     * Activation hook for the plugin.
     *
     * @since  1.0.0
     */
    function activate() {

        if( $this->meets_requirements() && ! $this->pro_installed() ) {

        }

    }

    /**
     * Deactivation hook for the plugin.
     *
     * @since  1.0.0
     */
    function deactivate() {

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

        if ( ! class_exists( 'GFForms' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Gravity_Forms' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Gravity_Forms instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Gravity_Forms The one true AutomatorWP_Integration_Gravity_Forms
 */
function AutomatorWP_Integration_Gravity_Forms() {
    return AutomatorWP_Integration_Gravity_Forms::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Gravity_Forms' );
