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
 * Register frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function automatorwp_register_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

    // Stylesheets
    wp_register_style( 'automatorwp-admin-bar-css', AUTOMATORWP_URL . 'assets/css/automatorwp-admin-bar' . $suffix . '.css', array( ), AUTOMATORWP_VER, 'all' );

    // Scripts
    wp_register_script( 'automatorwp-redirect-js', AUTOMATORWP_URL . 'assets/js/automatorwp-redirect' . $suffix . '.js', array( 'jquery' ), AUTOMATORWP_VER, true );

}
add_action( 'init', 'automatorwp_register_scripts' );

/**
 * Enqueue frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function automatorwp_enqueue_scripts( $hook = null ) {

    // Redirect script
    if( automatorwp_is_action_in_use( 'automatorwp_redirect_user' ) ) {

        $excluded_urls = array();

        /**
         * Filter to let plugins exclude URLs from the redirect check
         *
         * @since 1.6.9
         *
         * @param array $excluded_urls
         *
         * @return array
         */
        $excluded_urls = apply_filters( 'automatorwp_redirect_excluded_urls', $excluded_urls );

        $excluded_data = array();

        /**
         * Filter to let plugins exclude request data from the redirect check
         *
         * @since 1.6.9
         *
         * @param array $excluded_data
         *
         * @return array
         */
        $excluded_data = apply_filters( 'automatorwp_redirect_excluded_data', $excluded_data );

        $excluded_ajax_actions = array(
            'automatorwp_check_for_redirect',
            // GamiPress Notifications support
            'gamipress_notifications_get_notices',
            'gamipress_notifications_last_check',
            // GamiPress Congratulations Popups support
            'gamipress_congratulations_popups_get_popups',
            'gamipress_congratulations_popups_popup_shown',
            // WooCommerce support
            'woocommerce_load_products',
            // BuddyBoss support
            'buddyboss_load_user_profile',
            'buddyboss_theme_get_header_unread_messages',
            'buddyboss_theme_get_header_notifications',
            // WordPress support
            'heartbeat',
        );

        /**
         * Filter to let plugins exclude ajax actions from the redirect check
         *
         * @since 1.6.9
         *
         * @param array $excluded_ajax_actions
         *
         * @return array
         */
        $excluded_ajax_actions = apply_filters( 'automatorwp_redirect_excluded_ajax_actions', $excluded_ajax_actions );

        wp_localize_script( 'automatorwp-redirect-js', 'automatorwp_redirect', array(
            'ajaxurl'               => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
            'nonce'                 => automatorwp_get_nonce(),
            'user_id'               => get_current_user_id(),
            'excluded_urls'         => $excluded_urls,
            'excluded_data'         => $excluded_data,
            'excluded_ajax_actions' => $excluded_ajax_actions,
        ) );

        wp_enqueue_script( 'automatorwp-redirect-js' );

    }

}
add_action( 'wp_enqueue_scripts', 'automatorwp_enqueue_scripts' );

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
    wp_register_style( 'automatorwp-admin-rtl-css', AUTOMATORWP_URL . 'assets/css/automatorwp-admin-rtl' . $suffix . '.css', array( ), AUTOMATORWP_VER, 'all' );

    // Scripts
    wp_register_script( 'automatorwp-admin-functions-js', AUTOMATORWP_URL . 'assets/js/automatorwp-admin-functions' . $suffix . '.js', array( 'jquery', 'jquery-ui-dialog' ), AUTOMATORWP_VER, true );
    wp_register_script( 'automatorwp-admin-js', AUTOMATORWP_URL . 'assets/js/automatorwp-admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable', 'automatorwp-admin-functions-js', 'automatorwp-select2-js' ), AUTOMATORWP_VER, true );
    wp_register_script( 'automatorwp-admin-notices-js', AUTOMATORWP_URL . 'assets/js/automatorwp-admin-notices' . $suffix . '.js', array( 'jquery' ), AUTOMATORWP_VER, true );
    wp_register_script( 'automatorwp-import-automation-js', AUTOMATORWP_URL . 'assets/js/automatorwp-import-automation' . $suffix . '.js', array( 'jquery' ), AUTOMATORWP_VER, true );

}
add_action( 'admin_init', 'automatorwp_admin_register_scripts' );

/**
 * Enqueue admin scripts
 *
 * @since       1.0.0
 *
 * @param string $hook
 *
 * @return      void
 */
