<?php
/**
 * Admin Licenses Page
 *
 * @package     AutomatorWP\Admin\Licenses
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Setup the default parameters to license meta boxes
 *
 * @since   1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function automatorwp_licenses_meta_boxes_params( $meta_boxes ) {

    // Loop settings section meta boxes
    foreach( $meta_boxes as $meta_box_id => $meta_box ) {

        // Only add settings meta box if has fields
        if( isset( $meta_box['fields'] ) && ! empty( $meta_box['fields'] ) ) {

            // Loop meta box fields
            foreach( $meta_box['fields'] as $field_id => $field ) {

                // Update edd_license fields with default parameters to the AutomatorWP server
                if( $field['type'] !== 'edd_license' ) {
                    continue;
                }

                // if not server provider, then add AutomatorWP server
                if( ! isset( $field['server'] ) ) {
                    $field['server'] = 'https://automatorwp.com/edd-sl-api';
                }

                // Check if is a AutomatorWP hosted plugin
                if( $field['server'] !== 'https://automatorwp.com/edd-sl-api' ) {
                    continue;
                }

                // Renew link
                $field['renew_license_link'] = 'https://automatorwp.com/renew-a-license';
                $field['license_management_link'] = 'https://automatorwp.com/account';
                $field['contact_link'] = 'https://automatorwp.com/contact';

                // Before field row hook to render some extra information
                $field['before_row'] = 'automatorwp_license_field_before';

                // Update the field definition
                $meta_boxes[$meta_box_id]['fields'][$field_id] = $field;
                $meta_boxes[$meta_box_id]['priority'] = 'high'; // Fixes issue with CMB2 2.9.0

            }

        }

    }

    return $meta_boxes;

}
add_filter( 'automatorwp_licenses_meta_boxes', 'automatorwp_licenses_meta_boxes_params', 9999 );

/**
 * Setup the thumbnail to license meta boxes
 *
 * @since   1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function automatorwp_licenses_meta_boxes_thumbnails( $meta_boxes ) {

    // Check if we are on the licenses page to prevent API calls outside this page
    if( ! isset( $_GET['page'] ) ) {
        return $meta_boxes;
    }

    if( $_GET['page'] !== 'automatorwp_licenses' ) {
        return $meta_boxes;
    }

    // Get our add-ons
    $plugins = automatorwp_plugins_api();

    // Loop settings section meta boxes
    foreach( $meta_boxes as $meta_box_id => $meta_box ) {

        // Only add settings meta box if has fields
        if( isset( $meta_box['fields'] ) && ! empty( $meta_box['fields'] ) ) {

            // Loop meta box fields
            foreach( $meta_box['fields'] as $field_id => $field ) {

                // Update edd_license fields with default parameters to the AutomatorWP server
                if( $field['type'] !== 'edd_license' ) {
                    continue;
                }

                // if not server provider, then add AutomatorWP server
                if( ! isset( $field['server'] ) ) {
                    $field['server'] = 'https://automatorwp.com/edd-sl-api';
                }

                // Check if is an AutomatorWP hosted plugin
                if( $field['server'] !== 'https://automatorwp.com/edd-sl-api' ) {
                    continue;
                }

                // Try to find the plugin thumbnail from plugins API
                if ( ! is_wp_error( $plugins ) && isset( $field['file'] ) && ! isset( $field['thumbnail'] ) ) {

                    foreach ( $plugins as $plugin ) {

                        $slug = basename( $field['file'], '.php' );

                        if( $slug === 'automatorwp-' . $plugin->info->slug ) {
                            $field['thumbnail'] = $plugin->info->thumbnail;
                            // Thumbnail found so exit loop
                            break;
                        }

                    }

                }

                // Update the field definition
                $meta_boxes[$meta_box_id]['fields'][$field_id] = $field;

            }

        }

    }

    return $meta_boxes;

}
add_filter( 'automatorwp_licenses_meta_boxes', 'automatorwp_licenses_meta_boxes_thumbnails', 99999 );

/**
 * Register licenses page.
 *
 * @since  1.0.0
 *
 * @return void
 */
