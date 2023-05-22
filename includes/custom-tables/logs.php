<?php
/**
 * Logs
 *
 * @package     AutomatorWP\Custom_Tables\Logs
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Parse query args for logs
 *
 * @since 1.0.0
 *
 * @param string $where
 * @param CT_Query $ct_query
 *
 * @return string
 */
function automatorwp_logs_query_where( $where, $ct_query ) {

    global $ct_table;

    if( $ct_table->name !== 'automatorwp_logs' ) {
        return $where;
    }

    $table_name = $ct_table->db->table_name;

    // Shorthand
    $qv = $ct_query->query_vars;

    // Type
    $where .= automatorwp_custom_table_where( $qv, 'type', 'type', 'string' );

    // Object ID
    $where .= automatorwp_custom_table_where( $qv, 'object_id', 'object_id', 'integer' );

    // User ID
    $where .= automatorwp_custom_table_where( $qv, 'user_id', 'user_id', 'integer' );

    // Post ID
    $where .= automatorwp_custom_table_where( $qv, 'post_id', 'post_id', 'integer' );

    return $where;
}
add_filter( 'ct_query_where', 'automatorwp_logs_query_where', 10, 2 );

/**
 * Define the search fields for logs
 *
 * @since 1.0.0
 *
 * @param array $search_fields
 *
 * @return array
 */
function automatorwp_logs_search_fields( $search_fields ) {

    $search_fields[] = 'title';

    return $search_fields;

}
add_filter( 'ct_query_automatorwp_logs_search_fields', 'automatorwp_logs_search_fields' );

/**
 * Parse search query args for logs
 *
 * @since 1.0.0
 *
 * @param string $search
 * @param CT_Query $ct_query
 *
 * @return string
 */
function automatorwp_logs_query_search( $search, $ct_query ) {

    global $ct_table, $wpdb;

    if( $ct_table->name !== 'automatorwp_logs' ) {
        return $search;
    }

    $table_name = $ct_table->db->table_name;

    // Shorthand
    $qv = $ct_query->query_vars;

    // Check if is search and query is not filtered by an specific user
    if( isset( $qv['s'] ) && ! empty( $qv['s'] ) && ! isset( $qv['user_id'] ) ) {

        // Made a user sub-search to retrieve them
        $users = get_users( array(
            'search' => sprintf( '*%s*', $qv['s'] ),
            'search_columns' => array(
                'user_login',
                'user_email',
                'display_name',
            ),
            'fields' => 'ID',
        ) );

        if( ! empty( $users ) ) {
            $search .= " AND ( {$table_name}.user_id IN (" . implode( ',', array_map( 'absint', $users ) ) . ") )";
        }

    }

    return $search;

}
add_filter( 'ct_query_search', 'automatorwp_logs_query_search', 10, 2 );

/**
 * Define the field for logs views
 *
 * @since 1.0.0
 *
 * @param string $field_id
 *
 * @return string
 */
function automatorwp_logs_views_field( $field_id = '' ) {
    return 'type';
}
add_filter( 'ct_list_automatorwp_logs_views_field', 'automatorwp_logs_views_field' );

/**
 * Define the field labels for logs views
 *
 * @since 1.0.0
 *
 * @param array $field_labels
 *
 * @return array
 */
function automatorwp_logs_views_field_labels( $field_labels = array() ) {
    return automatorwp_get_log_types();
}
add_filter( 'ct_list_automatorwp_logs_views_field_labels', 'automatorwp_logs_views_field_labels' );

/**
 * Columns for logs list view
 *
 * @since 1.0.0
 *
 * @param array $columns
 *
 * @return array
 */
