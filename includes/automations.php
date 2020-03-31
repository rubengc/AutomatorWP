<?php
/**
 * Automations
 *
 * @package     AutomatorWP\Automations
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get automation registered statuses
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_get_automation_statuses() {

    return apply_filters( 'automatorwp_automation_statuses', array(
        'active' => __( 'Active', 'automatorwp' ),
        'inactive' => __( 'Inactive', 'automatorwp' ),
    ) );

}

/**
 * Get the automation object data
 *
 * @param int       $automation_id  The automation ID
 * @param string    $output         Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which correspond to
 *                                  a object, an associative array, or a numeric array, respectively. Default OBJECT.
 * @return array|stdClass|null
 */
function automatorwp_get_automation_object( $automation_id, $output = OBJECT ) {

    ct_setup_table( 'automatorwp_automations' );

    $automation = ct_get_object( $automation_id );

    ct_reset_setup_table();

    return $automation;

}

/**
 * Get automation triggers
 *
 * @since  1.0.0
 *
 * @param int       $automation_id  The automation ID
 * @param string    $output         The required return type (OBJECT|ARRAY_A|ARRAY_N)
 *
 * @return array                    Array of automation triggers
 */
function automatorwp_get_automation_triggers( $automation_id, $output = OBJECT ) {

    ct_setup_table( 'automatorwp_triggers' );

    $ct_query = new CT_Query( array(
        'automation_id' => $automation_id,
        'orderby' => 'position',
        'order' => 'ASC',
        'items_per_page' => -1,
    ) );

    $triggers = $ct_query->get_results();

    if( $output === ARRAY_N || $output === ARRAY_A ) {

        // Turn array of objects into an array of arrays
        foreach( $triggers as $i => $trigger ) {
            $triggers[$i] = (array) $trigger;
        }

    }

    ct_reset_setup_table();

    return $triggers;

}

/**
 * Get automation actions
 *
 * @since  1.0.0
 *
 * @param int       $automation_id  The automation ID
 * @param string    $output         The required return type (OBJECT|ARRAY_A|ARRAY_N)
 *
 * @return array                    Array of automation actions
 */
function automatorwp_get_automation_actions( $automation_id, $output = OBJECT ) {

    ct_setup_table( 'automatorwp_actions' );

    $ct_query = new CT_Query( array(
        'automation_id' => $automation_id,
        'orderby' => 'position',
        'order' => 'ASC',
        'items_per_page' => -1,
    ) );

    $actions = $ct_query->get_results();

    if( $output === ARRAY_N || $output === ARRAY_A ) {

        // Turn array of objects into an array of arrays
        foreach( $actions as $i => $trigger ) {
            $actions[$i] = (array) $trigger;
        }

    }

    ct_reset_setup_table();

    return $actions;

}