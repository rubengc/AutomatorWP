<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\Ajax_Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add an automation trigger/action through ajax
 *
 * @since   1.0.0
 */
function automatorwp_ajax_add_automation_item() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    // Sanitize parameters
    $automation_id = absint( $_POST['automation_id'] );
    $type = sanitize_text_field( $_POST['type'] );              // The trigger or action reference
    $item_type = sanitize_text_field( $_POST['item_type'] );    // 'trigger' or 'action'
    $position = absint( $_POST['position'] );

    // Check parameters
    if( $automation_id === 0 ) {
        wp_send_json_error( __( 'Invalid automation.', 'automatorwp' ) );
    }

    $automation = automatorwp_get_automation_object( $automation_id );

    if( ! $automation ) {
        wp_send_json_error( __( 'Invalid automation.', 'automatorwp' ) );
    }

    if( ! in_array( $item_type, array( 'trigger', 'action' ) ) ) {
        wp_send_json_error( __( 'Invalid item type.', 'automatorwp' ) );
    }

    ct_setup_table( "automatorwp_{$item_type}s" );

    if( $item_type === 'trigger' ) {
        $type_args = automatorwp_get_trigger( $type );
    } else if( $item_type === 'action' ) {
        $type_args = automatorwp_get_action( $type );
    }

    if( ! $type_args ) {
        if( $item_type === 'trigger' ) {
            wp_send_json_error( __( 'Invalid trigger.', 'automatorwp' ) );
        } else if( $item_type === 'action' ) {
            wp_send_json_error( __( 'Invalid action.', 'automatorwp' ) );
        }
    }

    $object = array(
        'automation_id' => $automation_id,
        'title' => '',
        'type' => $type,
        'status' => 'active',
        'position' => $position,
        'date' => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
    );

    // Create the new trigger
    $id = ct_insert_object( $object );

    // Initialize trigger options
    if( $id ) {

        // Append the id to the trigger data
        $object['id'] = $id;

        // Loop all options looking for their fields
        foreach( $type_args['options'] as $option => $args ) {

            // Loop all option fields to initialize them
            foreach( $args['fields'] as $field_id => $field_args ) {

                if( isset( $field_args['default'] ) ) {
                    ct_update_object_meta( $id, $field_id, $field_args['default'] );
                }

            }

        }

        // Update the trigger title
        ct_update_object( array(
            'id' => $id,
            'title' => automatorwp_parse_automation_item_edit_label( (object) $object, $item_type, 'view' )
        ) );
    }

    ct_reset_setup_table();

    // Flush cache to ensure that option replacement gets the newest value
    wp_cache_flush();

    if( $id ) {

        // Append the trigger html
        ob_start();
        automatorwp_automation_item_edit_html( (object) $object, $item_type, $automation );
        $edit_html = ob_get_clean();

        $tags_html = '';

        // Setup the tags html
        if( $item_type === 'trigger' ) {

            // Get the trigger tags
            $tags = automatorwp_get_trigger_tags( (object) $object );

            if( ! empty( $tags ) && isset( $tags[$id] ) ) {
                $tags_html = automatorwp_get_tags_selector_group_html( $id, $tags[$id] );
            }

        }

        // Send back a successful response
        wp_send_json_success( array(
            $item_type => $object,
            'edit_html' => $edit_html,
            'tags_html' => $tags_html,
        ) );
    } else {
        if( $item_type === 'trigger' ) {
            wp_send_json_error( __( 'Trigger can\'t be created.', 'automatorwp' ) );
        } else if( $item_type === 'action' ) {
            wp_send_json_error( __( 'Action can\'t be created.', 'automatorwp' ) );
        }
    }

}
add_action( 'wp_ajax_automatorwp_add_automation_item', 'automatorwp_ajax_add_automation_item' );

/**
 * Delete an automation trigger/action through ajax
 *
 * @since   1.0.0
 */
function automatorwp_ajax_delete_automation_item() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    // Sanitize parameters
    $id = absint( $_POST['id'] );
    $item_type = sanitize_text_field( $_POST['item_type'] );    // 'trigger' or 'action'

    // Check parameters
    if( $id === 0 ) {
        wp_send_json_error( __( 'Invalid item.', 'automatorwp' ) );
    }

    if( ! in_array( $item_type, array( 'trigger', 'action' ) ) ) {
        wp_send_json_error( __( 'Invalid item type.', 'automatorwp' ) );
    }

    ct_setup_table( "automatorwp_{$item_type}s" );

    ct_delete_object( $id );

    ct_reset_setup_table();

    // Send back a successful response
    wp_send_json_success( __( 'Item deleted.', 'automatorwp' ) );
}
add_action( 'wp_ajax_automatorwp_delete_automation_item', 'automatorwp_ajax_delete_automation_item' );

