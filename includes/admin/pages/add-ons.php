<?php
/**
 * Admin Add-ons Page
 *
 * @package     AutomatorWP\Admin\Add_ons
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add-ons page
 *
 * @since  1.0.0
 *
 * @return void
 */
function automatorwp_add_ons_page() {

    if( ! function_exists( 'plugins_api' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    }

    wp_enqueue_script( 'plugin-install' );
    add_thickbox();
    wp_enqueue_script( 'updates' );

    ?>
    <div class="wrap">
        <div id="icon-options-general" class="icon32"></div>
        <h1 class="wp-heading-inline"><?php _e( 'AutomatorWP Add-ons', 'automatorwp' ); ?></h1>
        <hr class="wp-header-end">

        <p><?php _e( 'Add-ons to extend and expand the functionality of AutomatorWP.', 'automatorwp' ); ?></p>

        <form id="plugin-filter" method="post">
            <div class="wp-list-table widefat automatorwp-add-ons">

            <?php

            $plugins = automatorwp_plugins_api();

            if ( is_wp_error( $plugins ) ) {
                echo $plugins->get_error_message();
                return;
            }

            foreach ( $plugins as $plugin ) {

                if ( ! str_contains($plugin->info->slug, '-pass')) {
                    automatorwp_render_plugin_card( $plugin );
                }              

            }

            ?>
            </div>
        </form>
    </div>
    <?php
}

/**
 * Return an array of all installed plugins slugs
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_get_installed_plugins_slugs() {

    $slugs = array();

    $plugin_info = get_site_transient( 'update_plugins' );

    if ( isset( $plugin_info->no_update ) ) {
        foreach ( $plugin_info->no_update as $plugin ) {
            $slugs[] = $plugin->slug;
        }
    }

    if ( isset( $plugin_info->response ) ) {
        foreach ( $plugin_info->response as $plugin ) {
            $slugs[] = $plugin->slug;
        }
    }

    return $slugs;

}

/**
 * Helper function to render a plugin card from the add-ons page
 *
 * @since  1.0.0
 *
 * @param stdClass $plugin
 *
 * @return void
 */
function automatorwp_render_plugin_card( $plugin ) {

    // Plugin title
    $name = $plugin->info->title;

    // Plugin slug
    $slug = property_exists( $plugin, 'wp_info' ) ? $plugin->wp_info->slug : 'automatorwp-' . $plugin->info->slug;

    // Available actions for this plugin
    $action_links = array();

    $details_link = esc_url( 'https://automatorwp.com/add-ons/' . $plugin->info->slug );

    if( property_exists( $plugin, 'wp_info' ) ) {
        // Free add-ons

        $class = 'automatorwp-free-add-on';

        // Check plugin status
        if ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) {
            $status = install_plugin_install_status( $plugin->wp_info );

            switch ( $status['status'] ) {
                case 'install':
                    if ( $status['url'] ) {
                        $action_links[] = '<a class="install-now button" data-slug="' . esc_attr( $slug ) . '" href="' . esc_url( $status['url'] ) . '" aria-label="' . esc_attr( sprintf( __( 'Install %s now' ), $name ) ) . '" data-name="' . esc_attr( $name ) . '">' . __( 'Install Now' ) . '</a>';
                    }
                    break;

                case 'update_available':
                    if ( $status['url'] ) {
                        $action_links[] = '<a class="update-now button aria-button-if-js" data-plugin="' . esc_attr( $status['file'] ) . '" data-slug="' . esc_attr( $slug ) . '" href="' . esc_url( $status['url'] ) . '" aria-label="' . esc_attr( sprintf( __( 'Update %s now' ), $name ) ) . '" data-name="' . esc_attr( $name ) . '">' . __( 'Update Now' ) . '</a>';
                    }
                    break;

                case 'latest_installed':
                case 'newer_installed':
                    if ( is_plugin_active( $status['file'] ) ) {
                        $action_links[] = '<button type="button" class="button button-disabled" disabled="disabled">' . _x( 'Active', 'plugin' ) . '</button>';
                    } elseif ( current_user_can( 'activate_plugins' ) ) {
                        $button_text  = __( 'Activate' );
                        $button_label = _x( 'Activate %s', 'plugin' );
                        $activate_url = add_query_arg( array(
                            '_wpnonce'    => wp_create_nonce( 'activate-plugin_' . $status['file'] ),
                            'action'      => 'activate',
                            'plugin'      => $status['file'],
                        ), network_admin_url( 'plugins.php' ) );

                        if ( is_network_admin() ) {
                            $button_text  = __( 'Network Activate' );
                            $button_label = _x( 'Network Activate %s', 'plugin' );
                            $activate_url = add_query_arg( array( 'networkwide' => 1 ), $activate_url );
                        }

                        $action_links[] = sprintf(
                            '<a href="%1$s" class="button activate-now" aria-label="%2$s">%3$s</a>',
                            esc_url( $activate_url ),
                            esc_attr( sprintf( $button_label, $name ) ),
                            $button_text
                        );
                    } else {
                        $action_links[] = '<button type="button" class="button button-disabled" disabled="disabled">' . _x( 'Installed', 'plugin' ) . '</button>';
                    }
                    break;
            }
        }
    } else {
        // Premium add-ons

        $class = 'automatorwp-premium-add-on';

        $plugin_file = $slug . '/' . $slug . '.php';

        // If is installed
        if ( is_dir( WP_PLUGIN_DIR . '/' . $slug ) ) {

            // If is active
            if ( is_plugin_active( $slug . '/' . $slug . '.php' ) ) {

                // If has licensing enabled
                if( $plugin->licensing->enabled ) {

                    // Plugin installed and active, so field should be registered
                    $field = cmb2_get_field( $slug . '-license', str_replace( '-', '_', $slug ) . '_license', 'automatorwp_settings', 'options-page' );

                    if( $field ) {
                        $license_key = $field->escaped_value();

                        $license = rgc_cmb2_edd_license_data( $license_key );
                        $license_status = ( $license !== false ) ? $license->license : false;

                        if( $license_status !== 'valid' ) {
                            // "Activate License" action
                            $action_links[] = '<a href="' . admin_url( 'admin.php?page=automatorwp_licenses' ) . '" class="button">' . __( 'Activate License', 'automatorwp' ) . '</a>';
                        } else {
                            // "Active and License Registered" action
                            $action_links[] = '<button type="button" class="button button-disabled" disabled="disabled">' . __( 'Active and License Registered', 'automatorwp' ) . '</button>';
                        }
                    }

                } else {
                    // "Active" action
                    $action_links[] = '<button type="button" class="button button-disabled" disabled="disabled">' . __( 'Active', 'automatorwp' ) . '</button>';
                }

            } else if ( current_user_can( 'activate_plugins' ) ) {
                // If not active and current user can activate plugins, then add the "Activate" action

                $button_text  = __( 'Activate' );
                $button_label = _x( 'Activate %s', 'plugin' );
                $activate_url = add_query_arg( array(
                    '_wpnonce'    => wp_create_nonce( 'activate-plugin_' . $plugin_file ),
                    'action'      => 'activate',
                    'plugin'      => $plugin_file,
                ), network_admin_url( 'plugins.php' ) );

                if ( is_network_admin() ) {
                    $button_text  = __( 'Network Activate' );
                    $button_label = _x( 'Network Activate %s', 'plugin' );
                    $activate_url = add_query_arg( array( 'networkwide' => 1 ), $activate_url );
                }

                // "Activate" action
                $action_links[] = sprintf(
                    '<a href="%1$s" class="button activate-now" aria-label="%2$s">%3$s</a>',
                    esc_url( $activate_url ),
                    esc_attr( sprintf( $button_label, $name ) ),
                    $button_text
                );
            }
        } else if( automatorwp_is_plugin_pass( $plugin ) ) {

            // "Get this pass" action
            $action_links[] = '<a href="https://automatorwp.com/add-ons/' . $plugin->info->slug . '" class="button button-primary" target="_blank">' . __( 'Get this pass', 'automatorwp' ) . '</a>';

        } else {

            // "Get this add-on" action
            $action_links[] = '<a href="https://automatorwp.com/add-ons/' . $plugin->info->slug . '" class="button button-primary" target="_blank">' . __( 'Get this add-on', 'automatorwp' ) . '</a>';

        }
    }

    if( ! empty( $details_link ) ) {
        // "More Details" action
        $action_links[] = '<a href="' . esc_url( $details_link ) . '" class="more-details" aria-label="' . esc_attr( sprintf( __( 'More information about %s' ), $name ) ) . '" data-title="' . esc_attr( $name ) . '" target="_blank">' . __( 'More Details' ) . '</a>';
    } ?>

    <div class="automatorwp-plugin-card plugin-card plugin-card-<?php echo sanitize_html_class( $slug ); ?> <?php echo $class; ?>">

        <div class="plugin-card-top">

            <div class="thumbnail column-thumbnail">
                <a href="<?php echo esc_url( $details_link ); ?>" class="thickbox open-plugin-details-modal">
                    <img src="<?php echo esc_attr( $plugin->info->thumbnail ) ?>" class="plugin-thumbnail" alt="">
                </a>
            </div>

            <div class="name column-name">
                <h3>
                    <a href="<?php echo esc_url( $details_link ); ?>" class="thickbox open-plugin-details-modal">
                        <?php echo $name; ?>
                    </a>
                </h3>
            </div>

            <div class="desc column-description">
                <p><?php echo automatorwp_esc_plugin_excerpt( $plugin->info->excerpt ); ?></p>
            </div>

        </div>

        <div class="plugin-card-bottom">
            <div class="action-links">
                <?php if ( $action_links ) {
                    echo '<ul class="plugin-action-buttons"><li>' . implode( '</li><li>', $action_links ) . '</li></ul>';
                } ?>
            </div>
        </div>

    </div>

    <?php
}

