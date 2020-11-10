<?php
/**
 * Admin
 *
 * @package     AutomatorWP\Admin
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Admin includes
require_once AUTOMATORWP_DIR . 'includes/admin/notices.php';
require_once AUTOMATORWP_DIR . 'includes/admin/upgrades.php';
// Admin pages
require_once AUTOMATORWP_DIR . 'includes/admin/pages/add-ons.php';
require_once AUTOMATORWP_DIR . 'includes/admin/pages/licenses.php';
require_once AUTOMATORWP_DIR . 'includes/admin/pages/settings.php';

/**
 * Helper function to get an option value.
 *
 * @since  1.0.0
 *
 * @param string    $option_name
 * @param bool      $default
 *
 * @return mixed Option value or default parameter value if not exists.
 */
function automatorwp_get_option( $option_name, $default = false ) {

    if( AutomatorWP()->settings === null ) {
        AutomatorWP()->settings = get_option( 'automatorwp_settings' );
    }

    return isset( AutomatorWP()->settings[ $option_name ] ) ? AutomatorWP()->settings[ $option_name ] : $default;

}

/**
 * Admin menus
 *
 * @since   1.0.0
 */
function automatorwp_admin_menu() {

    // AutomatorWP menu
    add_menu_page( __( 'AutomatorWP', 'automatorwp' ), __( 'AutomatorWP', 'automatorwp' ), 'manage_options', 'automatorwp', '', 'dashicons-automatorwp', 50 );

}
add_action( 'admin_menu', 'automatorwp_admin_menu' );

/**
 * Remove first AutomatorWP admin submenu since is empty
 * By default, WordPress assigns the first submenu item the same slug as parent, in this case, there isn't such page yet
 *
 * @since 1.0.0
 *
 * @param string $parent_file The parent file.
 *
 * @return string
 */
function automatorwp_admin_menu_fix( $parent_file ) {

    global $submenu;

    if( isset( $submenu['automatorwp'] ) && isset( $submenu['automatorwp'][0] ) ) {
        unset( $submenu['automatorwp'][0] );
    }

    return $parent_file;

}
add_filter( 'parent_file', 'automatorwp_admin_menu_fix' );

/**
 * Admin submenus
 *
 * @since 1.0.0
 */
function automatorwp_admin_submenu() {

    // AutomatorWP sub menus
    add_submenu_page( 'automatorwp', __( 'Add-ons', 'automatorwp' ), __( 'Add-ons', 'automatorwp' ), 'manage_options', 'automatorwp_add_ons', 'automatorwp_add_ons_page' );

}
add_action( 'admin_menu', 'automatorwp_admin_submenu', 12 );

/**
 * Add AutomatorWP admin bar menu
 *
 * @since 1.3.2
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function automatorwp_admin_bar_menu( $wp_admin_bar ) {

    // Bail if current user can't manage AutomatorWP
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Bail if admin bar menu disabled
    if( (bool) automatorwp_get_option( 'disable_admin_bar_menu', false ) ) {
        return;
    }

    // AutomatorWP
    $wp_admin_bar->add_node( array(
        'id'    => 'automatorwp',
        'title'	=>	'<span class="ab-icon"></span>' . __( 'AutomatorWP', 'automatorwp' ),
        'meta'  => array( 'class' => 'automatorwp' ),
    ) );

    // Automations
    $wp_admin_bar->add_node( array(
        'id'     => 'automatorwp-automations',
        'title'  => __( 'Automations', 'automatorwp' ),
        'parent' => 'automatorwp',
        'href'   => admin_url( 'admin.php?page=automatorwp_automations' )
    ) );

    // Logs
    $wp_admin_bar->add_node( array(
        'id'     => 'automatorwp-logs',
        'title'  => __( 'Logs', 'automatorwp' ),
        'parent' => 'automatorwp',
        'href'   => admin_url( 'admin.php?page=automatorwp_logs' )
    ) );

}
add_action( 'admin_bar_menu', 'automatorwp_admin_bar_menu', 100 );

/**
 * Add GamiPress admin bar menu
 *
 * @since 1.3.2
 */
