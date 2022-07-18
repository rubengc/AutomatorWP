<?php
/**
 * @package      RGC\CMB2\Field_Switch
 * @author       Ruben Garcia (RubenGC) <rubengcdev@gmail.com>, GamiPress <contact@gamipress.com>
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
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

global $cmb2_field_switch;

// Prevent CMB2 autoload adding "RGC_" at start
if( ! class_exists( 'RGC_CMB2_Field_Switch' ) ) {

    /**
     * Class RGC_CMB2_Field_Switch
     */
    class RGC_CMB2_Field_Switch {

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

    $cmb2_field_switch = new RGC_CMB2_Field_Switch();

}