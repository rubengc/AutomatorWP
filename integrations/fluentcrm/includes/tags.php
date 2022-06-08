<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Integrations\FluentCRM\Tags
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Contact tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_fluentcrm_contact_tags() {

    global $automatorwp_fluentcrm_contact_tags;

    if( ! is_array( $automatorwp_fluentcrm_contact_tags ) ) {

        $contact_tags = array(
            'fluentcrm_contact_field:status' => array(
                'label'     => __( 'Subscription status', 'automatorwp' ),
                'type'      => 'text',
                'preview'   => __( 'Subscribed', 'automatorwp' ),
            ),
        );

        // Standard fields
        $fields = \FluentCrm\App\Models\Subscriber::mappables();

        foreach( $fields as $field_id => $field_label ) {
            $contact_tags['fluentcrm_contact_field:' . $field_id] = array(
                'label'     => $field_label,
                'type'      => 'text',
                'preview'   => automatorwp_fluentcrm_get_contact_tag_preview( $field_id, $field_label ),
            );
        }

        // Custom fields
        $custom_fields = new \FluentCrm\App\Models\CustomContactField();
        $custom_fields = $custom_fields->getGlobalFields()['fields'];

        foreach( $custom_fields as $custom_field ) {
            $contact_tags['fluentcrm_contact_field:' . $custom_field['slug']] = array(
                'label'     => $custom_field['label'],
                'type'      => 'text',
                'preview'   => sprintf( __( 'The contact %s field', 'automatorwp' ), strtolower( $custom_field['label'] ) ),
            );
        }

        /**
         * Filter contact tags
         *
         * @since 1.0.0
         *
         * @param array $tags
         *
         * @return array
         */
        $contact_tags = apply_filters( 'automatorwp_fluentcrm_contact_tags', $contact_tags );

        $automatorwp_fluentcrm_contact_tags = $contact_tags;

    }

    return $automatorwp_fluentcrm_contact_tags;

}

/**
 * Get the contact tag preview
 *
 * @since 1.0.0
 *
 * @param string $field_id
 * @param string $field_label
 *
 * @return string
 */
function automatorwp_fluentcrm_get_contact_tag_preview( $field_id, $field_label ) {

    switch( $field_id ) {
        case 'prefix': return __( 'Mr', 'automatorwp' );
        case 'first_name': return 'AutomatorWP';
        case 'last_name': return 'Plugin';
        case 'full_name': return 'AutomatorWP Plugin';
        case 'email': return 'contact@automatorwp.com';
        case 'timezone': break;
        case 'address_line_1': return __( 'False Street, 123', 'automatorwp' );
        case 'address_line_2': return __( 'First floor, door 2', 'automatorwp' );
        case 'city': return __( 'Brooklyn', 'automatorwp' );
        case 'state': return __( 'New York', 'automatorwp' );
        case 'postal_code': return '12345';
        case 'country': return __( 'United States', 'automatorwp' );
        case 'ip': return '255.255.255.255';
        case 'phone': return __( '202-555-1234', 'automatorwp' );
        case 'source': break;
        case 'date_of_birth': return date( 'Y-m-d' );

    }

    return sprintf( __( 'The contact %s field', 'automatorwp' ), strtolower( $field_label ) );

}

/**
 * Order meta tag replacement
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
function automatorwp_fluentcrm_get_contact_field_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {

    global $wpdb;

    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Bail if no order ID attached
    if( ! $trigger_args ) {
        return $replacement;
    }

    // Bail if trigger is not from this integration
    if( $trigger_args['integration'] !== 'fluentcrm' ) {
        return $replacement;
    }

    // Bail if not is the contact field tag
    if( substr( $tag_name, 0, 23 ) !== 'fluentcrm_contact_field' ) {
        return $replacement;
    }

    // Bail if FluentCRM API not exists
    if ( ! function_exists( 'FluentCrmApi' ) ) {
        return $replacement;
    }

    $subscriber_email = automatorwp_get_log_meta( $log->id, 'subscriber_email', true );

    // Bail if no email attached
    if( $subscriber_email === '' ) {
        return $replacement;
    }

    $field = explode( ':', $tag_name );

    // Bail if field can't be found
    if( ! isset( $field[1] ) ) {
        return $replacement;
    }

    $field = $field[1];

    // Query the subscriber by email address
    $contact_api = FluentCrmApi( 'contacts' );
    $subscriber = $contact_api->getContactByUserRef( $subscriber_email );

    if ( isset( $subscriber->$field ) ) {
        // Get the field from the subscriber object
        $replacement = $subscriber->$field;
    } else {
        // Check if subscriber has the ID attached
        if ( isset( $subscriber->id ) ) {

            // Get the field from the subscriber meta
            $field_value = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT `value` 
                    FROM {$wpdb->prefix}fc_subscriber_meta 
                    WHERE `subscriber_id` = %d AND `key` = %s",
                    $subscriber->id,
                    $field
                )
            );

            // Unserialize
            if ( is_serialized( $field_value ) ) {
                $field_value = maybe_unserialize( $field_value );
            }

            // Turn arrays into a comma-separated list
            if ( is_array( $field_value ) ) {
                $field_value = implode( ', ', $field_value );
            }

            $replacement = $field_value;

        }


    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_fluentcrm_get_contact_field_tag_replacement', 10, 6 );