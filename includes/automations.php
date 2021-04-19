<?php
/**
 * Automations
 *
 * @package     AutomatorWP\Automations
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get automation registered statuses
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_get_automation_statuses() {

    return apply_filters( 'automatorwp_automation_statuses', array(
        'active' => __( 'Active', 'automatorwp' ),
        'inactive' => __( 'Inactive', 'automatorwp' ),
    ) );

}

/**
 * Get automation registered types
 *
 * @since  1.3.0
 *
 * @return array
 */
function automatorwp_get_automation_types() {

    return apply_filters( 'automatorwp_automation_types', array(
        'user' => array(
            'image' => AUTOMATORWP_URL . 'assets/img/automatorwp-logo.svg',
            'label' => __( 'Logged-in', 'automatorwp' ),
            'desc'  => __( 'Automation for logged-in users. Designed to run actions on the user who has completed the triggers.', 'automatorwp' ),
        ),
        'anonymous' => array(
            'image' => AUTOMATORWP_URL . 'assets/img/automatorwp-anonymous-logo.svg',
            'label' => __( 'Anonymous', 'automatorwp' ),
            'desc'  => __( 'Automation for anonymous users. Ideal for creating new users or for modifying existing users.', 'automatorwp' ),
        ),
    ) );

}

/**
 * Get automation registered types labels
 *
 * @since  1.3.0
 *
 * @return array
 */
function automatorwp_get_automation_types_labels() {

    $types = automatorwp_get_automation_types();
    $labels = array();

    foreach( $types as $type => $args ) {
        $labels[$type] = $args['label'];
    }

    return $labels;

}

/**
 * Get the automation object data
 *
 * @param int       $automation_id  The automation ID
 * @param string    $output         Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which correspond to
 *                                  a object, an associative array, or a numeric array, respectively. Default OBJECT.
 *
 * @return array|stdClass|null
 */
function automatorwp_get_automation_object( $automation_id, $output = OBJECT ) {

    ct_setup_table( 'automatorwp_automations' );

    $automation = ct_get_object( $automation_id );

    ct_reset_setup_table();

    return $automation;

}

/**
 * Get the automation object data
 *
 * @param int       $automation_id  The automation ID
 * @param string    $meta_key       Optional. The meta key to retrieve. By default, returns
 *                                  data for all keys. Default empty.
 * @param bool      $single         Optional. Whether to return a single value. Default false.
 *
 * @return mixed                    Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function automatorwp_get_automation_meta( $automation_id, $meta_key = '', $single = false ) {

    ct_setup_table( 'automatorwp_automations' );

    $meta_value = ct_get_object_meta( $automation_id, $meta_key, $single );

    ct_reset_setup_table();

    return $meta_value;

}

/**
 * Get automation triggers
 *
 * @since  1.0.0
 *
 * @param int       $automation_id  The automation ID
 * @param string    $output         The required return type (OBJECT|ARRAY_A|ARRAY_N)
 *
 * @return array                    Array of automation triggers
 */
function automatorwp_get_automation_triggers( $automation_id, $output = OBJECT ) {

    $cache = automatorwp_get_cache( 'automation_triggers', array(), false );

    if( isset( $cache[$automation_id] ) ) {

        // Use triggers already cached
        $triggers = $cache[$automation_id];

    } else {

        // Query the triggers for the first time
        ct_setup_table( 'automatorwp_triggers' );

        $ct_query = new CT_Query( array(
            'automation_id' => $automation_id,
            'orderby' => 'position',
            'order' => 'ASC',
            'items_per_page' => -1,
        ) );

        $triggers = $ct_query->get_results();

        ct_reset_setup_table();

        // Cache triggers
        $cache[$automation_id] = $triggers;

        automatorwp_set_cache( 'automation_triggers', $cache );

    }

    if( $output === ARRAY_N || $output === ARRAY_A ) {

        // Turn array of objects into an array of arrays
        foreach( $triggers as $i => $trigger ) {
            $triggers[$i] = (array) $trigger;
        }

    }

    return $triggers;

}

