<?php
/**
 * Events
 *
 * @package     AutomatorWP\Events
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Handles an event trigger
 *
 * @since 1.0.0
 *
 * @param array $event Event information
 *
 * @return array|false An array of triggered triggers or false if event is not correctly triggered
 */
function automatorwp_trigger_event( $event ) {

    global $automatorwp_completed_triggers, $automatorwp_event;

    $automatorwp_event = false;

    // Check the event received
    if( ! automatorwp_is_event_correct( $event ) ) {
        return false;
    }

    $automatorwp_event = $event;

    // Initialize completed triggers count
    if( ! is_array( $automatorwp_completed_triggers ) ) {
        $automatorwp_completed_triggers = array();
    }

    // Get triggered triggers
    $triggers = automatorwp_get_event_triggered_triggers( $event['trigger'] );

    foreach( $triggers as $trigger ) {

        if( automatorwp_maybe_completed_trigger( $trigger, $event ) ) {
            $automatorwp_completed_triggers[] = $trigger;
        }

    }

    return ( count( $automatorwp_completed_triggers ) ? $automatorwp_completed_triggers : false );

}

/**
 * Checks if event contains the required attributes (by default, user_id and trigger)
 *
 * @since 1.0.0
 *
 * @param array $event Event information
 *
 * @return bool
 */
function automatorwp_is_event_correct( $event = array() ) {

    // Check trigger
    if( ! isset( $event['trigger'] ) ) {
        return false;
    }

    if( ! isset( AutomatorWP()->triggers[$event['trigger']] ) ) {
        return false;
    }

    $trigger = AutomatorWP()->triggers[$event['trigger']];

    // Check the user ID if trigger is not anonymous
    if( ! $trigger['anonymous'] ) {

        // Check user
        if( ! isset( $event['user_id'] ) ) {
            return false;
        }

        $event['user_id'] = absint( $event['user_id'] );

        if( $event['user_id'] === 0 ) {
            return false;
        }

    }

    /**
     * Filter to add custom checks to event checking
     *
     * @since 1.0.0
     *
     * @param bool  $is_correct If event is correct or not
     * @param array $event      Event information
     *
     * @return bool
     */
    return apply_filters( 'automatorwp_is_event_correct', true, $event );

}

/**
 * Get triggers by type commonly triggered by automatorwp_trigger_event() function
 *
 * @since 1.0.0
 *
 * @param string $trigger_type The trigger type triggered in event
 *
 * @return array
 */
function automatorwp_get_event_triggered_triggers( $trigger_type ) {

    ct_setup_table( 'automatorwp_triggers' );

    $ct_query = new CT_Query( array(
        'type' => $trigger_type,
        'orderby' => 'position',
        'order' => 'ASC',
        'items_per_page' => -1,
    ) );

    $triggers = $ct_query->get_results();

    ct_reset_setup_table();

    if( is_array( $triggers ) ) {
        return $triggers;
    } else {
        return array();
    }

}

/**
 * Check if trigger can be marked as completed
 *
 * @since 1.0.0
 *
 * @param stdClass  $trigger        The trigger object
 * @param array     $event          Event information
 *
 * @return bool
 */
function automatorwp_maybe_completed_trigger( $trigger, $event = array() ) {

    // Check if trigger is correct
    if( ! is_object( $trigger ) ) {
        return false;
    }

    // Check if automation is correct
    $automation = automatorwp_get_trigger_automation( $trigger->id );

    if( ! is_object( $automation ) ) {
        return false;
    }

    $trigger_args = automatorwp_get_trigger( $trigger->type );

    if( $automation->type === 'anonymous' ) {
        // Anonymous automation

        // Bail if user trigger is used on an anonymous automation
        if( ! $trigger_args['anonymous'] ) {
            return false;
        }

        return automatorwp_maybe_anonymous_completed_trigger( $trigger, $event );

    } else {
        // User automation

        // Bail if anonymous trigger is used on a user automation
        if( $trigger_args['anonymous'] ) {
            return false;
        }

        // Args has been checked on automatorwp_is_event_correct() function
        $user_id = $event['user_id'];

        return automatorwp_maybe_user_completed_trigger( $trigger, $user_id, $event );

    }

}

// -------------------------------------------
// Guest event
// -------------------------------------------

/**
 * Check if anonymous has completed a trigger
 *
 * @since 1.3.0
 *
 * @param stdClass  $trigger        The trigger object
 * @param array     $event          Event information
 *
 * @return bool
 */
function automatorwp_maybe_anonymous_completed_trigger( $trigger, $event = array() ) {

    // Check if trigger is correct
    if( ! is_object( $trigger ) ) {
        return false;
    }

    // Check if automation is correct
    $automation = automatorwp_get_trigger_automation( $trigger->id );

    if( ! is_object( $automation ) ) {
        return false;
    }

    // Get the trigger stored options
    $trigger_options = automatorwp_get_trigger_stored_options( $trigger->id );

    // Check if anonymous has access to the trigger
    if( ! automatorwp_anonymous_has_access_to_trigger( $trigger, $event, $trigger_options, $automation ) ) {
        return false;
    }

    // Check if anonymous deserves the trigger
    if( ! automatorwp_anonymous_deserves_trigger( $trigger, $event, $trigger_options, $automation ) ) {
        return false;
    }

    // Mark trigger as completed
    $trigger_completed = automatorwp_anonymous_completed_trigger( $trigger, $event, $trigger_options, $automation );

    if( ! $trigger_completed ) {
        return false;
    }

    $user_id = automatorwp_get_anonymous_automation_user_id( $automation, $event );

    // Bail if user can't be created
    if( ! $user_id ) {
        return false;
    }

    // Update anonymous log user ID
    automatorwp_update_anonymous_completed_trigger_log_user_id( $user_id );

    automatorwp_maybe_user_completed_automation( $automation, $user_id, $event );

    return true;

}