function automatorwp_manage_logs_columns( $columns = array() ) {

    $columns['title']       = __( 'Title', 'automatorwp' );
    $columns['type']        = __( 'Type', 'automatorwp' );
    $columns['object_id']   = __( 'Automation', 'automatorwp' );
    $columns['user_id']     = __( 'User', 'automatorwp' );
    $columns['post_id']     = __( 'Post', 'automatorwp' );
    $columns['date']        = __( 'Date', 'automatorwp' );

    return $columns;
}
add_filter( 'manage_automatorwp_logs_columns', 'automatorwp_manage_logs_columns' );

/**
 * Sortable columns for logs list view
 *
 * @since 1.0.0
 *
 * @param array $sortable_columns
 *
 * @return array
 */
function automatorwp_manage_logs_sortable_columns( $sortable_columns ) {

    $sortable_columns['title']      = array( 'title', false );
    $sortable_columns['type']       = array( 'type', false );
    $sortable_columns['object_id']  = array( 'object_id', false );
    $sortable_columns['user_id']    = array( 'user_id', false );
    $sortable_columns['post_id']    = array( 'post_id', false );
    $sortable_columns['date']       = array( 'date', true );

    return $sortable_columns;

}
add_filter( 'manage_automatorwp_logs_sortable_columns', 'automatorwp_manage_logs_sortable_columns' );

/**
 * Columns rendering for logs list view
 *
 * @since  1.0.0
 *
 * @param string $column_name
 * @param integer $object_id
 */
function automatorwp_manage_logs_custom_column(  $column_name, $object_id ) {

    // Setup vars
    $log = ct_get_object( $object_id );

    switch( $column_name ) {
        case 'title':

            automatorwp_get_log_integration_icon( $log );

            $title = ! empty( $log->title ) ? $log->title : __( '(No title)', 'automatorwp' ); ?>
            <strong><a href="<?php echo ct_get_edit_link( 'automatorwp_logs', $log->id ); ?>"><?php echo esc_html( $title ); ?></a></strong>
            <?php

            break;
        case 'type':
            $types = automatorwp_get_log_types();
            $type = isset( $types[$log->type] ) ? $types[$log->type] : $log->type;
            ?>

            <span class="automatorwp-log-type automatorwp-log-type-<?php echo esc_attr( $log->type ); ?>"><?php echo esc_html( $type ); ?></span>

            <?php

            break;
        case 'object_id':

            $automation_id = false;

            switch ( $log->type ) {
                case 'automation':
                case 'anonymous':
                    $automation_id = $log->object_id;
                    break;
                case 'trigger':
                    $trigger = automatorwp_get_trigger_object( $log->object_id );

                    if( $trigger ) {
                        $automation_id = $trigger->automation_id;
                    }
                    break;
                case 'action':
                    $action = automatorwp_get_action_object( $log->object_id );

                    if( $action ) {
                        $automation_id = $action->automation_id;
                    }
                    break;
                case 'filter':
                    $item_type = automatorwp_get_log_meta( $log->id, 'item_type', true );

                    if( $item_type === 'trigger' ) {
                        $object = automatorwp_get_trigger_object( $log->object_id );
                    } else if( $item_type === 'action' ) {
                        $object = automatorwp_get_action_object( $log->object_id );
                    }

                    if( $object ) {
                        $automation_id = $object->automation_id;
                    }
                    break;

            }

            $automation = automatorwp_get_automation_object( $automation_id );

            if( $automation ) {

                $title = ! empty( $automation->title ) ? $automation->title : __( '(No title)', 'automatorwp' ); ?>
                <a href="<?php echo ct_get_edit_link( 'automatorwp_automations', $automation->id ); ?>"><?php echo esc_html( $title ); ?></a>
                <?php

            } else {
                echo '&nbsp;';
            }
            break;
        case 'user_id':
            $user = get_userdata( $log->user_id );

            if( $user ) {

                if( current_user_can( 'edit_users' ) ) {
                    ?>

                    <a href="<?php echo get_edit_user_link( $user->ID ); ?>"><?php echo $user->display_name . ' (' . $user->user_login . ')'; ?></a>

                    <?php
                } else {
                    echo $user->display_name;
                }

            } else {
                echo '&nbsp;';
            }
            break;
        case 'post_id':
            $post = get_post( $log->post_id );

            if( $post ) {

                if( current_user_can( 'edit_post', $post->ID ) ) {
                    ?>

                    <a href="<?php echo get_edit_post_link( $post->ID ); ?>"><?php echo $post->post_title; ?></a>

                    <?php
                } else {
                    echo $post->post_title;
                }

            } else {
                echo '&nbsp;';
            }
            break;
        case 'date':
            $date = date( 'Y/m/d', strtotime( $log->date ) );
            $time = date( 'H:i:s', strtotime( $log->date ) );
            ?>

            <span class="automatorwp-log-date"><?php echo $date; ?></span>
            <br>
            <span class="automatorwp-log-time"><?php echo $time; ?></span>

            <?php
            break;
    }
}
add_action( 'manage_automatorwp_logs_custom_column', 'automatorwp_manage_logs_custom_column', 10, 2 );

