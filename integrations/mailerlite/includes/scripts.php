<?php
/**
 * Scripts
 *
 * @package     AutomatorWP\MailerLite\Scripts
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
function automatorwp_mailerlite_admin_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Scripts
    wp_register_script( 'automatorwp-mailerlite-js', AUTOMATORWP_MAILERLITE_URL . 'assets/js/automatorwp-mailerlite' . $suffix . '.js', array( 'jquery' ), AUTOMATORWP_MAILERLITE_VER, true );
    
}
add_action( 'admin_init', 'automatorwp_mailerlite_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function automatorwp_mailerlite_admin_enqueue_scripts( $hook ) {

    wp_localize_script( 'automatorwp-mailerlite-js', 'automatorwp_mailerlite', array(
        'nonce' => automatorwp_get_admin_nonce(),
    ) );

    wp_enqueue_script( 'automatorwp-mailerlite-js' );

}
add_action( 'admin_enqueue_scripts', 'automatorwp_mailerlite_admin_enqueue_scripts', 100 );