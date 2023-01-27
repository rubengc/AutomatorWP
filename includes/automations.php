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
        'in-progress' => __( 'In Progress', 'automatorwp' ),
        'inactive' => __( 'Inactive', 'automatorwp' ),
    ) );

}

/**
 * Get automation registered types
 *
 * @since  1.3.0
 *
 * @return array
 */
function automatorwp_get_automation_types() {

    return apply_filters( 'automatorwp_automation_types', array(
        'user' => array(
            'image' => AUTOMATORWP_URL . 'assets/img/automatorwp-logo.svg',
            'label' => __( 'Logged-in', 'automatorwp' ),
            'desc'  => __( 'Automation for logged-in users.', 'automatorwp' ),
            'info'  => __( 'Designed to run actions on the user who has completed the triggers.', 'automatorwp' ),

        ),
        'anonymous' => array(
            'image' => AUTOMATORWP_URL . 'assets/img/automatorwp-anonymous-logo.svg',
            'label' => __( 'Anonymous', 'automatorwp' ),
            'desc'  => __( 'Automation for not logged-in users.', 'automatorwp' ),
            'info'  => __( 'Ideal for creating new users or modifying existing users.', 'automatorwp' ),
        ),
        'all-users' => array(
            'image' => AUTOMATORWP_URL . 'assets/img/automatorwp-all-users-logo.svg',
            'label' => __( 'All users', 'automatorwp' ),
            'desc'  => __( 'Automation to run actions on a filtered group of users.', 'automatorwp' ),
            'info'  => __( 'This automation can be run <b>manually</b>, on a <b>specific date</b> or on a <b>recurring</b> basis.', 'automatorwp' ),
        ),
        'all-posts' => array(
            'image' => AUTOMATORWP_URL . 'assets/img/automatorwp-all-posts-logo.svg',
            'label' => __( 'All posts', 'automatorwp' ),
            'desc'  => __( 'Automation to run actions on a filtered group of posts.', 'automatorwp' ),
            'info'  => __( 'This automation can be run <b>manually</b>, on a <b>specific date</b> or on a <b>recurring</b> basis.', 'automatorwp' ),
        ),
    ) );

}

/**
 * Get automation registered types labels
 *
 * @since  1.3.0
 *
 * @return array
 */
function automatorwp_get_automation_types_labels() {

    $types = automatorwp_get_automation_types();
    $labels = array();

    foreach( $types as $type => $args ) {
        $labels[$type] = $args['label'];
    }

    return $labels;

}

/**
 * Get the automation object data
 *
 * @param int       $automation_id  The automation ID
 * @param string    $output         Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which correspond to
 *                                  a object, an associative array, or a numeric array, respectively. Default OBJECT.
 *
 * @return array|stdClass|null
 */
function automatorwp_get_automation_object( $automation_id, $output = OBJECT ) {

    ct_setup_table( 'automatorwp_automations' );

    $automation = ct_get_object( $automation_id );

    ct_reset_setup_table();

    return $automation;

}