/**
 * Update automation triggers/actions order through ajax
 *
 * @since   1.0.0
 */
function automatorwp_ajax_update_automation_items_order() {

    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    // Sanitize parameters
    $items_order = $_POST['items_order']; // Sanitized before
    $item_type = sanitize_text_field( $_POST['item_type'] );    // 'trigger' or 'action'

    // Check parameters
    if( ! is_array( $items_order ) ) {
        wp_send_json_error( __( 'Invalid items.', 'automatorwp' ) );
    }

    if( empty( $items_order ) ) {
        wp_send_json_error( __( 'Invalid items.', 'automatorwp' ) );
    }

    if( ! in_array( $item_type, array( 'trigger', 'action' ) ) ) {
        wp_send_json_error( __( 'Invalid item type.', 'automatorwp' ) );
    }

    ct_setup_table( "automatorwp_{$item_type}s" );

    foreach( $items_order as $id => $position ) {

        // Sanitize id and position
        $id = absint( $id );
        $position = absint( $position );

        // Skip if not ID provided
        if( $id === 0 ) {
            continue;
        }

        // Update item position
        ct_update_object( array(
            'id' => $id,
            'position' => $position,
        ) );
    }

    ct_reset_setup_table();

    // Send back a successful response
    wp_send_json_error( __( 'Items order updated.', 'automatorwp' ) );

}
add_action( 'wp_ajax_automatorwp_update_automation_items_order', 'automatorwp_ajax_update_automation_items_order' );

/**
 * Update a trigger option through ajax
 *
 * @since   1.0.0
 */
function automatorwp_ajax_update_item_option() {

    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    // Sanitize parameters
    $id = absint( $_POST['id'] );
    $item_type = sanitize_text_field( $_POST['item_type'] );
    $option = sanitize_text_field( $_POST['option_name'] );

    // Check parameters
    if( $id === 0 ) {
        wp_send_json_error( __( 'Invalid trigger.', 'automatorwp' ) );
    }

    if( ! in_array( $item_type, array( 'trigger', 'action' ) ) ) {
        wp_send_json_error( __( 'Invalid item type.', 'automatorwp' ) );
    }

    if( $item_type === 'trigger' ) {
        $automation = automatorwp_get_trigger_automation( $id );
    } else if( $item_type === 'action' ) {
        $automation = automatorwp_get_action_automation( $id );
    }

    ct_setup_table( "automatorwp_{$item_type}s" );

    // Check object
    $object = ct_get_object( $id );

    ct_reset_setup_table();

    if( ! $object ) {
        wp_send_json_error( __( 'Invalid item.', 'automatorwp' ) );
    }

    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    // Check if trigger is registered
    if( ! $type_args ) {
        wp_send_json_error( __( 'Invalid item.', 'automatorwp' ) );
    }

    // Get the option form
    $cmb2 = automatorwp_get_automation_item_option_form( $object, $item_type, $option, $automation );

    if( ! $cmb2 ) {
        wp_send_json_error( __( 'Invalid option.', 'automatorwp' ) );
    }

    // Get option sanitized values
    $sanitized_values = $cmb2->get_sanitized_values( $_POST );

    // Fields not in sanitized values need to recover its default value
    foreach ( $type_args['options'][$option]['fields'] as $field_id => $field ) {

        // Skip fields in sanitized values
        if( isset( $sanitized_values[$field_id] ) ) {
            continue;
        }

        // If field has a default value, add it to the array of sanitize values
        if( isset( $field['default'] ) ) {
            $sanitized_values[$field_id] = sanitize_text_field( $field['default'] );
        }

    }

    // Field groups requires a custom way to handle their values saving
    foreach ( $type_args['options'][$option]['fields'] as $field_id => $field ) {

        // Skip fields that are not group
        if( $field['type'] !== 'group' ) {
            continue;
        }

        // Skip group fields that have not been received any value
        if( ! isset( $_POST[$field_id] ) ) {
            continue;
        }

        if( ! is_array( $_POST[$field_id] ) ) {
            continue;
        }

        $sanitized_values[$field_id] = array();

        // Group fields are correctly setup on $_POST and needs get sanitized
        foreach( $_POST[$field_id] as $i => $field_values ) {

            $values = array();

            foreach( $field_values as $field_group_id => $value ) {

                // Skip if this field is not registered
                if( ! isset( $field['fields'][$field_group_id] ) ) {
                    continue;
                }

                if( ! empty( $value ) ) {
                    // Add the value from $_POST
                    $values[$field_group_id] = sanitize_text_field( $value );
                } else if( isset( $field['fields'][$field_group_id]['default'] ) ) {
                    // If field has a default value, add it to the array of sanitize values
                    $values[$field_group_id] = sanitize_text_field( $field['fields'][$field_group_id]['default']  );
                }
            }

            $sanitized_values[$field_id][] = $values;

        }

    }

    // Setup the table here to ensure to store meta data on the correct table
    ct_setup_table( "automatorwp_{$item_type}s" );

    foreach( $sanitized_values as $field_id => $value ) {
        ct_update_object_meta( $object->id, $field_id, $value );
    }

    // Flush cache to ensure that option replacement gets the newest value
    wp_cache_flush();

    // Update the trigger title
    ct_update_object( array(
        'id' => $id,
        'title' => automatorwp_parse_automation_item_edit_label( $object, $item_type, 'view' )
    ) );

    ct_reset_setup_table();

    $tags_html = '';

    // Setup the tags html
    if( $item_type === 'trigger' ) {
        // Get the trigger tags
        $tags = automatorwp_get_trigger_tags( (object) $object );

        if( ! empty( $tags ) && isset( $tags[$id] ) ) {
            $tags_html = automatorwp_get_tags_selector_group_html( $id, $tags[$id] );
        }
    }

    wp_send_json_success( array(
        'edit_html' => automatorwp_parse_automation_item_edit_label( (object) $object, $item_type ),
        'tags_html' => $tags_html
    ) );

}
add_action( 'wp_ajax_automatorwp_update_item_option', 'automatorwp_ajax_update_item_option' );

