<?php
/**
 * Plugin Name:           AutomatorWP - Presto Player integration
 * Plugin URI:            https://automatorwp.com/add-ons/presto-player/
 * Description:           Connect AutomatorWP with Presto Player.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-presto-player-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Presto_Player
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Presto_Player {

    /**
     * @var         AutomatorWP_Integration_Presto_Player $instance The one true AutomatorWP_Integration_Presto_Player
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Presto_Player self::$instance The one true AutomatorWP_Integration_Presto_Player
     */
    public static function instance() {
        if( ! self::$instance ) {

            self::$instance = new AutomatorWP_Integration_Presto_Player();

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
        define( 'AUTOMATORWP_PRESTO_PLAYER_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_PRESTO_PLAYER_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_PRESTO_PLAYER_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_PRESTO_PLAYER_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_PRESTO_PLAYER_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_PRESTO_PLAYER_DIR . 'includes/functions.php';
            require_once AUTOMATORWP_PRESTO_PLAYER_DIR . 'includes/tags.php';

            // Triggers
            require_once AUTOMATORWP_PRESTO_PLAYER_DIR . 'includes/triggers/watch-video.php';

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

        automatorwp_register_integration( 'presto_player', array(
            'label' => 'Presto Player',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/presto-player.svg',
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

        if ( ! function_exists( 'presto_player_plugin' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_Presto_Player' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Presto_Player instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Presto_Player The one true AutomatorWP_Integration_Presto_Player
 */
function AutomatorWP_Integration_Presto_Player() {
    return AutomatorWP_Integration_Presto_Player::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Presto_Player' );