/**
 * Get the automation metadata
 *
 * @param int       $automation_id  The automation ID
 * @param string    $meta_key       Optional. The meta key to retrieve. By default, returns
 *                                  data for all keys. Default empty.
 * @param bool      $single         Optional. Whether to return a single value. Default false.
 *
 * @return mixed                    Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function automatorwp_get_automation_meta( $automation_id, $meta_key = '', $single = false ) {

    ct_setup_table( 'automatorwp_automations' );
    $meta_value = ct_get_object_meta( $automation_id, $meta_key, $single );
    ct_reset_setup_table();

    return $meta_value;

}

/**
 * Update automation metadata
 *
 * @param int    $automation_id The automation ID.
 * @param string $meta_key      Metadata key.
 * @param mixed  $meta_value    Metadata value. Must be serializable if non-scalar.
 * @param mixed  $prev_value    Optional. Previous value to check before removing. Default empty.
 *
 * @return int|bool         Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function automatorwp_update_automation_meta( $automation_id, $meta_key, $meta_value, $prev_value = '' ) {

    ct_setup_table( 'automatorwp_automations' );
    $meta_id = ct_update_object_meta( $automation_id, $meta_key, $meta_value, $prev_value );
    ct_reset_setup_table();

    return $meta_id;

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

    $cache = automatorwp_get_cache( 'automation_triggers', array(), false );

    if( isset( $cache[$automation_id] ) ) {

        // Use triggers already cached
        $triggers = $cache[$automation_id];

    } else {

        // Query the triggers for the first time
        ct_setup_table( 'automatorwp_triggers' );

        $ct_query = new CT_Query( array(
            'automation_id' => $automation_id,
            'orderby' => 'position',
            'order' => 'ASC',
            'items_per_page' => -1,
        ) );

        $triggers = $ct_query->get_results();

        ct_reset_setup_table();

        // Cache triggers
        $cache[$automation_id] = $triggers;

        automatorwp_set_cache( 'automation_triggers', $cache );

    }

    if( $output === ARRAY_N || $output === ARRAY_A ) {

        // Turn array of objects into an array of arrays
        foreach( $triggers as $i => $trigger ) {
            $triggers[$i] = (array) $trigger;
        }

    }

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

    $cache = automatorwp_get_cache( 'automation_actions', array(), false );

    if( isset( $cache[$automation_id] ) ) {

        // Use triggers already cached
        $actions = $cache[$automation_id];

    } else {

        // Query the triggers for the first time

        ct_setup_table( 'automatorwp_actions' );

        $ct_query = new CT_Query( array(
            'automation_id' => $automation_id,
            'orderby' => 'position',
            'order' => 'ASC',
            'items_per_page' => -1,
        ) );

        $actions = $ct_query->get_results();

        ct_reset_setup_table();

        // Cache actions
        $cache[$automation_id] = $actions;

        automatorwp_set_cache( 'automation_actions', $cache );

    }

    if( $output === ARRAY_N || $output === ARRAY_A ) {

        // Turn array of objects into an array of arrays
        foreach( $actions as $i => $action ) {
            $actions[$i] = (array) $action;
        }

    }

    return $actions;

}

/**
 * Get the all users trigger from an automation
 *
 * @since 2.2.2
 *
 * @param stdClass  $automation         The automation object.
 *
 * @return stdClass|null
 */
function automatorwp_get_all_users_trigger( $automation ) {

    global $automatorwp_run_automation_error;

    if( ! is_object( $automation ) ) {
        $automatorwp_run_automation_error = __( 'Invalid automation.', 'automatorwp' );
        return null;
    }

    $cache = automatorwp_get_cache( 'automation_all_users_trigger', array(), false );

    if( isset( $cache[$automation->id] ) ) {

        // Use triggers already cached
        $all_users_trigger = $cache[$automation->id];

    } else {

        $triggers = automatorwp_get_automation_triggers( $automation->id );
        $all_users_trigger = null;

        // Check if user has completed all automation trigger
        foreach( $triggers as $trigger ) {

            if( $trigger->type === 'automatorwp_all_users' ) {
                $all_users_trigger = $trigger;
                break;
            }

        }

        // Cache triggers
        $cache[$automation->id] = $all_users_trigger;

        automatorwp_set_cache( 'automation_all_users_trigger', $cache );

    }

    return $all_users_trigger;

}

/**
 * Get all users automation users IDs
 *
 * @since 2.2.2
 *
 * @param stdClass  $automation         The automation object.
 *
 * @return array|false                  Array with all users IDs.
 */
function automatorwp_get_all_users_automation_users_ids( $automation ) {

    global $automatorwp_run_automation_error, $wpdb;

    if( ! is_object( $automation ) ) {
        $automatorwp_run_automation_error = __( 'Invalid automation.', 'automatorwp' );
        return false;
    }

    $trigger = automatorwp_get_all_users_trigger( $automation );

    // Bail if trigger not found
    if( ! $trigger ) {
        $automatorwp_run_automation_error = __( 'Trigger configuration not found.', 'automatorwp' );
        return false;
    }

    // Get the users SQL
    $sql = automatorwp_get_all_users_automation_sql( $automation, $trigger, false );

    if( ! $sql ) {
        return false;
    }

    return $wpdb->get_col( $sql );

}

/**
 * Get all users automation users count
 *
 * @since 2.2.2
 *
 * @param stdClass  $automation         The automation object.
 *
 * @return int|false                    Number of users to process in this automation
 */
function automatorwp_get_all_users_automation_users_count( $automation ) {

    global $automatorwp_run_automation_error, $wpdb;

    if( ! is_object( $automation ) ) {
        $automatorwp_run_automation_error = __( 'Invalid automation.', 'automatorwp' );
        return false;
    }

    $trigger = automatorwp_get_all_users_trigger( $automation );

    // Prevent automations already in progress
    if( ! $trigger ) {
        $automatorwp_run_automation_error = __( 'Trigger configuration not found.', 'automatorwp' );
        return false;
    }

    // Get the users SQL
    $sql = automatorwp_get_all_users_automation_sql( $automation, $trigger, true );

    if( ! $sql ) {
        return false;
    }

    return absint( $wpdb->get_var( $sql ) );

}