/**
 * Function to contact with the AutomatorWP plugins API
 *
 * @since  1.0.0
 *
 * @return object|WP_Error Object with AutomatorWP plugins
 */
function automatorwp_plugins_api() {

    // If a plugins api request has been cached already, then use cached plugins
    if ( false !== ( $res = get_transient( 'automatorwp_plugins_api' ) ) ) {
        return $res;
    }

    $url = $http_url = 'https://automatorwp.com/wp-json/api/add-ons';

    if ( $ssl = wp_http_supports( array( 'ssl' ) ) ) {
        $url = set_url_scheme( $url, 'https' );
    }

    $http_args = array(
        'timeout' => 15,
    );

    $request = wp_remote_get( $url, $http_args );

    if ( $ssl && is_wp_error( $request ) ) {
        trigger_error(
            sprintf(
                __( 'An unexpected error occurred. Something may be wrong with automatorwp.com or this server&#8217;s configuration. If you continue to have problems, please try to <a href="%s">contact us</a>.', 'automatorwp' ),
                'https://automatorwp.com/contact-us/'
            ) . ' ' . __( '(WordPress could not establish a secure connection to automatorwp.com. Please contact your server administrator.)' ),
            headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE
        );

        $request = wp_remote_get( $http_url, $http_args );
    }

    if ( is_wp_error( $request ) ) {
        $res = new WP_Error( 'automatorwp_plugins_api_failed',
            sprintf(
                __( 'An unexpected error occurred. Something may be wrong with automatorwp.com or this server&#8217;s configuration. If you continue to have problems, please try to <a href="%s">contact us</a>.', 'automatorwp' ),
                'https://automatorwp.com/contact-us/'
            ),
            $request->get_error_message()
        );
    } else {
        $res = json_decode( $request['body'] );

        $res = (array) $res->products;

        // Set a transient for 1 week with api plugins
        set_transient( 'automatorwp_plugins_api', $res, ( 24 * 7 ) * HOUR_IN_SECONDS );
    }

    return $res;

}