/**
 * Place the title at top of edit log screen
 *
 * @since  1.0.0
 *
 * @param stdClass $log
 */
function automatorwp_logs_edit_form_top( $log ) {
    global $ct_table;

    if( $ct_table->name !== 'automatorwp_logs' ) {
        return;
    }

    ?>
    <div class="automatorwp-log-title-preview">
        <h1>
            <?php automatorwp_get_log_integration_icon( $log ); ?>
            <?php echo ( ! empty( $log->title ) ? esc_html( $log->title ) : __( '(No title)', 'automatorwp' ) ); ?>
        </h1>
    </div>
    <?php
}
add_action( 'ct_edit_form_top', 'automatorwp_logs_edit_form_top' );

/**
 * Logs meta boxes
 *
 * @since  1.0.0
 */
function automatorwp_add_logs_meta_boxes() {
    remove_meta_box( 'submitdiv', 'automatorwp_logs', 'side' );
    add_meta_box( 'automatorwp_log_details', __( 'Log Details', 'automatorwp' ), 'automatorwp_log_details_meta_box', 'automatorwp_logs', 'side', 'default' );
    add_meta_box( 'automatorwp_log_data', __( 'Log Data', 'automatorwp' ), 'automatorwp_log_data_meta_box', 'automatorwp_logs', 'normal', 'default' );
}
add_action( 'add_meta_boxes', 'automatorwp_add_logs_meta_boxes' );

/**
 * Render the log details meta box
 *
 * @since  1.0.0
 *
 * @param  stdClass $object The current object
 */
function automatorwp_log_details_meta_box( $object = null ) {

    global $ct_table;

    // Setup vars
    $log_types = automatorwp_get_log_types();

    ?>
    <div class="submitbox" id="submitpost" style="margin: -6px -12px -12px;">

        <div id="minor-publishing">

            <div id="misc-publishing-actions">

                <div class="misc-pub-section misc-pub-post-status">
                    <?php echo __( 'Type:', 'automatorwp' ); ?> <span id="post-status-display"><?php echo isset( $log_types[$object->type] ) ? $log_types[$object->type] : esc_html( $object->type ) ; ?></span>
                </div>

                <div class="misc-pub-section misc-pub-post-user" id="user">
                    <?php $user = get_userdata( $object->user_id ); ?>
                    <?php echo __( 'User:', 'automatorwp' ); ?> <span id="post-user-display"><?php
                        if( $user ) {
                            if( current_user_can( 'edit_users' ) ) {
                                ?>

                                <a href="<?php echo get_edit_user_link( $user->ID ); ?>"><?php echo $user->display_name . ' (' . $user->user_login . ')'; ?></a>

                                <?php
                            } else {
                                echo $user->display_name;
                            }
                        } else {
                            echo '';
                        } ?></span>
                </div>

                <div class="misc-pub-section curtime misc-pub-curtime">
                    <span id="timestamp"><?php echo __( 'Date:', 'automatorwp' ); ?> <b><?php echo date( 'Y/m/d H:i:s', strtotime( $object->date ) ); ?></b></span>
                </div>

            </div>

        </div>

        <div id="major-publishing-actions">

            <div id="delete-action">
                <?php
                printf(
                    '<a href="%s" class="submitdelete deletion" onclick="%s" aria-label="%s">%s</a>',
                    ct_get_delete_link( $ct_table->name, $object->id ),
                    "return confirm('" .
                    esc_attr( __( "Are you sure you want to delete this item?\\n\\nClick \\'Cancel\\' to go back, \\'OK\\' to confirm the delete." ) ) .
                    "');",
                    esc_attr( __( 'Delete permanently' ) ),
                    __( 'Delete Permanently' )
                );
                ?>
            </div>

            <div class="clear"></div>

        </div>

    </div>
    <?php
}