/**
 * Get all users automation users SQL
 *
 * @since 2.2.2
 *
 * @param stdClass  $automation         The automation object.
 * @param stdClass  $trigger            The trigger object.
 *
 * @return string|false
 */
function automatorwp_get_all_users_automation_sql( $automation, $trigger, $count = false ) {

    global $automatorwp_run_automation_error;

    if( ! is_object( $automation ) ) {
        $automatorwp_run_automation_error = __( 'Invalid automation.', 'automatorwp' );
        return false;
    }

    $loop = 0;
    $users_per_loop = 0;

    if( ! $count ) {
        $users_per_loop = absint( automatorwp_get_automation_meta( $automation->id, 'users_per_loop', true ) );

        // Bail if users per loop not correctly configured
        if( $users_per_loop <= 0 ) {
            $automatorwp_run_automation_error = __( 'Users per loop need to be higher than 0.', 'automatorwp' );
            return false;
        }

        // Get the loop stored in options to calculate the offset
        $loop = absint( automatorwp_get_automation_meta( $automation->id, 'current_loop', true ) );
    }

    // Get the trigger stored options
    $trigger_options = automatorwp_get_trigger_stored_options( $trigger->id );

    $sql = false;

    /**
     * Available filter to override the all users automation SQL
     *
     * @since 2.2.2
     *
     * @param string    $sql                The SQL query
     * @param stdClass  $automation         The automation object
     * @param stdClass  $trigger            The trigger object
     * @param bool      $count              True if is looking for the SQL to count the number of users
     * @param array     $trigger_options    The trigger's stored options
     * @param int       $users_per_loop     The automation users per loop option
     * @param int       $loop               The current loop
     */
    $sql = apply_filters( 'automatorwp_get_all_users_automation_sql', $sql, $automation, $trigger, $count, $trigger_options, $users_per_loop, $loop );

    return $sql;
}

/**
 * Get the all posts trigger from an automation
 *
 * @since 2.2.2
 *
 * @param stdClass  $automation         The automation object.
 *
 * @return stdClass|null
 */
function automatorwp_get_all_posts_trigger( $automation ) {

    global $automatorwp_run_automation_error;

    if( ! is_object( $automation ) ) {
        $automatorwp_run_automation_error = __( 'Invalid automation.', 'automatorwp' );
        return null;
    }

    $cache = automatorwp_get_cache( 'automation_all_posts_trigger', array(), false );

    if( isset( $cache[$automation->id] ) ) {

        // Use triggers already cached
        $all_posts_trigger = $cache[$automation->id];

    } else {

        $triggers = automatorwp_get_automation_triggers( $automation->id );
        $all_posts_trigger = null;

        // Check if post has completed all automation trigger
        foreach( $triggers as $trigger ) {

            if( $trigger->type === 'automatorwp_all_posts' ) {
                $all_posts_trigger = $trigger;
                break;
            }

        }

        // Cache triggers
        $cache[$automation->id] = $all_posts_trigger;

        automatorwp_set_cache( 'automation_all_posts_trigger', $cache );

    }

    return $all_posts_trigger;

}

/**
 * Get all posts automation posts IDs
 *
 * @since 2.2.2
 *
 * @param stdClass  $automation         The automation object.
 *
 * @return array|false                  Array with all posts IDs.
 */
function automatorwp_get_all_posts_automation_posts_ids( $automation ) {

    global $automatorwp_run_automation_error, $wpdb;

    if( ! is_object( $automation ) ) {
        $automatorwp_run_automation_error = __( 'Invalid automation.', 'automatorwp' );
        return false;
    }

    $trigger = automatorwp_get_all_posts_trigger( $automation );

    // Bail if trigger not found
    if( ! $trigger ) {
        $automatorwp_run_automation_error = __( 'Trigger configuration not found.', 'automatorwp' );
        return false;
    }

    // Get the posts SQL
    $sql = automatorwp_get_all_posts_automation_sql( $automation, $trigger, false );

    if( ! $sql ) {
        return false;
    }

    return $wpdb->get_col( $sql );

}

/**
 * Get all posts automation posts count
 *
 * @since 2.2.2
 *
 * @param stdClass  $automation         The automation object.
 *
 * @return int|false                    Number of posts to process in this automation
 */