/**
 * AJAX Helper for selecting posts
 *
 * @since 1.0.0
 */
function automatorwp_ajax_get_posts() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    // Setup where conditions (initialized with 1=1)
    $where = '1=1';

    // Post type conditional
    $post_type = ( isset( $_REQUEST['post_type'] ) && ! empty( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : array( 'post', 'page' ) );

    // Add support to the post_type_cb parameter (used on post selector fields)
    if( isset( $_REQUEST['post_type_cb'] ) && ! empty( $_REQUEST['post_type_cb'] ) ) {

        $post_type_cb = sanitize_text_field( $_REQUEST['post_type_cb'] );

        if( is_callable( $post_type_cb ) ) {
            $post_type = call_user_func( $post_type_cb );
        }

    }

    if ( is_array( $post_type ) ) {

        // Support for any post type
        if( ! in_array( 'any', $post_type ) ) {

            // Sanitize all post types given
            foreach( $post_type as $i => $value ) {
                $post_type[$i] = sanitize_text_field( $value );
            }

            $where .= sprintf( ' AND p.post_type IN(\'%s\')', implode( "','", $post_type ) );

        }

    } else {

        // Support for any post type
        if( $post_type !== 'any' ) {

            // Sanitize the post type
            $post_type = sanitize_text_field( $post_type );

            $where .= sprintf( ' AND p.post_type = \'%s\'', $post_type );
        }
    }

    // Post title conditional
    $where .= " AND p.post_title LIKE %s";

    // Post status conditional
    $where .= " AND p.post_status NOT IN ( 'pending', 'draft' )";

    // Check for trigger type extra conditionals
    if( isset( $_REQUEST['trigger_type'] ) ) {

        $query_args = array();
        $trigger_type = sanitize_text_field( $_REQUEST['trigger_type'] );

        /**
         * Specific activity triggers query args (used on requirements UI and on ajax get posts)
         * Note: Use $_REQUEST for all given parameters
         *
         * @since  1.0.0
         *
         * @param array|string 	$query_args
         * @param string 		$trigger_type
         *
         * @return array|string
         */
        $query_args = apply_filters( 'automatorwp_specific_trigger_type_query_args', $query_args, $trigger_type );

        if( ! empty( $query_args ) ) {

            if( is_array( $query_args ) ) {
                // If is an array of conditionals, then build the new conditionals
                foreach( $query_args as $field => $value ) {
                    $where .= " AND p.{$field} = '$value'";
                }
            } else {
                // Leave an extra space if query args doesn't have one
                $where .= ' ' . $query_args;
            }

        }

    }

    /**
     * Ajax posts query args (used on almost every post selector)
     * Note: Use $_REQUEST for all given parameters
     *
     * @since  1.0.0
     *
     * @param string $query_args
     *
     * @return array|string
     */
    $extra_query_args = apply_filters( 'automatorwp_ajax_get_posts_query_args', '' );

    // Check for extra conditionals
    if( ! empty( $extra_query_args ) ) {

        if( is_array( $extra_query_args ) ) {
            // If is an array of conditionals, then build the new conditionals
            foreach( $extra_query_args as $field => $value ) {
                $where .= " AND p.{$field} = '$value'";
            }
        } else {
            // Leave an extra space if extra query args doesn't have one
            $where .= ' ' . $extra_query_args;
        }

    }

    // Setup from (from is filtered to allow joins)
    $from = "{$wpdb->posts} AS p";

    /**
     * Ajax posts from (used on almost every post selector)
     * Note: Use $_REQUEST for all given parameters
     *
     * @since  1.0.0
     *
     * @param string $from By default '{$wpdb->posts} AS p'
     *
     * @return string
     */
    $from = apply_filters( 'automatorwp_ajax_get_posts_from', $from );

    /**
     * Ajax posts where (used on almost every post selector)
     * Note: Use $_REQUEST for all given parameters
     *
     * @since  1.0.0
     *
     * @param string $where Contains all wheres
     *
     * @return string
     */
    $where = apply_filters( 'automatorwp_ajax_get_posts_where', $where );

    // Setup order by
    $order_by = "p.post_type ASC, p.menu_order DESC";

    /**
     * Ajax posts order by (used on almost every post selector)
     * Note: Use $_REQUEST for all given parameters
     *
     * @since  1.0.0
     *
     * @param string $order_by By default 'p.post_type ASC, p.menu_order DESC'
     *
     * @return string
     */
    $order_by = apply_filters( 'automatorwp_ajax_get_posts_order_by', $order_by );

    // Pagination args
    $page = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
    $limit = 20;
    $offset = $limit * ( $page - 1 );

    // On this query, keep $wpdb->posts to get current site posts
    $results = $wpdb->get_results( $wpdb->prepare(
        "SELECT p.ID, p.post_title, p.post_type
         FROM {$from}
         WHERE {$where}
         ORDER BY {$order_by}
         LIMIT {$offset}, {$limit}",
        "%%{$search}%%"
    ) );

    $count = absint( $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$from} WHERE {$where}",
        "%%{$search}%%"
    ) ) );

    /**
     * Ajax posts results (used on almost every post selector)
     * Note: Use $_REQUEST for all given parameters
     *
     * @since  1.0.0
     *
     * @param array $results
     *
     * @return array
     */
    $results = apply_filters( 'automatorwp_ajax_get_posts_results', $results );

    $response = array(
        'results' => $results,
        'more_results' => $count > $limit && $count > $offset,
    );

    // Return our results
    wp_send_json_success( $response );

}
add_action( 'wp_ajax_automatorwp_get_posts', 'automatorwp_ajax_get_posts' );

