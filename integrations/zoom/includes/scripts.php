<?php
/**
 * Scripts
 *
 * @package     AutomatorWP\Zoom\Scripts
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function automatorwp_zoom_admin_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'automatorwp-zoom-css', AUTOMATORWP_ZOOM_URL . 'assets/css/automatorwp-zoom' . $suffix . '.css', array(), AUTOMATORWP_ZOOM_VER, 'all' );

    // Scripts
    wp_register_script( 'automatorwp-zoom-js', AUTOMATORWP_ZOOM_URL . 'assets/js/automatorwp-zoom' . $suffix . '.js', array( 'jquery' ), AUTOMATORWP_ZOOM_VER, true );

}
add_action( 'admin_init', 'automatorwp_zoom_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function automatorwp_zoom_admin_enqueue_scripts( $hook ) {

    // Stylesheets
    wp_enqueue_style( 'automatorwp-zoom-css' );

    wp_localize_script( 'automatorwp-zoom-js', 'automatorwp_zoom', array(
        'nonce' => automatorwp_get_admin_nonce(),
    ) );

    wp_enqueue_script( 'automatorwp-zoom-js' );

}
add_action( 'admin_enqueue_scripts', 'automatorwp_zoom_admin_enqueue_scripts', 100 );