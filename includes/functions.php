<?php
/**
 * Functions
 *
 * @package     AutomatorWP\Functions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Generates the required HTML with the dashicon provided
 *
 * @since 1.0.0
 *
 * @param string $dashicon      Dashicon class
 * @param string $tag           Optional, tag used (recommended i or span)
 *
 * @return string
 */
function automatorwp_dashicon( $dashicon = 'automatorwp', $tag = 'i' ) {

    return '<' . $tag . ' class="dashicons dashicons-' . $dashicon . '"></' . $tag . '>';

}