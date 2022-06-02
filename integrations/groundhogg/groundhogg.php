<?php
/**
 * Plugin Name:           AutomatorWP - Groundhogg integration
 * Plugin URI:            https://automatorwp.com/add-ons/groundhogg/
 * Description:           Connect AutomatorWP with Groundhogg.
 * Version:               1.0.2
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-groundhogg-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Groundhogg
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Groundhogg {

    /**
     * @var         AutomatorWP_Integration_Groundhogg $instance The one true AutomatorWP_Integration_Groundhogg
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Groundhogg self::$instance The one true AutomatorWP_Integration_Groundhogg
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Groundhogg();
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
        define( 'AUTOMATORWP_GROUNDHOGG_VER', '1.0.2' );

        // Plugin file
        define( 'AUTOMATORWP_GROUNDHOGG_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_GROUNDHOGG_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_GROUNDHOGG_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_GROUNDHOGG_DIR . 'includes/triggers/tag-added.php';

            // Actions
            require_once AUTOMATORWP_GROUNDHOGG_DIR . 'includes/actions/user-tag.php';

            // Includes
            require_once AUTOMATORWP_GROUNDHOGG_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_GROUNDHOGG_DIR . 'includes/functions.php';

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

        automatorwp_register_integration( 'groundhogg', array(
            'label' => 'Groundhogg',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/groundhogg.svg',
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

        if ( ! defined( 'GROUNDHOGG_VERSION' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Groundhogg' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Groundhogg instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Groundhogg The one true AutomatorWP_Integration_Groundhogg
 */
function AutomatorWP_Integration_Groundhogg() {
    return AutomatorWP_Integration_Groundhogg::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Groundhogg' );
