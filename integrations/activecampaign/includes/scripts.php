<?php
/**
 * Scripts
 *
 * @package     AutomatorWP\ActiveCampaign\Scripts
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
function automatorwp_activecampaign_admin_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'automatorwp-activecampaign-css', AUTOMATORWP_ACTIVECAMPAIGN_URL . 'assets/css/automatorwp-activecampaign' . $suffix . '.css', array(), AUTOMATORWP_ACTIVECAMPAIGN_VER, 'all' );

    // Scripts
    wp_register_script( 'automatorwp-activecampaign-js', AUTOMATORWP_ACTIVECAMPAIGN_URL . 'assets/js/automatorwp-activecampaign' . $suffix . '.js', array( 'jquery' ), AUTOMATORWP_ACTIVECAMPAIGN_VER, true );
    
}
add_action( 'admin_init', 'automatorwp_activecampaign_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function automatorwp_activecampaign_admin_enqueue_scripts( $hook ) {

    // Stylesheets
    wp_enqueue_style( 'automatorwp-activecampaign-css' );

    wp_localize_script( 'automatorwp-activecampaign-js', 'automatorwp_activecampaign', array(
        'nonce' => automatorwp_get_admin_nonce(),
    ) );

    wp_enqueue_script( 'automatorwp-activecampaign-js' );

}
add_action( 'admin_enqueue_scripts', 'automatorwp_activecampaign_admin_enqueue_scripts', 100 );