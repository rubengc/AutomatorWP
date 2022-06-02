<?php
/**
 * Plugin Name:           AutomatorWP - Thrive Quiz Builder integration
 * Plugin URI:            https://automatorwp.com/add-ons/thrive-quiz-builder/
 * Description:           Connect AutomatorWP with Thrive Quiz Builder.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-thrive-quiz-builder-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.9
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Thrive_Quiz_Builder
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Thrive_Quiz_Builder {

    /**
     * @var         AutomatorWP_Integration_Thrive_Quiz_Builder $instance The one true AutomatorWP_Integration_Thrive_Quiz_Builder
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Thrive_Quiz_Builder self::$instance The one true AutomatorWP_Integration_Thrive_Quiz_Builder
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Thrive_Quiz_Builder();
            
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
        define( 'AUTOMATORWP_THRIVE_QUIZ_BUILDER_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_THRIVE_QUIZ_BUILDER_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_THRIVE_QUIZ_BUILDER_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_THRIVE_QUIZ_BUILDER_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_THRIVE_QUIZ_BUILDER_DIR . 'includes/triggers/complete-quiz.php';

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

        automatorwp_register_integration( 'thrive_quiz_builder', array(
            'label' => 'Thrive Quiz Builder',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/thrive-quiz-builder.svg',
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

        if ( ! class_exists( 'Thrive_Quiz_Builder' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Thrive_Quiz_Builder' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Thrive_Quiz_Builder instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Thrive_Quiz_Builder The one true AutomatorWP_Integration_Thrive_Quiz_Builder
 */
function AutomatorWP_Integration_Thrive_Quiz_Builder() {
    return AutomatorWP_Integration_Thrive_Quiz_Builder::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Thrive_Quiz_Builder' );
