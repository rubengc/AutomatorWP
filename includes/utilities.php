<?php
/**
 * Utilities
 *
 * @package     AutomatorWP\Utilities
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Utility function to get the times option parameter
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_utilities_times_option() {
    return array(
        'from' => 'times',
        'fields' => array(
            'times' => array(
                'name' => __( 'Number of times:', 'automatorwp' ),
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                    'min' => '1',
                ),
                'default' => 1
            )
        )
    );
}

/**
 * Utility function to get the post option parameter
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function automatorwp_utilities_post_option( $args = array() ) {

    $args = automatorwp_utilities_parse_selector_args( $args, array(
        'name'                  => __( 'Post:', 'automatorwp' ),
        'post_type'             => 'post',
        'post_type_cb'          => '',
        'placeholder'           => __( 'Select a post', 'automatorwp' ),
        'option_none_label'     => __( 'any post', 'automatorwp' ),
        'option_custom_desc'    => __( 'Post ID', 'automatorwp' ),
    ) );

    $option = array(
        'from' => 'post',
        'default' => ( isset( $args['option_default'] ) ? $args['option_default'] : '' ),
        'fields' => array(
            'post' => automatorwp_utilities_post_field( $args )
        )
    );

    // Add the custom field
    if( $args['option_custom'] ) {
        $option['fields']['post_custom'] = automatorwp_utilities_custom_field( $args );
    }

    return $option;

}

/**
 * Utility function to get a post field parameters
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function automatorwp_utilities_post_field( $args = array() ) {

    $args = automatorwp_utilities_parse_selector_args( $args, array(
        'name'                  => __( 'Post:', 'automatorwp' ),
        'post_type'             => 'post',
        'post_type_cb'          => '',
        'placeholder'           => __( 'Select a post', 'automatorwp' ),
        'option_none_label'     => __( 'any post', 'automatorwp' ),
        'option_custom_desc'    => __( 'Post ID', 'automatorwp' ),
    ) );

    if( ! is_array( $args['post_type'] ) ) {
        $args['post_type'] = array( $args['post_type'] );
    }

    $attributes = automatorwp_utilities_get_selector_attributes( $args );
    $attributes['data-post-type'] = implode(',', $args['post_type'] );
    $attributes['data-post-type-cb'] = $args['post_type_cb'];

    return array(
        'name'                  => $args['name'],
        'desc'                  => $args['desc'],
        'type'                  => ( $args['multiple'] ? 'automatorwp_select' : 'select' ),
        'classes'               => 'automatorwp-post-selector',
        'option_none'           => $args['option_none'],
        'option_none_value'     => $args['option_none_value'],
        'option_none_label'     => $args['option_none_label'],
        'option_custom'         => $args['option_custom'],
        'option_custom_value'   => $args['option_custom_value'],
        'option_custom_label'   => $args['option_custom_label'],
        'post_type_cb'          => '',
        'attributes'            => $attributes,
        'options_cb'            => 'automatorwp_options_cb_posts',
        'default'               => $args['default']
    );

}

/**
 * Utility function to get the term option parameter
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function automatorwp_utilities_term_option( $args = array() ) {

    $args = automatorwp_utilities_parse_selector_args( $args, array(
        'name'                  => __( 'Category:', 'automatorwp' ),
        'taxonomy'              => 'category',
        'placeholder'           => __( 'Select a category', 'automatorwp' ),
        'option_none_label'     => __( 'any category', 'automatorwp' ),
        'option_custom_desc'    => __( 'Category ID', 'automatorwp' ),
    ) );

    $option = array(
        'from' => 'term',
        'default' => ( isset( $args['option_default'] ) ? $args['option_default'] : '' ),
        'fields' => array(
            'term' => automatorwp_utilities_term_field( $args )
        )
    );

    // Add the custom field
    if( $args['option_custom'] ) {
        $option['fields']['term_custom'] = automatorwp_utilities_custom_field( $args );
    }

    return $option;

}

/**
 * Utility function to get a term field parameters
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function automatorwp_utilities_term_field( $args = array() ) {

    $args = automatorwp_utilities_parse_selector_args( $args, array(
        'name'                  => __( 'Category:', 'automatorwp' ),
        'taxonomy'              => 'category',
        'placeholder'           => __( 'Select a category', 'automatorwp' ),
        'option_none_label'     => __( 'any category', 'automatorwp' ),
        'option_custom_desc'    => __( 'Category ID', 'automatorwp' ),
    ) );

    $attributes = automatorwp_utilities_get_selector_attributes( $args );
    $attributes['data-taxonomy'] = $args['taxonomy'];

    return array(
        'name' => $args['name'],
        'desc' => $args['desc'],
        'type' => ( $args['multiple'] ? 'automatorwp_select' : 'select' ),
        'classes' => 'automatorwp-term-selector',
        'option_none'           => $args['option_none'],
        'option_none_value'     => $args['option_none_value'],
        'option_none_label'     => $args['option_none_label'],
        'option_custom'         => $args['option_custom'],
        'option_custom_value'   => $args['option_custom_value'],
        'option_custom_label'   => $args['option_custom_label'],
        'taxonomy' => $args['taxonomy'],
        'attributes' => $attributes,
        'options_cb' => 'automatorwp_options_cb_terms',
        'default' => $args['default']
    );

}

/**
 * Utility function to get the taxonomy option parameter
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function automatorwp_utilities_taxonomy_option( $args = array() ) {

    $args = automatorwp_utilities_parse_selector_args( $args, array(
        'name'                  => __( 'Taxonomy:', 'automatorwp' ),
        'option_default'        => __( 'any taxonomy', 'automatorwp' ),
        'multiple'              => false,
        'placeholder'           => __( 'Select a taxonomy', 'automatorwp' ),
        'option_none_label'     => __( 'any taxonomy', 'automatorwp' ),
        'option_custom_desc'    => __( 'Taxonomy', 'automatorwp' ),
    ) );

    $attributes = automatorwp_utilities_get_selector_attributes( $args );

    $term_args = $args;

    $term_args['name'] = __( 'Term:', 'automatorwp' );
    $term_args['option_none_label'] = __( 'any term', 'automatorwp' );
    $term_args['option_custom_desc'] = __( 'Term ID', 'automatorwp' );

    return array(
        'from' => 'term',
        'default' => $args['option_default'],
        'fields' => array(
            'taxonomy' => array(
                'name' => $args['name'],
                'desc' => $args['desc'],
                'type' => ( $args['multiple'] ? 'automatorwp_select' : 'select' ),
                'classes' => 'automatorwp-taxonomy-selector',
                'option_none'           => $args['option_none'],
                'option_none_value'     => $args['option_none_value'],
                'option_none_label'     => $args['option_none_label'],
                'option_custom'         => $args['option_custom'],
                'option_custom_value'   => $args['option_custom_value'],
                'option_custom_label'   => $args['option_custom_label'],
                'attributes' => $attributes,
                'options_cb' => 'automatorwp_options_cb_taxonomies',
                'default' => $args['default']
            ),
            'term' => automatorwp_utilities_term_field( $term_args )
        )
    );

}

/**
 * Utility function to get ajax selector option parameter
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function automatorwp_utilities_ajax_selector_option( $args = array() ) {

    $args = automatorwp_utilities_parse_selector_args( $args, array(
        'field'             => 'ajax_options',
        'action_cb'         => '',
    ) );

    $option = array(
        'from' => $args['field'],
        'default' => $args['option_default'],
        'fields' => array(
            $args['field'] => automatorwp_utilities_ajax_selector_field( $args )
        )
    );

    // Add the custom field
    if( $args['option_custom'] ) {
        $option['fields'][$args['field'] . '_custom'] = automatorwp_utilities_custom_field( $args );
    }

    return $option;

}

/**
 * Utility function to get ajax selector field
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function automatorwp_utilities_ajax_selector_field( $args = array() ) {

    $args = automatorwp_utilities_parse_selector_args( $args, array(
        'field'             => 'ajax_options',
        'action_cb'         => '',
    ) );

    $attributes = automatorwp_utilities_get_selector_attributes( $args );
    $attributes['data-action'] = $args['action_cb'];

    return array(
        'name'                  => $args['name'],
        'desc'                  => $args['desc'],
        'type'                  => ( $args['multiple'] ? 'automatorwp_select' : 'select' ),
        'classes'               => 'automatorwp-ajax-selector',
        'option_none'           => $args['option_none'],
        'option_none_value'     => $args['option_none_value'],
        'option_none_label'     => $args['option_none_label'],
        'option_custom'         => $args['option_custom'],
        'option_custom_value'   => $args['option_custom_value'],
        'option_custom_label'   => $args['option_custom_label'],
        'attributes'            => $attributes,
        'options_cb'            => $args['options_cb'],
        'default'               => $args['default']
    );

}

/**
 * Utility function to get the automation option parameter
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function automatorwp_utilities_automation_option( $args = array() ) {

    $args = automatorwp_utilities_parse_selector_args( $args, array(
        'name'                  => __( 'Automation:', 'automatorwp' ),
        'option_none_label'     => __( 'any automation', 'automatorwp' ),
        'option_custom_desc'    => __( 'Automation ID', 'automatorwp' ),
    ) );

    $attributes = automatorwp_utilities_get_selector_attributes( $args );
    $attributes['data-table'] = 'automatorwp_automations';

    $option = array(
        'from' => 'automation',
        'default' => $args['option_default'],
        'fields' => array(
            'automation' => array(
                'name' => $args['name'],
                'desc' => $args['desc'],
                'type' => ( $args['multiple'] ? 'automatorwp_select' : 'select' ),
                'classes' => 'automatorwp-object-selector',
                'option_none'           => $args['option_none'],
                'option_none_value'     => $args['option_none_value'],
                'option_none_label'     => $args['option_none_label'],
                'option_custom'         => $args['option_custom'],
                'option_custom_value'   => $args['option_custom_value'],
                'option_custom_label'   => $args['option_custom_label'],
                'attributes' => $attributes,
                'options_cb' => 'automatorwp_options_cb_objects',
                'default' => $args['default']
            )
        )
    );

    // Add the custom field
    if( $args['option_custom'] ) {
        $option['fields']['automation_custom'] = automatorwp_utilities_custom_field( $args );
    }

    return $option;

}

/**
 * Utility function to get the role option parameter
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function automatorwp_utilities_role_option( $args = array() ) {

    $args = automatorwp_utilities_parse_selector_args( $args, array(
        'name'              => __( 'Role:', 'automatorwp' ),
        'placeholder'       => __( 'Select a role', 'automatorwp' ),
        'option_none_label' => __( 'any role', 'automatorwp' ),
        'option_custom_desc'    => __( 'Role name.', 'automatorwp' ),
    ) );

    $option = array(
        'from' => 'role',
        'default' => $args['option_default'],
        'fields' => array(
            'role' => automatorwp_utilities_role_field( $args )
        )
    );

    // Add the custom field
    if( $args['option_custom'] ) {
        $option['fields']['role_custom'] = automatorwp_utilities_custom_field( $args );
    }

    return $option;

}

/**
 * Utility function to get a role field parameters
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function automatorwp_utilities_role_field( $args = array() ) {

    $args = automatorwp_utilities_parse_selector_args( $args, array(
        'name'              => __( 'Role:', 'automatorwp' ),
        'placeholder'       => __( 'Select a role', 'automatorwp' ),
        'option_none_label' => __( 'any role', 'automatorwp' ),
        'option_custom_desc'    => __( 'Role name.', 'automatorwp' ),
    ) );

    $attributes = automatorwp_utilities_get_selector_attributes( $args );

    return array(
        'name' => $args['name'],
        'desc' => $args['desc'],
        'type' => ( $args['multiple'] ? 'automatorwp_select' : 'select' ),
        'classes' => 'automatorwp-selector',
        'option_none' => $args['option_none'],
        'option_none_value' => $args['option_none_value'],
        'option_none_label' => $args['option_none_label'],
        'option_custom' => $args['option_custom'],
        'option_custom_value' => $args['option_custom_value'],
        'option_custom_label' => $args['option_custom_label'],
        'attributes' => $attributes,
        'options_cb' => 'automatorwp_options_cb_roles',
        'default' => $args['default']
    );

}

/**
 * Utility function to get a selector custom field parameters
 *
 * @since 1.3.7
 *
 * @param array $args
 *
 * @return array
 */
