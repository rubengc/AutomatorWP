<?php
/**
 * Users
 *
 * @package     AutomatorWP\Users
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get object completion times of a given object
 *
 * @since 1.0.0
 *
 * @param int       $object_id  The object ID
 * @param string    $type       The object type
 * @param int       $since      The user ID
 *
 * @return int
 */
function automatorwp_get_object_completion_times( $object_id, $type, $since = 0 ) {

    global $wpdb;

    $object_id = absint( $object_id );

    // Check the object ID
    if( $object_id === 0 ) {
        return 0;
    }

    $types = automatorwp_get_log_types();

    // Check the type
    if( ! isset( $types[$type] ) ) {
        return 0;
    }

    // Since
    $date = false;

    if( absint( $since ) > 0 ) {
        $date = date( 'Y-m-d H:i:s', $since );
    }

    $ct_table = ct_setup_table( 'automatorwp_logs' );

    $completion_times = (int) $wpdb->get_var(
        "SELECT COUNT(*) 
        FROM {$ct_table->db->table_name} AS l
        WHERE 1=1 
        AND l.object_id = {$object_id}
        AND l.type = '{$type}' "
        . ( $date !== false ? "AND l.date > '{$date}'" : '' )
    );

    ct_reset_setup_table();

    return $completion_times;

}

/**
 * Get user completion times of a given object
 *
 * @since 1.0.0
 *
 * @param int       $object_id  The object ID
 * @param int       $user_id    The user ID
 * @param string    $type       The object type
 * @param int       $since      The user ID
 *
 * @return int
 */
function automatorwp_get_user_completion_times( $object_id, $user_id, $type, $since = 0 ) {

    global $wpdb;

    $object_id = absint( $object_id );

    // Check the object ID
    if( $object_id === 0 ) {
        return 0;
    }

    $user_id = absint( $user_id );

    // Check the user ID
    if( $user_id === 0 ) {
        return 0;
    }

    $types = automatorwp_get_log_types();

    // Check the type
    if( ! isset( $types[$type] ) ) {
        return 0;
    }

    // Since
    $date = false;

    if( absint( $since ) > 0 ) {
        $date = date( 'Y-m-d H:i:s', $since );
    }

    $ct_table = ct_setup_table( 'automatorwp_logs' );

    $completion_times = (int) $wpdb->get_var(
        "SELECT COUNT(*) 
        FROM {$ct_table->db->table_name} AS l
        WHERE 1=1 
        AND l.object_id = {$object_id}
        AND l.user_id = {$user_id}
        AND l.type = '{$type}' "
        . ( $date !== false ? "AND l.date > '{$date}'" : '' )
    );

    ct_reset_setup_table();

    return $completion_times;

}

/**
 * Get user trigger completion times of a given object
 *
 * @since 1.0.0
 *
 * @param int $trigger_id   The trigger ID
 * @param int $user_id      The user ID
 *
 * @return int
 */
function automatorwp_get_user_trigger_completion_times( $trigger_id, $user_id ) {

    $trigger = automatorwp_get_trigger_object( $trigger_id );

    if( ! $trigger ) {
        return 0;
    }

    // Get the last automation completion time (triggers always need to be based on this)
    $last_completion_time = automatorwp_get_user_last_completion_time( $trigger->automation_id, $user_id, 'automation' );

    return automatorwp_get_user_completion_times( $trigger->id, $user_id, 'trigger', $last_completion_time );

}

/**
 * Get user last object completion time
 *
 * @since 1.0.0
 *
 * @param int       $object_id  The object ID
 * @param int       $user_id    The user ID
 * @param string    $type       The object type
 *
 * @return int
 */
function automatorwp_get_user_last_completion_time( $object_id, $user_id, $type ) {

    global $wpdb;

    $object_id = absint( $object_id );

    // Check the object ID
    if( $object_id === 0 ) {
        return 0;
    }

    $user_id = absint( $user_id );

    // Check the user ID
    if( $user_id === 0 ) {
        return 0;
    }

    $types = automatorwp_get_log_types();

    // Check the type
    if( ! isset( $types[$type] ) ) {
        return 0;
    }

    $cache = automatorwp_get_cache( 'user_last_completion_time', array() );

    // If result already cached, return it
    if( isset( $cache[$user_id] ) && isset( $cache[$user_id][$type][$object_id] ) && isset( $cache[$user_id][$type][$object_id] ) ) {
        return $cache[$user_id][$type][$object_id];
    }

    $ct_table = ct_setup_table( 'automatorwp_logs' );

    $date =  $wpdb->get_var(
        "SELECT l.date
        FROM {$ct_table->db->table_name} AS l
        WHERE 1=1 
        AND l.object_id = {$object_id}
        AND l.user_id = {$user_id}
        AND l.type = '{$type}'
        ORDER BY l.date DESC
        LIMIT 1"
    );

    ct_reset_setup_table();

    if( $date ) {
        $result = strtotime( $date );
    } else {
        $result = 0;
    }

    // Cache function result
    if( ! isset( $cache[$user_id] ) ) {
        $cache[$user_id] = array();
    }

    if( ! isset( $cache[$user_id][$type] ) ) {
        $cache[$user_id][$type] = array();
    }

    $cache[$user_id][$type][$object_id] = $result;

    return $result;

}

