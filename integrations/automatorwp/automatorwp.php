<?php
/**
 * AutomatorWP
 *
 * @package     AutomatorWP\Integrations\AutomatorWP
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Triggers
require_once plugin_dir_path( __FILE__ ) . 'triggers/complete-automation.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/user-created.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/post-created.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/all-users.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/all-posts.php';
// Actions
require_once plugin_dir_path( __FILE__ ) . 'actions/anonymous-user.php';
require_once plugin_dir_path( __FILE__ ) . 'actions/redirect-user.php';
require_once plugin_dir_path( __FILE__ ) . 'actions/call-function.php';
require_once plugin_dir_path( __FILE__ ) . 'actions/do-action.php';
require_once plugin_dir_path( __FILE__ ) . 'actions/run-all-users-automation.php';
require_once plugin_dir_path( __FILE__ ) . 'actions/run-all-posts-automation.php';
// Filters
require_once plugin_dir_path( __FILE__ ) . 'filters/flat-condition.php';

/**
 * Registers this integration
 *
 * @since 1.0.0
 */
function automatorwp_register_automatorwp_integration() {

    automatorwp_register_integration( 'automatorwp', array(
        'label' => 'AutomatorWP',
        'icon'  => plugin_dir_url( __FILE__ ) . 'assets/automatorwp.svg',
    ) );

}
add_action( 'automatorwp_init', 'automatorwp_register_automatorwp_integration', 1 );