function automatorwp_utilities_custom_field( $args = array() ) {

    return array(
        'name'              => ( isset( $args['option_custom_name'] ) ? $args['option_custom_name'] : '' ),
        'desc'              => ( isset( $args['option_custom_desc'] ) ? $args['option_custom_desc'] : __( 'Post ID', 'automatorwp' ) ),
        'type'              => 'text',
        'classes'           => 'automatorwp-selector-custom-input',
    );

}

/**
 * Utility function to parse selector args
 *
 * @since 1.3.7
 *
 * @param array $args
 * @param array $defaults
 *
 * @return array
 */
function automatorwp_utilities_parse_selector_args( $args = array(), $defaults = array() ) {

    $selector_defaults = array(
        'name'              => '',
        'desc'              => '',
        'option_default'    => '',
        'multiple'          => false,
        'placeholder'       => '',
        'default'           => 'any',
        // Option none
        'option_none'       => true,
        'option_none_value' => 'any',
        'option_none_label' => '',
        // Option custom
        'option_custom'         => false,
        'option_custom_value'   => 'custom',
        'option_custom_label'   => __( 'Use a custom value', 'automatorwp' ),
        'option_custom_name'    => '',
        'option_custom_desc'    => '',
    );

    $final_defaults = array_merge( $selector_defaults, $defaults );

    $args = wp_parse_args( $args, $final_defaults );

    return $args;

}