/**
 * Parse the posts results to prepend custom options
 *
 * @since 1.0.0
 *
 * @param array $results
 *
 * @return array
 */
function automatorwp_ajax_parse_posts_results( $results ) {
    return automatorwp_ajax_parse_extra_options( $results, 'ID', 'post_title' );
}
add_filter( 'automatorwp_ajax_get_posts_results', 'automatorwp_ajax_parse_posts_results' );

/**
 * AJAX Helper for selecting terms
 *
 * @since 1.0.0
 */
function automatorwp_ajax_get_terms() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? sanitize_text_field( $_REQUEST['q'] ) : '';

    // Taxonomy conditional
    $taxonomy = ( isset( $_REQUEST['taxonomy'] ) && ! empty( $_REQUEST['taxonomy'] ) ? sanitize_text_field( $_REQUEST['taxonomy'] ) : 'category' );

    // Pagination args
    $page = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
    $limit = 20;
    $offset = $limit * ( $page - 1 );

    $term_query = new WP_Term_Query( array(
        'search'                => $search,
        'taxonomy'              => $taxonomy,
        'hide_empty'            => false,
        'number'                => $limit,
        'offset'                => $offset,
    ) );

    $results = $term_query->get_terms();

    $count_term_query = new WP_Term_Query( array(
        'fields'                => 'count',
        'taxonomy'              => $taxonomy,
        'hide_empty'            => false,
    ) );

    $count = $count_term_query->get_terms();

    /**
     * Ajax posts results (used on almost every post selector)
     * Note: Use $_REQUEST for all given parameters
     *
     * @since  1.0.0
     *
     * @param array $results
     *
     * @return array
     */
    $results = apply_filters( 'automatorwp_ajax_get_terms_results', $results );

    $response = array(
        'results' => $results,
        'more_results' => $count > $limit && $count > $offset,
    );

    // Return our results
    wp_send_json_success( $response );

}
add_action( 'wp_ajax_automatorwp_get_terms', 'automatorwp_ajax_get_terms' );

