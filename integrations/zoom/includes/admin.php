<?php
/**
 * Admin
 *
 * @package     AutomatorWP\Integrations\Zoom\Admin
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
function automatorwp_zoom_get_option( $option_name, $default = false ) {

    $prefix = 'automatorwp_zoom_';

    return automatorwp_get_option( $prefix . $option_name, $default );
}

/**
 * Register plugin settings sections
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_zoom_settings_sections( $automatorwp_settings_sections ) {

    $automatorwp_settings_sections['zoom'] = array(
        'title' => __( 'Zoom', 'automatorwp' ),
        'icon' => 'dashicons-video-alt2',
    );

    return $automatorwp_settings_sections;

}
add_filter( 'automatorwp_settings_sections', 'automatorwp_zoom_settings_sections' );

/**
 * Register plugin settings meta boxes
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_zoom_settings_meta_boxes( $meta_boxes )  {

    $prefix = 'automatorwp_zoom_';

    $meta_boxes['automatorwp-zoom-meetings-settings'] = array(
        'title' => automatorwp_dashicon( 'groups' ) . __( 'Zoom Meetings', 'automatorwp' ),
        'fields' => apply_filters( 'automatorwp_zoom_meetings_settings_fields', array(
            $prefix . 'meetings_client_id' => array(
                'name' => __( 'Client ID:', 'automatorwp' ),
                'desc' => __( 'Your Zoom app client ID.', 'automatorwp' ),
                'type' => 'text',
            ),
            $prefix . 'meetings_client_secret' => array(
                'name' => __( 'Client Secret:', 'automatorwp' ),
                'desc' => __( 'Your Zoom app client secret.', 'automatorwp' ),
                'type' => 'text',
            ),
            $prefix . 'meetings_redirect_url' => array(
                'type' => 'text',
                'render_row_cb' => 'automatorwp_zoom_redirect_url_display_cb',
            ),
            $prefix . 'meetings_authorize' => array(
                'type' => 'text',
                'render_row_cb' => 'automatorwp_zoom_authorize_display_cb',
            ),
        ) ),
    );

    return $meta_boxes;

}
add_filter( "automatorwp_settings_zoom_meta_boxes", 'automatorwp_zoom_settings_meta_boxes' );

/**
 * Display callback for the authorize setting
 *
 * @since  1.0.0
 *
 * @param array      $field_args Array of field arguments.
 * @param CMB2_Field $field      The field object
 */
function automatorwp_zoom_redirect_url_display_cb( $field_args, $field ) {
    $admin_url = str_replace( 'http://', 'https://', get_admin_url() )  . 'admin.php?page=automatorwp_settings&tab=opt-tab-zoom';

    ?>
    <div class="cmb-row cmb-type-custom cmb2-id-automatorwp-zoom-redirect-url table-layout" data-fieldtype="custom">
        <div class="cmb-th">
            <label><?php echo __( 'Redirect and Whitelist URL:', 'automatorwp' ); ?></label>
        </div>
        <div class="cmb-td">
            <input type="text" class="regular-text" value="<?php echo $admin_url; ?>" readonly>
            <p class="cmb2-metabox-description"><?php echo __( 'Copy this URL and place it on your Zoom app redirect and whitelist URL fields.', 'automatorwp' ); ?></p>
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
function automatorwp_zoom_authorize_display_cb( $field_args, $field ) {

    $field_id = $field_args['id'];
    $platform = str_replace( 'automatorwp_zoom_', '', str_replace( '_authorize', '', $field_id ) );
    $client_id = automatorwp_zoom_get_option( $platform . '_client_id', '' );
    $client_secret = automatorwp_zoom_get_option( $platform . '_client_secret', '' );
    $auth = get_option( 'automatorwp_zoom_' . $platform . '_auth' );

    ?>
    <div class="cmb-row cmb-type-custom cmb2-id-automatorwp-zoom-authorize table-layout" data-fieldtype="custom">
        <div class="cmb-th">
            <label><?php echo __( 'Connect with Zoom:', 'automatorwp' ); ?></label>
        </div>
        <div class="cmb-td">
            <a id="<?php echo $field_id; ?>" class="button button-primary" href="#"><?php echo __( 'Authorize', 'automatorwp' ); ?></a>
            <p class="cmb2-metabox-description"><?php echo __( 'Add you Zoom app client ID and secret fields and click on "Authorize" button to generate access keys for this site.', 'automatorwp' ); ?></p>
            <?php if ( is_array( $auth ) ) : ?>
                <div class="automatorwp-notice-success"><?php echo __( 'Site connected with Zoom successfully.', 'automatorwp' ); ?></div>
                <p class="automatorwp-zoom-access-token"><strong><?php echo __( 'Access token:', 'automatorwp' ); ?></strong> <input type="text" value="<?php echo $auth['access_token']; ?>" readonly></p>
                <p class="automatorwp-zoom-refresh-token"><strong><?php echo __( 'Refresh token:', 'automatorwp' ); ?></strong> <input type="text" value="<?php echo $auth['refresh_token']; ?>" readonly></p>
            <?php elseif( ! empty( $client_id ) && ! empty( $client_secret ) ) : ?>
                <div class="automatorwp-notice-error"><?php echo __( 'Site not connected with Zoom.', 'automatorwp' ); ?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Check if authorization process has been completed
 *
 * @since  1.0.0
 */
function automatorwp_zoom_maybe_authorize_complete() {

    if( isset( $_GET['code'] )
        && isset( $_GET['state'] )
        && isset( $_GET['page'] ) && $_GET['page'] == 'automatorwp_settings'
        && isset( $_GET['tab'] ) && $_GET['tab'] == 'opt-tab-zoom' ) {

        $platform = str_replace( 'automatorwp_zoom_', '', $_GET['state'] );
        $client_id = automatorwp_zoom_get_option( $platform . '_client_id', '' );
        $client_secret = automatorwp_zoom_get_option( $platform . '_client_secret', '' );

        $params = array(
            'headers' => array(
                'Content-Type'  => 'application/x-www-form-urlencoded; charset=utf-8',
                'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $client_secret ),
                'Accept'        => 'application/json',
            ),
            'body'	=> array(
                'grant_type'	=> 'authorization_code',
                'redirect_uri'	=> str_replace( 'http://', 'https://', get_admin_url() ) . 'admin.php?page=automatorwp_settings&tab=opt-tab-zoom',
                'code'			=> $_GET['code']
            )
        );

        $response = wp_remote_post( 'https://zoom.us/oauth/token', $params );

        // Bail if can't contact with the server
        if ( is_wp_error( $response ) ) {
            return;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ) );

        // Bail on receive an error
        if( isset( $body->error ) ) {
            return;
        }

        $auth = array(
            'access_token'  => $body->access_token,
            'refresh_token' => $body->refresh_token,
            'token_type'    => $body->token_type,
            'expires_in'    => $body->expires_in,
            'scope'         => $body->scope,
        );

        // Update the access and refresh tokens
        update_option( 'automatorwp_zoom_' . $platform . '_auth', $auth );

        // Redirect to settings again
        wp_redirect( get_admin_url() . 'admin.php?page=automatorwp_settings&tab=opt-tab-zoom' );
        exit;

    }

}
add_action( 'admin_init', 'automatorwp_zoom_maybe_authorize_complete' );