/**
 * Clears the 'user_last_completion_time' every time a trigger is completed.
 * Cache used on automatorwp_get_user_last_completion_time() function.
 *
 * @since 1.0.0
 *
 * @param stdClass  $object     The trigger object
 * @param int       $user_id    The user ID
 */
function automatorwp_clear_trigger_last_completion_time_cache( $object, $user_id ) {

    $cache = automatorwp_get_cache( 'user_last_completion_time', array() );

    // If result already cached, return it
    if( isset( $cache[$user_id] ) && isset( $cache[$user_id]['trigger'] ) && isset( $cache[$user_id]['trigger'][$object->id] ) ) {
        unset( $cache[$user_id]['trigger'][$object->id] );
    }

}
add_action( 'automatorwp_user_completed_trigger', 'automatorwp_clear_trigger_last_completion_time_cache', 10, 2 );

/**
 * Clears the 'user_last_completion_time' every time an action is completed.
 * Cache used on automatorwp_get_user_last_completion_time() function.
 *
 * @since 1.0.0
 *
 * @param stdClass  $object     The action object
 * @param int       $user_id    The user ID
 */
function automatorwp_clear_action_last_completion_time_cache( $object, $user_id ) {

    $cache = automatorwp_get_cache( 'user_last_completion_time', array() );

    // If result already cached, return it
    if( isset( $cache[$user_id] ) && isset( $cache[$user_id]['action'] ) && isset( $cache[$user_id]['action'][$object->id] ) ) {
        unset( $cache[$user_id]['action'][$object->id] );
    }

}
add_action( 'automatorwp_user_completed_action', 'automatorwp_clear_action_last_completion_time_cache', 10, 2 );

/**
 * Clears the 'user_last_completion_time' every time an automation is completed.
 * Cache used on automatorwp_get_user_last_completion_time() function.
 *
 * @since 1.0.0
 *
 * @param stdClass  $object     The automation/trigger/action object
 * @param int       $user_id    The user ID
 */
function automatorwp_clear_automation_last_completion_time_cache( $object, $user_id ) {

    $cache = automatorwp_get_cache( 'user_last_completion_time', array() );

    // If result already cached, return it
    if( isset( $cache[$user_id] ) && isset( $cache[$user_id]['automation'] ) && isset( $cache[$user_id]['automation'][$object->id] ) ) {
        unset( $cache[$user_id]['automation'][$object->id] );
    }

}
add_action( 'automatorwp_user_completed_automation', 'automatorwp_clear_automation_last_completion_time_cache', 10, 2 );

/**
 * Get user last object completion log
 *
 * @since 1.0.0
 *
 * @param int       $object_id  The object ID
 * @param int       $user_id    The user ID
 * @param string    $type       The object type
 *
 * @return stdClass
 */
function automatorwp_get_user_last_completion( $object_id, $user_id, $type ) {

    global $wpdb;

    $object_id = absint( $object_id );

    // Check the object ID
    if( $object_id === 0 ) {
        return false;
    }

    $user_id = absint( $user_id );

    // Check the user ID
    if( $user_id === 0 ) {
        return false;
    }

    $types = automatorwp_get_log_types();

    // Check the type
    if( ! isset( $types[$type] ) ) {
        return 0;
    }

    $ct_table = ct_setup_table( 'automatorwp_logs' );

    $log = $wpdb->get_row(
        "SELECT *
        FROM {$ct_table->db->table_name} AS l
        WHERE 1=1 
        AND l.object_id = {$object_id}
        AND l.user_id = {$user_id}
        AND l.type = '{$type}'
        ORDER BY l.date DESC
        LIMIT 1"
    );

    ct_reset_setup_table();

    return $log;

}

/**
 * Check if user has completed the trigger
 *
 * @since 1.0.0
 *
 * @param int $trigger_id   The trigger object
 * @param int $user_id      The user ID
 *
 * @return bool
 */
function automatorwp_has_user_completed_trigger( $trigger_id, $user_id ) {

    $trigger = automatorwp_get_trigger_object( $trigger_id );

    if( ! $trigger ) {
        return false;
    }

    // Get the number of times the user has completed this trigger
    $completion_times = automatorwp_get_user_trigger_completion_times( $trigger->id, $user_id );

    // Get trigger required times
    $required_times = automatorwp_get_trigger_required_times( $trigger->id );

    // If user has not completed this trigger the number of times required then break to finish this function
    return ( $completion_times >= $required_times );

}