/**
 * Utility function to get selector attributes
 *
 * @since 1.3.7
 *
 * @param array $args
 * @param array $defaults
 *
 * @return array
 */
function automatorwp_utilities_get_selector_attributes( $args = array() ) {

    $args = automatorwp_utilities_parse_selector_args( $args, array() );

    $attributes = array(
        'data-placeholder' => $args['placeholder'],
        // Option none
        'data-option-none' => $args['option_none'],
        'data-option-none-value' => $args['option_none_value'],
        'data-option-none-label' => $args['option_none_label'],
        // Option custom
        'data-option-custom'        => $args['option_custom'],
        'data-option-custom-value'  => $args['option_custom_value'],
        'data-option-custom-label'  => $args['option_custom_label'],
    );

    if( $args['multiple'] ) {
        $attributes['multiple'] = true;
    }

    return $attributes;

}

/**
 * Utility function to get the times tag
 *
 * @since 1.0.0
 *
 * @param bool $only_args Set to true to return only tag args
 *
 * @return array
 */
function automatorwp_utilities_times_tag( $only_args = false ) {

    $args = array(
        'label'     => __( 'Number of times', 'automatorwp' ),
        'type'      => 'integer',
        'preview'   => '1',
    );

    if( $only_args ) {
        return $args;
    }

    return array(
        'times' => $args
    );
}

