<?php
/**
 * Plugin Name:           AutomatorWP - FluentCRM integration
 * Plugin URI:            https://automatorwp.com/add-ons/fluentcrm/
 * Description:           Connect AutomatorWP with FluentCRM.
 * Version:               1.0.1
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-fluentcrm-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\FluentCRM
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_FluentCRM {

    /**
     * @var         AutomatorWP_Integration_FluentCRM $instance The one true AutomatorWP_Integration_FluentCRM
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_FluentCRM self::$instance The one true AutomatorWP_Integration_FluentCRM
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_FluentCRM();

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
        define( 'AUTOMATORWP_FLUENTCRM_VER', '1.0.1' );

        // Plugin file
        define( 'AUTOMATORWP_FLUENTCRM_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_FLUENTCRM_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_FLUENTCRM_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_FLUENTCRM_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_FLUENTCRM_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_FLUENTCRM_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_FLUENTCRM_DIR . 'includes/triggers/tag-added.php';
            require_once AUTOMATORWP_FLUENTCRM_DIR . 'includes/triggers/list-added.php';
            require_once AUTOMATORWP_FLUENTCRM_DIR . 'includes/triggers/status-change.php';
            require_once AUTOMATORWP_FLUENTCRM_DIR . 'includes/triggers/anonymous-tag-added.php';
            require_once AUTOMATORWP_FLUENTCRM_DIR . 'includes/triggers/anonymous-list-added.php';
            require_once AUTOMATORWP_FLUENTCRM_DIR . 'includes/triggers/anonymous-status-change.php';

            // Actions
            require_once AUTOMATORWP_FLUENTCRM_DIR . 'includes/actions/user-tag.php';
            require_once AUTOMATORWP_FLUENTCRM_DIR . 'includes/actions/user-list.php';

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

        automatorwp_register_integration( 'fluentcrm', array(
            'label' => 'FluentCRM',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/fluentcrm.svg',
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

        if ( ! defined( 'FLUENTCRM' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_FluentCRM' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_FluentCRM instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_FluentCRM The one true AutomatorWP_Integration_FluentCRM
 */
function AutomatorWP_Integration_FluentCRM() {
    return AutomatorWP_Integration_FluentCRM::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_FluentCRM' );
