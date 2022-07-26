<?php
/**
 * CMB2
 *
 * @package     AutomatorWP\CMB2
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Tooltip callback for CMB2 fields (used as 'after_field' callback)
 *
 * @since 1.0.0
 */
function automatorwp_tooltip_cb() {
    ?>
    <div class="automatorwp-tooltip"><span class="dashicons dashicons-editor-help"></span></div>
    <?php
}

/**
 * Options callback for select2 fields assigned to posts
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_options_cb_posts( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any post', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $post_id ) {

            // Skip option none and custom
            if( isset( $options[$post_id] ) ) {
                continue;
            }

            $options[$post_id] = get_post_field( 'post_title', $post_id );
        }
    }

    return $options;

}

/**
 * Options callback for select2 fields assigned to terms
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_options_cb_terms( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $taxonomy = $field->args['taxonomy'];
    $none_value = 'any';
    $none_label = __( 'any category', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $term_id ) {

            // Skip option none and custom
            if( isset( $options[$term_id] ) ) {
                continue;
            }

            $term = get_term( $term_id );

            if( $term ) {
                $options[$term_id] = $term->name;
            }
        }
    }

    return $options;

}

/**
 * Options callback for select2 fields assigned to taxonomies
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_options_cb_taxonomies( $field ) {

    // Setup vars
    $none_value = 'any';
    $none_label = __( 'any taxonomy', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    $post_types = get_post_types( array( 'public' => true ), 'objects' );
    $taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
    $taxonomies_to_exclude = array( 'wp_log_type' );

    foreach( $taxonomies as $taxonomy => $taxonomy_obj ) {

        // Skip some private taxonomies
        if( in_array( $taxonomy, $taxonomies_to_exclude ) ) {
            continue;
        }

        $post_types_label = '';
        $taxonomy_post_types = array();

        // Loop all taxonomy post types
        if( is_array( $taxonomy_obj->object_type ) ) {

            foreach( $taxonomy_obj->object_type as $post_type ) {

                if( isset( $post_types[$post_type] ) ) {
                    $post_type_obj = $post_types[$post_type];
                    $taxonomy_post_types[] = $post_type_obj->labels->name;
                }
            }

        }

        // Setup the post types labels
        if( ! empty( $taxonomy_post_types ) ) {
            $post_types_label = ' (' . implode( ', ', $taxonomy_post_types ) . ')';
        }

        $options[$taxonomy] = $taxonomy_obj->labels->name . $post_types_label;
    }

    return $options;

}

/**
 * Options callback for select2 fields assigned to users
 *
 * @since 1.0.0
 *
 * @param CMB2_Field $field
 *
 * @return array
 */
function automatorwp_options_cb_users( $field ) {

    $value = $field->escaped_value;
    $options = array();

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $user_id ) {
            $user_data = get_userdata( $user_id );

            $options[$user_id] = $user_data->user_login;
        }
    }

    return $options;

}

/**
 * Display callback for select2 fields assigned to users
 *
 * @since 1.3.0
 *
 * @param array         $field_args
 * @param CMB2_Field    $field
 *
 * @return array
 */
function automatorwp_display_cb_users( $field_args, $field ) {

    $value = $field->escaped_value();
    $options = array();

    if( ! empty( $value ) ) {

        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $user_id ) {
            $user_data = get_userdata( $user_id );

            $options[$user_id] = $user_data->user_login;
        }

        $value = implode( ', ', $options );
    }

    return $value;

}

/**
 * Options callback for post type options
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_options_cb_post_types( $field ) {

    // Setup vars
    $none_value = 'any';
    $none_label = __( 'a post of any type', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    // Get all public post types which means they are visitable
    $post_types = get_post_types( array(), 'objects' );

    $post_types_excluded = array(
        'revision',
        'nav_menu_item',
        'custom_css',
        'customize_changeset',
        'user_request',
        'oembed_cache',
        'wp_block',
        'wp_template',
    );

    /**
     * Filter available to extend the post types excluded for the post type selector
     *
     * @since 1.0.0
     *
     * @param array     $post_types_excluded
     * @param stdClass  $field
     *
     * @return array
     */
    $post_types_excluded = apply_filters( 'automatorwp_options_cb_post_types_excluded', $post_types_excluded, $field );

    // Unset excluded post types
    foreach( $post_types_excluded as $excluded ) {
        if( isset( $post_types[$excluded] ) ) {
            unset( $post_types[$excluded] );
        }
    }

    /**
     * Filter available to extend the post types for the post type selector
     *
     * @since 1.0.0
     *
     * @param array     $post_types
     * @param stdClass  $field
     *
     * @return array
     */
    $post_types = apply_filters( 'automatorwp_options_cb_post_types', $post_types, $field );

    foreach( $post_types as $post_type => $post_type_object ) {
        $options[$post_type] = sprintf( __( 'a %s', 'automatorwp' ), strtolower( $post_type_object->labels->singular_name ) );
    }

    return $options;

}

