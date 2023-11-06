<?php
/**
 * Plugin Name:           AutomatorWP - Kadence Blocks
 * Plugin URI:            https://automatorwp.com/add-ons/kadence-blocks/
 * Description:           Connect AutomatorWP with Kadence Blocks.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-kadence-blocks
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.3
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Kadence_Blocks
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Kadence_Blocks {

    /**
     * @var         AutomatorWP_Integration_Kadence_Blocks $instance The one true AutomatorWP_Integration_Kadence_Blocks
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Kadence_Blocks self::$instance The one true AutomatorWP_Integration_Kadence_Blocks
     */
    public static function instance() {
        if( ! self::$instance ) {

            self::$instance = new AutomatorWP_Integration_Kadence_Blocks();

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
        define( 'AUTOMATORWP_KADENCE_BLOCKS_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_KADENCE_BLOCKS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_KADENCE_BLOCKS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_KADENCE_BLOCKS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_KADENCE_BLOCKS_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_KADENCE_BLOCKS_DIR . 'includes/functions.php';
            
            // Triggers
            require_once AUTOMATORWP_KADENCE_BLOCKS_DIR . 'includes/triggers/submit-form.php';

            // Anonymous Triggers
            require_once AUTOMATORWP_KADENCE_BLOCKS_DIR . 'includes/triggers/anonymous-submit-form.php';

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

        automatorwp_register_integration( 'kadence_blocks', array(
            'label' => 'Kadence Blocks',
            'icon'  => AUTOMATORWP_KADENCE_BLOCKS_URL . 'assets/kadence-blocks.svg',
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

        if ( ! defined( 'KADENCE_BLOCKS_VERSION' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Kadence_Blocks' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Kadence_Blocks instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Kadence_Blocks The one true AutomatorWP_Integration_Kadence_Blocks
 */
function AutomatorWP_Integration_Kadence_Blocks() {
    return AutomatorWP_Integration_Kadence_Blocks::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Kadence_Blocks' );
