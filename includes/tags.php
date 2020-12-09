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
 * Get tags
 *
 * @since 1.0.0
 *
 * @param array $tags The global tags
 *
 * @return array
 */
function automatorwp_get_tags() {

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

    $tags['user']['tags']['reset_password_url'] = array(
        'label'     => __( 'Reset password URL', 'automatorwp' ),
        'type'      => 'text',
        'preview'   => get_option( 'home' ) . '/wp-login.php?action=rp',
    );

    $tags['user']['tags']['reset_password_link'] = array(
        'label'     => __( 'Reset password link', 'automatorwp' ),
        'type'      => 'text',
        'preview'   => '<a href="' . get_option( 'home' ) . '/wp-login.php?action=rp' . '">' . __( 'Click here to reset your password', 'automatorwp' ) . '</a>',
    );

    $tags['user']['tags']['user_meta:META_KEY'] = array(
        'label'     => __( 'User Meta', 'automatorwp' ),
        'type'      => 'text',
        'preview'   => __( 'User meta value, replace "META_KEY" by the user meta key', 'automatorwp' ),
    );

    /**
     * Filter tags
     *
     * @since 1.0.0
     *
     * @param array $tags The tags
     *
     * @return array
     */
    return apply_filters( 'automatorwp_get_tags', $tags );

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

    $tags = automatorwp_get_tags();

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
 * @param stdClass  $automation The automation object
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 *
 * @return string
 */
function automatorwp_get_tags_selector_html( $automation, $object, $item_type ) {

    $tags = automatorwp_get_automation_tags( $automation->id );

    if( $automation->type === 'anonymous' && $object->type === 'automatorwp_anonymous_user' && isset( $tags['user'] ) ) {
        unset( $tags['user'] );
    }

    /**
     * Available filter to override tags displayed on the tag selector
     *
     * @since 1.3.0
     *
     * @param array     $tags       The tags
     * @param stdClass  $automation The automation object
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The item type (trigger|action)
     *
     * @return array
     */
    $tags = apply_filters( 'automatorwp_tags_selector_html_tags', $tags, $automation, $object, $item_type );

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
            $text = '<strong>' . esc_attr( $tag['label'] ) . '</strong> <span>' . ( isset( $tag['preview'] ) ? htmlspecialchars( esc_attr( $tag['preview'] ) ) : '' ) . '</span>'; ?>

            <option value="<?php echo esc_attr( $tag_id ); ?>" data-text="<?php echo $text; ?>"><?php echo $tag['label']; ?></option>
        <?php endforeach; ?>

    </optgroup>

    <?php $html = ob_get_clean();

    return $html;

}

/**
 * Parse automation tags to received content
 *
 * @since 1.1.0
 *
 * @param int       $automation_id  The automation ID
 * @param int       $user_id        The user ID
 * @param mixed     $content        The content to parse (arrays supported)
 *
 * @return string
 */
function automatorwp_parse_automation_tags( $automation_id = 0, $user_id = 0, $content = '' ) {

    // Check if content given is an array to parse each array element
    if( is_array( $content ) ) {

        foreach( $content as $k => $v ) {
            // Replace all tags on this array element
            $content[$k] = automatorwp_parse_automation_tags( $automation_id, $user_id, $v );
        }

        return $content;
        
    }

    // Get all tags replacements to being passed to all actions
    $replacements = automatorwp_get_automation_tags_replacements( $automation_id, $user_id, $content );

    $tags = array_keys( $replacements );

    $parsed_content = $content;

    // First, parse dynamic tags like post meta, user meta and other plugin tags

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
    $parsed_content = apply_filters( 'automatorwp_parse_automation_tags', $parsed_content, $replacements, $automation_id, $user_id, $content );

    // Finally, parse automation tags ensuring that all tags not parsed will be empty
    $parsed_content = str_replace( $tags, $replacements, $parsed_content );

    return $parsed_content;

}

/**
 * Get the automation tags replacements
 *
 * @since 1.0.0
 *
 * @param int       $automation_id  The automation ID
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 *
 * @return array
 */
function automatorwp_get_automation_tags_replacements( $automation_id = 0, $user_id = 0, $content = '' ) {

    $replacements = array();

    // Look for tags
    preg_match_all( "/\{\s*(.*?)\s*\}/", $content, $matches );

    if( is_array( $matches ) && isset( $matches[1] ) ) {

        foreach( $matches[1] as $tag_name ) {
            // Setup tags replacements
            $replacements['{' . $tag_name . '}'] = automatorwp_get_tag_replacement( $tag_name, $automation_id, $user_id, $content );
        }

    }

    // Get automation triggers to pass their tags
    $triggers = automatorwp_get_automation_triggers( $automation_id );

    foreach( $triggers as $trigger ) {

        $trigger_replacements = automatorwp_get_trigger_tags_replacements( $trigger, $user_id, $content );

        foreach( $trigger_replacements as $trigger_tag => $trigger_replacement ) {
            // Tags on triggers are as {id:tag}
            $replacements['{' . $trigger->id . ':' . $trigger_tag. '}'] = $trigger_replacement;
        }

    }

    /**
     * Available filter to setup custom replacements
     *
     * @since 1.0.0
     *
     * @param array     $replacements   Automation replacements
     * @param int       $automation_id  The automation ID
     * @param int       $user_id        The user ID
     * @param string    $content        The content to parse
     *
     * @return array
     */
    return apply_filters( 'automatorwp_get_automation_tags_replacements', $replacements, $automation_id, $user_id, $content );

}

/**
 * Get tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $tag_name       The tag name (without "{}")
 * @param int       $automation_id  The automation ID
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 *
 * @return string
 */
function automatorwp_get_tag_replacement( $tag_name = '', $automation_id = 0, $user_id = 0, $content = '' ) {

    $replacement = '';

    $user = get_userdata( $user_id );

    switch( $tag_name ) {
        case 'site_name':
            $replacement = get_bloginfo( 'name' );
            break;
        case 'site_url':
            $replacement = get_site_url();
            break;
        case 'admin_email':
            $replacement = get_bloginfo( 'admin_email' );
            break;
        case 'user_id':
            $replacement = ( $user ? $user->ID : '' );
            break;
        case 'user_login':
            $replacement = ( $user ? $user->user_login : '' );
            break;
        case 'user_email':
            $replacement = ( $user ? $user->user_email : '' );
            break;
        case 'display_name':
            $replacement = ( $user ? $user->display_name : '' );
            break;
        case 'first_name':
            $replacement = ( $user ? $user->first_name : '' );
            break;
        case 'last_name':
            $replacement = ( $user ? $user->last_name : '' );
            break;
        case 'reset_password_url':
        case 'reset_password_link':
            $key = ( $user ?  get_password_reset_key( $user ) : '' );
            $login = ( $user ?  rawurlencode( $user->user_login ) : '' );
            $url = ( $user ? network_site_url( 'wp-login.php?action=rp&key=' . $key . '&login=' . $login, 'login' ) : '' );

            if( $tag_name === 'reset_password_url' ) {
                $replacement = $url;
            } else if( $tag_name === 'reset_password_link' ) {
                $replacement = '<a href="' . $url . '">' . __( 'Click here to reset your password', 'automatorwp' ) . '</a>';
            }

            break;
    }

    /**
     * Filter the tag replacement
     *
     * @since 1.0.0
     *
     * @param string    $replacement    The tag replacement
     * @param string    $tag_name       The tag name (without "{}")
     * @param int       $automation_id  The automation ID
     * @param int       $user_id        The user ID
     * @param string    $content        The content to parse
     *
     * @return string
     */
    return apply_filters( 'automatorwp_get_tag_replacement', $replacement, $tag_name, $automation_id, $user_id, $content );

}

/**
 * Get the trigger tags replacements
 *
 * @since 1.0.0
 *
 * @param stdClass  $trigger    The trigger object
 * @param int       $user_id    The user ID
 * @param string    $content    The content to parse
 *
 * @return array
 */
function automatorwp_get_trigger_tags_replacements( $trigger, $user_id, $content = '' ) {

    global $automatorwp_last_anonymous_trigger_log_id;

    if( $user_id === 0 && absint( $automatorwp_last_anonymous_trigger_log_id ) !== 0 ) {
        // Get the last anonymous trigger log if is parsing tags for an anonymous user
        $log = automatorwp_get_log_object( $automatorwp_last_anonymous_trigger_log_id );
    } else {
        // Get the last trigger log (where data for tags replacement is)
        $log = automatorwp_get_user_last_completion( $trigger->id, $user_id, 'trigger' );
    }

    if( ! $log ) {
        return array();
    }

    ct_setup_table( 'automatorwp_logs' );

    $replacements = array();

    // Look for trigger tags
    preg_match_all( "/\{" . $trigger->id . ":\s*(.*?)\s*\}/", $content, $matches );

    if( is_array( $matches ) && isset( $matches[1] ) ) {

        foreach( $matches[1] as $tag_name ) {

            $replacements[$tag_name] = automatorwp_get_trigger_tag_replacement( $tag_name, $trigger, $user_id, $content, $log );

        }

    }

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
 * Trigger tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $tag_name       The tag name (without "{}")
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 * @param stdClass  $log            The last trigger log object
 *
 * @return string
 */
function automatorwp_get_trigger_tag_replacement( $tag_name, $trigger, $user_id, $content, $log ) {

    $replacement = '';

    switch( $tag_name ) {
        case 'times':
            $replacement = automatorwp_get_log_meta( $log->id, 'times', true );
            break;
    }

    // Post tags
    $post_tags = array_keys( automatorwp_utilities_post_tags() );

    // If is a post tag and log has a post assigned, pass its replacements
    if( in_array( $tag_name, $post_tags ) && $log->post_id !== 0 ) {

        $post = get_post( $log->post_id );

        switch( $tag_name ) {
            case 'post_id':
                $replacement = ( $post ? $post->ID : '' );
                break;
            case 'post_title':
                $replacement = ( $post ? $post->post_title : '' );
                break;
            case 'post_url':
                $replacement = (  $post ? get_permalink( $post->ID ) : '' );
                break;
            case 'post_link':
                $replacement = (  $post ? '<a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>' : '' );
                break;
            case 'post_type':
                $replacement = (  $post ? $post->post_type : '' );
                break;
            case 'post_author':
                $replacement = (  $post ? $post->post_author : '' );
                break;
            case 'post_author_email':
                $author = ( $post ? get_userdata( $post->post_author ) : false );

                $replacement = (  $author ? $author->user_email : '' );
                break;
            case 'post_content':
                $replacement = (  $post ? $post->post_content : '' );
                break;
            case 'post_excerpt':
                $replacement = (  $post ? $post->post_excerpt : '' );
                break;
            case 'post_status':
                $replacement = (  $post ? $post->post_status : '' );
                break;
            case 'post_parent':
                $replacement = (  $post ? $post->post_parent : '' );
                break;
            case 'menu_order':
                $replacement = (  $post ? $post->menu_order : '' );
                break;
        }

    }

    // Comment tags
    $comment_tags = array_keys( automatorwp_utilities_comment_tags() );

    // If is a comment tag, pass its replacements
    if( in_array( $tag_name, $comment_tags ) ) {

        $comment_id = (int) ct_get_object_meta( $log->id, 'comment_id', true );

        if( $comment_id !== 0 ) {

            $comment = get_comment( $comment_id );
            $post = get_post( $comment->comment_post_ID );

            switch( $tag_name ) {
                case 'comment_id':
                    $replacement = ( $comment ? $comment->comment_ID : '' );
                    break;
                case 'comment_post_id':
                    $replacement = ( $post ? $post->ID : '' );
                    break;
                case 'comment_post_title':
                    $replacement = ( $post ? $post->post_title : '' );
                    break;
                case 'comment_user_id':
                    $replacement = ( $comment ? $comment->user_id : '' );
                    break;
                case 'comment_author':
                    $replacement = ( $comment ? $comment->comment_author : '' );
                    break;
                case 'comment_author_email':
                    $replacement = ( $comment ? $comment->comment_author_email : '' );
                    break;
                case 'comment_author_url':
                    $replacement = ( $comment ? $comment->comment_author_url : '' );
                    break;
                case 'comment_author_ip':
                    $replacement = ( $comment ? $comment->comment_author_IP : '' );
                    break;
                case 'comment_content':
                    $replacement = ( $comment ? $comment->comment_content : '' );
                    break;
                case 'comment_type':
                    $replacement = ( $comment ? $comment->comment_type : '' );
                    break;
            }

        }

    }

    /**
     * Filter the trigger tag replacement
     *
     * @since 1.0.0
     *
     * @param string    $replacement    The tag replacement
     * @param string    $tag_name       The tag name (without "{}")
     * @param stdClass  $trigger        The trigger object
     * @param int       $user_id        The user ID
     * @param string    $content        The content to parse
     * @param stdClass  $log            The last trigger log object
     *
     * @return string
     */
    return apply_filters( 'automatorwp_get_trigger_tag_replacement', $replacement, $tag_name, $trigger, $user_id, $content, $log );

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

        // If log has a post assigned, pass the post meta replacements
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