/**
 * Check if anonymous has access to trigger
 *
 * @since 1.3.0
 *
 * @param stdClass  $trigger            The trigger object
 * @param array     $event              Event information
 * @param array     $trigger_options    The trigger's stored options
 * @param stdClass  $automation         The trigger's automation object
 *
 * @return bool                         True if anonymous has access, false otherwise
 */
function automatorwp_anonymous_has_access_to_trigger( $trigger = null, $event = array(), $trigger_options = array(), $automation = null ) {

    // Check if trigger is correct
    if( ! is_object( $trigger ) ) {
        return false;
    }

    // Check if automation is correct
    if( $automation === null ) {
        $automation = automatorwp_get_trigger_automation( $trigger->id );

        if( ! is_object( $automation ) ) {
            return false;
        }
    }

    $has_access = true;

    // Bail if automation is not active
    if( $automation->status !== 'active' ) {
        $has_access = false;
    }

    $now = current_time( 'timestamp' );

    // Bail if is a future automation
    if( $has_access && strtotime( $automation->date ) > $now ) {
        $has_access = false;
    }

    // Check if exceeded automation completion times
    $times = absint( $automation->times );

    if( $has_access && $times > 0 ) {
        $completion_times = automatorwp_get_object_completion_times( $automation->id, 'automation' );

        if( $completion_times >= $times ) {
            $has_access = false;
        }
    }

    /**
     * Filter to override the has access check.
     * This filter is to check the automation configuration.
     * Triggers should use the 'automatorwp_anonymous_deserves_trigger' filter instead.
     *
     * @since 1.3.0
     *
     * @param bool      $has_access         True if anonymous has access, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                         True if anonymous has access, false otherwise
     */
    return apply_filters( 'automatorwp_anonymous_has_access_to_trigger', $has_access, $trigger, $event, $trigger_options, $automation );

}

/**
 * Check if anonymous deserves trigger
 *
 * @since 1.3.0
 *
 * @param stdClass  $trigger            The trigger object
 * @param array     $event              Event information
 * @param array     $trigger_options    The trigger's stored options
 * @param stdClass  $automation         The trigger's automation object
 *
 * @return bool                         True if anonymous deserves trigger, false otherwise
 */
function automatorwp_anonymous_deserves_trigger( $trigger = null, $event = array(), $trigger_options = array(), $automation = null ) {

    // Check if trigger is correct
    if( ! is_object( $trigger ) ) {
        return false;
    }

    // Check if automation is correct
    if( $automation === null ) {
        $automation = automatorwp_get_trigger_automation( $trigger->id );

        if( ! is_object( $automation ) ) {
            return false;
        }
    }

    $deserves_trigger = true;

    /**
     * Filter to override the anonymous deserves trigger check.
     * This filter is to check the trigger configuration.
     * Triggers should use this filter.
     *
     * @since 1.3.0
     *
     * @param bool      $deserves_trigger   True if anonymous deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                         True if anonymous deserves trigger, false otherwise
     * @return bool                         True if anonymous deserves trigger, false otherwise
     */
    return apply_filters( 'automatorwp_anonymous_deserves_trigger', $deserves_trigger, $trigger, $event, $trigger_options, $automation );

}

/**
 * Registers the trigger completion
 *
 * @since 1.3.0
 *
 * @param stdClass  $trigger            The trigger object
 * @param array     $event              Event information
 * @param array     $trigger_options    The trigger's stored options
 * @param stdClass  $automation         The trigger's automation object
 *
 * @return bool
 */