/**
 * Options callback for post type options
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_options_cb_post_status( $field ) {

    global $wp_post_statuses;

    if ( ! is_array( $wp_post_statuses ) ) {
        $wp_post_statuses = array();
    }

    // Setup vars
    $none_value = 'any';
    $none_label = __( 'any status', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( count( $wp_post_statuses ) ) {

        // Get statuses from registered post statuses
        foreach( $wp_post_statuses as $post_status => $args ) {
            $options[$post_status] = $args->label . ' (' . $post_status . ')';
        }

    } else {
        // If post statuses global is empty fallback to get_post_statuses()

        $post_statuses = get_post_statuses();

        foreach( $post_statuses as $post_status => $post_status_label ) {
            $options[$post_status] = $post_status_label;
        }

    }

    return $options;

}

/**
 * Options callback for post fields
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_options_cb_post_fields( $field ) {

    // Option none
    $none_value = 'any';
    $none_label = __( 'any field', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    $post_fields = array(
        'ID'                        => __( 'ID', 'automatorwp' ),
        'post_title'                => __( 'Title', 'automatorwp' ),
        'post_name'                 => __( 'Slug', 'automatorwp' ),
        'post_type'                 => __( 'Type', 'automatorwp' ),
        'post_status'               => __( 'Status', 'automatorwp' ),
        'post_date'                 => __( 'Date', 'automatorwp' ),
        'post_date_gmt'             => __( 'Date (GMT)', 'automatorwp' ),
        'post_date_modified'        => __( 'Date Modified', 'automatorwp' ),
        'post_date_modified_gmt'    => __( 'Date Modified (GMT)', 'automatorwp' ),
        'post_author'               => __( 'Author', 'automatorwp' ),
        'post_content'              => __( 'Content', 'automatorwp' ),
        'post_excerpt'              => __( 'Excerpt', 'automatorwp' ),
        'post_parent'               => __( 'Parent', 'automatorwp' ),
        'menu_order'                => __( 'Order', 'automatorwp' ),
        'post_password'             => __( 'Password', 'automatorwp' ),
    );

    $post_fields = apply_filters( 'automatorwp_get_post_fields', $post_fields );

    // Excluded roles
    $field->args['excluded_post_fields'] = ( isset( $field->args['excluded_post_fields'] ) ? $field->args['excluded_post_fields'] : array() );

    // Ensure excluded roles as array
    if( ! is_array( $field->args['excluded_post_fields'] ) ) {
        $field->args['excluded_post_fields'] = array( $field->args['excluded_post_fields'] );
    }

    foreach ( $post_fields as $post_field => $post_field_label ) {

        // Skip excluded roles
        if( in_array( $post_field, $field->args['excluded_post_fields'] ) ) {
            continue;
        }

        $options[$post_field] = $post_field_label;

    }

    return $options;

}

/**
 * Options callback for user fields
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_options_cb_user_fields( $field ) {

    // Option none
    $none_value = 'any';
    $none_label = __( 'any field', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    $user_fields = array(
        'ID'                        => __( 'ID', 'automatorwp' ),
        'user_login'                => __( 'Username', 'automatorwp' ),
        'user_email'                => __( 'Email', 'automatorwp' ),
        'display_name'              => __( 'Display name', 'automatorwp' ),
        'user_nicename'             => __( 'Nicename', 'automatorwp' ),
        'user_url'                  => __( 'Website', 'automatorwp' ),
        'user_registered'           => __( 'Registration Date', 'automatorwp' ),
    );

    $user_fields = apply_filters( 'automatorwp_get_user_fields', $user_fields );

    // Excluded roles
    $field->args['excluded_user_fields'] = ( isset( $field->args['excluded_user_fields'] ) ? $field->args['excluded_user_fields'] : array() );

    // Ensure excluded roles as array
    if( ! is_array( $field->args['excluded_user_fields'] ) ) {
        $field->args['excluded_user_fields'] = array( $field->args['excluded_user_fields'] );
    }

    foreach ( $user_fields as $user_field => $user_field_label ) {

        // Skip excluded roles
        if( in_array( $user_field, $field->args['excluded_user_fields'] ) ) {
            continue;
        }

        $options[$user_field] = $user_field_label;

    }

    return $options;

}

/**
 * Options callback for WordPress roles
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_options_cb_roles( $field ) {

    // Option none
    $none_value = 'any';
    $none_label = __( 'any role', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    $roles = automatorwp_get_editable_roles();

    // Excluded roles
    $field->args['excluded_roles'] = ( isset( $field->args['excluded_roles'] ) ? $field->args['excluded_roles'] : array() );

    // Ensure excluded roles as array
    if( ! is_array( $field->args['excluded_roles'] ) ) {
        $field->args['excluded_roles'] = array( $field->args['excluded_roles'] );
    }

    foreach ( $roles as $role => $details ) {

        // Skip excluded roles
        if( in_array( $role, $field->args['excluded_roles'] ) ) {
            continue;
        }

        $options[$role] = translate_user_role( $details['name'] );

    }

    return $options;

}

/**
 * Options callback for select2 fields assigned to objects
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_options_cb_objects( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = '';
    $none_label = __( 'any item', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    // Ensure that required attributes are set
    if( ! isset( $field->args['attributes'] ) ) {
        return $options;
    }

    // Ensure that data-table is set
    if( ! isset( $field->args['attributes']['data-table'] ) ) {
        return $options;
    }

    if( ! empty( $value ) ) {

        ct_setup_table( $field->args['attributes']['data-table'] );

        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $object_id ) {

            // Skip option none and custom
            if( isset( $options[$object_id] ) ) {
                continue;
            }

            $object = ct_get_object( $object_id );

            $title = ( ! empty( $object->title ) ? $object->title : __( '(No title)', 'automatorwp' ) );

            $options[$object_id] = $title;

        }

        ct_reset_setup_table();
    }

    return $options;

}

/**
 * Options callback for select2 fields assigned to posts
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_options_cb_filters( $field ) {

    $options = array(
        'any' => __( 'Choose a filter', 'automatorwp' )
    );

    foreach( AutomatorWP()->filters as $filter => $args ) {

        // Skip if integration is not registered
        if( ! isset( AutomatorWP()->integrations[$args['integration']] ) ) {
            continue;
        }

        $integration = $args['integration'];

        if( ! isset( $options[$integration] ) ) {
            $options[$integration] = array();
        }

        $options[$integration][$filter] = $args['label'];
    }

    return $options;

}

/**
 * Options callback for select2 fields assigned to posts
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_options_cb_automation_statuses( $field ) {

    $options = automatorwp_get_automation_statuses();

    $object_id = $field->object_id;

    $object = ct_get_object( $object_id );

    if( ! in_array( $object->type, array( 'all-users' , 'all-posts' ) ) ) {
        unset( $options['in-progress'] );
    }

    return $options;

}

/**
 * Helper function to handle option none
 *
 * @since 1.0.0
 *
 * @param stdClass  $field
 * @param string    $default_value
 * @param string    $default_label
 *
 * @return array
 */
