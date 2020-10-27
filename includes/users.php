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

    $cache = automatorwp_get_cache( 'user_completion_times', array(), false );

    // If result already cached, return it
    if( isset( $cache[$user_id] )
        && isset( $cache[$user_id][$type] )
        && isset( $cache[$user_id][$type][$object_id] )
        && isset( $cache[$user_id][$type][$object_id][$date] ) ) {
        return $cache[$user_id][$type][$object_id][$date];
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

    // Cache function result
    if( ! isset( $cache[$user_id] ) ) {
        $cache[$user_id] = array();
    }

    if( ! isset( $cache[$user_id][$type] ) ) {
        $cache[$user_id][$type] = array();
    }

    if( ! isset( $cache[$user_id][$type][$object_id] ) ) {
        $cache[$user_id][$type][$object_id] = array();
    }

    $cache[$user_id][$type][$object_id][$date] = $completion_times;

    automatorwp_set_cache( 'user_completion_times', $cache );

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
 * @param string    $type       The object type (trigger|action|automation)
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

    $cache = automatorwp_get_cache( 'user_last_completion_time', array(), false );

    // If result already cached, return it
    if( isset( $cache[$user_id] )
        && isset( $cache[$user_id][$type] )
        && isset( $cache[$user_id][$type][$object_id] ) ) {
        return $cache[$user_id][$type][$object_id];
    }

    $ct_table = ct_setup_table( 'automatorwp_logs' );

    $date = $wpdb->get_var(
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

    automatorwp_set_cache( 'user_last_completion_time', $cache );

    return $result;

}

/**
 * Get user last object completion log
 *
 * @since 1.0.0
 *
 * @param int       $object_id  The object ID
 * @param int       $user_id    The user ID
 * @param string    $type       The object type
 *
 * @return stdClass|false
 */
function automatorwp_get_user_last_completion( $object_id, $user_id, $type ) {

    global $wpdb, $automatorwp_last_anonymous_trigger_log_id;

    $object_id = absint( $object_id );

    // Check the object ID
    if( $object_id === 0 ) {
        return false;
    }

    $types = automatorwp_get_log_types();

    // Check the type
    if( ! isset( $types[$type] ) ) {
        return false;
    }

    $user_id = absint( $user_id );

    // For backward compatibility, check if is trying to get a last anonymous trigger log ID
    if( $type === 'trigger' && $user_id === 0 && absint( $automatorwp_last_anonymous_trigger_log_id ) !== 0 ) {

        // Get the last anonymous trigger log if is parsing tags for an anonymous user
        $log = automatorwp_get_log_object( $automatorwp_last_anonymous_trigger_log_id );

        return ( $log ? $log : false );
    }

    // Check the user ID
    if( $user_id === 0 ) {
        return false;
    }

    $cache = automatorwp_get_cache( 'user_last_completion', array(), false );

    // If result already cached, return it
    if( isset( $cache[$user_id] )
        && isset( $cache[$user_id][$type] )
        && isset( $cache[$user_id][$type][$object_id] ) ) {
        return $cache[$user_id][$type][$object_id];
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

    $cache[$user_id][$type][$object_id] = $log;

    automatorwp_set_cache( 'user_last_completion', $cache );

    return $log;

}

/**
 * Clears the 'user_last_completion' and 'user_last_completion_time' every time a trigger, action or automation is completed.
 * Cache used on automatorwp_get_user_last_completion() and automatorwp_get_user_last_completion_time() functions.
 *
 * @since 1.0.0
 *
 * @param stdClass  $object     The trigger object
 * @param int       $user_id    The user ID
 */
function automatorwp_clear_user_last_completion_cache( $object, $user_id ) {

    $type = str_replace( 'automatorwp_user_completed_', '', current_filter() );
    $caches_to_clear = array(
        'user_completion_times',
        'user_last_completion',
        'user_last_completion_time',
    );

    // Loop all caches to clear
    foreach( $caches_to_clear as $cache_to_clear ) {

        $cache = automatorwp_get_cache( $cache_to_clear, array(), false );

        if( isset( $cache[$user_id] )
            && isset( $cache[$user_id][$type] )
            && isset( $cache[$user_id][$type][$object->id] ) ) {
            // Clear the cache entry if exists
            unset( $cache[$user_id][$type][$object->id] );
            automatorwp_set_cache( $cache_to_clear, $cache );
        }

    }

}
add_action( 'automatorwp_user_completed_trigger', 'automatorwp_clear_user_last_completion_cache', 10, 2 );
add_action( 'automatorwp_user_completed_action', 'automatorwp_clear_user_last_completion_cache', 10, 2 );
add_action( 'automatorwp_user_completed_automation', 'automatorwp_clear_user_last_completion_cache', 10, 2 );

/**
 * Check if user has completed the trigger
 *
 * @since 1.0.0
 *
 * @param int $trigger_id   The trigger ID
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

/**
 * Check if has been executed all automation actions for an user
 *
 * @since 1.0.0
 *
 * @param int $automation_id    The automation ID
 * @param int $user_id          The user ID
 *
 * @return bool
 */
function automatorwp_has_user_executed_all_automation_actions( $automation_id, $user_id ) {

    $automation = automatorwp_get_automation_object( $automation_id );

    if( ! $automation ) {
        return false;
    }

    $last_completion_time = automatorwp_get_user_last_completion_time( $automation->id, $user_id, 'automation' );

    $actions = automatorwp_get_automation_actions( $automation->id );

    $all_completed = true;

    foreach( $actions as $action ) {
        if( ! automatorwp_get_user_completion_times( $action->id, $user_id, 'action', $last_completion_time ) ) {
            $all_completed = false;
            break;
        }
    }

    return $all_completed;

}