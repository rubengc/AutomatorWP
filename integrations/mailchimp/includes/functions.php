<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Mailchimp\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get authorization from Mailchimp
 *
 * @since 1.0.0
 *
 * @return MailchimpMarketing\ApiClient|false
 */
function automatorwp_mailchimp_get_authorization( ) {

    $api_key = automatorwp_mailchimp_get_option( 'api_key', '' );
    $server_prefix = automatorwp_mailchimp_get_option( 'server_prefix', '' );

    if( empty( $api_key ) || empty( $server_prefix ) ) {
        return false;
    }

    // Mailchimp API
    require_once AUTOMATORWP_MAILCHIMP_DIR . 'vendor/autoload.php';

    $mailchimp = new MailchimpMarketing\ApiClient();
        
    $mailchimp->setConfig([
        'apiKey' => $api_key,
        'server' => $server_prefix
    ]);

    return $mailchimp;

}


/**
 * Get lists/audiences from Mailchimp
 *
 * @since 1.0.0
 * 
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_mailchimp_options_cb_lists( $field ) {

        // Setup vars
        $value = $field->escaped_value;
        $none_value = 'any';
        $none_label = __( 'any list', 'automatorwp' );
        $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );
        
        if( ! empty( $value ) ) {
            if( ! is_array( $value ) ) {
                $value = array( $value );
            }
    
            foreach( $value as $list_id ) {
    
                // Skip option none
                if( $list_id === $none_value ) {
                    continue;
                }
    
                $options[$list_id] = automatorwp_mailchimp_get_list_name( $list_id );
            }
        }
    
        return $options;

}

/**
 * Get the list/audience name
 *
 * @since 1.0.0
 * 
 * @param string $list_id
 *
 * @return array
 */
function automatorwp_mailchimp_get_list_name( $list_id ) {

    $audiences = array();

    $mailchimp = automatorwp_mailchimp_get_authorization( );

    // Bail if no authorization
    if ( ! $mailchimp ){
        return;
    }

    $list = $mailchimp->lists->getList( $list_id );

    return $list->name;
}



/**
 * Get the lists/audiences
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_mailchimp_get_lists() {

    $audiences = array();

    $mailchimp = automatorwp_mailchimp_get_authorization();

    // Bail if no authorization
    if ( ! $mailchimp ){
        return;
    }

    $all_lists = $mailchimp->lists->getAllLists();
        
    foreach ( $all_lists->lists as $list ){
    
        $audiences[] = array(
            'id' => $list->id,
            'name' => $list->name,
        );
        
    }

    return $audiences;
}

/**
 * Options callback for select2 fields assigned to tags
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_mailchimp_options_cb_tags( $field ) {
    
    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any tag', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    $list_id = ct_get_object_meta( $field->object_id, 'audience', true );
    
 
    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $tag_id ) {

            // Skip option none
            if( $tag_id === $none_value ) {
                continue;
            }

            $options[$tag_id] = automatorwp_mailchimp_get_tag_name( $list_id, $tag_id );
        }
    }

    return $options;

}

/**
 * Get tags from Mailchimp
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_mailchimp_get_tags( $list_id ) {

    $tags = array();
    $mailchimp = automatorwp_mailchimp_get_authorization();

    // Bail if no authorization
    if ( ! $mailchimp ) {
        return array();
    }

    $all_tags = $mailchimp->lists->listSegments( $list_id, $fields = null, $exclude_fields = null, $count = '1000', $offset = '0', $type = 'static' );

    foreach ( $all_tags->segments as $tag ){
        $tags[] = array(
            'id' => $tag->id,
            'name' => $tag->name,
        );
    }

    return $tags;
}

/**
 * Get the tag names
 *
 * @since 1.0.0
 *
 * @param string $list_id
 *
 * @return string
 */
function automatorwp_mailchimp_get_tag_name( $list_id, $tag_id ) {

    $mailchimp = automatorwp_mailchimp_get_authorization();

    // Bail if no authorization
    if ( ! $mailchimp ){
        return '';
    }

    $all_tags = $mailchimp->lists->listSegments( $list_id, $fields = null, $exclude_fields = null, $count = '1000', $offset = '0', $type = 'static' );

    foreach ( $all_tags->segments as $tag ){

        if ( $tag_id == $tag->id ){
            $tag_name = $tag->name;
            break;
        }
    }

    return $tag_name;

}

/**
 * Options callback for select2 fields assigned to campaigns
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_mailchimp_options_cb_campaigns( $field ) {
    
    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any campaign', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );
 
    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $campaign_id ) {

            // Skip option none
            if( $campaign_id === $none_value ) {
                continue;
            }

            $options[$campaign_id] = automatorwp_mailchimp_get_campaign_name( $campaign_id );
        }
    }

    return $options;

}

/**
 * Get the list of campaigns
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_mailchimp_get_campaigns() {

    $options = array();

    $mailchimp = automatorwp_mailchimp_get_authorization();

    // Bail if no authorization
    if ( ! $mailchimp ){
        return;
    }

    $all_campaigns = $mailchimp->campaigns->list();
        
    foreach ( $all_campaigns->campaigns as $campaign ){
        
        $options[] = array(
            'id' => $campaign->id,
            'name' => $campaign->settings->title,
        );
        
    }

    return $options;
}

/**
 * Get the campaign names
 *
 * @since 1.0.0
 * 
 * @param string $list_id
 *
 * @return array
 */
function automatorwp_mailchimp_get_campaign_name( $campaign_id ) {

    $mailchimp = automatorwp_mailchimp_get_authorization();

    // Bail if no authorization
    if ( ! $mailchimp ){
        return;
    }

    $campaign_name = $mailchimp->campaigns->get( $campaign_id );

    return $campaign_name->settings->title;

}

/**
 * Options callback for select2 fields assigned to campaigns
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_mailchimp_options_cb_templates( $field ) {
    
    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any template', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );
 
    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $template_id ) {

            // Skip option none
            if( $template_id === $none_value ) {
                continue;
            }

            $options[$template_id] = automatorwp_mailchimp_get_template_name( $template_id );
        }
    }

    return $options;

}

/**
 * Get the list of campaigns
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_mailchimp_get_templates() {

    $options = array();

    $mailchimp = automatorwp_mailchimp_get_authorization();

    // Bail if no authorization
    if ( ! $mailchimp ){
        return;
    }

    $all_templates = $mailchimp->templates->list();
        
    foreach ( $all_templates->templates as $template ){
        
        $options[] = array(
            'id' => $template->id,
            'name' => $template->name,
        );
        
    }
    
    return $options;
}

/**
 * Get the campaign names
 *
 * @since 1.0.0
 * 
 * @param string $list_id
 *
 * @return array
 */
function automatorwp_mailchimp_get_template_name( $template_id ) {

    $mailchimp = automatorwp_mailchimp_get_authorization();
    
    // Bail if no authorization
    if ( ! $mailchimp ){
        return;
    }
    
    $template = $mailchimp->templates->get( $template_id );

    return $template->name;

}