<?php
/**
 * Actions
 *
 * @package     AutomatorWP\Custom_Tables\Actions
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Parse query args for actions
 *
 * @since   1.0.0
 *
 * @param string $where
 * @param CT_Query $ct_query
 *
 * @return string
 */
function automatorwp_actions_query_where( $where, $ct_query ) {

    global $ct_table;

    if( $ct_table->name !== 'automatorwp_actions' ) {
        return $where;
    }

    $table_name = $ct_table->db->table_name;

    // Shorthand
    $qv = $ct_query->query_vars;

    // Type
    $where .= automatorwp_custom_table_where( $qv, 'type', 'type', 'string' );

    // Automation ID
    $where .= automatorwp_custom_table_where( $qv, 'automation_id', 'automation_id', 'integer' );

    return $where;
}
add_filter( 'ct_query_where', 'automatorwp_actions_query_where', 10, 2 );

/**
 * On delete an action
 *
 * @since 1.0.0
 *
 * @param int $object_id
 */
function automatorwp_actions_delete_object( $object_id ) {

    global $wpdb, $ct_table;

    if( ! ( $ct_table instanceof CT_Table ) ) {
        return;
    }

    if( $ct_table->name !== 'automatorwp_actions' ) {
        return;
    }

    $logs       = AutomatorWP()->db->logs;
    $logs_meta 	= AutomatorWP()->db->logs_meta;

    // Delete all logs assigned to this action
    $wpdb->query( "DELETE l FROM {$logs} AS l WHERE l.object_id = {$object_id} AND l.type = 'action'" );

    // Delete orphaned log metas
    $wpdb->query( "DELETE lm FROM {$logs_meta} lm LEFT JOIN {$logs} l ON l.id = lm.id WHERE l.id IS NULL" );

}
add_action( 'delete_object', 'automatorwp_actions_delete_object' );