/**
 * Get automation actions
 *
 * @since  1.0.0
 *
 * @param int       $automation_id  The automation ID
 * @param string    $output         The required return type (OBJECT|ARRAY_A|ARRAY_N)
 *
 * @return array                    Array of automation actions
 */
function automatorwp_get_automation_actions( $automation_id, $output = OBJECT ) {

    $cache = automatorwp_get_cache( 'automation_actions', array(), false );

    if( isset( $cache[$automation_id] ) ) {

        // Use triggers already cached
        $actions = $cache[$automation_id];

    } else {

        // Query the triggers for the first time

        ct_setup_table( 'automatorwp_actions' );

        $ct_query = new CT_Query( array(
            'automation_id' => $automation_id,
            'orderby' => 'position',
            'order' => 'ASC',
            'items_per_page' => -1,
        ) );

        $actions = $ct_query->get_results();

        ct_reset_setup_table();

        // Cache actions
        $cache[$automation_id] = $actions;

        automatorwp_set_cache( 'automation_actions', $cache );

    }

    if( $output === ARRAY_N || $output === ARRAY_A ) {

        // Turn array of objects into an array of arrays
        foreach( $actions as $i => $action ) {
            $actions[$i] = (array) $action;
        }

    }

    return $actions;

}

/**
 * Clone automation
 *
 * @since  1.0.0
 *
 * @param int       $automation_id  The automation ID
 * @param int       $user_id        The user ID to assign to the automation
 *
 * @return int|bool                 Clone result
 */
function automatorwp_clone_automation( $automation_id, $user_id = 0 ) {

    if( $user_id === 0 ) {
        $user_id = get_current_user_id();
    }

    ct_setup_table( 'automatorwp_automations' );

    $automation = ct_get_object( $automation_id );

    // Bail if automation does not exists
    if( ! $automation ) {
        ct_reset_setup_table();
        return false;
    }

    $automation = ( array ) $automation;

    // Setup the new automation info
    unset( $automation['id'] );
    $automation['title'] .= ( ! empty( $automation['title'] ) ? ' ' : '' ) . __( '(Cloned)', 'automatorwp' );
    $automation['user_id'] = $user_id;
    $automation['status'] = 'inactive';
    $automation['date'] = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );

    // Insert the new automation
    $new_automation_id = ct_insert_object( $automation );

    if( ! $new_automation_id ) {
        ct_reset_setup_table();
        return false;
    }

    automatorwp_clone_automation_items( $automation_id, $new_automation_id );

    ct_reset_setup_table();
    return $new_automation_id;

}

/**
 * Clone all automation items to a new one
 *
 * @since  1.0.0
 *
 * @param int       $automation_id      The automation ID
 * @param int       $new_automation_id  The automation ID to clone all the items
 */