function automatorwp_admin_bar_menu_bottom( $wp_admin_bar ) {

    // Bail if current user can't manage AutomatorWP
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Bail if admin bar menu disabled
    if( (bool) automatorwp_get_option( 'disable_admin_bar_menu', false ) ) {
        return;
    }

    // Add-ons
    $wp_admin_bar->add_node( array(
        'id'     => 'automatorwp-add-ons',
        'title'  => __( 'Add-ons', 'automatorwp' ),
        'parent' => 'automatorwp',
        'href'   => admin_url( 'admin.php?page=automatorwp_add_ons' )
    ) );

    // Licenses
    $wp_admin_bar->add_node( array(
        'id'     => 'automatorwp-licenses',
        'title'  => __( 'Licenses', 'automatorwp' ),
        'parent' => 'automatorwp',
        'href'   => admin_url( 'admin.php?page=automatorwp_licenses' )
    ) );

    // Settings
    $wp_admin_bar->add_node( array(
        'id'     => 'automatorwp-settings',
        'title'  => __( 'Settings', 'automatorwp' ),
        'parent' => 'automatorwp',
        'href'   => admin_url( 'admin.php?page=automatorwp_settings' )
    ) );

}
add_action( 'admin_bar_menu', 'automatorwp_admin_bar_menu_bottom', 999 );

/**
 * Helper function to register custom meta boxes
 *
 * @since  1.0.8
 *
 * @param string 		$id
 * @param string 		$title
 * @param string|array 	$object_types
 * @param array 		$fields
 * @param array 		$args
 */
function automatorwp_add_meta_box( $id, $title, $object_types, $fields, $args = array() ) {

    // ID for hooks
    $hook_id = str_replace( '-', '_', $id );

    /**
     * Filter box fields to allow extend it
     *
     * @since  1.0.8
     *
     * @param array $fields Box fields
     * @param array $args   Box args
     *
     * @return array
     */
    $fields = apply_filters( "automatorwp_{$hook_id}_fields", $fields, $args );

    foreach( $fields as $field_id => $field ) {

        $fields[$field_id]['id'] = $field_id;

        // Support for group fields
        if( isset( $field['fields'] ) && is_array( $field['fields'] ) ) {

            foreach( $field['fields'] as $group_field_id => $group_field ) {

                $fields[$field_id]['fields'][$group_field_id]['id'] = $group_field_id;

            }

        }

    }

    $args = wp_parse_args( $args, array(
        'vertical_tabs' => false,
        'tabs'      	=> array(),
        'context'      	=> 'normal',
        'priority'     	=> 'default',
    ) );

    /**
     * Filter box tabs to allow extend it
     *
     * @since  1.0.8
     *
     * @param array $tabs   Box tabs
     * @param array $fields Box fields
     * @param array $args   Box args
     *
     * @return array
     */
    $tabs = apply_filters( "automatorwp_{$hook_id}_tabs", $args['tabs'], $fields, $args );

    // Parse tabs
    foreach( $tabs as $tab_id => $tab ) {

        $tabs[$tab_id]['id'] = $tab_id;

    }

    // Setup the final box arguments
    $box = array(
        'id'           	=> $id,
        'title'        	=> $title,
        'object_types' 	=> ! is_array( $object_types) ? array( $object_types ) : $object_types,
        'tabs'      	=> $tabs,
        'vertical_tabs' => $args['vertical_tabs'],
        'context'      	=> $args['context'],
        'priority'     	=> $args['priority'],
        'classes'		=> 'automatorwp-form automatorwp-box-form',
        'fields' 		=> $fields
    );

    /**
     * Filter the final box args that will be passed to CMB2
     *
     * @since  1.0.0
     *
     * @param array 		$box            Final box args
     * @param string 		$id             Box id
     * @param string 		$title          Box title
     * @param string|array 	$object_types   Object types where box will appear
     * @param array 		$fields         Box fields
     * @param array 		$tabs           Box tabs
     * @param array 		$args           Box args
     */
    apply_filters( "automatorwp_{$hook_id}_box", $box, $id, $title, $object_types, $fields, $tabs, $args );

    // Instance the CMB2 box
    new_cmb2_box( $box );

}