function automatorwp_anonymous_completed_trigger( $trigger = null, $event = array(), $trigger_options = array(), $automation = null ) {

    global $automatorwp_last_anonymous_trigger_log_id;

    // Initialize last anonymous trigger log ID
    $automatorwp_last_anonymous_trigger_log_id = 0;

    // Check if trigger is correct
    if( ! is_object( $trigger ) ) {
        return false;
    }

    // Check if automation is correct
    if( $automation === null ) {
        $automation = automatorwp_get_trigger_automation( $trigger->id );

        if( ! is_object( $automation ) ) {
            return false;
        }
    }

    // Get the trigger completion times
    $log_meta = array(
        'times' => 1
    );

    if( isset( $event['comment_id'] ) ) {
        $log_meta['comment_id'] = $event['comment_id'];
    }

    /**
     * Filter to add custom log meta to meet that user has completed this trigger
     *
     * @since 1.3.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    $log_meta = apply_filters( 'automatorwp_anonymous_completed_trigger_log_meta', $log_meta, $trigger, $event, $trigger_options, $automation );

    /**
     * Backward compatibility filter!
     *
     * Filter to add custom log meta to meet that user has completed this trigger
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID (here will be 0, since user hasn't been created yet)
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    $log_meta = apply_filters( 'automatorwp_user_completed_trigger_log_meta', $log_meta, $trigger, 0, $event, $trigger_options, $automation );

    // Insert a new log entry to register the trigger completion
    $automatorwp_last_anonymous_trigger_log_id = automatorwp_insert_log( array(
        'title'     => automatorwp_parse_automation_item_log_label( $trigger, 'trigger', 'view' ),
        'type'      => 'trigger',
        'object_id' => $trigger->id,
        'user_id'   => 0,
        'post_id'   => ( isset( $event['post_id'] ) ? $event['post_id'] : 0 ),
        'date'      => automatorwp_get_event_log_date(),
    ), $log_meta );

    if( is_wp_error( $automatorwp_last_anonymous_trigger_log_id ) ) {
        $automatorwp_last_anonymous_trigger_log_id = 0;
    }

    /**
     * Available action to hook on a trigger completion
     *
     * @since 1.3.0
     *
     * @param stdClass  $trigger            The trigger object
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     */
    do_action( 'automatorwp_anonymous_completed_trigger', $trigger, $event, $trigger_options, $automation );

    return true;

}

/**
 * Get the anonymous automation user ID
 *
 * @since 1.3.0
 *
 * @param stdClass  $automation         The automation object
 * @param array     $event              Event information
 *
 * @return int|false                    The user ID, false otherwise
 */
function automatorwp_get_anonymous_automation_user_id( $automation = null, $event = array() ) {

    if( ! is_object( $automation ) ) {
        return false;
    }

    $actions = automatorwp_get_automation_actions( $automation->id );

    // Check if isset the first action
    if( ! isset( $actions[0] ) ) {
        return false;
    }

    // Check if anonymous user action exists
    if( isset( $actions[0] ) && $actions[0]->type !== 'automatorwp_anonymous_user' ) {
        return false;
    }

    $action = $actions[0];

    // Get all action options to parse all replacements
    $action_options = automatorwp_get_action_stored_options( $action->id );

    foreach( $action_options as $option => $value ) {
        // Replace all tags by their replacements
        $action_options[$option] = automatorwp_parse_automation_tags( $automation->id, 0, $value );
    }

    // Execute the anonymous user action to decide the user to assign the logs
    $user_id = false;

    /**
     * Filter to decide to which user ID will be assigned to the automation
     *
     * @since 1.0.0
     *
     * @param int       $user_id            The user ID
     * @param stdClass  $action             The action object
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     *
     * @return int|false                    The user ID, false otherwise
     */
    return apply_filters( 'automatorwp_get_anonymous_automation_user_id', $user_id, $action, $action_options, $automation );

}

/**
 * Update the trigger log user ID for anonymous events
 *
 * @since 1.3.0
 *
 * @param int  $user_id            The user ID
 */
function automatorwp_update_anonymous_completed_trigger_log_user_id( $user_id ) {

    global $automatorwp_last_anonymous_trigger_log_id;

    // Bail if not ID found
    if( absint( $automatorwp_last_anonymous_trigger_log_id ) === 0 ) {
        return;
    }

    ct_setup_table( 'automatorwp_logs' );

    // Update the log user ID
    ct_update_object( array(
        'id' => $automatorwp_last_anonymous_trigger_log_id,
        'user_id' => $user_id,
    ) );

    ct_reset_setup_table();

}

// -------------------------------------------
// User event
// -------------------------------------------

/**
 * Check if user has completed a trigger
 *
 * @since 1.0.0
 *
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param array     $event          Event information
 *
 * @return bool
 */
function automatorwp_maybe_user_completed_trigger( $trigger, $user_id = 0, $event = array() ) {

    // Check if trigger is correct
    if( ! is_object( $trigger ) ) {
        return false;
    }

    // Check the user ID
    if( $user_id === 0 ) {
        return false;
    }

    // Check if automation is correct
    $automation = automatorwp_get_trigger_automation( $trigger->id );

    if( ! is_object( $automation ) ) {
        return false;
    }

    // Get the trigger stored options
    $trigger_options = automatorwp_get_trigger_stored_options( $trigger->id );

    // Check if user has access to the trigger
    if( ! automatorwp_user_has_access_to_trigger( $trigger, $user_id, $event, $trigger_options, $automation ) ) {
        return false;
    }

    // Check if user deserves the trigger
    if( ! automatorwp_user_deserves_trigger( $trigger, $user_id, $event, $trigger_options, $automation ) ) {
        return false;
    }

    // Check if user deserves the trigger filters
    if( ! automatorwp_user_deserves_trigger_filters( $trigger, $user_id, $event, $trigger_options, $automation ) ) {
        return false;
    }

    // Mark trigger as completed
    return automatorwp_user_completed_trigger( $trigger, $user_id, $event, $trigger_options, $automation );

}