function automatorwp_clone_automation_items( $automation_id, $new_automation_id ) {

    global $wpdb;

    $item_types = array( 'trigger', 'action' );
    $ids = array();
    $replacements = array();

    // Migrate all items to the new automation and collect the old and new IDs
    foreach( $item_types as $item_type ) {

        if( $item_type === 'trigger' ) {
            $items = automatorwp_get_automation_triggers( $automation_id );
        } else {
            $items = automatorwp_get_automation_actions( $automation_id );
        }

        ct_setup_table( "automatorwp_{$item_type}s" );

        foreach( $items as $item ) {

            $new_item = ( array ) $item;

            unset( $new_item['id'] );
            $new_item['automation_id'] = $new_automation_id;
            $new_item['date'] = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );

            $new_item_id = ct_insert_object( $new_item );

            // Update old ids and replacements to be used on metas
            if( $new_item_id ) {
                $ids[$item->id] = $new_item_id;
                $replacements['{' . $item->id . ':'] = '{' . $new_item_id . ':';
            }

        }

        ct_reset_setup_table();

    }

    $tags = array_keys( $replacements );

    // Loop again the items to update their metas
    foreach( $item_types as $item_type ) {

        // Do not worry about performance, AutomatorWP already caches this functions
        if( $item_type === 'trigger' ) {
            $items = automatorwp_get_automation_triggers( $automation_id );
        } else {
            $items = automatorwp_get_automation_actions( $automation_id );
        }

        $ct_table = ct_setup_table( "automatorwp_{$item_type}s" );
        $metas = array();

        foreach( $items as $item ) {

            // Skip if item not has been cloned
            if( ! isset( $ids[$item->id] ) ) {
                continue;
            }

            // Get all item metas
            $item_metas = $wpdb->get_results( "SELECT meta_key, meta_value FROM {$ct_table->meta->db->table_name} WHERE id = {$item->id}", ARRAY_A );

            foreach( $item_metas as $i => $item_meta ) {

                $meta_key = $item_metas[$i]['meta_key'];

                /**
                 * Filter to exclude a meta on clone this item
                 * $item_type: trigger | action
                 * $item->type: The trigger or action type
                 * $meta_key: The meta key
                 *
                 * @since  1.0.0
                 *
                 * @param bool $exclude
                 *
                 * @return bool
                 */
                $exclude = apply_filters( "automatorwp_clone_{$item_type}_{$item->type}_meta_{$meta_key}_excluded", false );

                // Skip if meta gets excluded on clone
                if( $exclude ) {
                    continue;
                }

                // Replace metas with old IDs with the new ones
                $meta_value = str_replace( $tags, $replacements, $item_metas[$i]['meta_value'] );

                // Prepare for the upcoming insert
                $metas[] = $wpdb->prepare( '%d, %s, %s', array( $ids[$item->id], $meta_key, $meta_value ) );
            }

            // Update the new item title
            ct_update_object( array(
                'id' => $ids[$item->id],
                'title' => str_replace( $tags, $replacements, $item->title ),
            ) );

        }

        if( count( $metas ) ) {
            $metas = implode( '), (', $metas );

            // Run a single query to insert all metas instead of insert them one-by-one
            $wpdb->query( "INSERT INTO {$ct_table->meta->db->table_name} (id, meta_key, meta_value) VALUES ({$metas})" );
        }

        ct_reset_setup_table();

    }

}

/**
 * Turn an automation into an exportable URL
 *
 * @since  1.0.0
 *
 * @param int $automation_id The automation ID
 *
 * @return string
 */
function automatorwp_get_automation_export_url( $automation_id ) {

    $automation = automatorwp_get_automation_object( $automation_id );

    if( ! $automation ) {
        return '';
    }

    $url = 'wp-admin/admin.php?page=automatorwp_import_automation';

    // Setup the automation args
    // Only include those parameters if they are different from their default value to reduce the URL length
    if( ! empty( $automation->title ) ) {
        $url = add_query_arg( 'ti', $automation->title, $url );
    }

    if( $automation->type !== 'user' ) {
        $url = add_query_arg( 'ty', $automation->type, $url );
    }

    if( absint( $automation->sequential ) !== 0 ) {
        $url = add_query_arg( 's', $automation->sequential, $url );
    }

    if( absint( $automation->times_per_user ) !== 1 ) {
        $url = add_query_arg( 'tu', $automation->times_per_user, $url );
    }

    if( absint( $automation->times ) !== 0 ) {
        $url = add_query_arg( 'tim', $automation->times, $url );
    }

    $url .= automatorwp_get_automation_items_export_url( $automation_id );

    return $url;

}

/**
 * Turn automation items into an exportable URL
 *
 * @since  1.0.0
 *
 * @param int $automation_id The automation ID
 *
 * @return string
 */