/**
 * Function to contact with the AutomatorWP website API
 *
 * @since  1.0.0
 *
 * @param string  $action
 * @param array   $data
 *
 * @return object|WP_Error Object with AutomatorWP website API response
 */
function automatorwp_api_request( $action, $data ) {

    // Slug is required to meet the plugin to work
    if( ! isset( $data['slug'] ) ) {
        return false;
    }

    $slug = $data['slug'];

    $cache_key = "automatorwp_api_request_{$action}_{$slug}";

    // If a AutomatorWP api request has been cached already, then use cached response
    if ( false !== ( $response = get_transient( $cache_key ) ) ) {
        return $response;
    }

    $api_params = array(
        'edd_action' => 'get_version',
        'license'    => ! empty( $data['license'] ) ? $data['license'] : '',
        'item_name'  => isset( $data['item_name'] ) ? $data['item_name'] : false,
        'item_id'    => isset( $data['item_id'] ) ? $data['item_id'] : false,
        'version'    => isset( $data['version'] ) ? $data['version'] : false,
        'slug'       => $data['slug'],
        'author'     => isset( $data['author'] ) ? $data['author'] : '',
        'url'        => home_url(),
        'beta'       => ! empty( $data['beta'] ),
    );

    $request = wp_remote_post( 'http://automatorwp.com/edd-sl-api/', array( 'timeout' => 15, 'sslverify' => true, 'body' => $api_params ) );

    if ( is_wp_error( $request ) ) {
        // Return the error
        return $request;
    }

    $request = json_decode( wp_remote_retrieve_body( $request ) );

    // Unserialize sections
    if ( $request && isset( $request->sections ) ) {
        $request->sections = maybe_unserialize( $request->sections );
    } else {
        $request = false;
    }

    // Unserialize banners
    if ( $request && isset( $request->banners ) ) {
        $request->banners = maybe_unserialize( $request->banners );
    }

    // Turn each section into array
    if( ! empty( $request->sections ) ) {
        foreach( $request->sections as $key => $section ) {
            $request->$key = (array) $section;
        }
    }

    // Set a transient of 24 hours with AutomatorWP api response
    set_transient( $cache_key, $request, 24 * HOUR_IN_SECONDS );

    return $request;
}

