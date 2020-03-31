<?php
/**
 * Scripts
 *
 * @package     AutomatorWP\Scripts
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
function automatorwp_admin_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Libraries
    wp_register_style( 'automatorwp-select2-css', AUTOMATORWP_URL . 'assets/libs/select2/css/select2' . $suffix . '.css', array( ), AUTOMATORWP_VER, 'all' );
    wp_register_script( 'automatorwp-select2-js', AUTOMATORWP_URL . 'assets/js/automatorwp-select2' . $suffix . '.js', array( 'jquery' ), AUTOMATORWP_VER, true );

    wp_register_script( 'automatorwp-select2-dropdown-position-js', AUTOMATORWP_URL . 'assets/libs/select2-dropdownPosition/select2-dropdownPosition' . $suffix . '.js', array( 'jquery' ), AUTOMATORWP_VER, true );

    // Stylesheets
    wp_register_style( 'automatorwp-admin-css', AUTOMATORWP_URL . 'assets/css/automatorwp-admin' . $suffix . '.css', array( ), AUTOMATORWP_VER, 'all' );

    // Scripts
    wp_register_script( 'automatorwp-admin-functions-js', AUTOMATORWP_URL . 'assets/js/automatorwp-admin-functions' . $suffix . '.js', array( 'jquery' ), AUTOMATORWP_VER, true );
    wp_register_script( 'automatorwp-admin-js', AUTOMATORWP_URL . 'assets/js/automatorwp-admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable', 'automatorwp-admin-functions-js', 'automatorwp-select2-js' ), AUTOMATORWP_VER, true );

}
add_action( 'admin_init', 'automatorwp_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 * @return      void
 */
function automatorwp_admin_enqueue_scripts( $hook ) {

    global $post_type;

    // Stylesheets
    wp_enqueue_style( 'automatorwp-admin-css' );

    automatorwp_enqueue_admin_functions_script();

    // Enqueue editor assets
    wp_enqueue_editor();
    wp_enqueue_media();

    // Localize admin script
    wp_localize_script( 'automatorwp-admin-js', 'automatorwp_admin', array(
        'nonce' => automatorwp_get_admin_nonce(),
        'save_text' => __( 'Save', 'automatorwp' ),
        'saving_text' => __( 'Saving', 'automatorwp' ),
    ) );

    // Scripts
    wp_enqueue_script( 'automatorwp-admin-js' );

}
add_action( 'admin_enqueue_scripts', 'automatorwp_admin_enqueue_scripts' );

/**
 * Enqueue the admin functions script with all required components
 *
 * @since       1.7.4.1
 * @return      void
 */
function automatorwp_enqueue_admin_functions_script() {

    // Enqueue Select2 library
    wp_enqueue_script( 'automatorwp-select2-js' );
    wp_enqueue_style( 'automatorwp-select2-css' );

    // Enqueue Select2 dropdown position
    wp_enqueue_script( 'automatorwp-select2-dropdown-position-js' );

    // Setup an array of post type labels to use on post selector field
    $post_types = get_post_types( array(), 'objects' );
    $post_type_labels = array();

    foreach( $post_types as $key => $obj ) {
        $post_type_labels[$key] = $obj->labels->singular_name;
    }

    // Localize admin functions script
    wp_localize_script( 'automatorwp-admin-functions-js', 'automatorwp_admin_functions', array(
        'nonce'                     => automatorwp_get_admin_nonce(),
        'post_type_labels'          => $post_type_labels,
        // Selector placeholders
        'selector_placeholder'      => __( 'Select an option', 'automatorwp' ),
        'post_selector_placeholder' => __( 'Select a post', 'automatorwp' ),
        'user_selector_placeholder' => __( 'Select a user', 'automatorwp' ),
        'object_selector_placeholder' => __( 'Select an item', 'automatorwp' ),
    ) );

    wp_enqueue_script( 'automatorwp-admin-functions-js' );

}

/**
 * Setup a global nonce for all frontend scripts
 *
 * @since       1.7.9
 *
 * @return      string
 */
function automatorwp_get_nonce() {

    if( ! defined( 'AUTOMATORWP_NONCE' ) )
        define( 'AUTOMATORWP_NONCE', wp_create_nonce( 'automatorwp' ) );

    return AUTOMATORWP_NONCE;

}

/**
 * Setup a global nonce for all admin scripts
 *
 * @since       1.7.9
 *
 * @return      string
 */
function automatorwp_get_admin_nonce() {

    if( ! defined( 'AUTOMATORWP_ADMIN_NONCE' ) )
        define( 'AUTOMATORWP_ADMIN_NONCE', wp_create_nonce( 'automatorwp_admin' ) );

    return AUTOMATORWP_ADMIN_NONCE;

}