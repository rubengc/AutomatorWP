<?php
/**
 * Plugin Name:           AutomatorWP - Thrive Ovation
 * Plugin URI:            https://automatorwp.com/add-ons/thrive-ovation/
 * Description:           Connect AutomatorWP with Thrive Ovation.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-thrive-ovation
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.2
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Thrive_Ovation
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Thrive_Ovation {

    /**
     * @var         AutomatorWP_Integration_Thrive_Ovation $instance The one true AutomatorWP_Integration_Thrive_Ovation
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Thrive_Ovation self::$instance The one true AutomatorWP_Integration_Thrive_Ovation
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Thrive_Ovation();
            
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
        define( 'AUTOMATORWP_THRIVE_OVATION_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_THRIVE_OVATION_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_THRIVE_OVATION_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_THRIVE_OVATION_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_THRIVE_OVATION_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_THRIVE_OVATION_DIR . 'includes/triggers/user-submit-testimonial.php';

            // Anonymous Triggers
            require_once AUTOMATORWP_THRIVE_OVATION_DIR . 'includes/triggers/anonymous-submit-testimonial.php';

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

        automatorwp_register_integration( 'thrive_ovation', array(
            'label' => 'Thrive Ovation',
            'icon'  => AUTOMATORWP_THRIVE_OVATION_URL . 'assets/thrive-ovation.svg',
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

        if ( ! defined( 'TVO_PLUGIN_FILE_PATH' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Thrive_Ovation' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Thrive_Ovation instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Thrive_Ovation The one true AutomatorWP_Integration_Thrive_Ovation
 */
function AutomatorWP_Integration_Thrive_Ovation() {
    return AutomatorWP_Integration_Thrive_Ovation::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Thrive_Ovation' );
