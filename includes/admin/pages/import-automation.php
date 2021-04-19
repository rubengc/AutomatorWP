<?php
/**
 * Import Automation Page
 *
 * @package     AutomatorWP\Admin\Import_Automation
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function automatorwp_hide_import_automation_page( $submenu_file ) {

    // Hide the submenu
    remove_submenu_page( 'automatorwp', 'automatorwp_import_automation' );

    return $submenu_file;
}
add_filter( 'submenu_file', 'automatorwp_hide_import_automation_page' );

/**
 * Import Automation page
 *
 * @since  1.0.0
 *
 * @return void
 */
function automatorwp_import_automation_page() {
    ?>
    <div class="wrap">

        <div class="automatorwp-import-automation-text"><?php _e( 'Importing automation...', 'automatorwp' ); ?></div>
        <div class="automatorwp-import-automation-icon">
            <img src="<?php echo AUTOMATORWP_URL . 'assets/img/automatorwp-logo.svg'; ?>" class="automatorwp-import-automation-icon-default"/>
            <img src="<?php echo AUTOMATORWP_URL . 'assets/img/automatorwp-logo-success.svg'; ?>" class="automatorwp-import-automation-icon-success" style="display: none"/>
            <img src="<?php echo AUTOMATORWP_URL . 'assets/img/automatorwp-logo-error.svg'; ?>" class="automatorwp-import-automation-icon-error" style="display: none"/>

        </div>
        <div class="automatorwp-import-automation-loader">
            <div class="automatorwp-import-automation-loader-background"></div>
            <div class="automatorwp-import-automation-loader-icon"></div>
        </div>

    </div>
    <?php
}

/**
 * Import an automation from URL through ajax
 *
 * @since   1.0.0
 */
function automatorwp_ajax_get_automation_export_url() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    $automation_id = ( isset( $_REQUEST['automation_id'] ) ? absint( $_REQUEST['automation_id'] ) : 0 );

    // Bail if automation ID not provided
    if( $automation_id === 0 ) {
        wp_send_json_error( __( 'Invalid automation ID.', 'automatorwp' ) );
    }

    $url = automatorwp_get_automation_export_url( $automation_id );

    wp_send_json_success( $url );

}
add_action( 'wp_ajax_automatorwp_get_automation_export_url', 'automatorwp_ajax_get_automation_export_url' );

/**
 * Import an automation from URL through ajax
 *
 * @since   1.0.0
 */
