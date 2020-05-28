<?php
/**
 * Admin Notices
 *
 * @package     AutomatorWP\Admin\Notices
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.1.1
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * AutomatorWP admin notices
 *
 * @since 1.1.1
 */
function automatorwp_admin_notices() {

    // Bail if current user is not a site administrator
    if( ! current_user_can( 'update_plugins' ) ) {
        return;
    }

    // Check if user checked already hide the review notice
    $hide_review_notice = ( $exists = get_option( 'automatorwp_hide_review_notice' ) ) ? $exists : '';

    if( $hide_review_notice !== 'yes' ) {

        // Get the installation date
        $automatorwp_install_date = ( $exists = get_option( 'automatorwp_install_date' ) ) ? $exists : date( 'Y-m-d H:i:s' );

        $now = date( 'Y-m-d h:i:s' );
        $datetime1 = new DateTime( $automatorwp_install_date );
        $datetime2 = new DateTime( $now );

        // Difference in days between installation date and now
        $diff_interval = round( ( $datetime2->format( 'U' ) - $datetime1->format( 'U' ) ) / ( 60 * 60 * 24 ) );

        if( $diff_interval >= 7 ) {
            ?>

            <div class="notice automatorwp-review-notice">
                <div class="automatorwp-logo"></div>
                <p>
                    <?php _e( 'Awesome! You\'ve been using <strong>AutomatorWP</strong> for a while.', 'automatorwp' ); ?><br>
                    <?php _e( 'May I ask you to give it a <strong>5-star rating</strong> on WordPress?', 'automatorwp' ); ?><br>
                    <?php _e( 'This will help to spread its popularity and to make this plugin a better one.', 'automatorwp' ); ?><br>
                    <br>
                    <?php _e( 'Your help is much appreciated. Thank you very much,', 'automatorwp' ); ?><br>
                    <span>~Ruben Garcia</span>
                </p>
                <ul>
                    <li><a href="https://wordpress.org/support/plugin/automatorwp/reviews/?rate=5#new-post" class="button button-primary" target="_blank" title="<?php _e( 'Yes, I want to rate it!', 'automatorwp' ); ?>"><?php _e( 'Yes, I want to rate it!', 'automatorwp' ); ?></a></li>
                    <li><a href="javascript:void(0);" class="automatorwp-hide-review-notice button" title="<?php _e( 'I already did', 'automatorwp' ); ?>"><?php _e( 'I already did', 'automatorwp' ); ?></a></li>
                    <li><a href="javascript:void(0);" class="automatorwp-hide-review-notice" title="<?php _e( 'No, I don\'t want to rate it', 'automatorwp' ); ?>"><small><?php _e( 'No, I don\'t want to rate it', 'automatorwp' ); ?></small></a></li>
                </ul>
            </div>

            <?php
        }

    }

}
add_action( 'admin_notices', 'automatorwp_admin_notices' );

/**
 * Ajax handler for hide review notice action
 *
 * @since 1.1.1
 */
function automatorwp_ajax_hide_review_notice() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    update_option( 'automatorwp_hide_review_notice', 'yes' );

    wp_send_json_success( array( 'success' ) );
    exit;
}

add_action( 'wp_ajax_automatorwp_hide_review_notice', 'automatorwp_ajax_hide_review_notice' );