function automatorwp_register_licenses_page() {

    $tabs = array();
    $boxes = array();

    $meta_boxes = array();

    /**
     * Filter: automatorwp_licenses_meta_boxes
     *
     * @param array $meta_boxes
     *
     * @return array
     */
    $meta_boxes = apply_filters( "automatorwp_licenses_meta_boxes", $meta_boxes );

    if( ! empty( $meta_boxes ) ) {

        // Loop licenses section meta boxes
        foreach( $meta_boxes as $meta_box_id => $meta_box ) {

            // Check meta box tabs
            if( isset( $meta_box['tabs'] ) && ! empty( $meta_box['tabs'] ) ) {

                // Loop meta box tabs
                foreach( $meta_box['tabs'] as $tab_id => $tab ) {

                    $tab['id'] = $tab_id;

                    $meta_box['tabs'][$tab_id] = $tab;

                }

            }

            // Only add licenses meta box if has fields
            if( isset( $meta_box['fields'] ) && ! empty( $meta_box['fields'] ) ) {

                // Loop meta box fields
                foreach( $meta_box['fields'] as $field_id => $field ) {

                    $field['id'] = $field_id;

                    // If license field without name, set "License" as standard name
                    if( ! isset( $field['name'] ) && $field['type'] === 'edd_license' ) {
                        $field['name'] = __( 'License', 'automatorwp' );
                    }

                    // Support for group fields
                    if( isset( $field['fields'] ) && is_array( $field['fields'] ) ) {

                        foreach( $field['fields'] as $group_field_id => $group_field ) {

                            $field['fields'][$group_field_id]['id'] = $group_field_id;

                        }

                    }

                    // Register custom update message on plugins menu
                    if( isset( $field['file'] ) && ! is_multisite() ) {

                        $plugin_file = plugin_basename( $field['file'] );

                        // Register custom plugin row for licensed plugins to update package if an active license exists
                        add_action( "after_plugin_row_$plugin_file", 'automatorwp_license_plugin_update_row', 5, 2 );
                        add_action( "in_plugin_update_message-$plugin_file", 'automatorwp_license_in_plugin_update_message', 10, 2 );

                    }

                    $meta_box['fields'][$field_id] = $field;

                }

                $meta_box['id'] = $meta_box_id;

                $meta_box['display_cb'] = false;
                $meta_box['admin_menu_hook'] = false;

                $meta_box['show_on'] = array(
                    'key'   => 'options-page',
                    'value' => array( 'automatorwp_licenses' ),
                    'option_key' => 'automatorwp_settings',
                );

                $box = new_cmb2_box( $meta_box );

                $box->object_type( 'options-page' );

                $boxes[] = $box;

            }
        }
    }

    try {
        // Create the options page
        new Cmb2_Metatabs_Options( array(
            'key'      => 'automatorwp_settings',
            'class'    => 'automatorwp-page',
            'title'    => __( 'Licenses', 'automatorwp' ),
            'topmenu'  => 'automatorwp',
            'view_capability' => 'manage_options',
            'cols'     => 1,
            'boxes'    => $boxes,
            //'tabs'     => $tabs,
            'menuargs' => array(
                'menu_title' => __( 'Licenses', 'automatorwp' ),
                'menu_slug'  => 'automatorwp_licenses',
            ),
            'savetxt' => __( 'Save Changes', 'automatorwp' ),
            'resettxt' => false,
        ) );
    } catch ( Exception $e ) {

    }

}
add_action( 'cmb2_admin_init', 'automatorwp_register_licenses_page' );

/**
 * License field thumbnail.
 *
 * @since  1.0.0
 *
 * @param  array        $field_args Current field args
 * @param  CMB2_Field   $field      Current field object
 */
function automatorwp_license_field_before( $field_args, $field ) {

    if( isset( $field_args['thumbnail'] ) && ! empty( $field_args['thumbnail'] ) ) : ?>

        <div class="automatorwp-license-thumbnail">
            <img src="<?php echo $field_args['thumbnail']; ?>" alt="<?php echo $field_args['item_name']; ?>">
        </div>

    <?php endif;

}

