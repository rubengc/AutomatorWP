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

    $args = wp_parse_args( $args, array(
        'name'              => __( 'Post:', 'automatorwp' ),
        'option_default'    => '',
        'option_none'       => true,
        'option_none_value' => 'any',
        'option_none_label' => __( 'any post', 'automatorwp' ),
        'post_type'         => 'post',
        'default'           => 'any'
    ) );

    if( ! is_array( $args['post_type'] ) ) {
        $args['post_type'] = array( $args['post_type'] );
    }

    return array(
        'from' => 'post',
        'default' => $args['option_default'],
        'fields' => array(
            'post' => automatorwp_utilities_post_field( $args )
        )
    );

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

    $args = wp_parse_args( $args, array(
        'name'              => __( 'Post:', 'automatorwp' ),
        'option_default'    => '',
        'option_none'       => true,
        'option_none_value' => 'any',
        'option_none_label' => __( 'any post', 'automatorwp' ),
        'post_type'         => 'post',
        'placeholder'       => __( 'Select a post', 'automatorwp' ),
        'default'           => 'any'
    ) );

    if( ! is_array( $args['post_type'] ) ) {
        $args['post_type'] = array( $args['post_type'] );
    }

    return array(
        'name' => $args['name'],
        'type' => 'select',
        'classes' => 'automatorwp-post-selector',
        'option_none' => $args['option_none'],
        'option_none_value' => $args['option_none_value'],
        'option_none_label' => $args['option_none_label'],
        'attributes' => array(
            'data-option-none' => $args['option_none'],
            'data-option-none-value' => $args['option_none_value'],
            'data-option-none-label' => $args['option_none_label'],
            'data-placeholder' => $args['placeholder'],
            'data-post-type' => implode(',', $args['post_type'] ),
        ),
        'options_cb' => 'automatorwp_options_cb_posts',
        'default' => $args['default']
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

    $args = wp_parse_args( $args, array(
        'field'             => 'ajax_options',
        'name'              => '',
        'action_cb'         => '',
        'option_default'    => '',
        'option_none'       => true,
        'option_none_value' => 'any',
        'option_none_label' => '',
        'placeholder'       => '',
        'options_cb'        => '',
        'default'           => 'any'
    ) );

    return array(
        'from' => $args['field'],
        'default' => $args['option_default'],
        'fields' => array(
            $args['field'] => array(
                'name' => $args['name'],
                'type' => 'select',
                'classes' => 'automatorwp-ajax-selector',
                'option_none' => $args['option_none'],
                'option_none_value' => $args['option_none_value'],
                'option_none_label' => $args['option_none_label'],
                'attributes' => array(
                    'data-action' => $args['action_cb'],
                    'data-option-none' => $args['option_none'],
                    'data-option-none-value' => $args['option_none_value'],
                    'data-option-none-label' => $args['option_none_label'],
                    'data-placeholder' => $args['placeholder'],
                ),
                'options_cb' => $args['options_cb'],
                'default' => $args['default']
            )
        )
    );

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
        'label'     => __( 'Number of times' ),
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
 * Utility function to get the times tag
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_utilities_post_tags() {

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
            'label'     => __( 'Post ID' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'post_title' => array(
            'label'     => __( 'Post Title' ),
            'type'      => 'text',
            'preview'   => __( 'The post Title', 'automatorwp' ),
        ),
        'post_type'  => array(
            'label' => __( 'Post Type' ),
            'type'  => 'text',
            'preview'   => __( 'post', 'automatorwp' ),
        ),
        'post_author'  => array(
            'label'     => __( 'Post Author ID' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'post_author_email'  => array(
            'label'     => __( 'Post Author Email' ),
            'type'      => 'email',
            'preview'   => 'contact@automatorwp.com',
        ),
        'post_content'  => array(
            'label' => __( 'Post Content' ),
            'type'  => 'text',
            'preview'   => __( 'The post content', 'automatorwp' ),
        ),
        'post_excerpt'  => array(
            'label' => __( 'Post Excerpt' ),
            'type'  => 'text',
            'preview'   => __( 'The post excerpt', 'automatorwp' ),
        ),
        'post_status'  => array(
            'label' => __( 'Post Status' ),
            'type'  => 'text',
            'preview'   => __( 'publish', 'automatorwp' ),
        ),
        'post_parent' => array(
            'label'     => __( 'Post Parent ID' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'menu_order' => array(
            'label'     => __( 'Post Menu Order' ),
            'type'      => 'integer',
            'preview'   => '1',
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
            'condition' => array(
                'name' => __( 'Condition:', 'automatorwp' ),
                'type' => 'select',
                'options' => array(
                    'equal'  => __( 'equal to', 'automatorwp' ),
                    'not_equal' => __( 'not equal to', 'automatorwp' ),
                    'less_than'  => __( 'less than', 'automatorwp' ),
                    'greater_than'  => __( 'greater than', 'automatorwp' ),
                    'less_or_equal' => __( 'less or equal to', 'automatorwp' ),
                    'greater_or_equal' => __( 'greater or equal to', 'automatorwp' ),
                ),
                'default' => 'equal'
            )
        )
    );
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
 * Utility function to get the condition option parameter
 *
 * @since 1.0.0
 *
 * @param int|float $to_match   Number to match
 * @param int|float $to_compare Number to compare
 * @param string    $condition  The coondition to compare numbers
 *
 * @return bool
 */
function automatorwp_number_condition_matches( $to_match, $to_compare, $condition ) {

    $matches = false;

    switch( $condition ) {
        case 'equal':
        case '=':
            $matches = ( $to_match = $to_compare );
            break;
        case 'not_equal':
        case '!=':
            $matches = ( $to_match != $to_compare );
            break;
        case 'less_than':
        case '<':
            $matches = ( $to_match < $to_compare );
            break;
        case 'greater_than':
        case '>':
            $matches = ( $to_match > $to_compare );
            break;
        case 'less_or_equal':
        case '<=':
            $matches = ( $to_match <= $to_compare );
            break;
        case 'greater_or_equal':
        case '>=':
            $matches = ( $to_match >= $to_compare );
            break;
    }

    return $matches;
}