function automatorwp_get_all_posts_automation_posts_count( $automation ) {

    global $automatorwp_run_automation_error, $wpdb;

    if( ! is_object( $automation ) ) {
        $automatorwp_run_automation_error = __( 'Invalid automation.', 'automatorwp' );
        return false;
    }

    $trigger = automatorwp_get_all_posts_trigger( $automation );

    // Prevent automations already in progress
    if( ! $trigger ) {
        $automatorwp_run_automation_error = __( 'Trigger configuration not found.', 'automatorwp' );
        return false;
    }

    // Get the posts SQL
    $sql = automatorwp_get_all_posts_automation_sql( $automation, $trigger, true );

    if( ! $sql ) {
        return false;
    }

    return absint( $wpdb->get_var( $sql ) );

}

/**
 * Get all posts automation posts SQL
 *
 * @since 2.2.2
 *
 * @param stdClass  $automation         The automation object.
 * @param stdClass  $trigger            The trigger object.
 *
 * @return string|false
 */
function automatorwp_get_all_posts_automation_sql( $automation, $trigger, $count = false ) {

    global $automatorwp_run_automation_error;

    if( ! is_object( $automation ) ) {
        $automatorwp_run_automation_error = __( 'Invalid automation.', 'automatorwp' );
        return false;
    }

    $loop = 0;
    $posts_per_loop = 0;

    if( ! $count ) {
        $posts_per_loop = absint( automatorwp_get_automation_meta( $automation->id, 'posts_per_loop', true ) );

        // Bail if posts per loop not correctly configured
        if( $posts_per_loop <= 0 ) {
            $automatorwp_run_automation_error = __( 'Posts per loop need to be higher than 0.', 'automatorwp' );
            return false;
        }

        // Get the loop stored in options to calculate the offset
        $loop = absint( automatorwp_get_automation_meta( $automation->id, 'current_loop', true ) );
    }

    // Get the trigger stored options
    $trigger_options = automatorwp_get_trigger_stored_options( $trigger->id );

    $sql = false;

    /**
     * Available filter to override the all posts automation SQL
     *
     * @since 2.2.2
     *
     * @param string    $sql                The SQL query
     * @param stdClass  $automation         The automation object
     * @param stdClass  $trigger            The trigger object
     * @param bool      $count              True if is looking for the SQL to count the number of posts
     * @param array     $trigger_options    The trigger's stored options
     * @param int       $posts_per_loop     The automation posts per loop option
     * @param int       $loop               The current loop
     */
    $sql = apply_filters( 'automatorwp_get_all_posts_automation_sql', $sql, $automation, $trigger, $count, $trigger_options, $posts_per_loop, $loop );

    return $sql;
}

/**
 * Get the automation run details
 *
 * @since 2.2.2
 *
 * @param stdClass $automation The automation object
 *
 * @return array|false
 */
function automatorwp_get_automation_run_details( $automation ) {

    global $automatorwp_run_automation_error;

    if( ! is_object( $automation ) ) {
        $automatorwp_run_automation_error = __( 'Invalid automation.', 'automatorwp' );
        return false;
    }

    $items_per_loop = 0;

    if( $automation->type === 'all-users' ) {
        $items_per_loop = absint( automatorwp_get_automation_meta( $automation->id, 'users_per_loop', true ) );
        $count = automatorwp_get_all_users_automation_users_count( $automation );
    } else if( $automation->type === 'all-posts' ) {
        $items_per_loop = absint( automatorwp_get_automation_meta( $automation->id, 'posts_per_loop', true ) );
        $count = automatorwp_get_all_posts_automation_posts_count( $automation );
    }

    $loop = absint( automatorwp_get_automation_meta( $automation->id, 'current_loop', true ) );
    $processed = ( $loop * $items_per_loop ) + $items_per_loop;
    $processed = min( $processed, $count );
    $percentage = ( $count > 0 ? ( $processed / $count ) * 100 : 100 );

    return array(
        'loop' => $loop,
        'items_per_loop' => $items_per_loop,
        'count' => $count,
        'processed' => $processed,
        'percentage' => $percentage,
        'run_again' => ( $processed < $count ),
    );

}

/**
 * Calculate the next run date for an all users automation
 *
 * @since  1.0.0
 *
 * @param int   $automation_id  The automation ID
 * @param bool  $is_saving      Flag to meet if the automation is being saved from the edit screen to handle the values from $_POST
 */
