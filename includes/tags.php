<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Tags
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get the default tags
 *
 * @since 1.0.0
 *
 * @param array $tags The default tags
 *
 * @return array
 */
function automatorwp_get_default_tags() {

    $tags = array();

    // Site tags
    $tags['site'] = array(
        'label' => __( 'Site', 'automatorwp' ),
        'tags'  => array(),
        'icon'  => AUTOMATORWP_URL . 'assets/img/integration-default.svg',
    );

    $tags['site']['tags']['site_name'] = array(
        'label'     => __( 'Site name', 'automatorwp' ),
        'type'      => 'text',
        'preview'   => get_bloginfo( 'name' ),
    );

    $tags['site']['tags']['site_url'] = array(
        'label'     => __( 'Site URL', 'automatorwp' ),
        'type'      => 'text',
        'preview'   => get_site_url(),
    );

    $tags['site']['tags']['admin_email'] = array(
        'label'     => __( 'Admin email', 'automatorwp' ),
        'type'      => 'email',
        'preview'   => get_bloginfo( 'admin_email' ),
    );

    // User tags
    $tags['user'] = array(
        'label' => __( 'User', 'automatorwp' ),
        'tags'  => array(),
        'icon'  => AUTOMATORWP_URL . 'assets/img/integration-default.svg',
    );
    $tags['user']['tags']['user_id'] = array(
        'label'     => __( 'ID', 'automatorwp' ),
        'type'      => 'integer',
        'preview'   => '123',
    );

    $tags['user']['tags']['user_login'] = array(
        'label'     => __( 'Username', 'automatorwp' ),
        'type'      => 'text',
        'preview'   => 'automatorwp',
    );

    $tags['user']['tags']['user_email'] = array(
        'label'     => __( 'Email', 'automatorwp' ),
        'type'      => 'text',
        'preview'   => 'contact@automatorwp.com',
    );

    $tags['user']['tags']['display_name'] = array(
        'label'     => __( 'Display name', 'automatorwp' ),
        'type'      => 'text',
        'preview'   => __( 'AutomatorWP Plugin', 'automatorwp' ),
    );

    $tags['user']['tags']['first_name'] = array(
        'label'     => __( 'First name', 'automatorwp' ),
        'type'      => 'text',
        'preview'   => 'AutomatorWP',
    );

    $tags['user']['tags']['last_name'] = array(
        'label'     => __( 'Last name', 'automatorwp' ),
        'type'      => 'text',
        'preview'   => __( 'Plugin', 'automatorwp' ),
    );

    $tags['user']['tags']['user_meta:META_KEY'] = array(
        'label'     => __( 'User Meta', 'automatorwp' ),
        'type'      => 'text',
        'preview'   => __( 'User meta value, replace "META_KEY" by the user meta key', 'automatorwp' ),
    );

    /**
     * Filter the default tags
     *
     * @since 1.0.0
     *
     * @param array $tags The default tags
     *
     * @return array
     */
    return apply_filters( 'automatorwp_default_tags', $tags );

}

/**
 * Get all automation tags
 *
 * @since 1.0.0
 *
 * @param int $automation_id The automation ID
 *
 * @return array
 */
function automatorwp_get_automation_tags( $automation_id ) {

    $tags = automatorwp_get_default_tags();

    // Get all automation triggers to generate their tags
    $triggers = automatorwp_get_automation_triggers( $automation_id );

    foreach( $triggers as $object ) {

        $trigger_tags = automatorwp_get_trigger_tags( $object );

        // Append all trigger tags to the tags array
        foreach( $trigger_tags as $trigger_tag_id => $trigger_tag ) {
            // Note: Don't use array merge since trigger IDs indexes gets replaced
            $tags[$trigger_tag_id] = $trigger_tag;
        }

    }

    /**
     * Filter all automation tags
     *
     * @since 1.0.0
     *
     * @param array $tags           The automation tags
     * @param int   $automation_id  The automation ID
     *
     * @return array
     */
    return apply_filters( 'automatorwp_automation_tags', $tags, $automation_id );

}

/**
 * Get trigger tags
 *
 * @since 1.0.0
 *
 * @param stdClass $object The trigger object
 *
 * @return array
 */