/**
 * Overrides the plugin information when plugin is not stored on WordPress.org just on AutomatorWP.com
 *
 * @since  1.0.0
 *
 * @param mixed   $data
 * @param string  $action
 * @param object  $args
 *
 * @return object $data
 */
function automatorwp_override_plugin_information( $data, $action = '', $args = null ) {

    if ( $action != 'plugin_information' ) {
        return $data;
    }

    if ( ! isset( $args->slug ) ) {
        return $data;
    }

    $plugins = automatorwp_plugins_api();

    $override_plugin = false;

    foreach( $plugins as $plugin ) {
        // Just override if plugin has not assigned to a WordPress.org plugin
        if ( $args->slug === 'automatorwp-' . $plugin->info->slug && ! property_exists( $plugin, 'wp_info' ) ) {

            $override_plugin = $plugin;

            // Plugin to override found, so exit loop
            break;

        }
    }

    // if not plugin to override, return
    if ( $override_plugin === false ) {
        return $data;
    }

    $to_send = array(
        'slug'   => $override_plugin->info->slug,
        'item_name'   => $override_plugin->info->title,
        'is_ssl' => is_ssl(),
        'fields' => array(
            'banners' => array(),
            'reviews' => false
        )
    );

    $api_response = automatorwp_api_request( 'plugin_information', $to_send );

    if ( $api_response !== false) {
        $data = $api_response;
    }

    // Convert sections into an associative array, since we're getting an object, but Core expects an array.
    if ( isset( $data->sections ) && is_array( $data->sections ) ) {
        $new_sections = array();
        foreach ( $data->sections as $key => $value ) {
            $new_sections[ $key ] = $value;
        }

        $data->sections = $new_sections;
    }

    // Convert banners into an associative array, since we're getting an object, but Core expects an array.
    if ( isset( $data->banners ) && ! is_array( $data->banners ) ) {
        $new_banners = array();
        foreach ( $data->banners as $key => $value ) {
            $new_banners[ $key ] = $value;
        }

        $data->banners = $new_banners;
    }

    return $data;

}
add_filter( 'plugins_api', 'automatorwp_override_plugin_information', 10, 3 );

/**
 * Escape plugin description
 *
 * @since 1.0.0
 *
 * @param string $excerpt
 *
 * @return string
 */
function automatorwp_esc_plugin_excerpt( $excerpt ) {

    // To prevent execute shortcodes on website, the are double capsuled on []
    $excerpt = str_replace( '[[', '[', $excerpt );
    $excerpt = str_replace( ']]', ']', $excerpt );

    return $excerpt;

}

/**
 * Append custom body classes to plugin information iframe
 *
 * @since 1.0.0
 *
 * @param string $classes
 *
 * @return string
 */
function automatorwp_plugin_information_admin_body_class( $classes ) {

    $body_id = ( isset( $GLOBALS['body_id'] )  ? $GLOBALS['body_id'] : '' );

    if( $body_id === 'plugin-information' && isset( $_REQUEST['plugin'] ) ) {

        // Get the plugin queried
        $plugin_slug = wp_unslash( $_REQUEST['plugin'] );

        // Check if is a automatorwp plugin
        $plugins = automatorwp_plugins_api();

        $override_plugin = false;

        foreach( $plugins as $plugin ) {

            // Just override if is a plugin we own
            if ( $plugin_slug === 'automatorwp-' . $plugin->info->slug ) {

                $override_plugin = $plugin;

                // Plugin to override found, so exit loop
                break;

            }
        }

        // if not plugin to override, return
        if ( $override_plugin === false ) {
            return $classes;
        }

        $classes .= ' is-automatorwp-plugin';
    }

    return $classes;
}
add_filter( 'admin_body_class', 'automatorwp_plugin_information_admin_body_class' );

/**
 * Helper function to determine if give plugin has the passes category
 *
 * @since   1.0.0
 *
 * @param stdClass $plugin
 *
 * @return bool
 */
function automatorwp_is_plugin_pass( $plugin ) {

    // Check if plugin has categories
    if( is_array( $plugin->info->category ) && count( $plugin->info->category ) ) {

        // Loop plugin categories
        foreach( $plugin->info->category as $category ) {

            // Passes category found
            if( $category->slug === 'access-pass' ) {
                return true;
            }
        }

    }

    return false;
}