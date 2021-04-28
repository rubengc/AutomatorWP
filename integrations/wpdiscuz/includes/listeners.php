<?php
/**
 * Listeners
 *
 * @package     AutomatorWP\Integrations\wpDiscuz\Listeners
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add vote listener
 *
 * @since 1.0.0
 *
 * @param int           $vote           1 on vote up, -1 on vote down
 * @param WP_Comment    $comment        Comment object
 */
function automatorwp_wpdiscuz_add_vote_listener( $vote, $comment ) {

    do_action( 'automatorwp_wpdiscuz_add_vote', $vote, $comment );
}
add_action( 'wpdiscuz_add_vote', 'automatorwp_wpdiscuz_add_vote_listener', 10, 2 );

/**
 * Update vote listener
 *
 * @since 1.0.0
 *
 * @param int           $vote           1 on vote up, -1 on vote down
 * @param int           $is_user_voted
 * @param WP_Comment    $comment        Comment object
 */
function automatorwp_wpdiscuz_update_vote_listener( $vote, $is_user_voted, $comment ) {

    do_action( 'automatorwp_wpdiscuz_add_vote', $vote, $comment );
}
add_action( 'wpdiscuz_update_vote', 'automatorwp_wpdiscuz_update_vote_listener', 10, 3 );