function automatorwp_get_trigger_tags( $object ) {

    $trigger = automatorwp_get_trigger( $object->type );

    // Skip item if not has a trigger registered
    if( ! $trigger ) {
        return array();
    }

    // Skip trigger if not has any tags
    if( empty( $trigger['tags'] ) ) {
        return array();
    }

    $trigger_tags = array();

    foreach( $trigger['tags'] as $tag_id => $tag ) {
        $trigger_tags[$object->id . ':' . $tag_id] = $tag;
    }

    /**
     * Filter trigger tags to ally dynamic tags inserting
     *
     * @since 1.0.0
     *
     * @param array $tags       The trigger tags
     * @param int   $trigger    The trigger object
     *
     * @return array
     */
    $trigger_tags = apply_filters( 'automatorwp_trigger_tags', $trigger_tags, $object );

    // Skip trigger if not has any tags
    if( empty( $trigger_tags ) ) {
        return array();
    }

    $integration = automatorwp_get_integration( $trigger['integration'] );

    $tags = array();

    $tags[$object->id] = array(
        'label' => automatorwp_parse_automation_item_edit_label( $object, 'trigger', 'edit' ),
        'tags' => array(),
        'icon' => $integration['icon'],
    );

    $tags[$object->id]['tags'] = $trigger_tags;

    return $tags;

}

/**
 * Get the tags select element
 *
 * @since 1.0.0
 *
 * @param int $automation_id The automation ID
 *
 * @return string
 */
function automatorwp_get_tags_selector_html( $automation_id ) {

    $tags = automatorwp_get_automation_tags( $automation_id );

    ob_start(); ?>
    <select class="automatorwp-automation-tag-selector">

        <?php foreach( $tags as $tags_group_id => $tags_group ) {
            echo automatorwp_get_tags_selector_group_html( $tags_group_id, $tags_group );
        } ?>

    </select>

    <?php $html = ob_get_clean();

    return $html;

}

/**
 * Get optgroup element from a group of tags
 *
 * @since 1.0.0
 *
 * @param string    $tags_group_id  The tags group ID
 * @param array     $tags_group      The tags group args
 *
 * @return string
 */
function automatorwp_get_tags_selector_group_html( $tags_group_id, $tags_group ) {

    ob_start(); ?>

    <optgroup label="<?php echo esc_attr( $tags_group['label'] ); ?>"
              data-id="<?php echo esc_attr( $tags_group_id ); ?>"
              data-icon="<?php echo esc_attr( $tags_group['icon'] ); ?>">

        <?php foreach( $tags_group['tags'] as $tag_id => $tag ) :
            // Formatted text to make tags more visible
            $text = '<strong>' . $tag['label'] . '</strong> <span>' . ( isset( $tag['preview'] ) ? $tag['preview'] : '' ) . '</span>'; ?>

            <option value="<?php echo esc_attr( $tag_id ); ?>" data-text="<?php echo esc_attr( $text ); ?>"><?php echo $tag['label']; ?></option>
        <?php endforeach; ?>

    </optgroup>

    <?php $html = ob_get_clean();

    return $html;

}

/**
 * Parse automation tags to received content
 * Note: Uses a the global var $automatorwp_tags_parser for caching all the vars
 *
 * @since 1.1.0
 *
 * @param int $automation_id    The automation ID
 * @param int $user_id          The user ID
 *
 * @return string
 */
function automatorwp_parse_automation_tags( $automation_id = 0, $user_id = 0, $content = '' ) {

    global $automatorwp_tags_parser;

    // Initialize tags parser
    if( ! is_array( $automatorwp_tags_parser ) ) {
        $automatorwp_tags_parser = array();
    }

    // Initialize tags parser for this user
    if( ! isset( $automatorwp_tags_parser[$user_id] ) ) {
        $automatorwp_tags_parser[$user_id] = array();
    }

    if( ! isset( $automatorwp_tags_parser[$user_id][$automation_id] ) ) {

        // Get all tags replacements to being passed to all actions
        $replacements = automatorwp_get_automation_tags_replacements( $automation_id, $user_id );

        $tags = array_keys( $replacements );

        $automatorwp_tags_parser[$user_id][$automation_id] = array(
            'replacements' => $replacements,
            'tags' => $tags,
        );

    }

    $replacements = $automatorwp_tags_parser[$user_id][$automation_id]['replacements'];
    $tags = $automatorwp_tags_parser[$user_id][$automation_id]['tags'];

    // Parse automation tags
    $parsed_content = str_replace( $tags, $replacements, $content );

    // Parse user meta tags (required here since user meta tags are based on the content)
    $parsed_content = automatorwp_parse_user_meta_tags( $user_id, $parsed_content );

    // Parse post meta tags (required here since post meta tags are based on the content)
    $parsed_content = automatorwp_parse_post_meta_tags( $automation_id, $user_id, $parsed_content );

    /**
     * Available filter to setup custom replacements
     *
     * @since 1.0.0
     *
     * @param string    $parsed_content     Content parsed
     * @param array     $replacements       Automation replacements
     * @param int       $automation_id      The automation ID
     * @param int       $user_id            The user ID
     * @param string    $content            The content to parse
     *
     * @return string
     */
    return apply_filters( 'automatorwp_parse_automation_tags', $parsed_content, $replacements, $automation_id, $user_id, $content );

}

/**
 * Get the automation tags replacements
 *
 * @since 1.0.0
 *
 * @param int $automation_id    The automation ID
 * @param int $user_id          The user ID
 *
 * @return array
 */
