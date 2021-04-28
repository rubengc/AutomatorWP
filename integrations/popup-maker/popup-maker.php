<?php
/**
 * Plugin Name:           AutomatorWP - Popup Maker integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-popup-maker-integration/
 * Description:           Connect AutomatorWP with Popup Maker.
 * Version:               1.0.3
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-popup-maker-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Popup_Maker_Integration
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Popup_Maker_Integration {

    /**
     * @var         AutomatorWP_Popup_Maker_Integration $instance The one true AutomatorWP_Popup_Maker_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Popup_Maker_Integration self::$instance The one true AutomatorWP_Popup_Maker_Integration
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Popup_Maker_Integration();
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
        define( 'AUTOMATORWP_POPUP_MAKER_VER', '1.0.3' );

        // Plugin file
        define( 'AUTOMATORWP_POPUP_MAKER_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_POPUP_MAKER_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_POPUP_MAKER_URL', plugin_dir_url( __FILE__ ) );
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

            // Actions
            require_once AUTOMATORWP_POPUP_MAKER_DIR . 'includes/actions/show-popup.php';

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

        automatorwp_register_integration( 'popup_maker', array(
            'label' => 'Popup Maker',
            'icon'  => AUTOMATORWP_POPUP_MAKER_URL . 'assets/popup-maker.svg',
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

        if ( ! class_exists( 'Popup_Maker' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Popup_Maker' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Popup_Maker_Integration instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Popup_Maker_Integration The one true AutomatorWP_Popup_Maker_Integration
 */
function AutomatorWP_Popup_Maker_Integration() {
    return AutomatorWP_Popup_Maker_Integration::instance();
}
add_action( 'plugins_loaded', 'AutomatorWP_Popup_Maker_Integration' );
