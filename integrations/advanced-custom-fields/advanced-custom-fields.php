<?php
/**
 * Plugin Name:           AutomatorWP - Advanced Custom Fields integration
 * Plugin URI:            https://automatorwp.com/add-ons/advanced-custom-fields/
 * Description:           Connect AutomatorWP with Advanced Custom Fields.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.0
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Advanced_Custom_Fields
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Advanced_Custom_Fields {

    /**
     * @var         AutomatorWP_Integration_Advanced_Custom_Fields $instance The one true AutomatorWP_Integration_Advanced_Custom_Fields
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Advanced_Custom_Fields self::$instance The one true AutomatorWP_Integration_Advanced_Custom_Fields
     */
    public static function instance() {
        if( ! self::$instance ) {

            self::$instance = new AutomatorWP_Integration_Advanced_Custom_Fields();

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
        define( 'AUTOMATORWP_ADVANCED_CUSTOM_FIELDS_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_ADVANCED_CUSTOM_FIELDS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_ADVANCED_CUSTOM_FIELDS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_ADVANCED_CUSTOM_FIELDS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_ADVANCED_CUSTOM_FIELDS_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_ADVANCED_CUSTOM_FIELDS_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_ADVANCED_CUSTOM_FIELDS_DIR . 'includes/triggers/post-field-updated.php';

            // Actions
            require_once AUTOMATORWP_ADVANCED_CUSTOM_FIELDS_DIR . 'includes/actions/update-post-field.php';

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

        automatorwp_register_integration( 'advanced_custom_fields', array(
            'label' => 'Advanced Custom Fields',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/advanced-custom-fields.svg',
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

        if ( ! class_exists( 'ACF' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Advanced_Custom_Fields' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Advanced_Custom_Fields instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Advanced_Custom_Fields The one true AutomatorWP_Integration_Advanced_Custom_Fields
 */
function AutomatorWP_Integration_Advanced_Custom_Fields() {
    return AutomatorWP_Integration_Advanced_Custom_Fields::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Advanced_Custom_Fields' );
