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
 * Utility function to get the term option parameter
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function automatorwp_utilities_term_option( $args = array() ) {

    $args = wp_parse_args( $args, array(
        'name'              => __( 'Category:', 'automatorwp' ),
        'option_default'    => '',
        'option_none'       => true,
        'option_none_value' => 'any',
        'option_none_label' => __( 'any category', 'automatorwp' ),
        'taxonomy'          => 'category',
        'default'           => 'any'
    ) );

    return array(
        'from' => 'term',
        'default' => $args['option_default'],
        'fields' => array(
            'term' => automatorwp_utilities_term_field( $args )
        )
    );

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

    $args = wp_parse_args( $args, array(
        'name'              => __( 'Category:', 'automatorwp' ),
        'option_default'    => '',
        'option_none'       => true,
        'option_none_value' => 'any',
        'option_none_label' => __( 'any category', 'automatorwp' ),
        'taxonomy'          => 'category',
        'placeholder'       => __( 'Select a category', 'automatorwp' ),
        'default'           => 'any'
    ) );

    return array(
        'name' => $args['name'],
        'type' => 'select',
        'classes' => 'automatorwp-term-selector',
        'option_none' => $args['option_none'],
        'option_none_value' => $args['option_none_value'],
        'option_none_label' => $args['option_none_label'],
        'taxonomy' => $args['taxonomy'],
        'attributes' => array(
            'data-option-none' => $args['option_none'],
            'data-option-none-value' => $args['option_none_value'],
            'data-option-none-label' => $args['option_none_label'],
            'data-placeholder' => $args['placeholder'],
            'data-taxonomy' => $args['taxonomy'],
        ),
        'options_cb' => 'automatorwp_options_cb_terms',
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
function automatorwp_utilities_post_tags( $post_label = '' ) {

    if( empty( $post_label ) ) {
        $post_label = __( 'Post', 'automatorwp' );
    }

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
            'preview'   => __( 'The Title', 'automatorwp' ),
        ),
        'post_type'  => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Type', 'automatorwp' ), $post_label ),
            'type'      => 'text',
            'preview'   => __( 'post', 'automatorwp' ),
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
        'post_status'  => array(
            /* translators: %s: Post label (by default: Post). */
            'label'     => sprintf( __( '%s Status', 'automatorwp' ), $post_label ),
            'type'      => 'text',
            'preview'   => __( 'publish', 'automatorwp' ),
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