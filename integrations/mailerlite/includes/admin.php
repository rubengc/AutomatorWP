<?php
/**
 * Admin
 *
 * @package     AutomatorWP\Integrations\MailerLite\Admin
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
function automatorwp_mailerlite_get_option( $option_name, $default = false ) {

    $prefix = 'automatorwp_mailerlite_';

    return automatorwp_get_option( $prefix . $option_name, $default );
}

/**
 * Register plugin settings sections
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_mailerlite_settings_sections( $automatorwp_settings_sections ) {

    $automatorwp_settings_sections['mailerlite'] = array(
        'title' => __( 'MailerLite', 'automatorwp-pro' ),
        'icon' => 'dashicons-admin-comments',
    );

    return $automatorwp_settings_sections;

}
add_filter( 'automatorwp_settings_sections', 'automatorwp_mailerlite_settings_sections' );

/**
 * Register plugin settings meta boxes
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_mailerlite_settings_meta_boxes( $meta_boxes )  {

    $prefix = 'automatorwp_mailerlite_';

    $meta_boxes['automatorwp-mailerlite-settings'] = array(
        'title' => automatorwp_dashicon( 'mailerlite' ) . __( 'MailerLite', 'automatorwp-pro' ),
        'fields' => apply_filters( 'automatorwp_mailerlite_settings_fields', array(
            $prefix . 'token' => array(
                'name' => __( 'API token:', 'automatorwp-pro' ),
                'desc' => sprintf( __( 'Your MailerLite API token.'), 'automatorwp-pro' ),
                'type' => 'text',
            ),
            $prefix . 'authorize' => array(
                'type' => 'text',
                'render_row_cb' => 'automatorwp_mailerlite_authorize_display_cb'
            ),
        ) ),
    );

    return $meta_boxes;

}
add_filter( "automatorwp_settings_mailerlite_meta_boxes", 'automatorwp_mailerlite_settings_meta_boxes' );


/**
 * Display callback for the authorize setting
 *
 * @since  1.0.0
 *
 * @param array      $field_args Array of field arguments.
 * @param CMB2_Field $field      The field object
 */
function automatorwp_mailerlite_authorize_display_cb( $field_args, $field ) {

    $field_id = $field_args['id'];
    
    $token = automatorwp_mailerlite_get_option( 'token', '' );

    ?>
    <div class="cmb-row cmb-type-custom cmb2-id-automatorwp-mailerlite-authorize table-layout" data-fieldtype="custom">
        <div class="cmb-th">
            <label><?php echo __( 'Connect with MailerLite:', 'automatorwp-pro' ); ?></label>
        </div>
        <div class="cmb-td">
            <a id="<?php echo $field_id; ?>" class="button button-primary" href="#"><?php echo __( 'Save credentials', 'automatorwp-pro' ); ?></a>
            <p class="cmb2-metabox-description"><?php echo __( 'Add you MailerLite API Token and click on "Authorize" to connect.', 'automatorwp-pro' ); ?></p>
            <?php if ( ! empty( $token ) ) : ?>
                <div class="automatorwp-notice-success"><?php echo __( 'Site connected with MailerLite successfully.', 'automatorwp-pro' ); ?></div>
            <?php endif; ?>
        </div>    
    </div>
    <?php
}