<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\Presto_Player\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting videos
 *
 * @since 1.0.0
 */
function automatorwp_presto_player_ajax_get_videos() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    $model = new \PrestoPlayer\Models\Video();

    if( $model ) {
        // Get the videos
        $videos = $model->all();

        if ( $videos ) {

            foreach ( $videos as $video ) {

                if( $search && ( strpos( strtolower( $video->__get( 'title' ) ), $search) === false) ) {
                    continue;
                }

                // Results should meet the Select2 structure
                $results[] = array(
                    'id' => $video->__get( 'id' ),
                    'text' => $video->__get( 'title' )
                );
            }
        }
    }

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_presto_player_get_videos', 'automatorwp_presto_player_ajax_get_videos' );