<?php
/**
 * @package      CMB2\Field_Switch
 * @author       GamiPress
 * @copyright    Copyright (c) GamiPress
 *
 * Plugin Name: CMB2 Field Type: Switch
 * Plugin URI: https://github.com/rubengc/cmb2-field-switch
 * GitHub Plugin URI: https://github.com/rubengc/cmb2-field-switch
 * Description: CMB2 field to make checkboxes and radios look as a WordPress Gutenberg switch.
 * Version: 1.0.0
 * Author: GamiPress
 * Author URI: https://gamipress.com/
 * License: GPLv2+
 */

global $cmb2_field_switch;

if( ! class_exists( 'CMB2_Field_Switch' ) ) {

    /**
     * Class CMB2_Field_Switch
     */
    class CMB2_Field_Switch {

        /**
         * Current version number
         */
        const VERSION = '1.0.0';

        /**
         * Initialize the plugin by hooking into CMB2
         */
        public function __construct() {

            add_action( 'admin_enqueue_scripts', array( $this, 'setup_admin_scripts' ) );

        }

        /**
         * Enqueue scripts and styles
         */
        public function setup_admin_scripts() {

            // CSS needs to be enqueued
            wp_enqueue_style( 'cmb-switch-css', plugins_url( 'switch.css', __FILE__ ), array(), self::VERSION );

        }

    }

    $cmb2_field_switch = new CMB2_Field_Switch();

}