/**
 * Render the log data meta box
 *
 * @since  1.0.0
 *
 * @param  stdClass $object The current object
 */
function automatorwp_log_data_meta_box( $object = null ) {

    $log_fields = automatorwp_get_log_fields( $object );

    // Setup the CMB2 form
    $cmb2 = new CMB2( array(
        'id'        => 'automatorwp_logs_form',
        'object_types' => array( 'automatorwp_logs' ),
        'classes'   => 'automatorwp-form automatorwp-log-form',
        'hookup'    => false,
    ), $object->id );

    // Setup the options fields
    foreach ( $log_fields as $field_id => $field ) {

        $field['id'] = $field_id;

        if( $field['type'] !== 'title' && ! isset( $field['before_field'] ) ) {
            $field['before_field'] = 'automatorwp_log_field_cb';
        }

        if( ! isset( $field['attributes'] ) ) {
            $field['attributes'] = array();
        }

        if( $field['type'] !== 'title' ) {
            $field['type'] = 'text';
        }

        $field['attributes']['type'] = 'hidden';

        // Add the field to the form
        $cmb2->add_field( $field );

    }

    // Render the form
    CMB2_Hookup::enqueue_cmb_css();
    CMB2_Hookup::enqueue_cmb_js();
    $cmb2->show_form();

    ?>

    <?php
}

/**
 * Get log fields
 *
 * @since 1.0.0
 *
 * @param stdClass $log The log object
 *
 * @return array
 */
function automatorwp_get_log_fields( $log ) {

    // Setup the log field
    $log_fields = array();
    $object = false;

    switch( $log->type ) {
        case 'trigger':
            $log_fields = array(
                'object_id' => array(
                    'name' => __( 'Trigger:', 'automatorwp' ),
                    'desc' => __( 'Trigger assigned to this log.', 'automatorwp' ),
                    'type' => 'text',
                ),
                'automation' => array(
                    'name' => __( 'Automation:', 'automatorwp' ),
                    'desc' => __( 'Trigger\'s automation.', 'automatorwp' ),
                    'type' => 'text',
                ),
            );

            $object = automatorwp_get_trigger_object( $log->object_id );
            break;
        case 'action':
            $log_fields = array(
                'object_id' => array(
                    'name' => __( 'Action:', 'automatorwp' ),
                    'desc' => __( 'Action assigned to this log.', 'automatorwp' ),
                    'type' => 'text',
                ),
                'automation' => array(
                    'name' => __( 'Automation:', 'automatorwp' ),
                    'desc' => __( 'Action\'s automation.', 'automatorwp' ),
                    'type' => 'text',
                ),
            );

            $object = automatorwp_get_action_object( $log->object_id );
            break;
        case 'filter':
            $log_fields = array(
                'object_id' => array(
                    'name' => __( 'Filter:', 'automatorwp' ),
                    'desc' => __( 'Filter assigned to this log.', 'automatorwp' ),
                    'type' => 'text',
                ),
                'automation' => array(
                    'name' => __( 'Automation:', 'automatorwp' ),
                    'desc' => __( 'Filter\'s automation.', 'automatorwp' ),
                    'type' => 'text',
                ),
            );

            $item_type = automatorwp_get_log_meta( $log->id, 'item_type', true );

            if( $item_type === 'trigger' ) {
                $object = automatorwp_get_trigger_object( $log->object_id );
            } else if( $item_type === 'action' ) {
                $object = automatorwp_get_action_object( $log->object_id );
            }
            break;
        case 'automation':
            $log_fields = array(
                'object_id' => array(
                    'name' => __( 'Automation:', 'automatorwp' ),
                    'desc' => __( 'Automation assigned to this log.', 'automatorwp' ),
                    'type' => 'text',
                ),
            );

            $object = automatorwp_get_automation_object( $log->object_id );
            break;
    }

    $log_fields['post_id'] =  array(
        'name' => __( 'Post:', 'automatorwp' ),
        'desc' => __( 'Post assigned to this log.', 'automatorwp' ),
        'type' => 'text',
    );

    /**
     * Filter to set custom log fields
     *
     * @since 1.0.0
     *
     * @param array     $log_fields The log fields
     * @param stdClass  $log        The log object
     * @param stdClass  $object     The trigger/action/automation object attached to the log
     *
     * @return array
     */
    return apply_filters( 'automatorwp_log_fields', $log_fields, $log, $object );

}