function automatorwp_get_automation_tags_replacements( $automation_id = 0, $user_id = 0 ) {

    $user = get_userdata( $user_id );

    $replacements = array(
        '{site_name}'       => get_bloginfo( 'name' ),
        '{site_url}'        => get_site_url(),
        '{admin_email}'     => get_bloginfo( 'admin_email' ),
        '{user_id}'         => ( $user ? $user->ID : '' ),
        '{user_login}'      => ( $user ? $user->user_login : '' ),
        '{user_email}'      => ( $user ? $user->user_email : '' ),
        '{display_name}'    => ( $user ? $user->display_name : '' ),
        '{first_name}'      => ( $user ? $user->first_name : '' ),
        '{last_name}'       => ( $user ? $user->last_name : '' ),
    );

    // Get automation triggers to pass their tags
    $triggers = automatorwp_get_automation_triggers( $automation_id );

    foreach( $triggers as $trigger ) {

        $trigger_replacements = automatorwp_get_trigger_tags_replacements( $trigger, $user_id );

        foreach( $trigger_replacements as $trigger_tag => $trigger_replacement ) {
            // Tags on actions are as {id:tag}
            $replacements['{' . $trigger->id . ':' . $trigger_tag. '}'] = $trigger_replacement;
        }

    }

    /**
     * Available filter to setup custom replacements
     *
     * @since 1.0.0
     *
     * @param array $replacements   Automation replacements
     * @param int   $automation_id  The automation ID
     * @param int   $user_id        The user ID
     *
     * @return array
     */
    return apply_filters( 'automatorwp_get_automation_tags_replacements', $replacements, $automation_id, $user_id );

}

/**
 * Get the trigger tags replacements
 *
 * @since 1.0.0
 *
 * @param stdClass  $trigger    The trigger object
 * @param int       $user_id    The user ID
 *
 * @return array
 */
function automatorwp_get_trigger_tags_replacements( $trigger, $user_id ) {

    // Get the last trigger log (where data for tags replacement will be get
    $log = automatorwp_get_user_last_completion( $trigger->id, $user_id, 'trigger' );

    if( ! $log ) {
        return array();
    }

    ct_setup_table( 'automatorwp_logs' );

    $replacements = array();

    // If log has a post assigned, pass his replacements
    if( $log->post_id !== 0 ) {
        $replacements = automatorwp_get_post_tags_replacements( $log->post_id );
    }

    // Times replacement by default
    $replacements['times'] = ct_get_object_meta( $log->id, 'times', true );

    /**
     * Filter to setup custom trigger tags replacements
     *
     * Note: Post and times tags replacements are already passed
     *
     * @since 1.0.0
     *
     * @param array     $replacements   The trigger replacements
     * @param stdClass  $trigger        The trigger object
     * @param int       $user_id        The user ID
     * @param stdClass  $log            The last trigger log object
     *
     * @return array
     */
    $replacements = apply_filters( 'automatorwp_trigger_tags_replacements', $replacements, $trigger, $user_id, $log );

    ct_reset_setup_table();

    return $replacements;

}

/**
 * Get the post tags replacements
 *
 * @since 1.0.0
 *
 * @param int $post_id The post ID
 *
 * @return array
 */
function automatorwp_get_post_tags_replacements( $post_id ) {

    $replacements = array();
    $post = get_post( $post_id );
    $author = ( $post ? get_userdata( $post->post_author ) : false );

    $replacements['post_id'] = ( $post ? $post->ID : '' );
    $replacements['post_title'] = ( $post ? $post->post_title : '' );
    $replacements['post_type'] = ( $post ? $post->post_type : '' );
    $replacements['post_author'] = ( $post ? $post->post_author : '' );
    $replacements['post_author_email'] = ( $author ? $author->user_email : '' );
    $replacements['post_content'] = ( $post ? $post->post_content : '' );
    $replacements['post_excerpt'] = ( $post ? $post->post_excerpt : '' );
    $replacements['post_status'] = ( $post ? $post->post_status : '' );
    $replacements['post_parent'] = ( $post ? $post->post_parent : '' );
    $replacements['menu_order'] = ( $post ? $post->menu_order : '' );

    /**
     * Filter to set custom post tags replacements
     *
     * @since 1.0.0
     *
     * @param array     $replacements
     * @param int       $post_id
     * @param WP_Post   $post
     * @param WP_User   $author
     *
     * @return array
     */
    return apply_filters( 'automatorwp_get_post_tags_replacements', $replacements, $post_id, $post, $author );

}

/**
 * Get the user meta tags replacements
 *
 * @since 1.1.0
 *
 * @param int       $user_id The user ID
 * @param string    $content The content to parse
 *
 * @return array
 */
