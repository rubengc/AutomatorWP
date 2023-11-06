<?php
/**
 * Plugin Name:           AutomatorWP - ClickUp
 * Plugin URI:            https://automatorwp.com/add-ons/clickup/
 * Description:           Connect AutomatorWP with ClickUp.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-clickup
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.3
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\ClickUp
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_ClickUp {

    /**
     * @var         AutomatorWP_Integration_ClickUp $instance The one true AutomatorWP_Integration_ClickUp
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_ClickUp self::$instance The one true AutomatorWP_Integration_ClickUp
     */
    public static function instance() {
        if( ! self::$instance ) {

            self::$instance = new AutomatorWP_Integration_ClickUp();

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
        define( 'AUTOMATORWP_CLICKUP_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_CLICKUP_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_CLICKUP_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_CLICKUP_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_CLICKUP_DIR . 'includes/admin.php';
            require_once AUTOMATORWP_CLICKUP_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_CLICKUP_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_CLICKUP_DIR . 'includes/scripts.php';

            // Actions
            require_once AUTOMATORWP_CLICKUP_DIR . 'includes/actions/create-list.php';
            require_once AUTOMATORWP_CLICKUP_DIR . 'includes/actions/create-task.php'; 
            require_once AUTOMATORWP_CLICKUP_DIR . 'includes/actions/add-comment-task.php';
            require_once AUTOMATORWP_CLICKUP_DIR . 'includes/actions/add-tag-task.php';

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

        automatorwp_register_integration( 'clickup', array(
            'label' => 'ClickUp',
            'icon'  => AUTOMATORWP_CLICKUP_URL . 'assets/clickup.svg',
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

        if ( ! class_exists( 'AutomatorWP_ClickUp' ) ) {
            return false;
        }

        return true;

    }


}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_ClickUp instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_ClickUp The one true AutomatorWP_Integration_ClickUp
 */
function AutomatorWP_Integration_ClickUp() {
    return AutomatorWP_Integration_ClickUp::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_ClickUp' );
