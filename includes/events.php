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
 * Autoload triggers in use hooks
 *
 * @since 1.0.0
 */
function automatorwp_autoload_triggers_hooks() {

    $triggers = automatorwp_get_triggers();
    $triggers_in_user = automatorwp_get_triggers_in_use();

    foreach( $triggers_in_user as $trigger_name ) {

        // Skip unregistered triggers
        if( ! isset( $triggers[$trigger_name] ) ) {
            continue;
        }

        $trigger = $triggers[$trigger_name];

        // Skip if not action or filter is provided
        if( empty( $trigger['action'] ) && empty( $trigger['filter'] ) ) {
            continue;
        }

        // Skip if not callback function is provided
        if( empty( $trigger['function'] ) ) {
            continue;
        }

        // Register the trigger hook (action or filter)
        if( ! empty( $trigger['action'] ) ) {

            // Ensure that is an array
            if( ! is_array( $trigger['action'] ) ) {
                $trigger['action'] = array( $trigger['action'] );
            }

            // Add all the actions
            foreach( $trigger['action'] as $action ) {
                add_action( $action, $trigger['function'], $trigger['priority'], $trigger['accepted_args'] );
            }

        } else if( ! empty( $trigger['filter'] ) ) {

            // Ensure that is an array
            if( ! is_array( $trigger['filter'] ) ) {
                $trigger['filter'] = array( $trigger['filter'] );
            }

            // Add all triggers
            foreach( $trigger['filter'] as $filter ) {
                add_filter( $filter, $trigger['function'], $trigger['priority'], $trigger['accepted_args'] );
            }

        }

    }

}
add_action( 'automatorwp_post_init', 'automatorwp_autoload_triggers_hooks' );

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

    // Args has been checked on automatorwp_is_event_correct() function
    $user_id = $event['user_id'];

    // Get triggered triggers
    $triggers = automatorwp_get_event_triggered_triggers( $event['trigger'] );

    $automatorwp_completed_triggers = array();

    foreach( $triggers as $trigger ) {
        if( automatorwp_maybe_user_completed_trigger( $trigger, $user_id, $event ) ) {
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

    // Check user
    if( ! isset( $event['user_id'] ) ) {
        return false;
    }

    $event['user_id'] = absint( $event['user_id'] );

    if( $event['user_id'] === 0 ) {
        return false;
    }

    // Check trigger
    if( ! isset( $event['trigger'] ) ) {
        return false;
    }

    if( ! isset( AutomatorWP()->triggers[$event['trigger']] ) ) {
        return false;
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
     * Triggers should us the 'automatorwp_user_deserves_trigger' filter instead.
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
     * Triggers should us this filter.
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

    global $automatorwp_completed_triggers;

    // The global $automatorwp_completed_triggers is used to increase log time by the number of loops perform
    // This prevents unlimited completions when multiples triggers has been triggered
    if( ! is_array( $automatorwp_completed_triggers ) ) {
        $automatorwp_completed_triggers = array();
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
        'date'      => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) + count( $automatorwp_completed_triggers ) ),
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

    automatorwp_maybe_user_completed_automation( $automation, $user_id, $event );

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

    global $automatorwp_completed_triggers;

    // The global $automatorwp_completed_triggers is used to increase log time by the number of loops perform
    // This prevents unlimited completions when multiples triggers has been triggered
    if( ! is_array( $automatorwp_completed_triggers ) ) {
        $automatorwp_completed_triggers = array();
    }

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

    // Insert a new log entry to register the automation completion
    automatorwp_insert_log( array(
        'title'     => $automation->title,
        'type'      => 'automation',
        'object_id' => $automation->id,
        'user_id'   => $user_id,
        'post_id'   => ( isset( $event['post_id'] ) ? $event['post_id'] : 0 ),
        'date'      => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) + count( $automatorwp_completed_triggers ) ),
    ) );

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

    global $automatorwp_completed_triggers;

    // The global $automatorwp_completed_triggers is used to increase log time by the number of loops perform
    // This prevents unlimited completions when multiples triggers has been triggered
    if( ! is_array( $automatorwp_completed_triggers ) ) {
        $automatorwp_completed_triggers = array();
    }

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

        // Get all action options to parse all replacements
        $action_options = automatorwp_get_action_stored_options( $action->id );

        foreach( $action_options as $option => $value ) {

            // Replace all tags by their replacements
            $action_options[$option] = automatorwp_parse_automation_tags( $automation->id, $user_id, $value );

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

        // Insert a new log entry to register the trigger completion
        automatorwp_insert_log( array(
            'title'     => automatorwp_parse_automation_item_log_label( $action, 'action', 'view' ),
            'type'      => 'action',
            'object_id' => $action->id,
            'user_id'   => $user_id,
            'post_id'   => $post_id,
            'date'      => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) + count( $automatorwp_completed_triggers ) ),
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

    }

    return true;

}