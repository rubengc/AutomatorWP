<?php
/**
 * WordPress
 *
 * @package     AutomatorWP\Integrations\WordPress
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Triggers
require_once plugin_dir_path( __FILE__ ) . 'triggers/register.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/login.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/visit-site.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/view-post.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/view-post-category.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/view-post-tag.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/view-page.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/view-post-type.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/publish-post.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/publish-post-category.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/publish-post-tag.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/publish-page.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/comment-post.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/comment-post-category.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/comment-post-tag.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/comment-page.php';
require_once plugin_dir_path( __FILE__ ) . 'triggers/comment-post-type.php';
// Actions
require_once plugin_dir_path( __FILE__ ) . 'actions/send-email.php';
require_once plugin_dir_path( __FILE__ ) . 'actions/user-role.php';
require_once plugin_dir_path( __FILE__ ) . 'actions/create-user.php';
require_once plugin_dir_path( __FILE__ ) . 'actions/user-meta.php';
require_once plugin_dir_path( __FILE__ ) . 'actions/create-post.php';
require_once plugin_dir_path( __FILE__ ) . 'actions/post-meta.php';

/**
 * Registers this integration
 *
 * @since 1.0.0
 */
function automatorwp_register_wordpress_integration() {

    automatorwp_register_integration( 'wordpress', array(
        'label' => 'WordPress',
        'icon'  => plugin_dir_url( __FILE__ ) . 'assets/wordpress.svg',
    ) );

}
add_action( 'automatorwp_init', 'automatorwp_register_wordpress_integration', 1 );