function automatorwp_update_automation_next_run_date( $automation_id, $is_saving = false ) {

    // Setup vars
    $args = automatorwp_setup_automation_next_run_date_args( $automation_id, $is_saving );
    $current_time = current_time( 'timestamp' );
    $date_format = automatorwp_get_date_format( array( 'Y-m-d', 'm/d/Y' ) );
    $time_format = automatorwp_get_time_format();
    $datetime_format = $date_format . ' ' . $time_format;
    $timestamp = false;

    if( $args['schedule_run'] === true ) {
        // Schedule run

        $schedule_run_datetime = $args['schedule_run_datetime'];

        // Check if schedule run datetime is correctly setup (this only when saving)
        if( is_array( $schedule_run_datetime ) && isset( $schedule_run_datetime['date'] ) && isset( $schedule_run_datetime['time'] ) ) {

            $date = sanitize_text_field( $schedule_run_datetime['date'] );
            $time = sanitize_text_field( $schedule_run_datetime['time'] );

            $timestamp = automatorwp_get_timestamp_from_value( $date . ' ' . $time, $date_format );
        } else if( ! empty( $schedule_run_datetime ) ) {
            // Schedule run datetime is stored as a timestamp
            $timestamp = $schedule_run_datetime;
        }
    } else if( $args['recurring_run'] === true ) {
        // Recurring run

        $day = $args['recurring_run_day'];
        $period = $args['recurring_run_period'];
        $time = $args['recurring_run_time'];

        switch( $period ) {
            case 'day':
                $date = date( 'Y-m-d', strtotime(' +1 day', $current_time ) );
                break;
            case 'week':
                $days_of_the_week = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );
                $week_day = $day - 1;

                // Get the current week timestamp
                $week_timestamp = strtotime( $days_of_the_week[$week_day], strtotime( 'this week', $current_time ) );

                // If lower than today, get the next week
                if( $week_timestamp <= $current_time ) {
                    $week_timestamp = strtotime( $days_of_the_week[$week_day], strtotime( 'next week', $current_time ) );
                }

                $date = date( 'Y-m-d', $week_timestamp );
                break;
            case 'month':
                // Get the current month timestamp
                $month = absint( date('m', $current_time) );

                // Check the max day of this month
                $max_day = absint( date( 't', $current_time ) );
                $max_day = min( $day, $max_day );

                $month_timestamp = strtotime( date( "{$max_day}-{$month}-Y", $current_time ) );

                // If lower than today, get the next month
                if( $month_timestamp <= $current_time ) {
                    $next_month = strtotime( 'next month', $current_time );
                    $month = absint( date('m', $next_month) );

                    // Check the max day of next month
                    $max_day = absint( date( 't', $next_month ) );
                    $max_day = min( $day, $max_day );

                    $month_timestamp = strtotime( date( "{$max_day}-{$month}-Y", $current_time ) );
                }

                $date = date( 'Y-m-d', $month_timestamp );
                break;
            case 'year':
                $year = absint( date('Y', $current_time) );
                $year_day = $day - 1;

                // Check if is a leap year
                if( $day === 365 && checkdate( 2, 29, $year ) ) {
                    $year_day = 365;
                }

                // Get the current year timestamp

                $date = DateTime::createFromFormat( 'Y z' , $year . ' ' . $year_day);
                $year_timestamp = $date->getTimestamp();

                // If lower than today, get the next year
                if( $year_timestamp <= $current_time ) {
                    // Increase year and restore the day to check if next year is leap
                    $year += 1;
                    $year_day = $day - 1;

                    // Check if is a leap year
                    if( $day === 365 && checkdate( 2, 29, $year ) ) {
                        $year_day = 365;
                    }

                    $date = DateTime::createFromFormat( 'Y z' , $year . ' ' . $year_day);

                    $year_timestamp = $date->getTimestamp();
                }

                $date = date( 'Y-m-d', $year_timestamp );
                break;
        }

        $datetime = $date . ' ' . $time;
        $timestamp = automatorwp_get_timestamp_from_value( $datetime, $datetime_format );

    }

    // Update the next run date meta
    if( $timestamp && $timestamp > $current_time ) {
        automatorwp_update_automation_meta( $automation_id, 'next_run_date', date( 'Y-m-d H:i:s', $timestamp ) );
    } else {
        automatorwp_update_automation_meta( $automation_id, 'next_run_date', '' );
    }

}

/**
 * Setup args for the next run date calculation
 *
 * @since  1.0.0
 *
 * @param int   $automation_id  The automation ID
 * @param bool  $is_saving      Flag to meet if the automation is being saved from the edit screen to handle the values from $_POST
 */
