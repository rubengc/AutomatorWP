<?php
/**
 * Plugin Name:           AutomatorWP - Elementor integration
 * Plugin URI:            https://automatorwp.com/add-ons/elementor-forms/
 * Description:           Connect AutomatorWP with Elementor Forms.
 * Version:               1.0.5
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-elementor-forms
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Elementor_Forms
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Elementor_Forms {

    /**
     * @var         AutomatorWP_Integration_Elementor_Forms $instance The one true AutomatorWP_Integration_Elementor_Forms
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Elementor_Forms self::$instance The one true AutomatorWP_Integration_Elementor_Forms
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Elementor_Forms();

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
        define( 'AUTOMATORWP_ELEMENTOR_FORMS_VER', '1.0.5' );

        // Plugin file
        define( 'AUTOMATORWP_ELEMENTOR_FORMS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_ELEMENTOR_FORMS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_ELEMENTOR_FORMS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_ELEMENTOR_FORMS_DIR . 'includes/triggers/submit-form.php';
            // Anonymous Triggers
            require_once AUTOMATORWP_ELEMENTOR_FORMS_DIR . 'includes/triggers/anonymous-submit-form.php';

            // Includes
            require_once AUTOMATORWP_ELEMENTOR_FORMS_DIR . 'includes/functions.php';

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

        automatorwp_register_integration( 'elementor', array(
            'label' => 'Elementor',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/elementor.svg',
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

        if ( ! defined( 'ELEMENTOR_VERSION' ) && ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
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

        if ( class_exists( 'AutomatorWP_Elementor' ) || class_exists( 'AutomatorWP_Elementor_Forms' ) ) {
            return true;
        }

        return false;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Elementor_Forms instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Elementor_Forms The one true AutomatorWP_Integration_Elementor_Forms
 */
function AutomatorWP_Integration_Elementor_Forms() {
    return AutomatorWP_Integration_Elementor_Forms::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Elementor_Forms' );
