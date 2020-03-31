<?php
/**
 * Triggers
 *
 * @package     AutomatorWP\Triggers
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Registers a new integration
 *
 * @since 1.0.0
 *
 * @param string $integration
 * @param array $args
 */
function automatorwp_register_integration( $integration, $args ) {

    $args = wp_parse_args( $args, array(
        'label' => '',
        'icon'  => AUTOMATORWP_URL . 'assets/img/integration-default.svg',
    ) );

    AutomatorWP()->integrations[$integration] = $args;

}

/**
 * Get registered integrations
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_get_integrations() {

    return AutomatorWP()->integrations;

}

/**
 * Get an integration
 *
 * @since 1.0.0
 *
 * @param string $integration
 *
 * @return array|false
 */
function automatorwp_get_integration( $integration ) {

    return ( isset( AutomatorWP()->integrations[$integration] ) ? AutomatorWP()->integrations[$integration] : false );

}