/**
 * Utility function to get the post tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_utilities_post_tags( $post_label = '' ) {

    if( empty( $post_label ) ) {
        $post_label = __( 'Post', 'automatorwp' );
    }

    $site_url = get_option( 'home' );

    /**
     * Filter to setup custom post tags
     *
     * @since 1.0.0
     *
     * @param array $post_tags
     *
     * @return array
     */
    return apply_filters( 'automatorwp_utilities_post_tags', array(
        'post_id' => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s ID', 'automatorwp' ), $post_label ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'post_title' => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Title', 'automatorwp' ), $post_label ),
            'type'      => 'text',
            'preview'   => __( 'The title', 'automatorwp' ),
        ),
        'post_url' => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s URL', 'automatorwp' ), $post_label ),
            'type'      => 'text',
            'preview'   => $site_url . '/sample-' . strtolower( $post_label ),
        ),
        'post_link' => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Link', 'automatorwp' ), $post_label ),
            'type'      => 'text',
            /* translators: %s: Post label (by default: Post). */
            'preview'   => '<a href="' . $site_url . '/sample-' . strtolower( $post_label ) . '">' . sprintf( __( '%s Title', 'automatorwp' ), $post_label ) . '</a>',
        ),
        'post_type'  => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Type', 'automatorwp' ), $post_label ),
            'type'      => 'text',
            'preview'   => 'post',
        ),
        'post_author'  => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Author ID', 'automatorwp' ), $post_label ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'post_author_email'  => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Author Email', 'automatorwp' ), $post_label ),
            'type'      => 'email',
            'preview'   => 'contact@automatorwp.com',
        ),
        'post_content'  => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Content', 'automatorwp' ), $post_label ),
            'type'      => 'text',
            'preview'   => __( 'The content', 'automatorwp' ),
        ),
        'post_excerpt'  => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Excerpt', 'automatorwp' ), $post_label ),
            'type'      => 'text',
            'preview'   => __( 'The excerpt', 'automatorwp' ),
        ),
        'post_thumbnail'  => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Featured Image', 'automatorwp' ), $post_label ),
            'type'      => 'text',
            'preview'   => '<img src="' . $site_url . '/sample-' . strtolower( $post_label ) . '-image"/>',
        ),
        'post_thumbnail_id'  => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Featured Image ID', 'automatorwp' ), $post_label ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'post_thumbnail_url'  => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Featured Image URL', 'automatorwp' ), $post_label ),
            'type'      => 'text',
            'preview'   => $site_url . '/sample-' . strtolower( $post_label ) . '-image',
        ),
        'post_status'  => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Status', 'automatorwp' ), $post_label ),
            'type'      => 'text',
            'preview'   => 'publish',
        ),
        'post_parent' => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Parent ID', 'automatorwp' ), $post_label ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'menu_order' => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Menu Order', 'automatorwp' ), $post_label ),
            'type'      => 'integer',
            'preview'   => '1',
        ),
        'post_meta:META_KEY' => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Meta', 'automatorwp' ), $post_label ),
            'type'      => 'text',
            'preview'   => sprintf( __( '%s meta value, replace "META_KEY" by the %s meta key', 'automatorwp' ), $post_label, strtolower( $post_label ) ),
        ),
    ) );

}

/**
 * Utility function to get the comment tags
 *
 * @since 1.3.0
 *
 * @return array
 */
