<?php
/**
 * Plugin Name:           AutomatorWP - LearnDash integration
 * Plugin URI:            https://automatorwp.com/add-ons/learndash/
 * Description:           Connect AutomatorWP with LearnDash.
 * Version:               1.0.4
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-learndash-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\LearnDash
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_LearnDash {

    /**
     * @var         AutomatorWP_Integration_LearnDash $instance The one true AutomatorWP_Integration_LearnDash
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_LearnDash self::$instance The one true AutomatorWP_Integration_LearnDash
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_LearnDash();
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
        define( 'AUTOMATORWP_LEARNDASH_VER', '1.0.4' );

        // Plugin file
        define( 'AUTOMATORWP_LEARNDASH_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_LEARNDASH_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_LEARNDASH_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/triggers/view-quiz.php';
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/triggers/view-lesson.php';
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/triggers/view-topic.php';
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/triggers/view-course.php';
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/triggers/complete-quiz.php';
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/triggers/complete-quiz-percentage.php';
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/triggers/complete-topic.php';
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/triggers/complete-lesson.php';
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/triggers/complete-course.php';

            // Actions
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/actions/mark-topic.php';
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/actions/mark-lesson.php';
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/actions/user-course.php';
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/actions/mark-course.php';
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/actions/create-group.php';
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/actions/user-group.php';
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/actions/user-group-leader.php';

            // Includes
            require_once AUTOMATORWP_LEARNDASH_DIR . 'includes/functions.php';

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

        automatorwp_register_integration( 'learndash', array(
            'label' => 'LearnDash',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/learndash.svg',
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

        if ( ! class_exists( 'SFWD_LMS' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_LearnDash' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_LearnDash instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_LearnDash The one true AutomatorWP_Integration_LearnDash
 */
function AutomatorWP_Integration_LearnDash() {
    return AutomatorWP_Integration_LearnDash::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_LearnDash' );
