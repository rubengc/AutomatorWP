<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Charitable\Tags
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Appointment tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_charitable_get_donation_tags() {

    return array(
        'donation_id' => array(
            'label'     => __( 'Donation ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The donation ID',
        ),
        'donation_title' => array(
            'label'     => __( 'Donation title', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The donation title',
        ),
        'donation_amount' => array(
            'label'     => __( 'Donation Amount', 'automatorwp' ),
            'type'      => 'float',
            'preview'   => '123.45',
        ),
        'donation_status' => array(
            'label'     => __( 'Donation status', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The donation status',
        ),
        'donation_payment_method' => array(
            'label'     => __( 'Donation payment method', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The donation payment method',
        ),
        'donor_id' => array(
            'label'     => __( 'Donor ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The donor ID',
        ),
        'donor_first_name' => array(
            'label'     => __( 'Donor first name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The donor first name',
        ),
        'donor_last_name' => array(
            'label'     => __( 'Donor last name', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The donor last name',
        ),
        'donor_country' => array(
            'label'     => __( 'Donor country code', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The donor country code',
        ),
        'donor_phone' => array(
            'label'     => __( 'Donor phone', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The donor phone',
        ),
        'campaign_id' => array(
            'label'     => __( 'Campaign ID', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The campaign ID',
        ),
        'campaign_title' => array(
            'label'     => __( 'Campaign title', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The campaign title',
        ),
        'campaign_goal' => array(
            'label'     => __( 'Campaign goal', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The campaign goal',
        ),
        'campaign_min_donation' => array(
            'label'     => __( 'Campaign minimum donation', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The campaign minimum donation',
        ),
        'campaign_end_date' => array(
            'label'     => __( 'Campaign end date', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => date( 'Y-m-d H:i:s' ),
        ),
    );

}

/**
 * Custom trigger tag replacement
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
function automatorwp_charitable_get_trigger_donation_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'charitable' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'donation_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'donation_id', true );
            break;
        case 'donation_title':
            $replacement = automatorwp_get_log_meta( $log->id, 'donation_title', true );
            break;
        case 'donation_amount':
            $replacement = automatorwp_get_log_meta( $log->id, 'donation_amount', true );
            break;
        case 'donation_status':
            $replacement = automatorwp_get_log_meta( $log->id, 'donation_status', true );
            break;
        case 'donation_payment_method':
            $replacement = automatorwp_get_log_meta( $log->id, 'donation_payment_method', true );
            break;
        case 'donor_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'donor_id', true );
            break;
        case 'donor_first_name':
            $replacement = automatorwp_get_log_meta( $log->id, 'donor_first_name', true );
            break;
        case 'donor_last_name':
            $replacement = automatorwp_get_log_meta( $log->id, 'donor_last_name', true );
            break;
        case 'donor_country':
            $replacement = automatorwp_get_log_meta( $log->id, 'donor_country', true );
            break;
        case 'donor_phone':
            $replacement = automatorwp_get_log_meta( $log->id, 'donor_phone', true );
            break;
        case 'campaign_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'campaign_id', true );
            break;
        case 'campaign_title':
            $replacement = automatorwp_get_log_meta( $log->id, 'campaign_title', true );
            break;
        case 'campaign_goal':
            $replacement = automatorwp_get_log_meta( $log->id, 'campaign_goal', true );
            break;
        case 'campaign_min_donation':
            $replacement = automatorwp_get_log_meta( $log->id, 'campaign_min_donation', true );
            break;
        case 'campaign_end_date':
            $replacement = automatorwp_get_log_meta( $log->id, 'campaign_end_date', true );
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_charitable_get_trigger_donation_tag_replacement', 10, 6 );
