<?php
/**
 * Install
 *
 * @package     AutomatorWP\Install
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function automatorwp_install() {

    // Setup default AutomatorWP options
    $automatorwp_settings = ( $exists = get_option( 'automatorwp_settings' ) ) ? $exists : array();

    if ( empty( $automatorwp_settings ) ) {

        $automatorwp_settings['minimum_role'] = 'manage_options';
        $automatorwp_settings['points_image_size'] = array( 'width' => 50, 'height' => 50 );
        $automatorwp_settings['achievement_image_size'] = array( 'width' => 100, 'height' => 100 );
        $automatorwp_settings['rank_image_size'] = array( 'width' => 100, 'height' => 100 );

        update_option( 'automatorwp_settings', $automatorwp_settings );
    }

    // Setup default AutomatorWP installation date
    $automatorwp_install_date = ( $exists = get_option( 'automatorwp_install_date' ) ) ? $exists : '';

    if ( empty( $automatorwp_install_date ) ) {
        update_option( 'automatorwp_install_date', date( 'Y-m-d H:i:s' ) );
    }

    // Register AutomatorWP custom DB tables
    automatorwp_register_custom_tables();
}