function automatorwp_setup_automation_next_run_date_args( $automation_id, $is_saving = false ) {

    $args = array();

    // Schedule run
    if( $is_saving ) {
        $args['schedule_run'] = ( isset( $_POST['schedule_run'] ) ? true : false );
    } else {
        $args['schedule_run'] = (bool) automatorwp_get_automation_meta( $automation_id, 'schedule_run', true );
    }

    if( $args['schedule_run'] ) {

        // Schedule run datetime
        if( $is_saving ) {
            $args['schedule_run_datetime'] = ( isset( $_POST['schedule_run_datetime'] ) ? $_POST['schedule_run_datetime'] : array() );
        } else {
            $args['schedule_run_datetime'] = automatorwp_get_automation_meta( $automation_id, 'schedule_run_datetime', true );
        }

    }

    // Recurring run
    if( $is_saving ) {
        $args['recurring_run'] = ( isset( $_POST['recurring_run'] ) ? true : false );
    } else {
        $args['recurring_run'] = (bool) automatorwp_get_automation_meta( $automation_id, 'recurring_run', true );
    }

    if( $args['recurring_run'] ) {

        // Recurring run day
        if( $is_saving ) {
            $args['recurring_run_day'] = absint( ( isset( $_POST['recurring_run_day'] ) ? $_POST['recurring_run_day'] : '1' ) );
        } else {
            $args['recurring_run_day'] = absint( automatorwp_get_automation_meta( $automation_id, 'recurring_run_day', true ) );
        }

        // Recurring run period
        if( $is_saving ) {
            $args['recurring_run_period'] = ( isset( $_POST['recurring_run_period'] ) ? sanitize_text_field( $_POST['recurring_run_period'] ) : 'day' );
        } else {
            $args['recurring_run_period'] = automatorwp_get_automation_meta( $automation_id, 'recurring_run_period', true );
        }

        // Recurring run time
        if( $is_saving ) {
            $args['recurring_run_time'] = ( isset( $_POST['recurring_run_time'] ) ? sanitize_text_field( $_POST['recurring_run_time'] ) : 'day' );
        } else {
            $args['recurring_run_time'] = automatorwp_get_automation_meta( $automation_id, 'recurring_run_time', true );
        }

    }

    return $args;

}

/**
 * Clone automation
 *
 * @since  1.0.0
 *
 * @param int       $automation_id  The automation ID
 * @param int       $user_id        The user ID to assign to the automation
 *
 * @return int|bool                 Clone result
 */
function automatorwp_clone_automation( $automation_id, $user_id = 0 ) {

    if( $user_id === 0 ) {
        $user_id = get_current_user_id();
    }

    ct_setup_table( 'automatorwp_automations' );

    $automation = ct_get_object( $automation_id );

    // Bail if automation does not exists
    if( ! $automation ) {
        ct_reset_setup_table();
        return false;
    }

    $automation = ( array ) $automation;

    // Setup the new automation info
    unset( $automation['id'] );
    $automation['title'] .= ( ! empty( $automation['title'] ) ? ' ' : '' ) . __( '(Cloned)', 'automatorwp' );
    $automation['user_id'] = $user_id;
    $automation['status'] = 'inactive';
    $automation['date'] = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );

    // Insert the new automation
    $new_automation_id = ct_insert_object( $automation );

    if( ! $new_automation_id ) {
        ct_reset_setup_table();
        return false;
    }

    automatorwp_clone_automation_items( $automation_id, $new_automation_id );

    ct_reset_setup_table();
    return $new_automation_id;

}

/**
 * Clone all automation items to a new one
 *
 * @since  1.0.0
 *
 * @param int       $automation_id      The automation ID
 * @param int       $new_automation_id  The automation ID to clone all the items
 */