function automatorwp_admin_enqueue_scripts( $hook ) {

    // Stylesheets
    wp_enqueue_style( 'automatorwp-admin-css' );
    wp_enqueue_style( 'automatorwp-admin-rtl-css' );

    // Localize admin script
    wp_localize_script( 'automatorwp-admin-notices-js', 'automatorwp_admin_notices', array(
        'nonce' => automatorwp_get_admin_nonce(),
    ) );

    // Scripts
    wp_enqueue_script( 'automatorwp-admin-notices-js' );

    // Import automation scripts
    if( $hook === 'automatorwp_page_automatorwp_import_automation' ) {
        wp_localize_script( 'automatorwp-import-automation-js', 'automatorwp_import_automation', array(
            'nonce' => automatorwp_get_admin_nonce(),
        ) );

        wp_enqueue_script( 'automatorwp-import-automation-js' );
    }

    $allow_enqueue = true;

    $allowed_hooks = array(
        'automatorwp_page_automatorwp_automations', // Automations list
        'admin_page_edit_automatorwp_automations',  // Automation edit
        'automatorwp_page_automatorwp_logs',        // Logs list
        'admin_page_edit_automatorwp_logs',         // Log edit
        'automatorwp_page_automatorwp_add_ons',     // Add-ons page
        'automatorwp_page_automatorwp_licenses',    // Licenses page
    );

    // Prevent to enqueue scripts outside our pages
    if( ! in_array( $hook, $allowed_hooks ) ) {
        $allow_enqueue = false;
    }

    /**
     * Filter to enqueue admin scripts
     *
     * @since 1.0.0
     *
     * @param bool      $allow_enqueue
     * @param string    $hook
     *
     * @return bool
     */
    $allow_enqueue = apply_filters( 'automatorwp_allow_enqueue_admin_scripts', $allow_enqueue, $hook );

    if( ! $allow_enqueue ) {
        return;
    }

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
 * @since       1.0.0
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

    // Setup an array of taxonomy labels to use on the taxonomy selector field
    $taxonomies = get_taxonomies( array(), 'objects' );
    $taxonomy_labels = array();

    foreach( $taxonomies as $key => $obj ) {
        $taxonomy_labels[$key] = $obj->labels->singular_name;
    }

    // Localize admin functions script
    wp_localize_script( 'automatorwp-admin-functions-js', 'automatorwp_admin_functions', array(
        'nonce'                                 => automatorwp_get_admin_nonce(),
        'post_type_labels'                      => $post_type_labels,
        'taxonomy_labels'                       => $taxonomy_labels,
        // Selector placeholders
        'selector_placeholder'                  => __( 'Select an option', 'automatorwp' ),
        'post_selector_placeholder'             => __( 'Select a post', 'automatorwp' ),
        'term_selector_placeholder'             => __( 'Select a term', 'automatorwp' ),
        /* translators: %s: Taxonomy title (category, tag, etc). */
        'taxonomy_selector_placeholder_pattern' => __( 'Select a %s', 'automatorwp' ),
        'user_selector_placeholder'             => __( 'Select a user', 'automatorwp' ),
        'object_selector_placeholder'           => __( 'Select an item', 'automatorwp' ),
    ) );

    wp_enqueue_script( 'automatorwp-admin-functions-js' );

}

/**
 * Register and enqueue admin bar scripts
 *
 * @since       1.3.2
 * @return      void
 */
function automatorwp_enqueue_admin_bar_scripts() {

    wp_enqueue_style( 'automatorwp-admin-bar-css' );

}
add_action( 'admin_bar_init', 'automatorwp_enqueue_admin_bar_scripts' );

/**
 * CMB2 script dependencies
 *
 * @since       1.0.0
 *
 * @param string $hook
 *
 * @return      void
 */
function automatorwp_cmb2_script_dependencies( $dependencies ) {

    // Bail if not is a custom page
    if( ! isset( $_GET['page'] ) ) {
        return $dependencies;
    }

    // Bail if not is the edit automation page
    if( $_GET['page'] !== 'edit_automatorwp_automations' ) {
        return $dependencies;
    }

    // Force to enqueue datetimepicker lib since triggers and actions forms are loaded through ajax
    if ( ! isset( $dependencies['jquery-ui-datetimepicker'] ) ) {
        $dependencies['jquery-ui-datetimepicker'] = 'jquery-ui-datetimepicker';
    }

    return $dependencies;

}
add_filter( 'cmb2_script_dependencies', 'automatorwp_cmb2_script_dependencies' );

/**
 * Setup a global nonce for all frontend scripts
 *
 * @since       1.0.0
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
 * @since       1.0.0
 *
 * @return      string
 */
function automatorwp_get_admin_nonce() {

    if( ! defined( 'AUTOMATORWP_ADMIN_NONCE' ) )
        define( 'AUTOMATORWP_ADMIN_NONCE', wp_create_nonce( 'automatorwp_admin' ) );

    return AUTOMATORWP_ADMIN_NONCE;

}