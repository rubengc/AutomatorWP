<?php
/**
 * Plugin Name:           AutomatorWP - Amelia integration
 * Plugin URI:            https://automatorwp.com/add-ons/ameliabooking/
 * Description:           Connect AutomatorWP with Amelia.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-ameliabooking-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\AmeliaBooking
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_AmeliaBooking {

    /**
     * @var         AutomatorWP_Integration_AmeliaBooking $instance The one true AutomatorWP_Integration_AmeliaBooking
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_AmeliaBooking self::$instance The one true AutomatorWP_Integration_AmeliaBooking
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_AmeliaBooking();
            
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
        define( 'AUTOMATORWP_AMELIABOOKING_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_AMELIABOOKING_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_AMELIABOOKING_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_AMELIABOOKING_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_AMELIABOOKING_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_AMELIABOOKING_DIR . 'includes/triggers/user-books-appointment.php';

            // Anonymous Triggers
            require_once AUTOMATORWP_AMELIABOOKING_DIR . 'includes/triggers/anonymous-books-appointment.php';

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

        automatorwp_register_integration( 'ameliabooking', array(
            'label' => 'Amelia',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/ameliabooking.svg',
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

        if ( ! class_exists( 'AmeliaBooking\Plugin' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_AmeliaBooking' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_AmeliaBooking instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_AmeliaBooking The one true AutomatorWP_Integration_AmeliaBooking
 */
function AutomatorWP_Integration_AmeliaBooking() {
    return AutomatorWP_Integration_AmeliaBooking::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_AmeliaBooking' );