/**
 * Check if user has access to trigger
 *
 * @since 1.0.0
 *
 * @param stdClass  $trigger            The trigger object
 * @param int       $user_id            The user ID
 * @param array     $event              Event information
 * @param array     $trigger_options    The trigger's stored options
 * @param stdClass  $automation         The trigger's automation object
 *
 * @return bool                         True if user has access, false otherwise
 */
function automatorwp_user_has_access_to_trigger( $trigger = null, $user_id = 0, $event = array(), $trigger_options = array(), $automation = null ) {

    // Check if trigger is correct
    if( ! is_object( $trigger ) ) {
        return false;
    }

    // Check the user ID
    if( $user_id === 0 ) {
        return false;
    }

    // Check if automation is correct
    if( $automation === null ) {
        $automation = automatorwp_get_trigger_automation( $trigger->id );

        if( ! is_object( $automation ) ) {
            return false;
        }
    }

    $has_access = true;

    // Bail if automation is not active
    if( $automation->status !== 'active' ) {
        $has_access = false;
    }

    $now = current_time( 'timestamp' );

    // Bail if is a future automation
    if( $has_access && strtotime( $automation->date ) > $now ) {
        $has_access = false;
    }

    // Check if user exceeded completion times
    $times_per_user = absint( $automation->times_per_user );

    if( $has_access && $times_per_user > 0 ) {
        $user_completion_times = automatorwp_get_user_completion_times( $automation->id, $user_id, 'automation' );

        if( $user_completion_times >= $times_per_user ) {
            $has_access = false;
        }
    }

    // Check if exceeded automation completion times
    $times = absint( $automation->times );

    if( $has_access && $times > 0 ) {
        $completion_times = automatorwp_get_object_completion_times( $automation->id, 'automation' );

        if( $completion_times >= $times ) {
            $has_access = false;
        }
    }

    // Check if automation is sequential
    if( $has_access && $automation->sequential ) {

        $automation_triggers = automatorwp_get_automation_triggers( $automation->id );

        // Loop all automation triggers to check if all previous triggers has been completed
        foreach( $automation_triggers as $automation_trigger ) {

            // If both triggers has the same position, then we have reached our current trigger
            if( $automation_trigger->position === $trigger->position ) {
                break;
            }

            // If user has not completed a previous trigger, set access to false
            if( ! automatorwp_has_user_completed_trigger( $automation_trigger->id, $user_id ) ) {
                $has_access = false;
                break;
            }

        }

    }

    /**
     * Filter to override the has access check.
     * This filter is to check the automation configuration.
     * Triggers should use the 'automatorwp_user_deserves_trigger' filter instead.
     *
     * @since 1.0.0
     *
     * @param bool      $has_access         True if user has access, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                         True if user has access, false otherwise
     */
    return apply_filters( 'automatorwp_user_has_access_to_trigger', $has_access, $trigger, $user_id, $event, $trigger_options, $automation );

}

/**
 * Check if user deserves trigger
 *
 * @since 1.0.0
 *
 * @param stdClass  $trigger            The trigger object
 * @param int       $user_id            The user ID
 * @param array     $event              Event information
 * @param array     $trigger_options    The trigger's stored options
 * @param stdClass  $automation         The trigger's automation object
 *
 * @return bool                         True if user deserves trigger, false otherwise
 */