/**
 * Callback used to render action on logs
 *
 * @since 1.0.0
 *
 * @param array         $field_args
 * @param CMB2_Field    $field
 */
function automatorwp_log_field_cb( $field_args, $field ) {

    global $ct_table;

    // Setup vars
    $primary_key = $ct_table->db->primary_key;
    $log_id = absint( $_GET[$primary_key] );
    $log = ct_get_object( $log_id );
    $field_id = $field_args['id'];
    $value = $field->value();

    if( ! is_array( $value ) ) {
        $value = str_replace( '\n', "\n", $value );
        $value = stripslashes_deep( $value );

        $wpautop = ( isset( $field_args['wpautop'] ) ? $field_args['wpautop'] : false );

        if( $wpautop ) {
            $value = wpautop( $value );
        }
    }

    /**
     * Filters the field value display
     *
     * @since 1.0.0
     *
     * @param string        $value      Field value
     * @param array         $field_args Field parameters
     * @param CMB2_Field    $field      Field object
     * @param stdClass      $log        Log object
     *
     * @return string
     */
    $value = apply_filters( "automatorwp_log_{$field_id}_field_value_display", $value, $field_args, $field, $log );

    // Check options_cb parameter
    if( isset( $field_args['options_cb'] ) && is_callable( $field_args['options_cb'] ) ) {
        $field_args['options'] = call_user_func( $field['options_cb'], (object) $field );
    }

    // Check options parameter
    if( isset( $field_args['options'] )
        && is_array( $field_args['options'] )
        && ! is_array( $value )
        && isset( $field_args['options'][$value] ) ) {
        // Set as value the option display instead
        $value =  $field_args['options'][$value];
    }

    // if empty value, set it as a space
    if( empty( $value ) ) {
        $value = '&nbsp;';
    }

    // Check if value is an array
    if( is_array( $value ) ) {
        $value = automatorwp_log_array_display( $value );
    }

    echo $value;

}

/**
 * Setup the display HTML for array values
 *
 * @since 1.0.0
 *
 * @param array $value
 * @param int   $level
 *
 * @return string
 */
