<?php
/**
 * Admin
 *
 * @package     AutomatorWP\Integrations\ActiveCampaign\Admin
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Shortcut function to get plugin options
 *
 * @since  1.0.0
 *
 * @param string    $option_name
 * @param bool      $default
 *
 * @return mixed
 */
function automatorwp_activecampaign_get_option( $option_name, $default = false ) {

    $prefix = 'automatorwp_activecampaign_';

    return automatorwp_get_option( $prefix . $option_name, $default );
}

/**
 * Register plugin settings sections
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_activecampaign_settings_sections( $automatorwp_settings_sections ) {

    $automatorwp_settings_sections['activecampaign'] = array(
        'title' => __( 'ActiveCampaign', 'automatorwp' ),
        'icon' => 'dashicons-activecampaign',
    );

    return $automatorwp_settings_sections;

}
add_filter( 'automatorwp_settings_sections', 'automatorwp_activecampaign_settings_sections' );

/**
 * Register plugin settings meta boxes
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_activecampaign_settings_meta_boxes( $meta_boxes )  {

    $prefix = 'automatorwp_activecampaign_';

    $meta_boxes['automatorwp-settings'] = array(
        'title' => automatorwp_dashicon( 'activecampaign' ) . __( 'ActiveCampaign', 'automatorwp' ),
        'fields' => apply_filters( 'automatorwp_activecampaign_settings_fields', array(
            $prefix . 'url' => array(
                'name' => __( 'API URL:', 'automatorwp' ),
                'desc' => sprintf( __( 'Your ActiveCampaign url.'), 'automatorwp' ),
                'type' => 'text',
            ),
            $prefix . 'key' => array(
                'name' => __( 'API key:', 'automatorwp' ),
                'desc' => sprintf( __( 'Your ActiveCampaign API key.'), 'automatorwp' ),
                'type' => 'text',
            ),
            $prefix . 'webhook' => array(
                'type' => 'text',
                'render_row_cb' => 'automatorwp_activecampaign_webhook_url_cb',
            ),
            $prefix . 'authorize' => array(
                'type' => 'text',
                'render_row_cb' => 'automatorwp_activecampaign_authorize_display_cb'
            ),
        ) ),
    );

    return $meta_boxes;

}
add_filter( "automatorwp_settings_activecampaign_meta_boxes", 'automatorwp_activecampaign_settings_meta_boxes' );


/**
 * Display callback for the webhook URL
 *
 * @since  1.0.0
 *
 */

function automatorwp_activecampaign_webhook_url_cb( ) {

    $webhook_url = automatorwp_activecampaign_get_webhook_url();

    ?>
    <div class="cmb-row cmb-type-custom cmb2-id-automatorwp-redirect-url table-layout" data-fieldtype="custom">
        <div class="cmb-th">
            <label><?php echo __( 'Webhook URL:', 'automatorwp' ); ?></label>
        </div>
        <div class="cmb-td">
            <input type="text" class="regular-text" value="<?php echo $webhook_url; ?>" readonly>
            <a id="automatorwp_activecampaign_refresh" class="button" href="#"><?php echo __( 'Regenerate URL', 'automatorwp' ); ?></a>
            <p class="cmb2-metabox-description"><?php echo __( 'Copy this URL and place it on your ActiveCampaign account.', 'automatorwp' ); ?></p>
        </div>
    </div>
    <?php

}


/**
 * Display callback for the authorize setting
 *
 * @since  1.0.0
 *
 * @param array      $field_args Array of field arguments.
 * @param CMB2_Field $field      The field object
 */
function automatorwp_activecampaign_authorize_display_cb( $field_args, $field ) {

    $field_id = $field_args['id'];
    
    $url = automatorwp_activecampaign_get_option( 'url', '' );
    $key = automatorwp_activecampaign_get_option( 'key', '' );

    ?>
    <div class="cmb-row cmb-type-custom cmb2-id-automatorwp-authorize table-layout" data-fieldtype="custom">
        <div class="cmb-th">
            <label><?php echo __( 'Connect with ActiveCampaign:', 'automatorwp' ); ?></label>
        </div>
        <div class="cmb-td">
            <a id="<?php echo $field_id; ?>" class="button button-primary" href="#"><?php echo __( 'Save credentials', 'automatorwp' ); ?></a>
            <p class="cmb2-metabox-description"><?php echo __( 'Add you ActiveCampaign API key and URL fields and click on "Authorize" to connect.', 'automatorwp' ); ?></p>
            <?php if ( ! empty( $url ) && ! empty( $key ) ) : ?>
                <div class="automatorwp-notice-success"><?php echo __( 'Site connected with ActiveCampaign successfully.', 'automatorwp' ); ?></div>
            <?php endif; ?>
        </div>    
    </div>
    <?php
}