function automatorwp_user_deserves_trigger( $trigger = null, $user_id = 0, $event = array(), $trigger_options = array(), $automation = null ) {

    // Check if trigger is correct
    if( ! is_object( $trigger ) ) {
        return false;
    }

    // Check the user ID
    if( $user_id === 0 ) {
        return false;
    }

    // Check if automation is correct
    if( $automation === null ) {
        $automation = automatorwp_get_trigger_automation( $trigger->id );

        if( ! is_object( $automation ) ) {
            return false;
        }
    }

    if( automatorwp_has_user_completed_trigger( $trigger->id, $user_id ) ) {
        return false;
    }

    $deserves_trigger = true;

    /**
     * Filter to override the user deserves trigger check.
     * This filter is to check the trigger configuration.
     * Triggers should use this filter.
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if user deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    return apply_filters( 'automatorwp_user_deserves_trigger', $deserves_trigger, $trigger, $user_id, $event, $trigger_options, $automation );

}

/**
 * Check if user deserves trigger filters
 *
 * @since 1.0.0
 *
 * @param stdClass  $trigger            The trigger object
 * @param int       $user_id            The user ID
 * @param array     $event              Event information
 * @param array     $trigger_options    The trigger's stored options
 * @param stdClass  $automation         The trigger's automation object
 *
 * @return bool                         True if user deserves trigger, false otherwise
 */
function automatorwp_user_deserves_trigger_filters( $trigger = null, $user_id = 0, $event = array(), $trigger_options = array(), $automation = null ) {

    global $automatorwp_completed_trigger_filters;

    // Check if trigger is correct
    if( ! is_object( $trigger ) ) {
        return false;
    }

    // Check the user ID
    if( $user_id === 0 ) {
        return false;
    }

    // Check if automation is correct
    if( $automation === null ) {
        $automation = automatorwp_get_trigger_automation( $trigger->id );

        if( ! is_object( $automation ) ) {
            return false;
        }
    }

    if( automatorwp_has_user_completed_trigger( $trigger->id, $user_id ) ) {
        return false;
    }

    $deserves_trigger_filters = true;

    // Bail if is a filter
    if( $trigger->type === 'filter' ) {
        return $deserves_trigger_filters;
    }

    // Initialize completed filters
    $automatorwp_completed_trigger_filters = array();

    $filters = automatorwp_get_trigger_filters( $trigger );

    foreach( $filters as $i => $filter ) {

        $deserves_filter = true;

        // Get the trigger stored options
        $filter_options = automatorwp_get_filter_stored_options( $filter->id, 'trigger' );

        /**
         * Filter to override the user deserves filter check.
         * This filter is to check the filter configuration.
         * Filters should use this filter.
         *
         * @since 1.0.0
         *
         * @param bool      $deserves_filter    True if user deserves filter, false otherwise
         * @param stdClass  $filter             The filter object
         * @param int       $user_id            The user ID
         * @param array     $event              Event information
         * @param array     $filter_options     The filter's stored options
         * @param stdClass  $automation         The filter's automation object
         *
         * @return bool                         True if user deserves filter, false otherwise
         */
        $deserves_filter = apply_filters( 'automatorwp_user_deserves_trigger_filter', $deserves_filter, $filter, $user_id, $event, $filter_options, $automation );

        if( $i === 0 ) {
            // On first filter there is no need to apply the operator
            $deserves_trigger_filters = $deserves_filter;
        } else {
            // Apply the operator
            if( $filter_options['operator'] === 'and' ) {
                $deserves_trigger_filters = $deserves_trigger_filters && $deserves_filter;
            } else {
                $deserves_trigger_filters = $deserves_trigger_filters || $deserves_filter;
            }
        }

        // Store completed filters
        if( $deserves_filter ) {
            $automatorwp_completed_trigger_filters[] = $filter;
        }

        // Break this loop if the trigger filters are not passed
        if( ! $deserves_trigger_filters ) {

            // For OR operators, prevent to bail until check all of them
            if( $i < count( $filters ) && $filter_options['operator'] === 'or' ) {
                continue;
            }

            // Register why user has not completed this filter
            $log_meta = array(
                'item_type' => 'trigger'
            );

            /**
             * Filter to add custom log meta to meet that user has completed this trigger
             *
             * @since 1.0.0
             *
             * @param array     $log_meta           Log meta data
             * @param stdClass  $filter             The filter object
             * @param int       $user_id            The user ID
             * @param array     $event              Event information
             * @param array     $filter_options     The filter's stored options
             * @param stdClass  $automation         The filter's automation object
             *
             * @return array
             */
            $log_meta = apply_filters( 'automatorwp_user_not_passed_filter_log_meta', $log_meta, $filter, $user_id, $event, $filter_options, $automation );

            // Insert a new log entry to register why user not passed the trigger filter
            automatorwp_insert_log( array(
                'title'     => automatorwp_parse_automation_item_log_label( $filter, 'trigger', 'view' ),
                'type'      => 'filter',
                'object_id' => $filter->id,
                'user_id'   => $user_id,
                'post_id'   => ( isset( $event['post_id'] ) ? $event['post_id'] : 0 ),
                'date'      => automatorwp_get_event_log_date(),
            ), $log_meta );

            // Force to clear the cache for this filter
            automatorwp_clear_user_last_completion_cache( $filter, $user_id, 'filter' );

            break;
        }

    }

    /**
     * Filter to override the user deserves trigger filters check.
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger_filters   True if user deserves the trigger filters, false otherwise
     * @param stdClass  $trigger                    The trigger object
     * @param int       $user_id                    The user ID
     * @param array     $event                      Event information
     * @param array     $trigger_options            The trigger's stored options
     * @param stdClass  $automation                 The trigger's automation object
     *
     * @return bool                                 True if user deserves trigger, false otherwise
     */
    return apply_filters( 'automatorwp_user_deserves_trigger_filters', $deserves_trigger_filters, $trigger, $user_id, $event, $trigger_options, $automation );

}

/**
 * Registers the trigger completion
 *
 * @since 1.0.0
 *
 * @param stdClass  $trigger            The trigger object
 * @param int       $user_id            The user ID
 * @param array     $event              Event information
 * @param array     $trigger_options    The trigger's stored options
 * @param stdClass  $automation         The trigger's automation object
 *
 * @return bool
 */
function automatorwp_user_completed_trigger( $trigger = null, $user_id = 0, $event = array(), $trigger_options = array(), $automation = null ) {

    global $automatorwp_completed_trigger_filters;

    if( ! is_array( $automatorwp_completed_trigger_filters ) ) {
        $automatorwp_completed_trigger_filters = array();
    }

    // Check if trigger is correct
    if( ! is_object( $trigger ) ) {
        return false;
    }

    // Check the user ID
    if( $user_id === 0 ) {
        return false;
    }

    // Check if automation is correct
    if( $automation === null ) {
        $automation = automatorwp_get_trigger_automation( $trigger->id );

        if( ! is_object( $automation ) ) {
            return false;
        }
    }

    // Get the trigger completion times
    $completion_times = automatorwp_get_user_trigger_completion_times( $trigger->id, $user_id );

    $log_meta = array(
        'times' => ( $completion_times + 1 )
    );

    if( isset( $event['comment_id'] ) ) {
        $log_meta['comment_id'] = $event['comment_id'];
    }

    /**
     * Filter to add custom log meta to meet that user has completed this trigger
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    $log_meta = apply_filters( 'automatorwp_user_completed_trigger_log_meta', $log_meta, $trigger, $user_id, $event, $trigger_options, $automation );

    // Insert a new log entry to register the trigger completion
    automatorwp_insert_log( array(
        'title'     => automatorwp_parse_automation_item_log_label( $trigger, 'trigger', 'view' ),
        'type'      => 'trigger',
        'object_id' => $trigger->id,
        'user_id'   => $user_id,
        'post_id'   => ( isset( $event['post_id'] ) ? $event['post_id'] : 0 ),
        'date'      => automatorwp_get_event_log_date(),
    ), $log_meta );

    /**
     * Available action to hook on a trigger completion
     *
     * @since 1.0.0
     *
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     */
    do_action( 'automatorwp_user_completed_trigger', $trigger, $user_id, $event, $trigger_options, $automation );

    if( $trigger->type !== 'filter' ) {

        // Mark all completed filters
        foreach( $automatorwp_completed_trigger_filters as $filter ) {

            // Get the trigger stored options
            $filter_options = automatorwp_get_filter_stored_options( $filter->id, 'trigger' );

            // Registers the passed filter (as a trigger entry)
            automatorwp_user_completed_trigger( $filter, $user_id, $event, $filter_options, $automation );

        }

        // Check if user has completed the automation
        automatorwp_maybe_user_completed_automation( $automation, $user_id, $event );

    }

    return true;

}

/**
 * Registers the automation completion
 *
 * @since 1.0.0
 *
 * @param stdClass  $automation         The automation object
 * @param int       $user_id            The user ID
 * @param array     $event              Event information
 *
 * @return bool
 */
function automatorwp_maybe_user_completed_automation( $automation = null, $user_id = 0, $event = array() ) {

    // Check if automation is correct
    if( ! is_object( $automation ) ) {
        return false;
    }

    // Check the user ID
    if( $user_id === 0 ) {
        return false;
    }

    $triggers = automatorwp_get_automation_triggers( $automation->id );

    $all_completed = true;

    // Check if user has completed all automation trigger
    foreach( $triggers as $trigger ) {

        if( $trigger->type === 'filter' ) {
            continue;
        }

        // If user has not completed this trigger the number of times required then break to finish this function
        if( ! automatorwp_has_user_completed_trigger( $trigger->id, $user_id ) ) {
            $all_completed = false;
            break;
        }

    }

    // Bail if user has not completed all automation triggers
    if( ! $all_completed ) {
        return false;
    }

    // Mark trigger as completed
    return automatorwp_user_completed_automation( $automation, $user_id, $event );

}

/**
 * Registers the automation completion
 *
 * @since 1.0.0
 *
 * @param stdClass  $automation         The automation object
 * @param int       $user_id            The user ID
 * @param array     $event              Event information
 *
 * @return bool
 */
function automatorwp_user_completed_automation( $automation = null, $user_id = 0, $event = array() ) {

    // Check if automation is correct
    if( ! is_object( $automation ) ) {
        return false;
    }

    // Check the user ID
    if( $user_id === 0 ) {
        return false;
    }

    // Execute all automation actions
    automatorwp_execute_all_automation_actions( $automation, $user_id, $event );

    /**
     * Available filter to determine if user has completed an automation
     *
     * @since 1.2.4
     *
     * @param bool      $complete           Determines if the automation should be marked as completed, by default true
     * @param stdClass  $automation         The automation object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     *
     * @return bool
     */
    $complete = apply_filters( 'automatorwp_can_user_complete_automation', true, $automation, $user_id, $event );

    if( ! $complete ) {
        return false;
    }

    // Insert a new log entry to register the automation completion
    automatorwp_insert_log( array(
        'title'     => $automation->title,
        'type'      => 'automation',
        'object_id' => $automation->id,
        'user_id'   => $user_id,
        'post_id'   => ( isset( $event['post_id'] ) ? $event['post_id'] : 0 ),
        'date'      => automatorwp_get_event_log_date(),
    ) );

    ct_setup_table( 'automatorwp_automations' );

    // Increment the completions
    $completions = absint( ct_get_object_meta( $automation->id, 'completions', true ) );
    ct_update_object_meta( $automation->id, 'completions', $completions + 1 );

    ct_reset_setup_table();

    /**
     * Available action to hook on an automation completion
     *
     * @since 1.0.0
     *
     * @param stdClass  $automation         The automation object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     */
    do_action( 'automatorwp_user_completed_automation', $automation, $user_id, $event );

    return true;

}

/**
 * Execute all automation actions
 *
 * @since 1.0.0
 *
 * @param stdClass  $automation         The automation object
 * @param int       $user_id            The user ID
 * @param array     $event              Event information
 *
 * @return bool
 */
function automatorwp_execute_all_automation_actions( $automation = null, $user_id = 0, $event = array() ) {

    // Check if automation is correct
    if( ! is_object( $automation ) ) {
        return false;
    }

    // Check the user ID
    if( $user_id === 0 ) {
        return false;
    }

    // Get all automation action to execute them
    $actions = automatorwp_get_automation_actions( $automation->id );

    foreach( $actions as $action ) {
        automatorwp_execute_action( $action, $user_id, $event );
    }

    return true;

}

/**
 * Execute an action
 *
 * @since 1.0.0
 *
 * @param stdClass  $action             The action object
 * @param int       $user_id            The user ID
 * @param array     $event              Event information
 *
 * @return bool
 */
function automatorwp_execute_action( $action = null, $user_id = 0, $event = array() ) {

    // Check if action is correct
    if( ! is_object( $action ) ) {
        return false;
    }

    // Check the user ID
    if( $user_id === 0 ) {
        return false;
    }

    // Setup the automation assigned to this action
    $automation = automatorwp_get_automation_object( $action->automation_id );

    // Check if automation is correct
    if( ! is_object( $automation ) ) {
        return false;
    }

    // Get all action options to parse all replacements
    $action_options = automatorwp_get_action_stored_options( $action->id );

    foreach( $action_options as $option => $value ) {
        // Replace all tags by their replacements
        $action_options[$option] = automatorwp_parse_automation_tags( $automation->id, $user_id, $value );
    }

    // Bail if action can not be executed
    if( ! automatorwp_can_execute_action( $action, $user_id, $event, $action_options, $automation ) ) {
        return false;
    }

    // Check if user deserves the action filters
    if( ! automatorwp_user_deserves_action_filters( $action, $user_id, $event, $action_options, $automation ) ) {
        return false;
    }

    /**
     * Available action to hook for execute an action function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    do_action( 'automatorwp_execute_action', $action, $user_id, $event, $action_options, $automation );

    $log_meta = array();

    /**
     * Filter to add custom log meta to meet that and action has been executed to a specific user
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     *
     * @return array
     */
    $log_meta = apply_filters( 'automatorwp_user_completed_action_log_meta', $log_meta, $action, $user_id, $action_options, $automation );

    /**
     * Filter to assign a custom post ID to this action
     *
     * @since 1.0.0
     *
     * @param int       $post_id            The post ID, by default 0
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     *
     * @return int
     */
    $post_id = apply_filters( 'automatorwp_user_completed_action_post_id', 0, $action, $user_id, $event, $action_options, $automation );

    // Parse the log label (including the automation tags)
    $log_title = automatorwp_parse_automation_item_log_label( $action, 'action', 'view' );
    $log_title = automatorwp_parse_automation_tags( $automation->id, $user_id, $log_title );

    // Insert a new log entry to register the trigger completion
    automatorwp_insert_log( array(
        'title'     => $log_title,
        'type'      => 'action',
        'object_id' => $action->id,
        'user_id'   => $user_id,
        'post_id'   => $post_id,
        'date'      => automatorwp_get_event_log_date(),
    ), $log_meta );

    /**
     * Available action to hook on an action completion
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    do_action( 'automatorwp_user_completed_action', $action, $user_id, $event, $action_options, $automation );

    return true;

}

/**
 * Checks if an action should be executed or not
 *
 * @since 1.2.4
 *
 * @param stdClass  $action             The action object
 * @param int       $user_id            The user ID
 * @param array     $event              Event information
 * @param array     $action_options     The action's stored options (with tags already passed)
 * @param stdClass  $automation         The action's automation object
 *
 * @return bool
 */
function automatorwp_can_execute_action( $action = null, $user_id = 0, $event = array(), $action_options = array(), $automation = null ) {

    // Check if action is correct
    if( ! is_object( $action ) ) {
        return false;
    }

    // Check the user ID
    if( $user_id === 0 ) {
        return false;
    }

    // Check if automation is correct
    if( $automation === null ) {
        $automation = automatorwp_get_action_automation( $action->id );

        if( ! is_object( $automation ) ) {
            return false;
        }
    }

    $execute = true;

    /**
     * Available filter to determine if an action should be executed or not
     *
     * @since 1.2.4
     *
     * @param bool      $execute            Determines if the action should be executed, by default true
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     *
     * @return bool
     */
    return apply_filters( 'automatorwp_can_execute_action', $execute, $action, $user_id, $event, $action_options, $automation );

}

/**
 * Check if user deserves action filters
 *
 * @since 1.0.0
 *
 * @param stdClass  $action             The action object
 * @param int       $user_id            The user ID
 * @param array     $event              Event information
 * @param array     $action_options     The action's stored options
 * @param stdClass  $automation         The action's automation object
 *
 * @return bool                         True if user deserves action, false otherwise
 */
function automatorwp_user_deserves_action_filters( $action = null, $user_id = 0, $event = array(), $action_options = array(), $automation = null ) {

    global $automatorwp_completed_action_filters, $automatorwp_last_action_passed_all_filters;

    // Check if action is correct
    if( ! is_object( $action ) ) {
        return false;
    }

    // Check the user ID
    if( $user_id === 0 ) {
        return false;
    }

    // Check if automation is correct
    if( $automation === null ) {
        $automation = automatorwp_get_action_automation( $action->id );

        if( ! is_object( $automation ) ) {
            return false;
        }
    }

    $deserves_action_filters = true;

    // Bail if is a filter
    if( $action->type === 'filter' ) {

        if( ! is_array( $automatorwp_completed_action_filters ) ) {
            return false;
        }

        if( ! $automatorwp_last_action_passed_all_filters ) {
            return false;
        }

        $found = false;

        foreach ( $automatorwp_completed_action_filters as $filter ) {
            if( absint( $filter->id ) === absint( $action->id ) ) {
                $found = true;
            }
        }

        // If filter has not been passed, prevent its execution
        if( ! $found ) {
            return false;
        }

        return $deserves_action_filters;
    }

    // Initialize completed actions
    $automatorwp_completed_action_filters = array();
    $automatorwp_last_action_passed_all_filters = false;

    $filters = automatorwp_get_action_filters( $action );

    foreach( $filters as $i => $filter ) {

        $deserves_filter = true;

        // Get the action stored options
        $filter_options = automatorwp_get_filter_stored_options( $filter->id, 'action' );

        foreach( $filter_options as $option => $value ) {
            // Replace all tags by their replacements
            $filter_options[$option] = automatorwp_parse_automation_tags( $automation->id, $user_id, $value );
        }

        /**
         * Filter to override the user deserves filter check.
         * This filter is to check the filter configuration.
         * Filters should use this filter.
         *
         * @since 1.0.0
         *
         * @param bool      $deserves_filter    True if user deserves filter, false otherwise
         * @param stdClass  $filter             The filter object
         * @param int       $user_id            The user ID
         * @param array     $event              Event information
         * @param array     $filter_options     The filter's stored options
         * @param stdClass  $automation         The filter's automation object
         *
         * @return bool                         True if user deserves filter, false otherwise
         */
        $deserves_filter = apply_filters( 'automatorwp_user_deserves_action_filter', $deserves_filter, $filter, $user_id, $event, $filter_options, $automation );

        if( $i === 0 ) {
            // On first filter there is no need to apply the operator
            $deserves_action_filters = $deserves_filter;
        } else {
            // Apply the operator
            if( $filter_options['operator'] === 'and' ) {
                $deserves_action_filters = $deserves_action_filters && $deserves_filter;
            } else {
                $deserves_action_filters = $deserves_action_filters || $deserves_filter;
            }
        }

        // Store completed filters
        if( $deserves_filter ) {
            $automatorwp_completed_action_filters[] = $filter;
        }

        // Break this loop if the action filters are not passed
        if( ! $deserves_action_filters ) {

            // For OR operators, prevent to bail until check all of them
            if( $i < count( $filters ) && $filter_options['operator'] === 'or' ) {
                continue;
            }

            // Register why user has not completed this filter
            $log_meta = array(
                'item_type' => 'action'
            );

            /**
             * Filter to add custom log meta to meet that user has completed this action
             *
             * @since 1.0.0
             *
             * @param array     $log_meta           Log meta data
             * @param stdClass  $filter             The filter object
             * @param int       $user_id            The user ID
             * @param array     $event              Event information
             * @param array     $filter_options     The filter's stored options
             * @param stdClass  $automation         The filter's automation object
             *
             * @return array
             */
            $log_meta = apply_filters( 'automatorwp_user_not_passed_filter_log_meta', $log_meta, $filter, $user_id, $event, $filter_options, $automation );

            // Insert a new log entry to register why user not passed the action filter
            automatorwp_insert_log( array(
                'title'     => automatorwp_parse_automation_item_log_label( $filter, 'action', 'view' ),
                'type'      => 'filter',
                'object_id' => $filter->id,
                'user_id'   => $user_id,
                'post_id'   => ( isset( $event['post_id'] ) ? $event['post_id'] : 0 ),
                'date'      => automatorwp_get_event_log_date(),
            ), $log_meta );

            // Force to clear the cache for this filter
            automatorwp_clear_user_last_completion_cache( $filter, $user_id, 'filter' );

            break;
        }

    }

    /**
     * Filter to override the user deserves action filters check.
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_action_filters    True if user deserves the action filters, false otherwise
     * @param stdClass  $action                     The action object
     * @param int       $user_id                    The user ID
     * @param array     $event                      Event information
     * @param array     $action_options             The action's stored options
     * @param stdClass  $automation                 The action's automation object
     *
     * @return bool                                 True if user deserves action, false otherwise
     */
    $deserves_action_filters = apply_filters( 'automatorwp_user_deserves_action_filters', $deserves_action_filters, $action, $user_id, $event, $action_options, $automation );

    // Used to meet if should register the rest of filter actions
    $automatorwp_last_action_passed_all_filters = $deserves_action_filters;

    return $deserves_action_filters;
}

/**
 * Helper function to get the current log date on the events engine
 *
 * @since 1.0.0
 *
 * @return string
 */
function automatorwp_get_event_log_date() {

    global $automatorwp_completed_triggers;

    // The global $automatorwp_completed_triggers is used to increase log time by the number of loops perform
    // This prevents unlimited completions when multiples triggers has been triggered
    if( ! is_array( $automatorwp_completed_triggers ) ) {
        $automatorwp_completed_triggers = array();
    }

    return date( 'Y-m-d H:i:s', current_time( 'timestamp' ) + count( $automatorwp_completed_triggers ) );

}