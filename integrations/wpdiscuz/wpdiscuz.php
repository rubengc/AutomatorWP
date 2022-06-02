<?php
/**
 * Plugin Name:           AutomatorWP - wpDiscuz integration
 * Plugin URI:            https://automatorwp.com/add-ons/wpdiscuz/
 * Description:           Connect AutomatorWP with wpDiscuz.
 * Version:               1.0.4
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wpdiscuz-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\wpDiscuz
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_wpDiscuz {

    /**
     * @var         AutomatorWP_Integration_wpDiscuz $instance The one true AutomatorWP_Integration_wpDiscuz
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_wpDiscuz self::$instance The one true AutomatorWP_Integration_wpDiscuz
     */
    public static function instance() {

        if( ! self::$instance ) {

            self::$instance = new AutomatorWP_Integration_wpDiscuz();

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
        define( 'AUTOMATORWP_WPDISCUZ_VER', '1.0.4' );

        // Plugin file
        define( 'AUTOMATORWP_WPDISCUZ_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WPDISCUZ_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WPDISCUZ_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_WPDISCUZ_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_WPDISCUZ_DIR . 'includes/listeners.php';

            // Triggers
            require_once AUTOMATORWP_WPDISCUZ_DIR . 'includes/triggers/vote-comment.php';
            require_once AUTOMATORWP_WPDISCUZ_DIR . 'includes/triggers/get-vote.php';

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

        automatorwp_register_integration( 'wpdiscuz', array(
            'label' => 'wpDiscuz',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/wpdiscuz.svg',
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

        if ( ! class_exists( 'WpdiscuzCore' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_wpDiscuz' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_wpDiscuz instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_wpDiscuz The one true AutomatorWP_Integration_wpDiscuz
 */
function AutomatorWP_Integration_wpDiscuz() {
    return AutomatorWP_Integration_wpDiscuz::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_wpDiscuz' );
