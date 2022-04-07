<?php
/**
 * Scripts
 *
 * @package     AutomatorWP\Integrations\Mailchimp\Scripts
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
function automatorwp_mailchimp_admin_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'automatorwp-mailchimp-css', AUTOMATORWP_MAILCHIMP_URL . 'assets/css/automatorwp-mailchimp' . $suffix . '.css', array(), AUTOMATORWP_MAILCHIMP_VER, 'all' );


    // Scripts
    wp_register_script( 'automatorwp-mailchimp-js', AUTOMATORWP_MAILCHIMP_URL . 'assets/js/automatorwp-mailchimp' . $suffix . '.js', array( 'jquery' ), AUTOMATORWP_MAILCHIMP_VER, true );

}
add_action( 'admin_init', 'automatorwp_mailchimp_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function automatorwp_mailchimp_admin_enqueue_scripts( $hook ) {

    // Stylesheets
    wp_enqueue_style( 'automatorwp-mailchimp-css' );

    // Scripts
    wp_localize_script( 'automatorwp-mailchimp-js', 'automatorwp_mailchimp', array(
        'nonce' => automatorwp_get_admin_nonce(),
    ) );

    wp_enqueue_script( 'automatorwp-mailchimp-js' );

}
add_action( 'admin_enqueue_scripts', 'automatorwp_mailchimp_admin_enqueue_scripts', 100 );