/**
 * Parse the terms results to prepend custom options
 *
 * @since 1.0.0
 *
 * @param array $results
 *
 * @return array
 */
function automatorwp_ajax_parse_terms_results( $results ) {
    return automatorwp_ajax_parse_extra_options( $results, 'term_id', 'name' );
}
add_filter( 'automatorwp_ajax_get_terms_results', 'automatorwp_ajax_parse_terms_results' );

/**
 * AJAX Helper for selecting users
 *
 * @since 1.0.0
 */
function automatorwp_ajax_get_users() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    // If no word query sent, initialize it
    if ( ! isset( $_REQUEST['q'] ) ) {
        $_REQUEST['q'] = '';
    }

    global $wpdb;

    // Pull back the search string
    $search = esc_sql( $wpdb->esc_like( $_REQUEST['q'] ) );
    $where = '';

    if ( ! empty( $search ) ) {
        $where = " WHERE user_login LIKE '%{$search}%'";
        $where .= " OR user_email LIKE '%{$search}%'";
        $where .= " OR display_name LIKE '%{$search}%'";
    }

    // Pagination args
    $page = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
    $limit = 20;
    $offset = $limit * ( $page - 1 );

    // Fetch our results (store as associative array)
    $results = $wpdb->get_results(
        "SELECT ID, user_login, user_email, display_name
		 FROM {$wpdb->users}
		 {$where}
		 LIMIT {$offset}, {$limit}",
        'ARRAY_A'
    );

    $count = $wpdb->get_var(
        "SELECT COUNT(*)
		 FROM {$wpdb->users}
		 {$where}"
    );

    $response = array(
        'results' => $results,
        'more_results' => absint( $count ) > $offset,
    );

    // Return our results
    wp_send_json_success( $response );
}
add_action( 'wp_ajax_automatorwp_get_users', 'automatorwp_ajax_get_users' );

/**
 * AJAX Helper for selecting objects
 *
 * @since 1.0.0
 */
function automatorwp_ajax_get_objects() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $ct_registered_tables;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? sanitize_text_field( $_REQUEST['q'] ) : '';

    // Table conditional
    $table = ( isset( $_REQUEST['table'] ) ? sanitize_text_field( $_REQUEST['table'] ) : '' );

    if( ! isset( $ct_registered_tables[$table] ) ) {
        wp_send_json_error( __( 'Table not found.', 'automatorwp' ) );
    }

    $ct_table = ct_setup_table( $table );

    // Pagination args
    $page = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
    $limit = 20;
    $offset = $limit * ( $page - 1 );

    $ct_query_args = array(
        's' => $search,
        'items_per_page' => $limit,
        'page' => $page,
    );

    /**
     * Ajax objects query parameters (used on almost every object selector)
     * Note: Use $_REQUEST for all given parameters
     *
     * @since  1.0.0
     *
     * @param array $ct_query_args
     * @param CT_Table $ct_table
     *
     * @return array
     */
    $ct_query_args = apply_filters( 'automatorwp_ajax_get_objects_query_args', $ct_query_args, $ct_table );

    $ct_query = new CT_Query( $ct_query_args );

    $results = $ct_query->get_results();
    $count = $ct_query->found_results;

    /**
     * Ajax objects results (used on almost every object selector)
     * Note: Use $_REQUEST for all given parameters
     *
     * @since  1.0.0
     *
     * @param array $results
     * @param CT_Table $ct_table
     *
     * @return array Should return elements with an array of array( 'id' => 1, 'text' => '' )
     */
    $results = apply_filters( 'automatorwp_ajax_get_objects_results', $results, $ct_table );

    ct_reset_setup_table();

    $response = array(
        'results' => $results,
        'more_results' => $count > $limit && $count > $offset,
    );

    // Return our results
    wp_send_json_success( $response );

}
add_action( 'wp_ajax_automatorwp_get_objects', 'automatorwp_ajax_get_objects' );