function automatorwp_utilities_comment_tags( $comment_label = '' ) {

    if( empty( $comment_label ) ) {
        $comment_label = __( 'Comment', 'automatorwp' );
    }

    /**
     * Filter to setup custom comment tags
     *
     * @since 1.3.0
     *
     * @param array $comment_tags
     *
     * @return array
     */
    return apply_filters( 'automatorwp_utilities_comment_tags', array(
        'comment_id' => array(
            /* translators: %s: Comment label (by default: Comment). */
            'label'     => sprintf( __( '%s ID', 'automatorwp' ), $comment_label ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'comment_post_id' => array(
            /* translators: %s: Comment label (by default: Comment). */
            'label'     => sprintf( __( '%s Post ID', 'automatorwp' ), $comment_label ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'comment_post_title' => array(
            /* translators: %s: Comment label (by default: Comment). */
            'label'     => sprintf( __( '%s Post Title', 'automatorwp' ), $comment_label ),
            'type'      => 'text',
            'preview'   => __( 'The post title', 'automatorwp' ),
        ),
        'comment_user_id' => array(
            /* translators: %s: Comment label (by default: Comment). */
            'label'     => sprintf( __( '%s User ID', 'automatorwp' ), $comment_label ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'comment_author' => array(
            /* translators: %s: Comment label (by default: Comment). */
            'label'     => sprintf( __( '%s Author Name', 'automatorwp' ), $comment_label ),
            'type'      => 'text',
            'preview'   => 'AutomatorWP',
        ),
        'comment_author_email' => array(
            /* translators: %s: Comment label (by default: Comment). */
            'label'     => sprintf( __( '%s Author Email', 'automatorwp' ), $comment_label ),
            'type'      => 'email',
            'preview'   => 'contact@automatorwp.com',
        ),
        'comment_author_url' => array(
            /* translators: %s: Comment label (by default: Comment). */
            'label'     => sprintf( __( '%s Author URL', 'automatorwp' ), $comment_label ),
            'type'      => 'text',
            'preview'   => 'https://automatorwp.com',
        ),
        'comment_author_ip' => array(
            /* translators: %s: Comment label (by default: Comment). */
            'label'     => sprintf( __( '%s Author IP', 'automatorwp' ), $comment_label ),
            'type'      => 'text',
            'preview'   => '255.255.255.255',
        ),
        'comment_content' => array(
            /* translators: %s: Comment label (by default: Comment). */
            'label'     => sprintf( __( '%s Content', 'automatorwp' ), $comment_label ),
            'type'      => 'text',
            'preview'   => __( 'The content', 'automatorwp' ),
        ),
        'comment_type' => array(
            /* translators: %s: Comment label (by default: Comment). */
            'label'     => sprintf( __( '%s Type', 'automatorwp' ), $comment_label ),
            'type'      => 'text',
            'preview'   => 'comment',
        ),
    ) );

}

/**
 * Utility function to get the condition option parameter
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_utilities_condition_option() {
    return array(
        'from' => 'condition',
        'fields' => array(
            'condition' => automatorwp_utilities_condition_field()
        )
    );
}

/**
 * Utility function to get the condition field
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_utilities_condition_field() {
    return array(
        'name' => __( 'Condition:', 'automatorwp' ),
        'type' => 'select',
        'options' => array(
            'equal'             => __( 'is equal to', 'automatorwp' ),
            'not_equal'         => __( 'is not equal to', 'automatorwp' ),
            'contains'          => __( 'contains', 'automatorwp' ),
            'not_contains'      => __( 'does not contains', 'automatorwp' ),
            'start_with'        => __( 'starts with', 'automatorwp' ),
            'not_start_with'    => __( 'does not starts with', 'automatorwp' ),
            'ends_with'         => __( 'ends with', 'automatorwp' ),
            'not_ends_with'     => __( 'does not ends with', 'automatorwp' ),
            'less_than'         => __( 'is less than', 'automatorwp' ),
            'greater_than'      => __( 'is greater than', 'automatorwp' ),
            'less_or_equal'     => __( 'is less or equal to', 'automatorwp' ),
            'greater_or_equal'  => __( 'is greater or equal to', 'automatorwp' ),
        ),
        'default' => 'equal'
    );
}

/**
 * Utility function to get the condition option parameter
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_utilities_number_condition_option() {
    return array(
        'from' => 'condition',
        'fields' => array(
            'condition' => automatorwp_utilities_condition_field()
        )
    );
}

/**
 * Utility function to get the condition field
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_utilities_number_condition_field() {
    return array(
        'name' => __( 'Condition:', 'automatorwp' ),
        'type' => 'select',
        'options' => array(
            'equal'             => __( 'equal to', 'automatorwp' ),
            'not_equal'         => __( 'not equal to', 'automatorwp' ),
            'less_than'         => __( 'less than', 'automatorwp' ),
            'greater_than'      => __( 'greater than', 'automatorwp' ),
            'less_or_equal'     => __( 'less or equal to', 'automatorwp' ),
            'greater_or_equal'  => __( 'greater or equal to', 'automatorwp' ),
        ),
        'default' => 'equal'
    );
}

/**
 * Utility function to get the string condition option parameter
 *
 * @since 1.4.5
 *
 * @return array
 */
function automatorwp_utilities_string_condition_option() {
    return array(
        'from' => 'condition',
        'fields' => array(
            'condition' => automatorwp_utilities_string_condition_field()
        )
    );
}

/**
 * Utility function to get the string condition field
 *
 * @since 1.4.5
 *
 * @return array
 */
function automatorwp_utilities_string_condition_field() {
    return array(
        'name' => __( 'Condition:', 'automatorwp' ),
        'type' => 'select',
        'options' => array(
            'equal'             => __( 'is equal to', 'automatorwp' ),
            'not_equal'         => __( 'is not equal to', 'automatorwp' ),
            'contains'          => __( 'contains', 'automatorwp' ),
            'not_contains'      => __( 'does not contains', 'automatorwp' ),
            'start_with'        => __( 'starts with', 'automatorwp' ),
            'not_start_with'    => __( 'does not starts with', 'automatorwp' ),
            'ends_with'         => __( 'ends with', 'automatorwp' ),
            'not_ends_with'     => __( 'does not ends with', 'automatorwp' ),
        ),
        'default' => 'equal'
    );
}

/**
 * Utility function to get a condition label
 *
 * @since 1.4.5
 *
 * @param string $condition
 * @return string
 */
function automatorwp_utilities_get_condition_label( $condition ) {

    $conditions = array(
        // String
        'equal'             => __( 'is equal to', 'automatorwp' ),
        'not_equal'         => __( 'is not equal to', 'automatorwp' ),
        'contains'          => __( 'contains', 'automatorwp' ),
        'not_contains'      => __( 'does not contains', 'automatorwp' ),
        'start_with'        => __( 'starts with', 'automatorwp' ),
        'not_start_with'    => __( 'does not starts with', 'automatorwp' ),
        'ends_with'         => __( 'ends with', 'automatorwp' ),
        'not_ends_with'     => __( 'does not ends with', 'automatorwp' ),
        // Number
        'less_than'         => __( 'is less than', 'automatorwp' ),
        'greater_than'      => __( 'is greater than', 'automatorwp' ),
        'less_or_equal'     => __( 'is less or equal to', 'automatorwp' ),
        'greater_or_equal'  => __( 'is greater or equal to', 'automatorwp' ),
    );

    return ( isset( $conditions[$condition] ) ? $conditions[$condition] : $condition );
}

/**
 * Check if post ID matches with the required post ID
 *
 * @since 1.0.0
 *
 * @param int $post_id          The post ID
 * @param int $required_post_id The required post ID
 *
 * @return bool
 */
function automatorwp_posts_matches( $post_id, $required_post_id ) {

    $post_id = absint( $post_id );
    $required_post_id = absint( $required_post_id );

    $post = get_post( $post_id );

    // Bail if post doesn't exists
    if( ! $post ) {
        return false;
    }

    // Bail if post doesn't match with the trigger option
    if( $required_post_id !== 0 && $post->ID !== $required_post_id ) {
        return false;
    }

    return true;

}

/**
 * Check if term ID matches with the required term ID
 *
 * @since 1.0.0
 *
 * @param array|int $term_id          The term ID
 * @param int       $required_term_id The required term ID
 *
 * @return bool
 */
function automatorwp_terms_matches( $term_id, $required_term_id ) {

    $required_term_id = absint( $required_term_id );

    // Only parse this check if required term ID is provided
    if( $required_term_id !== 0 ) {

        if( is_array( $term_id ) ) {

            // Ensure terms IDs as integer
            $term_id = array_map( 'absint', $term_id );

            if( ! in_array( $required_term_id, $term_id ) ) {
                // If received an array of terms, bail if required term ID isn't in the array
                return false;
            }

        } else if( absint( $term_id ) !== $required_term_id ) {

            // If received a single term ID, bail if required term ID doesn't match
            return false;

        }


    }

    return true;

}

/**
 * Utility function to get the condition option parameter
 *
 * @since 1.0.0
 *
 * @param int|float $a          Number to match
 * @param int|float $b          Number to compare
 * @param string    $condition  The condition to compare numbers
 *
 * @return bool
 */
function automatorwp_number_condition_matches( $a, $b, $condition ) {

    if( empty( $condition ) ) {
        $condition = 'equal';
    }

    $matches = false;

    switch( $condition ) {
        case 'equal':
        case '=':
        case '==':
        case '===':
            $matches = ( $a == $b );
            break;
        case 'not_equal':
        case '!=':
        case '!==':
            $matches = ( $a != $b );
            break;
        case 'less_than':
        case '<':
            $matches = ( $a < $b );
            break;
        case 'greater_than':
        case '>':
            $matches = ( $a > $b );
            break;
        case 'less_or_equal':
        case '<=':
            $matches = ( $a <= $b );
            break;
        case 'greater_or_equal':
        case '>=':
            $matches = ( $a >= $b );
            break;
    }

    return $matches;

}

/**
 * Utility function to get the condition option parameter
 *
 * @since 1.4.5
 *
 * @param mixed     $a          Element to match
 * @param mixed     $b          Element to compare
 * @param string    $condition  The condition to compare elements
 *
 * @return bool
 */
function automatorwp_condition_matches( $a, $b, $condition ) {

    if( empty( $condition ) ) {
        $condition = 'equal';
    }

    $matches = false;

    // Ensure that the element to compare is a string
    if( is_array( $b ) ) {
        $b = implode( ',', $b );
    }

    $a = strval( $a );
    $b = strval( $b );

    // If not is a string condition and elements to compare are numerics, turn them to float
    if( ! automatorwp_is_string_condition( $condition ) ) {
        if( is_numeric( $a ) ) {
            $a = (float) $a;
        }

        if( is_numeric( $b ) ) {
            $b = (float) $b;
        }
    }

    switch( $condition ) {
        case 'equal':
        case '=':
        case '==':
        case '===':
            $matches = ( $a == $b );
            break;
        case 'not_equal':
        case '!=':
        case '!==':
            $matches = ( $a != $b );
            break;
        case 'less_than':
        case '<':
            $matches = ( $a < $b );
            break;
        case 'greater_than':
        case '>':
            $matches = ( $a > $b );
            break;
        case 'less_or_equal':
        case '<=':
            $matches = ( $a <= $b );
            break;
        case 'greater_or_equal':
        case '>=':
            $matches = ( $a >= $b );
            break;
        case 'contains':
            $matches = ( strpos( $a, strval( $b ) ) !== false );
            break;
        case 'not_contains':
            $matches = ( strpos( $a, strval( $b ) ) === false );
            break;
        case 'start_with':
            $matches = ( automatorwp_starts_with( $a, $b ) );
            break;
        case 'not_start_with':
            $matches = ( ! automatorwp_starts_with( $a, $b ) );
            break;
        case 'ends_with':
            $matches = ( automatorwp_ends_with( $a, $b ) );
            break;
        case 'not_ends_with':
            $matches = ( ! automatorwp_ends_with( $a, $b ) );
            break;
    }

    return $matches;

}

/**
 * Utility function to meet if condition is related to string
 *
 * @since 1.7.6
 *
 * @param string    $condition  The condition to check
 *
 * @return bool
 */
function automatorwp_is_string_condition( $condition ) {

    $return = false;

    switch( $condition ) {
        case 'contains':
        case 'not_contains':
        case 'start_with':
        case 'not_start_with':
        case 'ends_with':
        case 'not_ends_with':
            $return = true;
            break;
    }

    return $return;

}

/**
 * Retrieves post term ids for a taxonomy.
 *
 * @since  1.0.0
 *
 * @param  int    $post_id  Post ID.
 * @param  string $taxonomy Taxonomy slug.
 *
 * @return array
 */
function automatorwp_get_term_ids( $post_id, $taxonomy ) {

    $terms = get_the_terms( $post_id, $taxonomy );

    return ( empty( $terms ) || is_wp_error( $terms ) ) ? array() : wp_list_pluck( $terms, 'term_id' );

}

/**
 * Creates a toggleable list
 *
 * @since  1.0.0
 *
 * @param array $options
 *
 * @return string
 */
function automatorwp_toggleable_options_list( $options ) {

    // Bail if no options given
    if( ! is_array( $options ) ) {
        return '';
    }

    $show_text = __( 'Show options', 'automatorwp' );
    $hide_text = __( 'Hide options', 'automatorwp' );

    $html = '<a href="#" class="automatorwp-toggleable-options-list-toggle" data-show-text="' . $show_text . '" data-hide-text="' . $hide_text . '">' . $show_text . '</a>'
        . '<ul class="automatorwp-toggleable-options-list" style="display: none;">';

    foreach( $options as $option ) {
        $html .= "<li>{$option}</li>";
    }

    $html .= '</ul>';

    return $html;

}

/**
 * Helper function to get all editable roles included if get_editable_roles() doesn't exists
 *
 * @since 1.1.5
 *
 * @return array[]|mixed|void
 */
function automatorwp_get_editable_roles() {

    if( function_exists('get_editable_roles' ) ) {
        $roles = get_editable_roles();
    } else {
        $roles = wp_roles()->roles;

        $roles = apply_filters( 'editable_roles', $roles );
    }

    return $roles;

}

/**
 * Helper function to pull all values of an array to a single level
 *
 * -------------------------------------
 * Turns keyed arrays:
 * 'key_1' => array(
 *     'sub_1' => 'foo',
 *     'sub_2' => array(
 *         'sub_1' => 'bar',
 *     ),
 * ),
 *
 * Into:
 * 'key_1' => 'foo, bar'
 * 'key_1/sub_1' => 'foo',
 * 'key_1/sub_2' => 'bar'
 * 'key_1/sub_2/sub_1' => 'bar',
 * -------------------------------------
 * Turns numeric arrays:
 * 'key' => array(
 *     'foo',
 *     array(
 *         'bar',
 *     ),
 * ),
 *
 * Into:
 * 'key' => 'foo, bar'
 * 'key/0' => 'foo',
 * 'key/1' => 'bar'
 * 'key/1/0' => 'bar',
 * -------------------------------------
 *
 * @since 1.4.4
 *
 * @param array $array
 * @param string $separator
 *
 * @return array
 */
function automatorwp_utilities_pull_array_values( $array = array(), $separator = '/' ) {

    $new_array = array();

    foreach( $array as $key => $value ) {

        if( is_array( $value ) ) {
            // Pull all sub values
            $value = automatorwp_utilities_pull_array_values( $value );
        }

        $new_array[$key] = $value;

        if( is_array( $value ) ) {

            // Add new entries with the sub values
            foreach( $value as $sub_key => $sub_value ) {
                $new_array[$key . $separator . $sub_key] = $sub_value;
            }

            // Implode all sub values on the main key
            $new_array[$key] = implode( ', ', $value );
        }

    }

    return $new_array;

}

/**
 * Helper function to search an array key value recursively
 *
 * @since 1.4.5
 *
 * @param string $key
 * @param array $haystack
 *
 * @return bool
 */
function automatorwp_get_array_key_value( $key = '', $haystack = array() ) {

    if( isset( $haystack[$key] ) ) {
        return $haystack[$key];
    }

    foreach( $haystack as $value) {

        if ( is_array( $value ) ) {

            $found = automatorwp_get_array_key_value( $key, $value );

            if ( $found ) {
                return $found;
            }

        }

    }

    return false;

}

/**
 * Helper function to check if a string starts by needle string given
 *
 * @since 1.4.5
 *
 * @param string $haystack
 * @param string $needle
 *
 * @return bool
 */
function automatorwp_starts_with( $haystack, $needle ) {
    return strncmp( $haystack, $needle, strlen( $needle ) ) === 0;
}

/**
 * Helper function to check if a string ends by needle string given
 *
 * @since 1.4.5
 *
 * @param string $haystack
 * @param string $needle
 *
 * @return bool
 */
function automatorwp_ends_with( $haystack, $needle ) {
    return $needle === '' || substr_compare( $haystack, $needle, -strlen( $needle ) ) === 0;
}

/**
 * Helper function to parse the function args option
 *
 * @since 1.7.9
 *
 * @param array     $args Expects an array like: array( array( 'value' => 'value_1' ), array( 'value' => 'value_2' ) )
 * @param stdClass  $item
 * @param int       $user_id
 * @param array     $options
 * @param stdClass  $automation
 *
 * @return array
 */
function automatorwp_parse_function_args_option( $args, $item, $user_id, $options, $automation ) {

    foreach( $args as $key => $param ) {

        $value = automatorwp_parse_automation_tags( $automation->id, $user_id, $param['value'] );

        $value = automatorwp_parse_function_arg_value( $value );

        $args[$key] = $value;

    }

    /**
     * Filter available to override the function args option parsed
     *
     * @since 1.7.9
     *
     * @param array     $args
     * @param stdClass  $item
     * @param int       $user_id
     * @param array     $options
     * @param stdClass  $automation
     *
     * @return array
     */
    return apply_filters( 'automatorwp_parse_function_args_option', $args, $item, $user_id, $options, $automation );

}

/**
 * Helper function to parse the function arg value
 * Turn values like "null" to null or "array()" to array()
 *
 * @since 1.7.9
 *
 * @param mixed $value
 *
 * @return mixed
 */
function automatorwp_parse_function_arg_value( $value ) {

    // Check PHP objects
    switch ( $value ) {
        case 'null':
            $value = null;
            break;
        case 'true':
            $value = true;
            break;
        case 'TRUE':
            $value = TRUE;
            break;
        case 'false':
            $value = false;
            break;
        case 'FALSE':
            $value = FALSE;
            break;
        case 'array()':
        case '[]':
            $value = array();
            break;
    }

    // Check possible numeric values
    if( is_numeric( $value ) ) {
        if ( strpos( $value , '.') !== false ) {
            $value = (float) $value ;
        } else {
            $value = (int) $value;
        }
    }

    /**
     * Filter available to override the function arg value parsed
     *
     * @since 1.7.9
     *
     * @param mixed $value
     *
     * @return mixed
     */
    return apply_filters( 'automatorwp_parse_function_arg_value', $value );

}