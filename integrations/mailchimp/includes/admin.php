<?php
/**
 * Admin
 *
 * @package     AutomatorWP\Integrations\Mailchimp\Admin
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
function automatorwp_mailchimp_get_option( $option_name, $default = false ) {

    $prefix = 'automatorwp_mailchimp_';

    return automatorwp_get_option( $prefix . $option_name, $default );
}

/**
 * Register plugin settings sections
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_mailchimp_settings_sections( $automatorwp_settings_sections ) {

    $automatorwp_settings_sections['mailchimp'] = array(
        'title' => __( 'Mailchimp', 'automatorwp' ),
        'icon' => 'dashicons-mailchimp',
    );

    return $automatorwp_settings_sections;

}
add_filter( 'automatorwp_settings_sections', 'automatorwp_mailchimp_settings_sections' );

/**
 * Register plugin settings meta boxes
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_mailchimp_settings_meta_boxes( $meta_boxes )  {

    $prefix = 'automatorwp_mailchimp_';

    $meta_boxes['automatorwp-mailchimp-settings'] = array(
        'title' => automatorwp_dashicon( 'mailchimp' ) . __( 'Mailchimp', 'automatorwp' ),
        'fields' => apply_filters( 'automatorwp_mailchimp_settings_fields', array(
            $prefix . 'api_key' => array(
                'name' => __( 'API Key:', 'automatorwp' ),
                'desc' => __( 'Your Mailchimp API key.', 'automatorwp' ),
                'type' => 'text',
            ),
            $prefix . 'server_prefix' => array(
                'name' => __( 'Server prefix:', 'automatorwp' ),
                'desc' => __( 'Your Mailchimp server prefix.', 'automatorwp' ),
                'type' => 'text',
            ),
            $prefix . 'mailchimp_authorize' => array(
                'type' => 'text',
                'render_row_cb' => 'automatorwp_mailchimp_authorize_display_cb',
            ),
        ) ),
    );

    return $meta_boxes;

}
add_filter( "automatorwp_settings_mailchimp_meta_boxes", 'automatorwp_mailchimp_settings_meta_boxes' );

/**
 * Display callback for the authorize setting
 *
 * @since  1.0.0
 *
 * @param array      $field_args Array of field arguments.
 * @param CMB2_Field $field      The field object
 */
function automatorwp_mailchimp_authorize_display_cb( $field_args, $field ) {

    $access_valid = automatorwp_mailchimp_get_option( 'access_valid' );

    ?>

    <div class="cmb-row cmb-type-custom cmb2-id-automatorwp-mailchimp-authorize table-layout" data-fieldtype="custom">
        <div class="cmb-th">
            <label><?php echo __( 'Connect with Mailchimp:', 'automatorwp' ); ?></label>
        </div>
        <div class="cmb-td">
            <input type="hidden" name="awp-mailchimp-outh-ajax-nonce" id="awp-mailchimp-outh-ajax-nonce" value="<?php echo wp_create_nonce( 'awp-outh-ajax-nonce' ); ?>" />           
            <input type="button" name="automatorwp_save_mailchimp_oauth" id="automatorwp_save_mailchimp_oauth" value="Save Credentials" class="button button-primary" />
            <?php if ( $access_valid ){ ?>
            <input type="button" name="automatorwp_remove_mailchimp_oauth" id="automatorwp_remove_mailchimp_oauth" value="Delete Credentials" class="button button-danger" /><br>
            <?php } ?>
            <p id='awp_mailchimp_oauth_status'></p>
        </div>
    </div>
    	
    <?php
}