<?php
/**
 * Plugin Name:           AutomatorWP - WP Job Manager integration
 * Plugin URI:            https://automatorwp.com/add-ons/wp-job-manager/
 * Description:           Connect AutomatorWP with WP Job Manager.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wp-job-manager-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WP_Job_Manager
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_WP_Job_Manager {

    /**
     * @var         AutomatorWP_Integration_WP_Job_Manager $instance The one true AutomatorWP_Integration_WP_Job_Manager
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_WP_Job_Manager self::$instance The one true AutomatorWP_Integration_WP_Job_Manager
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_WP_Job_Manager();
            
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
        define( 'AUTOMATORWP_WP_JOB_MANAGER_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_WP_JOB_MANAGER_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WP_JOB_MANAGER_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WP_JOB_MANAGER_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_WP_JOB_MANAGER_DIR . 'includes/triggers/publish-job.php';

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

        automatorwp_register_integration( 'wp_job_manager', array(
            'label' => 'WP Job Manager',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/wp-job-manager.svg',
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

        if ( ! class_exists( 'WP_Job_Manager' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_WP_Job_Manager' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_WP_Job_Manager instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_WP_Job_Manager The one true AutomatorWP_Integration_WP_Job_Manager
 */
function AutomatorWP_Integration_WP_Job_Manager() {
    return AutomatorWP_Integration_WP_Job_Manager::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_WP_Job_Manager' );