/**
 * Force package and download link update for licensed plugins.
 *
 * @since  1.0.0
 *
 * @param  string   $file           Plugin file
 * @param  array    $plugin_data    An array of plugin data.
 */
function automatorwp_license_plugin_update_row( $file, $plugin_data ) {

    $update_cache = get_site_transient( 'update_plugins' );

    if ( ! isset( $update_cache->response[ $file ] ) ) {
        return;
    }

    $response = $update_cache->response[ $file ];

    // If there is not a package link, then try to update it
    if ( empty( $response->package ) ) {

        // Turn plugin slug like 'plugin-slug' to 'plugin_slug'
        $slug = str_replace( '-', '_', $response->slug );

        // Get the stored license key
        $license = automatorwp_get_option( $slug . '_license', '' );

        // Check the license status
        $license_status = rgc_cmb2_edd_license_status( $license );

        if( $license_status === 'valid' ) {

            // Make a new request to the API to check package and download link
            $api_params = array(
                'edd_action' => 'get_version',
                'license'    => $license,
                'item_name'  => $response->name,
                'slug'       => $response->slug,
                'url'        => home_url(),
            );

            $api_request = wp_remote_post( 'https://automatorwp.com/edd-sl-api', array( 'timeout' => 15, 'sslverify' => true, 'body' => $api_params ) );

            if ( ! is_wp_error( $api_request ) ) {

                // Decode the API response
                $version_info = json_decode( wp_remote_retrieve_body( $api_request ) );

                // If package link provided, update it
                if( ! empty( $version_info->package ) ) {
                    $update_cache->response[ $file ]->package = $version_info->package;
                }

                // If download link provided, update it
                if( ! empty( $version_info->download_link ) ) {
                    $update_cache->response[ $file ]->download_link = $version_info->download_link;
                }

                // Update site transient with updated data
                set_site_transient( 'update_plugins', $update_cache );

            }

        }

    }

}

/**
 * Advice to user about invalid license keys
 *
 * @param array $plugin_data
 * @param array $response
 */
function automatorwp_license_in_plugin_update_message( $plugin_data, $response ) {

    // Turn plugin slug like 'plugin-slug' to 'plugin_slug'
    $slug = str_replace( '-', '_', $response->slug );

    // Get the stored license key
    $license = automatorwp_get_option( $slug . '_license', '' );

    // Check the license status
    $license_status = rgc_cmb2_edd_license_status( $license );

    if( $license_status !== 'valid' ) {

        echo '&nbsp;<strong><a href="' . esc_url( admin_url( 'admin.php?page=automatorwp_licenses' ) ) . '">' . __( 'Enter valid license key for automatic updates.', 'automatorwp' ) . '</a></strong>';

    }

}

/**
 * Filter to set correct option key for our licenses
 *
 * @since 1.0.0
 *
 * @param string $option_key
 * @param CMB2 $cmb
 *
 * @return string
 */
function automatorwp_licenses_option_key( $option_key, $cmb ) {

    if( isset( $cmb->meta_box['show_on'] ) && isset( $cmb->meta_box['show_on']['option_key'] ) ) {
        return $cmb->meta_box['show_on']['option_key'];
    }

    return $option_key;

}
add_filter( 'cmb2_edd_license_option_key', 'automatorwp_licenses_option_key', 10, 2 );

/**
 * Before licenses form
 *
 * @since 1.0.0
 *
 * @param string $filterable
 * @param string $page
 *
 * @return string
 */
function automatorwp_licenses_before_form( $filterable, $page ) {

    if( $page !== 'automatorwp_licenses' ) {
        return $filterable;
    }

    $output = '<em class="automatorwp-licenses-intructions">'
        . sprintf( __( 'Looking to install a pro add-on? Check the <a href="%s" target="_blank">installation instructions</a>.', 'automatorwp' ), 'https://automatorwp.com/docs/getting-started/installing-pro-add-ons/' )
        . '</em>';

    return $filterable . $output;

}
add_filter( 'cmb2metatabs_before_form', 'automatorwp_licenses_before_form', 10, 2 );
