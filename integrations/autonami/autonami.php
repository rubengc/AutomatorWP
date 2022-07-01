<?php
/**
 * Plugin Name:           AutomatorWP - Autonami integration
 * Plugin URI:            https://automatorwp.com/add-ons/autonami/
 * Description:           Connect AutomatorWP with Autonami.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-autonami-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Autonami
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Autonami {

    /**
     * @var         AutomatorWP_Integration_Autonami $instance The one true AutomatorWP_Integration_Autonami
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Autonami self::$instance The one true AutomatorWP_Integration_Autonami
     */
    public static function instance() {
        if( ! self::$instance ) {

            self::$instance = new AutomatorWP_Integration_Autonami();

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
        define( 'AUTOMATORWP_AUTONAMI_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_AUTONAMI_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_AUTONAMI_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_AUTONAMI_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_AUTONAMI_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_AUTONAMI_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_AUTONAMI_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_AUTONAMI_DIR . 'includes/triggers/user-tag-added.php';
            require_once AUTOMATORWP_AUTONAMI_DIR . 'includes/triggers/user-added-list.php';

            // Anonymous Triggers
            require_once AUTOMATORWP_AUTONAMI_DIR . 'includes/triggers/anonymous-contact-added-list.php';
            require_once AUTOMATORWP_AUTONAMI_DIR . 'includes/triggers/anonymous-contact-tag-added.php';

            // Actions
            require_once AUTOMATORWP_AUTONAMI_DIR . 'includes/actions/add-user-tag.php';
            require_once AUTOMATORWP_AUTONAMI_DIR . 'includes/actions/add-contact-tag.php';

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

        automatorwp_register_integration( 'autonami', array(
            'label' => 'Autonami',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/autonami.svg',
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

        if ( ! class_exists( 'BWFAN_Core' ) ) {
            return false;
        }

        if ( ! class_exists( 'BWFAN_Pro' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Autonami' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Autonami instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Autonami The one true AutomatorWP_Integration_Autonami
 */
function AutomatorWP_Integration_Autonami() {
    return AutomatorWP_Integration_Autonami::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Autonami' );