function automatorwp_clone_automation_items( $automation_id, $new_automation_id ) {

    global $wpdb;

    $item_types = array( 'trigger', 'action' );
    $ids = array();
    $replacements = array();

    // Migrate all items to the new automation and collect the old and new IDs
    foreach( $item_types as $item_type ) {

        if( $item_type === 'trigger' ) {
            $items = automatorwp_get_automation_triggers( $automation_id );
        } else {
            $items = automatorwp_get_automation_actions( $automation_id );
        }

        ct_setup_table( "automatorwp_{$item_type}s" );

        foreach( $items as $item ) {

            $new_item = ( array ) $item;

            unset( $new_item['id'] );
            $new_item['automation_id'] = $new_automation_id;
            $new_item['date'] = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );

            $new_item_id = ct_insert_object( $new_item );

            // Update old ids and replacements to be used on metas
            if( $new_item_id ) {
                $ids[$item->id] = $new_item_id;
                $replacements['{' . $item->id . ':'] = '{' . $new_item_id . ':';
            }

        }

        ct_reset_setup_table();

    }

    $tags = array_keys( $replacements );

    // Loop again the items to update their metas
    foreach( $item_types as $item_type ) {

        // Do not worry about performance, AutomatorWP already caches this functions
        if( $item_type === 'trigger' ) {
            $items = automatorwp_get_automation_triggers( $automation_id );
        } else {
            $items = automatorwp_get_automation_actions( $automation_id );
        }

        $ct_table = ct_setup_table( "automatorwp_{$item_type}s" );
        $metas = array();

        foreach( $items as $item ) {

            // Skip if item not has been cloned
            if( ! isset( $ids[$item->id] ) ) {
                continue;
            }

            // Get all item metas
            $item_metas = $wpdb->get_results( "SELECT meta_key, meta_value FROM {$ct_table->meta->db->table_name} WHERE id = {$item->id}", ARRAY_A );

            foreach( $item_metas as $i => $item_meta ) {

                $meta_key = $item_metas[$i]['meta_key'];

                /**
                 * Filter to exclude a meta on clone this item
                 * $item_type: trigger | action
                 * $item->type: The trigger or action type
                 * $meta_key: The meta key
                 *
                 * @since  1.0.0
                 *
                 * @param bool $exclude
                 *
                 * @return bool
                 */
                $exclude = apply_filters( "automatorwp_clone_{$item_type}_{$item->type}_meta_{$meta_key}_excluded", false );

                // Skip if meta gets excluded on clone
                if( $exclude ) {
                    continue;
                }

                // Replace metas with old IDs with the new ones
                $meta_value = str_replace( $tags, $replacements, $item_metas[$i]['meta_value'] );

                // Prepare for the upcoming insert
                $metas[] = $wpdb->prepare( '%d, %s, %s', array( $ids[$item->id], $meta_key, $meta_value ) );
            }

            $item_title = str_replace( $tags, $replacements, $item->title );

            /**
             * Filter to parse the item title
             * $item_type: trigger | action
             * $item->type: The trigger or action type
             *
             * @since  1.0.0
             *
             * @param string    $item_title     The item title
             * @param int       $old_item_id    The old item ID
             * @param int       $new_item_id    The new item ID
             *
             * @return string
             */
            $item_title = apply_filters( "automatorwp_clone_{$item_type}_{$item->type}_title", $item_title, $item->id, $ids[$item->id] );

            // Update the new item title
            ct_update_object( array(
                'id' => $ids[$item->id],
                'title' => $item_title,
            ) );

        }

        if( count( $metas ) ) {
            $metas = implode( '), (', $metas );

            // Run a single query to insert all metas instead of insert them one-by-one
            $wpdb->query( "INSERT INTO {$ct_table->meta->db->table_name} (id, meta_key, meta_value) VALUES ({$metas})" );
        }

        ct_reset_setup_table();

    }

}

/**
 * Turn an automation into an exportable URL
 *
 * @since  1.0.0
 *
 * @param int $automation_id The automation ID
 *
 * @return string
 */
function automatorwp_get_automation_export_url( $automation_id ) {

    $automation = automatorwp_get_automation_object( $automation_id );

    if( ! $automation ) {
        return '';
    }

    $url = 'wp-admin/admin.php?page=automatorwp_import_automation';

    // Setup the automation args
    // Only include those parameters if they are different from their default value to reduce the URL length
    if( ! empty( $automation->title ) ) {
        $url = add_query_arg( 'ti', $automation->title, $url );
    }

    if( $automation->type !== 'user' ) {
        $url = add_query_arg( 'ty', $automation->type, $url );
    }

    if( absint( $automation->sequential ) !== 0 ) {
        $url = add_query_arg( 's', $automation->sequential, $url );
    }

    if( absint( $automation->times_per_user ) !== 1 ) {
        $url = add_query_arg( 'tu', $automation->times_per_user, $url );
    }

    if( absint( $automation->times ) !== 0 ) {
        $url = add_query_arg( 'tim', $automation->times, $url );
    }

    $url .= automatorwp_get_automation_items_export_url( $automation_id );

    return $url;

}

/**
 * Turn automation items into an exportable URL
 *
 * @since  1.0.0
 *
 * @param int $automation_id The automation ID
 *
 * @return string
 */