function automatorwp_log_array_display( $value, $level = 0 ) {

    // Check if not is an associative array
    if( $level === 0 && array_keys( $value ) === range( 0, count( $value ) - 1 ) && ! is_array( $value[0] ) ) {
        // Implode array values by a comma-separated list
        return implode( ', ', $value );
    }

    $padding_char = '&nbsp&nbsp&nbsp&nbsp';
    $new_value = '';

    // Only place the keys on first level
    if( $level !== 0 ) {
        $new_value .= '[' . '<br>';
    }

    foreach ( $value as $k => $v ) {

        // Add a inner padding per level
        $new_value .= str_repeat( $padding_char, $level );

        if ( is_array( $v ) ) {

            // Check if not is an associative array
            if( array_keys( $v ) === range( 0, count( $v ) - 1 ) && ! is_array( $v[0] ) ) {
                // Implode array values by a comma-separated list
                $new_value .= $k . ': [ ' . implode( ', ', $v ) . ' ]<br>';
            } else {
                // Display all sub arrays
                $new_value .= $k . ': ' . automatorwp_log_array_display( $v, $level + 1 ) . '<br>';
            }

        } else {
            $new_value .= $k . ': ' . $v . '<br>';
        }

    }

    // Only place the keys on first level
    if( $level !== 0 ) {
        // Add a outer padding
        $new_value .= str_repeat( $padding_char, max( $level - 1, 0 ) ) . ']';
    }

    return $new_value;

}

/**
 * Object id field display
 *
 * @since 1.0.0
 *
 * @param string        $value      Field value
 * @param array         $field_args Field parameters
 * @param CMB2_Field    $field      Field object
 * @param stdClass      $log        Log object
 *
 * @return string
 */
function automatorwp_log_object_id_field_display( $value, $field_args, $field, $log ) {

    switch ( $log->type ) {
        case 'trigger':
            $trigger = automatorwp_get_trigger_object( $log->object_id );

            if( $trigger ) {
                $value = $trigger->title;
            }
            break;
        case 'action':
            $action = automatorwp_get_action_object( $log->object_id );

            if( $action ) {
                $value = $action->title;
            }
            break;
        case 'filter':
            $item_type = automatorwp_get_log_meta( $log->id, 'item_type', true );

            if( $item_type === 'trigger' ) {
                $object = automatorwp_get_trigger_object( $log->object_id );
            } else if( $item_type === 'action' ) {
                $object = automatorwp_get_action_object( $log->object_id );
            }

            if( $object ) {
                $value = $object->title;
            }
            break;
        case 'automation':
            $automation = automatorwp_get_automation_object( $log->object_id );

            if( $automation ) {
                $title = ! empty( $automation->title ) ? $automation->title : __( '(No title)', 'automatorwp' );
                $value = '<a href="' . ct_get_edit_link( 'automatorwp_automations', $automation->id ) . '">' . esc_html( $title ) . '</a>';
            }
            break;
    }

    return $value;

}
add_filter( 'automatorwp_log_object_id_field_value_display', 'automatorwp_log_object_id_field_display', 10, 4 );

/**
 * Post id field display
 *
 * @since 1.0.0
 *
 * @param string        $value      Field value
 * @param array         $field_args Field parameters
 * @param CMB2_Field    $field      Field object
 * @param stdClass      $log        Log object
 *
 * @return string
 */
function automatorwp_log_post_id_field_display( $value, $field_args, $field, $log ) {

    $post = get_post( $value );

    if( $post ) {

        if( current_user_can( 'edit_post', $post->ID ) ) {
            $value = '<a href="' . get_edit_post_link( $post->ID ) . '">' . $post->post_title . '</a>';
        } else {
            $value = $post->post_title;
        }

    } else {
        $value = __( '(No post assigned)', 'automatorwp' );
    }

    return $value;

}
add_filter( 'automatorwp_log_post_id_field_value_display', 'automatorwp_log_post_id_field_display', 10, 4 );

/**
 * Automation field display
 *
 * @since 1.0.0
 *
 * @param string        $value      Field value
 * @param array         $field_args Field parameters
 * @param CMB2_Field    $field      Field object
 * @param stdClass      $log        Log object
 *
 * @return string
 */
