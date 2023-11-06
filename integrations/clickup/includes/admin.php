<?php
/**
 * Admin
 *
 * @package     AutomatorWP\Integrations\ClickUp\Admin
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
function automatorwp_clickup_get_option( $option_name, $default = false ) {

    $prefix = 'automatorwp_clickup_';

    return automatorwp_get_option( $prefix . $option_name, $default );
}

/**
 * Register plugin settings sections
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_clickup_settings_sections( $automatorwp_settings_sections ) {

    $automatorwp_settings_sections['clickup'] = array(
        'title' => __( 'ClickUp', 'automatorwp' ),
        'icon' => 'dashicons-admin-comments',
    );

    return $automatorwp_settings_sections;

}
add_filter( 'automatorwp_settings_sections', 'automatorwp_clickup_settings_sections' );

/**
 * Register plugin settings meta boxes
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_clickup_settings_meta_boxes( $meta_boxes )  {

    $prefix = 'automatorwp_clickup_';

    $meta_boxes['automatorwp-clickup-settings'] = array(
        'title' => automatorwp_dashicon( 'clickup' ) . __( 'ClickUp', 'automatorwp' ),
        'fields' => apply_filters( 'automatorwp_clickup_settings_fields', array(
            $prefix . 'token' => array(
                'name' => __( 'API token:', 'automatorwp' ),
                'desc' => sprintf( __( 'Your ClickUp API token.'), 'automatorwp' ),
                'type' => 'text',
            ),
            $prefix . 'authorize' => array(
                'type' => 'text',
                'render_row_cb' => 'automatorwp_clickup_authorize_display_cb'
            ),
        ) ),
    );

    return $meta_boxes;

}
add_filter( "automatorwp_settings_clickup_meta_boxes", 'automatorwp_clickup_settings_meta_boxes' );


/**
 * Display callback for the authorize setting
 *
 * @since  1.0.0
 *
 * @param array      $field_args Array of field arguments.
 * @param CMB2_Field $field      The field object
 */
function automatorwp_clickup_authorize_display_cb( $field_args, $field ) {

    $field_id = $field_args['id'];
    
    $token = automatorwp_clickup_get_option( 'token', '' );

    ?>
    <div class="cmb-row cmb-type-custom cmb2-id-automatorwp-clickup-authorize table-layout" data-fieldtype="custom">
        <div class="cmb-th">
            <label><?php echo __( 'Connect with ClickUp:', 'automatorwp' ); ?></label>
        </div>
        <div class="cmb-td">
            <a id="<?php echo $field_id; ?>" class="button button-primary" href="#"><?php echo __( 'Save credentials', 'automatorwp' ); ?></a>
            <p class="cmb2-metabox-description"><?php echo __( 'Add you ClickUp API Token and click on "Authorize" to connect.', 'automatorwp' ); ?></p>
            <?php if ( ! empty( $token ) ) : ?>
                <div class="automatorwp-notice-success"><?php echo __( 'Site connected with ClickUp successfully.', 'automatorwp' ); ?></div>
            <?php endif; ?>
        </div>    
    </div>
    <?php
}