function automatorwp_get_automation_items_export_url( $automation_id ) {

    $item_types = array( 'trigger', 'action' );
    $false_url = 'a.php?b=c';
    $url = $false_url;

    // Loop all automation items
    foreach( $item_types as $item_type ) {

        // Get the items
        if( $item_type === 'trigger' ) {
            $items = automatorwp_get_automation_triggers( $automation_id );
        } else {
            $items = automatorwp_get_automation_actions( $automation_id );
        }

        $url_items = array();

        ct_setup_table( "automatorwp_{$item_type}s" );

        foreach( $items as $item ) {

            // Get the type args
            if( $item_type === 'trigger' ) {
                $type_args = automatorwp_get_trigger( $item->type );
            } else {
                $type_args = automatorwp_get_action( $item->type );
            }

            if( ! $type_args ) {
                continue;
            }

            // Setup the item options
            $options = array();

            // Special check for filters
            if( $item->type === 'filter' ) {

                $filter = ct_get_object_meta( $item->id, 'filter', true );

                $filter_args = automatorwp_get_filter( $filter );

                // If filter args found, append the filter options to the type options
                if( $filter_args ) {
                    $type_args['options'] = array_merge( $type_args['options'], $filter_args['options'] );
                }

            }

            foreach( $type_args['options'] as $option => $option_args ) {

                // Skip option if not has fields
                if( ! isset( $option_args['fields'] ) ) {
                    continue;
                }

                foreach( $option_args['fields'] as $field_id => $field_args ) {

                    /**
                     * Filter to exclude a meta on export this item through URL
                     * $item_type: trigger | action
                     * $item->type: The trigger or action type
                     * $field_id: The meta key
                     *
                     * @since  1.0.0
                     *
                     * @param bool $exclude
                     *
                     * @return bool
                     */
                    $exclude = apply_filters( "automatorwp_export_url_{$item_type}_{$item->type}_meta_{$field_id}_excluded", false );

                    // Skip if meta gets excluded on export through URL
                    if( $exclude ) {
                        continue;
                    }

                    $field_value = ct_get_object_meta( $item->id, $field_id, true );

                    // Skip options with
                    if( isset( $field_args['default'] ) && $field_args['default'] == $field_value ) {
                        continue;
                    }

                    // Skip if no value entered
                    if( empty( $field_value ) ) {
                        continue;
                    }

                    $options[$field_id] = urlencode( maybe_serialize( $field_value ) );

                }

            }

            $url_items[] = array(
                'i' => $item->id,
                't' => $item->type,
                'o' => $options,
            );

        }

        ct_reset_setup_table();

        // Add the items to the URL
        if( $item_type === 'trigger' ) {
            $prefix = 't';
        } else {
            $prefix = 'a';
        }

        // Pull all url items to reduce the URL length
        $url = automatorwp_pull_array_for_export_url( $url_items, $url, $prefix );

    }

    // Remove the false URL part
    $url = str_replace( $false_url, '', $url );

    return $url;

}

/**
 * Helper function to pull array elements to an exportable URL
 *
 * @since  1.0.0
 *
 * @param array     $array
 * @param string    $url
 * @param string    $prefix
 *
 * @return string
 */
function automatorwp_pull_array_for_export_url( $array, $url, $prefix = '' ) {

    foreach( $array as $key => $value ) {

        if( is_array( $value ) ) {

            $url = automatorwp_pull_array_for_export_url( $value, $url, "{$prefix}-{$key}" );

        } else {
            $url = add_query_arg( "{$prefix}-{$key}", $value, $url );
        }

    }

    return $url;

}

/**
 * Helper function to get array elements from an exportable URL
 *
 * @since  1.0.0
 *
 * @param array     $request
 * @param string    $prefix
 *
 * @return array
 */
function automatorwp_get_array_from_export_url( $request, $prefix = '' ) {

    $params = array();
    $params[$prefix] = array();

    foreach( $request as $key => $value ) {

        // Skip if not starts with the required prefix
        if( ! automatorwp_starts_with( $key, "{$prefix}-" ) ) {
            continue;
        }

        $keys = explode( '-', $key );
        $length = count( $keys );

        $array = &$params[$prefix];

        foreach ( $keys as $i => $sub_key ) {

            // Skip the first sub key
            if( $i === 0 ) {
                continue;
            }

            // Create the sub key
            if( ! isset( $array[$sub_key] )  ) {
                $array[$sub_key] = array();
            }

            // If is the last sub key, assign the original value
            if( $i === ( $length - 1 ) ) {
                $array[$sub_key] = $value;
            }

            $array = &$array[$sub_key];

        }

    }

    return $params[$prefix];

}