function automatorwp_options_cb_none_option( $field, $default_value = '', $default_label = '' ) {

    $options = array();

    // Setup option custom
    if( isset( $field->args['option_custom'] ) && $field->args['option_custom'] ) {

        $custom_value = ( isset( $field->args['option_custom_value'] ) ? $field->args['option_custom_value'] : 'custom' );
        $custom_label = ( isset( $field->args['option_custom_label'] ) ? $field->args['option_custom_label'] : __( 'Use a custom value', 'automatorwp' ) );

        $options[$custom_value] = $custom_label;
    }

    $none_value = $default_value;
    $none_label = $default_label;

    // Setup option none
    if( isset( $field->args['option_none'] ) && $field->args['option_none'] ) {

        $none_value = ( isset( $field->args['option_none_value'] ) ? $field->args['option_none_value'] : $none_value );
        $none_label = ( isset( $field->args['option_none_label'] ) ? $field->args['option_none_label'] : $none_label );

        $options[$none_value] = $none_label;
    }

    return $options;

}

/**
 * Handles sanitization for textarea, wysiwyg and oembed fields to allow tags
 *
 * @since 1.3.3
 *
 * @param  mixed      $value      The unsanitized value from the form.
 * @param  array      $field_args Array of field arguments.
 * @param  CMB2_Field $field      The field object
 *
 * @return mixed                  Sanitized value to be stored.
 */
function automatorwp_textarea_sanitization_cb( $value, $field_args, $field ) {

    $allowed_protocols = wp_allowed_protocols();

    // Look for tags
    preg_match_all( "/\{\s*(.*?)\s*\}/", $value, $matches );

    if( is_array( $matches ) && isset( $matches[1] ) ) {

        foreach( $matches[1] as $tag_name ) {

            // Check if is a trigger tag
            if( strpos( $tag_name, ':' ) !== false) {

                $tag_parts = explode( ':',  $tag_name );

                if( isset( $tag_parts[0] ) ) {
                    $trigger_id = $tag_parts[0];
                    $protocol = "{{$trigger_id}";

                    if( ! in_array( $protocol, $allowed_protocols ) ) {
                        // Add the "{ID:" as allowed protocol
                        $allowed_protocols[] = $protocol;
                    }
                }

            }
        }

    }

    return wp_kses( $value, 'post', $allowed_protocols );

}

/**
 * Helper function to get a field options from the "options" or "options_cb"
 *
 * @since 2.0.4
 *
 * @param array $field The field configuration
 *
 * @return array
 */
function automatorwp_get_field_options( $field ) {

    // Get the field options
    $field_options = array();

    // Try to get the field options from field args
    if( isset( $field['options'] ) ) {

        $field_options = $field['options'];

    } else if( isset( $field['options_cb'] ) && is_callable( $field['options_cb'] ) ) {

        $value = ( isset( $field['value'] ) ? $field['value'] : '' );

        $field['value'] = $value;
        $field['escaped_value'] = $value;
        $field['args'] = $field;

        $field_options = call_user_func( $field['options_cb'], (object) $field );

    }

    return $field_options;

}