function automatorwp_get_user_meta_tags_replacements( $user_id = 0, $content = '' ) {

    $replacements = array();

    // Look for user meta tags
    preg_match_all( "/\{user_meta:\s*(.*?)\s*\}/", $content, $matches );

    if( is_array( $matches ) && isset( $matches[1] ) ) {

        foreach( $matches[1] as $meta_key ) {
            $replacements['{user_meta:' . $meta_key . '}'] = get_user_meta( $user_id, $meta_key, true );
        }

    }

    /**
     * Filter to set custom user meta tags replacements
     *
     * @since 1.1.0
     *
     * @param array     $replacements   Replacements
     * @param int       $user_id        The user ID
     * @param string    $content        The content to parse
     *
     * @return array
     */
    return apply_filters( 'automatorwp_get_user_meta_tags_replacements', $replacements, $user_id, $content );

}

/**
 * Parse user meta tags replacements
 *
 * @since 1.1.0
 *
 * @param int       $user_id The user ID
 * @param string    $content The content to replace
 *
 * @return string
 */
function automatorwp_parse_user_meta_tags( $user_id = 0, $content = '' ) {

    $parsed_content = $content;

    // Get user meta tags replacements
    $replacements = automatorwp_get_user_meta_tags_replacements( $user_id, $content );

    if( $replacements ) {

        $tags = array_keys( $replacements );

        // Replace all tags by their replacements
        $parsed_content = str_replace( $tags, $replacements, $content );

    }

    /**
     * Filter to modify a content parsed with user metas
     *
     * @since 1.1.0
     *
     * @param string    $parsed_content Content parsed
     * @param array     $replacements   Replacements
     * @param int       $user_id        The user ID
     * @param string    $content        The content to parse
     *
     * @return string
     */
    return apply_filters( 'automatorwp_parse_user_meta_tags', $parsed_content, $replacements, $user_id, $content );

}

/**
 * Get the post meta tags replacements
 *
 * @since 1.1.0
 *
 * @param int       $trigger_id The trigger ID
 * @param int       $post_id    The post ID
 * @param string    $content    The content to parse
 *
 * @return array
 */
function automatorwp_get_post_meta_tags_replacements( $trigger_id = 0, $post_id = 0, $content = '' ) {

    $replacements = array();

    // Look for post meta tags
    preg_match_all( "/\{" . $trigger_id . ":post_meta:\s*(.*?)\s*\}/", $content, $matches );

    if( is_array( $matches ) && isset( $matches[1] ) ) {

        foreach( $matches[1] as $meta_key ) {
            // Replace {ID:post_meta:KEY} by the post meta value
            $replacements['{' . $trigger_id . ':post_meta:' . $meta_key . '}'] = get_post_meta( $post_id, $meta_key, true );
        }

    }

    /**
     * Filter to set custom post meta tags replacements
     *
     * @since 1.1.0
     *
     * @param array     $replacements   Replacements
     * @param int       $post_id        The post ID
     * @param string    $content        The content to parse
     *
     * @return array
     */
    return apply_filters( 'automatorwp_get_post_meta_tags_replacements', $replacements, $post_id, $content );

}

/**
 * Parse post meta tags replacements
 *
 * @since 1.1.0
 *
 * @param int       $automation_id  The automation ID
 * @param int       $user_id        The user ID
 * @param string    $content        The content to replace
 *
 * @return string
 */
function automatorwp_parse_post_meta_tags( $automation_id = 0, $user_id = 0, $content = '' ) {

    $parsed_content = $content;

    // Get automation triggers to pass their tags
    $triggers = automatorwp_get_automation_triggers( $automation_id );

    $replacements = array();

    foreach( $triggers as $trigger ) {

        // Get the last trigger log (where data for tags replacement will be get
        $log = automatorwp_get_user_last_completion( $trigger->id, $user_id, 'trigger' );

        if( ! $log ) {
            continue;
        }

        // If log has a post assigned, pass his post meta replacements
        if( $log->post_id !== 0 ) {
            $trigger_replacements = automatorwp_get_post_meta_tags_replacements( $trigger->id, $log->post_id, $content );

            $replacements = array_merge( $replacements, $trigger_replacements );
        }

    }

    if( $replacements ) {

        $tags = array_keys( $replacements );

        // Replace all tags by their replacements
        $parsed_content = str_replace( $tags, $replacements, $content );

    }

    /**
     * Filter to modify a content parsed with post metas
     *
     * @since 1.1.0
     *
     * @param string    $parsed_content Content parsed
     * @param array     $replacements   Replacements
     * @param int       $automation_id  The automation ID
     * @param int       $user_id        The user ID
     * @param string    $content        The content to parse
     *
     * @return string
     */
    return apply_filters( 'automatorwp_parse_post_meta_tags', $parsed_content, $replacements, $automation_id, $user_id, $content );

}