/**
 * Parse automations objects results
 *
 * @since  1.0.0
 *
 * @param array $results
 * @param CT_Table $ct_table
 *
 * @return array
 */
function automatorwp_ajax_get_automations_results( $results, $ct_table ) {

    if( $ct_table->name !== 'automatorwp_automations' ) {
        return $results;
    }

    $new_results = array();

    foreach( $results as $object ) {

        $title = ( ! empty( $object->title ) ? $object->title : __( '(No title)', 'automatorwp' ) );

        $new_results[] = array(
            'id' => $object->id,
            'text' => $title,
        );
    }

    return $new_results;
}
add_filter( 'automatorwp_ajax_get_objects_results', 'automatorwp_ajax_get_automations_results', 10, 2 );

/**
 * Parse the objects results to prepend custom options
 *
 * @since 1.0.0
 *
 * @param array $results
 *
 * @return array
 */
function automatorwp_ajax_parse_objects_results( $results ) {
    return automatorwp_ajax_parse_extra_options( $results );
}
add_filter( 'automatorwp_ajax_get_objects_results', 'automatorwp_ajax_parse_objects_results', 11 );

/**
 * Helper function to prepend the option none to the ajax results
 *
 * @since 1.0.0
 *
 * @deprecated use automatorwp_ajax_parse_extra_options() instead
 * Note: Keep for backward compatibility
 *
 * @param array $results
 *
 * @return array
 */
function automatorwp_ajax_get_ajax_results_option_none( $results ) {
    return automatorwp_ajax_parse_extra_options( $results );
}

/**
 * Helper function to prepend extra options to the ajax results
 *
 * @since 1.0.0
 *
 * @param array $results
 * @param string $id_key
 * @param string $text_key
 *
 * @return array
 */
function automatorwp_ajax_parse_extra_options( $results, $id_key = 'id', $text_key = 'text' ) {

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';
    $page = isset( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;

    // Option none
    $option_none = isset( $_REQUEST['option_none'] ) ? absint( $_REQUEST['option_none'] ) : 0;
    $option_none_value = isset( $_REQUEST['option_none_value'] ) ? sanitize_text_field( $_REQUEST['option_none_value'] ) : '';
    $option_none_label = isset( $_REQUEST['option_none_label'] ) ? sanitize_text_field( $_REQUEST['option_none_label'] ) : '';

    if( $option_none && ! empty( $option_none_value ) && ! empty( $option_none_label ) ) {

        if( ( $page === 1 && empty( $search ) )             // Prepend option none if is first page
            || ( strpos( $option_none_label , $search ) )   // Prepend if search matches option none label
        ) {
            array_unshift( $results, array( $id_key => $option_none_value, $text_key => $option_none_label ) );
        }

    }

    // Option custom
    $option_custom = isset( $_REQUEST['option_custom'] ) ? absint( $_REQUEST['option_custom'] ) : 0;
    $option_custom_value = isset( $_REQUEST['option_custom_value'] ) ? sanitize_text_field( $_REQUEST['option_custom_value'] ) : '';
    $option_custom_label = isset( $_REQUEST['option_custom_label'] ) ? sanitize_text_field( $_REQUEST['option_custom_label'] ) : '';

    if( $option_custom && ! empty( $option_custom_value ) && ! empty( $option_custom_label ) ) {

        if( ( $page === 1 && empty( $search ) )                 // Prepend option custom if is first page
            || ( strpos( $option_custom_label , $search ) )     // Prepend if search matches option custom label
        ) {
            array_unshift( $results, array( $id_key => $option_custom_value, $text_key => $option_custom_label ) );
        }

    }

    return $results;

}