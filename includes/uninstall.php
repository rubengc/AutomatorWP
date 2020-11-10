<?php
/**
 * Install
 *
 * @package     AutomatorWP\Install
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * AutomatorWP uninstallation
 *
 * @since 1.0.0
 */
function automatorwp_uninstall() {

    // Clear scheduled events
    automatorwp_clear_scheduled_events();

}