function automatorwp_log_automation_field_display( $value, $field_args, $field, $log ) {

    $automation_id = false;

    switch ( $log->type ) {
        case 'trigger':
            $trigger = automatorwp_get_trigger_object( $log->object_id );

            if( $trigger ) {
                $automation_id = $trigger->automation_id;
            }
            break;
        case 'action':
            $action = automatorwp_get_action_object( $log->object_id );

            if( $action ) {
                $automation_id = $action->automation_id;
            }
            break;
        case 'filter':
            $item_type = automatorwp_get_log_meta( $log->id, 'item_type', true );

            if( $item_type === 'trigger' ) {
                $object = automatorwp_get_trigger_object( $log->object_id );
            } else if( $item_type === 'action' ) {
                $object = automatorwp_get_action_object( $log->object_id );
            }

            if( $object ) {
                $automation_id = $object->automation_id;
            }
            break;
        case 'automation':
            $automation_id = $log->object_id;
            break;
    }

    $automation = automatorwp_get_automation_object( $automation_id );

    if( $automation ) {

        $title = ! empty( $automation->title ) ? $automation->title : __( '(No title)', 'automatorwp' );
        $value = '<a href="' . ct_get_edit_link( 'automatorwp_automations', $automation_id ) . '">' . esc_html( $title ) . '</a>';

    }

    return $value;

}
add_filter( 'automatorwp_log_automation_field_value_display', 'automatorwp_log_automation_field_display', 10, 4 );

/**
 * Display the "Clear all logs" button
 *
 * @since 1.0.0
 *
 * @param array $views
 *
 * @return array
 */
function automatorwp_logs_clear_all_logs_button( $views ) {

    global $ct_table;

    if( $ct_table->name !== 'automatorwp_logs' ) {
        return $views;
    }

    $url = $ct_table->views->list->get_link();
    $url = add_query_arg( array( 'automatorwp-action' => 'delete_all_logs' ), $url );
    $url = add_query_arg( '_wpnonce', wp_create_nonce( 'automatorwp_delete_all_logs' ), $url );

    echo sprintf(
        '<a href="%s" class="button automatorwp-button-danger automatorwp-clear-all-logs-button" onclick="%s" aria-label="%s">%s</a>',
        $url,
        "return confirm('" .
        esc_attr( __( "Are you sure you want to delete all logs?\\n\\nClick \\'Cancel\\' to go back, \\'OK\\' to confirm the deletion.", 'automatorwp' ) ) .
        "');",
        esc_attr( __( 'Clear all logs', 'automatorwp' ) ),
        __( 'Clear all logs', 'automatorwp' )
    );

    return $views;
}
add_filter( 'views_logs', 'automatorwp_logs_clear_all_logs_button' );

/**
 * Process the deletion of all logs
 *
 * @since 1.0.0
 */
function automatorwp_action_delete_all_logs() {

    global $wpdb;

    // Nonce check
    if ( ! isset( $_REQUEST['_wpnonce'] ) ) {
        wp_die( __( 'You are not allowed to perform this.', 'automatorwp' ) );
    }

    if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'automatorwp_delete_all_logs' ) ) {
        wp_die( __( 'You are not allowed to perform this.', 'automatorwp' ) );
    }

    $ct_table = ct_setup_table( 'automatorwp_logs' );

    // If user can not delete it, bail
    if ( ! current_user_can( $ct_table->cap->delete_items ) ) {
        wp_die( __( 'You are not allowed to perform this.', 'automatorwp' ) );
    }

    $logs       = AutomatorWP()->db->logs;
    $logs_meta 	= AutomatorWP()->db->logs_meta;

    // Delete all logs
    $wpdb->query( "DELETE l FROM {$logs} AS l WHERE 1=1" );

    // Delete orphaned log metas
    $wpdb->query( "DELETE lm FROM {$logs_meta} lm LEFT JOIN {$logs} l ON l.id = lm.id WHERE l.id IS NULL" );

    // Redirect to logs list view
    wp_redirect( $ct_table->views->list->get_link() );
    return;
}
add_action( 'automatorwp_action_delete_all_logs', 'automatorwp_action_delete_all_logs' );