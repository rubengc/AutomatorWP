<?php
/**
 * Admin Settings Page
 *
 * @package     AutomatorWP\Admin\Settings
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.3.2
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once AUTOMATORWP_DIR . 'includes/admin/settings/general.php';

/**
 * Register AutomatorWP Settings with Settings API.
 *
 * @since  1.0.0
 *
 * @return void
 */
function automatorwp_register_settings() {

	register_setting( 'automatorwp_settings', 'automatorwp_settings' );

}
add_action( 'admin_init', 'automatorwp_register_settings' );

/**
 * Register settings page.
 *
 * @since  1.0.0
 *
 * @return void
 */
function automatorwp_register_settings_page() {

    $tabs = array();
    $boxes = array();

    $is_settings_page = ( isset( $_GET['page'] ) && $_GET['page'] === 'automatorwp_settings' );

    if( $is_settings_page ) {

        // Loop settings sections
        foreach( automatorwp_get_settings_sections() as $section_id => $section ) {

            $meta_boxes = array();

            /**
             * Filter: automatorwp_settings_{$section_id}_meta_boxes
             *
             * @param array $meta_boxes
             *
             * @return array
             */
            $meta_boxes = apply_filters( "automatorwp_settings_{$section_id}_meta_boxes", $meta_boxes );

            if( ! empty( $meta_boxes ) ) {

                // Loop settings section meta boxes
                foreach( $meta_boxes as $meta_box_id => $meta_box ) {

                    // Check meta box tabs
                    if( isset( $meta_box['tabs'] ) && ! empty( $meta_box['tabs'] ) ) {

                        // Loop meta box tabs
                        foreach( $meta_box['tabs'] as $tab_id => $tab ) {

                            $tab['id'] = $tab_id;

                            $meta_box['tabs'][$tab_id] = $tab;

                        }

                    }

                    // Only add settings meta box if has fields
                    if( isset( $meta_box['fields'] ) && ! empty( $meta_box['fields'] ) ) {

                        // Loop meta box fields
                        foreach( $meta_box['fields'] as $field_id => $field ) {

                            $field['id'] = $field_id;

                            // Support for group fields
                            if( isset( $field['fields'] ) && is_array( $field['fields'] ) ) {

                                foreach( $field['fields'] as $group_field_id => $group_field ) {

                                    $field['fields'][$group_field_id]['id'] = $group_field_id;

                                }

                            }

                            $meta_box['fields'][$field_id] = $field;

                        }

                        $meta_box['id'] = $meta_box_id;

                        $meta_box['display_cb'] = false;
                        $meta_box['admin_menu_hook'] = false;
                        $meta_box['priority'] = 'high'; // Fixes issue with CMB2 2.9.0

                        $meta_box['show_on'] = array(
                            'key'   => 'options-page',
                            'value' => array( 'automatorwp_settings' ),
                        );

                        $box = new_cmb2_box( $meta_box );

                        $box->object_type( 'options-page' );

                        $boxes[] = $box;

                    }
                }

                $tabs[] = array(
                    'id'    => $section_id,
                    'title' => ( ( isset( $section['icon'] ) ) ? '<i class="dashicons ' . $section['icon'] . '"></i>' : '' ) . $section['title'],
                    'desc'  => '',
                    'boxes' => array_keys( $meta_boxes ),
                );
            }
        }

    }

    try {
        // Create the options page
        new Cmb2_Metatabs_Options( array(
            'key'      => 'automatorwp_settings',
            'class'    => 'automatorwp-page',
            'title'    => __( 'Settings', 'automatorwp' ),
            'topmenu'  => 'automatorwp',
            'cols'     => 1,
            'boxes'    => $boxes,
            'tabs'     => $tabs,
            'menuargs' => array(
                'menu_title' => __( 'Settings', 'automatorwp' ),
                'capability'        => 'manage_options',
                'view_capability'   => 'manage_options',
            ),
            'savetxt' => __( 'Save Settings', 'automatorwp' ),
            'resettxt' => __( 'Reset Settings', 'automatorwp' ),
        ) );
    } catch ( Exception $e ) {

    }

}
add_action( 'cmb2_admin_init', 'automatorwp_register_settings_page', 12 );

/**
 * AutomatorWP registered settings sections
 *
 * @since  1.0.1
 *
 * @return array
 */
function automatorwp_get_settings_sections() {

    $automatorwp_settings_sections = array(
        'general' => array(
            'title' => __( 'Settings', 'automatorwp' ),
            'icon' => 'dashicons-admin-settings',
        ),
    );

    return apply_filters( 'automatorwp_settings_sections', $automatorwp_settings_sections );

}

/**
 * Get capability required for AutomatorWP administration.
 *
 * @since  1.0.0
 *
 * @return string User capability.
 */
function automatorwp_get_manager_capability() {

    return automatorwp_get_option( 'minimum_role', 'manage_options' );

}
