<?php
/**
 * Functions
 *
 * @package     AutomatorWP\BuddyBoss\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Options callback for select2 fields assigned to groups
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_buddyboss_options_cb_group( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any group', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $group_id ) {

            // Skip option none
            if( $group_id === $none_value ) {
                continue;
            }

            $options[$group_id] = automatorwp_buddyboss_get_group_title( $group_id );
        }
    }

    return $options;

}

/**
 * Get the group title
 *
 * @since 1.0.0
 *
 * @param int $group_id
 *
 * @return string|null
 */
function automatorwp_buddyboss_get_group_title( $group_id ) {

    // Empty title if no ID provided
    if( absint( $group_id ) === 0 ) {
        return '';
    }

    $group = groups_get_group( $group_id );

    return $group->name;

}

/**
 * Options callback for select fields assigned to member types
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_buddyboss_member_types_options_cb( $field ) {

    $none_value = 'any';
    $none_label = __( 'any profile type', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    $member_types = bp_get_member_types( array(), 'objects' );

    foreach( $member_types as $member_type => $member_type_obj ) {
        $options[$member_type] = $member_type_obj->labels['singular_name'];
    }

    return $options;

}

/**
 * Helper function to get the preview from a URL
 *
 * @since 1.5.0
 *
 * @param string $link
 *
 * @return array|false
 */
function automatorwp_buddyboss_get_link_preview( $link ) {

    // Bail if not is a valid URL
    if ( ! filter_var( $link, FILTER_VALIDATE_URL ) ) {
        return false;
    }

    // Extract HTML using curl
    $ch = curl_init();

    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_URL, $link );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );

    $data = curl_exec( $ch );
    curl_close( $ch );

    // Load HTML to DOM Object
    $dom = new DOMDocument();
    @$dom->loadHTML( $data );

    $title = '';
    $description = '';
    $image_url = '';

    // Parse DOM to get Title
    $nodes = $dom->getElementsByTagName('title');
    $title = $nodes->item(0)->nodeValue;

    // Parse DOM to get Meta Description
    $metas = $dom->getElementsByTagName('meta');

    for ($i = 0; $i < $metas->length; $i ++) {
        $meta = $metas->item( $i );

        // Description meta
        if ( $meta->getAttribute('name') == 'description' && empty( $description ) ) {
            $description = $meta->getAttribute('content');
        }

        // OG Metas
        if ( $meta->getAttribute('property') == 'og:title' ) {
            $title = $meta->getAttribute('content');
        }

        if ( $meta->getAttribute('property') == 'og:description' ) {
            $description = $meta->getAttribute('content');
        }

        if ( $meta->getAttribute('property') == 'og:image' ) {
            $image_url = $meta->getAttribute('content');
        }
    }

    // Parse DOM to get Images
    if( empty( $image_url ) ) {
        $images = $dom->getElementsByTagName('img');

        for ( $i = 0; $i < $images->length; $i ++) {
            $image = $images->item( $i );
            $src = $image->getAttribute( 'src' );

            if( filter_var( $src, FILTER_VALIDATE_URL ) ) {
                $image_url = $src;
                break;
            }
        }
    }

    return array(
        'url' => $link,
        'title' => $title,
        'description' => $description,
        'image_url' => $image_url
    );

}