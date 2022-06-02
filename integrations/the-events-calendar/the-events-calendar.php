<?php
/**
 * Plugin Name:           AutomatorWP - The Events Calendar integration
 * Plugin URI:            https://automatorwp.com/add-ons/the-events-calendar/
 * Description:           Connect AutomatorWP with The Events Calendar.
 * Version:               1.0.1
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-the-events-calendar-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\The_Events_Calendar
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_The_Events_Calendar {

    /**
     * @var         AutomatorWP_Integration_The_Events_Calendar $instance The one true AutomatorWP_Integration_The_Events_Calendar
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_The_Events_Calendar self::$instance The one true AutomatorWP_Integration_The_Events_Calendar
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_The_Events_Calendar();
            
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
        define( 'AUTOMATORWP_THE_EVENTS_CALENDAR_VER', '1.0.1' );

        // Plugin file
        define( 'AUTOMATORWP_THE_EVENTS_CALENDAR_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_THE_EVENTS_CALENDAR_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_THE_EVENTS_CALENDAR_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_THE_EVENTS_CALENDAR_DIR . 'includes/triggers/confirm-rsvp.php';

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

        automatorwp_register_integration( 'the_events_calendar', array(
            'label' => 'The Events Calendar',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/the-events-calendar.svg',
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

        // The Events Calendar
        if ( ! class_exists( 'Tribe__Events__Main' ) ) {
            return false;
        }

        // Events Tickets
        if ( ! class_exists( 'Tribe__Tickets__Main' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_The_Events_Calendar' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_The_Events_Calendar instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_The_Events_Calendar The one true AutomatorWP_Integration_The_Events_Calendar
 */
function AutomatorWP_Integration_The_Events_Calendar() {
    return AutomatorWP_Integration_The_Events_Calendar::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_The_Events_Calendar' );