function automatorwp_ajax_import_automation_from_url() {

    global $wpdb;

    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    $request = array();

    // Parse the given request
    parse_str( $_REQUEST['request'] , $request );

    // Get automation args
    $title = ( isset( $request['ti'] ) ? sanitize_text_field( $request['ti'] ) : '' );
    $type = ( isset( $request['ty'] ) ? sanitize_text_field( $request['ty'] ) : 'user' );
    $sequential = ( isset( $request['s'] ) ? absint( $request['s'] ) : 0 );
    $times_per_user = ( isset( $request['tu'] ) ? absint( $request['tu'] ) : 1 );
    $times = ( isset( $request['tim'] ) ? absint( $request['tim'] ) : 0 );

    // Get triggers and actions
    $received_triggers = automatorwp_get_array_from_export_url( $request, 't' );
    $received_actions = automatorwp_get_array_from_export_url( $request, 'a' );

    // Bail if no triggers and actions received
    if( empty( $received_triggers ) && empty( $received_actions ) ) {
        wp_send_json_error( __( 'No triggers and actions found from this URL.', 'automatorwp' ) );
    }

    $triggers = array();
    $actions = array();

    // Check if all received triggers are correct
    foreach ( $received_triggers as $trigger ) {
        // The unique required parameter is the trigger type
        if( ! isset( $trigger['t'] ) ) {
            continue;
        }

        $triggers[] = $trigger;
    }

    // Check if all received actions are correct
    foreach ( $received_actions as $action ) {
        // The unique required parameter is the action type
        if( ! isset( $action['t'] ) ) {
            continue;
        }

        $actions[] = $action;
    }

    // Bail if no triggers and actions found
    if( empty( $triggers ) && empty( $actions ) ) {
        wp_send_json_error( __( 'No triggers and actions found from this URL.', 'automatorwp' ) );
    }

    // All done!

    ct_setup_table( 'automatorwp_automations' );

    // Create the new automation
    $automation = array(
        'title'             => $title . ( ! empty( $title ) ? ' ' : '' ) . __( '(Imported)', 'automatorwp' ),
        'type'              => $type,
        'sequential'        => $sequential,
        'times_per_user'    => $times_per_user,
        'times'             => $times,
        'status'            => 'inactive',
        'date'              => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
    );

    $new_automation_id = ct_insert_object( $automation );

    ct_reset_setup_table();

    // Bail if could not create the automation
    if( ! $new_automation_id ) {
        wp_send_json_error( __( 'Could not create the automation from this URL.', 'automatorwp' ) );
    }

    $automation['id'] = $new_automation_id;

    // Lets insert the triggers and actions
    $item_types = array( 'trigger', 'action' );
    $ids = array();
    $replacements = array();
    $new_triggers = array();
    $new_actions = array();

    // Loop items to insert them
    foreach( $item_types as $item_type ) {

        if( $item_type === 'trigger' ) {
            $items = $triggers;
        } else {
            $items = $actions;
        }

        ct_setup_table( "automatorwp_{$item_type}s" );

        foreach( $items as $i => $item ) {

            // Create the new trigger or action
            $new_item = array(
                'automation_id' => $new_automation_id,
                'title'         => '',
                'type'          => sanitize_text_field( $item['t'] ),
                'status'        => 'active',
                'position'      => absint( $i ),
                'date'          => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
            );

            $new_item_id = ct_insert_object( $new_item );

            if( $new_item_id ) {

                if( isset( $item['i'] ) && absint( $item['i'] ) !== 0 ) {
                    // Update old ids and replacements to be used on metas
                    $ids[absint( $item['i'] )] = $new_item_id;
                    $replacements['{' . absint( $item['i'] ) . ':'] = '{' . $new_item_id . ':';
                } else {
                    // Ensure item ID
                    $items[$i]['i'] = $new_item_id;
                    $ids[$new_item_id] = $new_item_id;
                }

                $new_item['id'] = $new_item_id;
                $item_id = absint( $items[$i]['i'] );

                if( $item_type === 'trigger' ) {
                    $new_triggers[$item_id] = $new_item;
                } else {
                    $new_actions[$item_id] = $new_item;
                }

            }

        }

        ct_reset_setup_table();

    }

    $tags = array_keys( $replacements );

    // Loop items again to update their metas
    foreach( $item_types as $item_type ) {

        if( $item_type === 'trigger' ) {
            $items = $triggers;
        } else {
            $items = $actions;
        }

        $ct_table = ct_setup_table( "automatorwp_{$item_type}s" );
        $metas = array();

        foreach( $items as $i => $item ) {

            // Skip items without metas
            if( ! isset( $item['o'] ) ) {
                continue;
            }

            if( ! is_array( $item['o'] ) ) {
                continue;
            }

            $item_id = absint( $item['i'] );

            // Skip items without an ID
            if( ! isset( $ids[$item_id] ) ) {
                continue;
            }

            foreach ( $item['o'] as $meta_key => $meta_value ) {

                $type = sanitize_text_field( $item['t'] );

                /**
                 * Filter to exclude a meta on export this item through URL
                 * $item_type: trigger | action
                 * $type: The trigger or action type
                 * $meta_key: The meta key
                 *
                 * @since  1.0.0
                 *
                 * @param bool $exclude
                 *
                 * @return bool
                 */
                $exclude = apply_filters( "automatorwp_export_url_{$item_type}_{$type}_meta_{$meta_key}_excluded", false );

                // Skip if meta gets excluded on export through URL
                if( $exclude ) {
                    continue;
                }

                $meta_key = sanitize_key( $meta_key );
                $meta_key = wp_unslash( $meta_key );

                $meta_value = urldecode( $meta_value );
                $meta_value = maybe_unserialize( $meta_value );
                $meta_value = wp_unslash( $meta_value );
                $meta_value = esc_sql( $meta_value );
                $meta_value = sanitize_meta( $meta_key, $meta_value, $ct_table->name );
                $meta_value = maybe_serialize( $meta_value );

                // Replace metas with old IDs with the new ones
                $meta_value = str_replace( $tags, $replacements, $meta_value );

                // Prepare for the upcoming insert
                $metas[] = $wpdb->prepare( '%d, %s, %s', array( $ids[$item_id], $meta_key, $meta_value ) );
            }

        }

        if( count( $metas ) ) {
            $metas = implode( '), (', $metas );

            // Run a single query to insert all metas instead of insert them one-by-one
            $wpdb->query( "INSERT INTO {$ct_table->meta->db->table_name} (id, meta_key, meta_value) VALUES ({$metas})" );
        }

        ct_reset_setup_table();

    }

    // Loop items again to update their titles
    foreach( $item_types as $item_type ) {

        if( $item_type === 'trigger' ) {
            $items = $triggers;
        } else {
            $items = $actions;
        }

        ct_setup_table( "automatorwp_{$item_type}s" );

        foreach( $items as $i => $item ) {

            $item_id = absint( $item['i'] );

            // Skip items without an ID
            if( ! isset( $ids[$item_id] ) ) {
                continue;
            }

            if( $item_type === 'trigger' ) {
                $object = (object) $new_triggers[$item_id];
            } else {
                $object = (object) $new_actions[$item_id];
            }

            // Update the item title
            ct_update_object( array(
                'id' => $ids[$item_id],
                'title' => automatorwp_parse_automation_item_edit_label( $object, $item_type, 'view' )
            ) );

        }

        ct_reset_setup_table();

    }

    wp_send_json_success( array(
        'message' => __( 'Automation imported successfully! Redirecting...', 'automatorwp' ),
        'redirect_url' => ct_get_edit_link( 'automatorwp_automations', $new_automation_id ),
    ) );

}
add_action( 'wp_ajax_automatorwp_import_automation_from_url', 'automatorwp_ajax_import_automation_from_url' );