function automatorwp_get_automation_items_export_url( $automation_id ) {

    $item_types = array( 'trigger', 'action' );
    $false_url = 'a.php?b=c';
    $url = $false_url;

    // Loop all automation items
    foreach( $item_types as $item_type ) {

        // Get the items
        if( $item_type === 'trigger' ) {
            $items = automatorwp_get_automation_triggers( $automation_id );
        } else {
            $items = automatorwp_get_automation_actions( $automation_id );
        }

        $url_items = array();

        ct_setup_table( "automatorwp_{$item_type}s" );

        foreach( $items as $item ) {

            // Get the type args
            if( $item_type === 'trigger' ) {
                $type_args = automatorwp_get_trigger( $item->type );
            } else {
                $type_args = automatorwp_get_action( $item->type );
            }

            if( ! $type_args ) {
                continue;
            }

            // Setup the item options
            $options = array();

            // Special check for filters
            if( $item->type === 'filter' ) {

                $filter = ct_get_object_meta( $item->id, 'filter', true );

                $filter_args = automatorwp_get_filter( $filter );

                // If filter args found, append the filter options to the type options
                if( $filter_args ) {
                    $type_args['options'] = array_merge( $type_args['options'], $filter_args['options'] );
                }

            }

            foreach( $type_args['options'] as $option => $option_args ) {

                // Skip option if not has fields
                if( ! isset( $option_args['fields'] ) ) {
                    continue;
                }

                foreach( $option_args['fields'] as $field_id => $field_args ) {

                    /**
                     * Filter to exclude a meta on export this item through URL
                     * $item_type: trigger | action
                     * $item->type: The trigger or action type
                     * $field_id: The meta key
                     *
                     * @since  1.0.0
                     *
                     * @param bool $exclude
                     *
                     * @return bool
                     */
                    $exclude = apply_filters( "automatorwp_export_url_{$item_type}_{$item->type}_meta_{$field_id}_excluded", false );

                    // Skip if meta gets excluded on export through URL
                    if( $exclude ) {
                        continue;
                    }

                    $field_value = ct_get_object_meta( $item->id, $field_id, true );

                    // Skip options with
                    if( isset( $field_args['default'] ) && $field_args['default'] == $field_value ) {
                        continue;
                    }

                    // Skip if no value entered
                    if( empty( $field_value ) ) {
                        continue;
                    }

                    $options[$field_id] = urlencode( maybe_serialize( $field_value ) );

                }

            }

            $url_items[] = array(
                'i' => $item->id,
                't' => $item->type,
                'o' => $options,
            );

        }

        ct_reset_setup_table();

        // Add the items to the URL
        if( $item_type === 'trigger' ) {
            $prefix = 't';
        } else {
            $prefix = 'a';
        }

        // Pull all url items to reduce the URL length
        $url = automatorwp_pull_array_for_export_url( $url_items, $url, $prefix );

    }

    // Remove the false URL part
    $url = str_replace( $false_url, '', $url );

    return $url;

}

/**
 * Helper function to pull array elements to an exportable URL
 *
 * @since  1.0.0
 *
 * @param array     $array
 * @param string    $url
 * @param string    $prefix
 *
 * @return string
 */
function automatorwp_pull_array_for_export_url( $array, $url, $prefix = '' ) {

    foreach( $array as $key => $value ) {

        if( is_array( $value ) ) {

            $url = automatorwp_pull_array_for_export_url( $value, $url, "{$prefix}-{$key}" );

        } else {
            $url = add_query_arg( "{$prefix}-{$key}", $value, $url );
        }

    }

    return $url;

}

/**
 * Helper function to get array elements from an exportable URL
 *
 * @since  1.0.0
 *
 * @param array     $request
 * @param string    $prefix
 *
 * @return array
 */
function automatorwp_get_array_from_export_url( $request, $prefix = '' ) {

    $params = array();
    $params[$prefix] = array();

    foreach( $request as $key => $value ) {

        // Skip if not starts with the required prefix
        if( ! automatorwp_starts_with( $key, "{$prefix}-" ) ) {
            continue;
        }

        $keys = explode( '-', $key );
        $length = count( $keys );

        $array = &$params[$prefix];

        foreach ( $keys as $i => $sub_key ) {

            // Skip the first sub key
            if( $i === 0 ) {
                continue;
            }

            // Create the sub key
            if( ! isset( $array[$sub_key] )  ) {
                $array[$sub_key] = array();
            }

            // If is the last sub key, assign the original value
            if( $i === ( $length - 1 ) ) {
                $array[$sub_key] = $value;
            }

            $array = &$array[$sub_key];

        }

    }

    return $params[$prefix];

}