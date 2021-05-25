<?php
/**
 * Tags
 *
 * @package     AutomatorWP\Integrations\Modern_Events_Calendar\Tags
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Order tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_modern_events_calendar_event_tags() {

    $event_tags = array(
        'modern_events_calendar_event_start_date' => array(
            'label'     => __( 'Event Start Date', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => '1991-01-01',
        ),
        'modern_events_calendar_event_start_date_time' => array(
            'label'     => __( 'Event Start Time', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => '8:00 AM',
        ),
        'modern_events_calendar_event_end_date' => array(
            'label'     => __( 'Event End Date', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => '1991-01-01',
        ),
        'modern_events_calendar_event_end_date_time' => array(
            'label'     => __( 'Event End Time', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => '8:00 AM',
        ),
        'modern_events_calendar_event_location' => array(
            'label'     => __( 'Event Location', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => __( 'City Hall', 'automatorwp' ),
        ),
        'modern_events_calendar_event_organizer' => array(
            'label'     => __( 'Event Organizer', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'John Smith',
        ),
        'modern_events_calendar_event_cost' => array(
            'label'     => __( 'Event Cost', 'automatorwp' ),
            'type'      => 'decimal',
            'preview'   => '10.00',
        ),
    );

    /**
     * Filter event tags
     *
     * @since 1.0.0
     *
     * @param array $tags
     *
     * @return array
     */
    return apply_filters( 'automatorwp_modern_events_calendar_event_tags', $event_tags );

}

/**
 * Custom trigger tag replacement
 *
 * @since 1.0.0
 *
 * @param string    $replacement    The tag replacement
 * @param string    $tag_name       The tag name (without "{}")
 * @param stdClass  $trigger        The trigger object
 * @param int       $user_id        The user ID
 * @param string    $content        The content to parse
 * @param stdClass  $log            The last trigger log object
 *
 * @return string
 */
function automatorwp_modern_events_calendar_get_trigger_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {

    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Bail if no event ID attached
    if( ! $trigger_args ) {
        return $replacement;
    }

    // Bail if trigger is not from this integration
    if( $trigger_args['integration'] !== 'modern_events_calendar' ) {
        return $replacement;
    }

    $tags = array_keys( automatorwp_modern_events_calendar_event_tags() );

    // Bail if not event tags found
    if( ! in_array( $tag_name, $tags ) ) {
        return $replacement;
    }

    $event_id = (int) automatorwp_get_log_meta( $log->id, 'post_id', true );

    // Bail if no event ID attached
    if( $event_id === 0 ) {
        return $replacement;
    }

    // Format values for some tags
    switch( $tag_name ) {
        case 'modern_events_calendar_event_start_date':
            $date = get_post_meta( $event_id, 'mec_date', true );
            $replacement = $date['start']['date'];
            break;
        case 'modern_events_calendar_event_start_date_time':
            $date = get_post_meta( $event_id, 'mec_date', true );
            $replacement = sprintf(
                '%02d:%02d %s',
                $date['start']['hour'],
                $date['start']['minutes'],
                $date['start']['ampm']
            );
            break;
        case 'modern_events_calendar_event_end_date':
            $date = get_post_meta( $event_id, 'mec_date', true );
            $replacement = $date['end']['date'];
            break;
        case 'modern_events_calendar_event_end_date_time':
            $date = get_post_meta( $event_id, 'mec_date', true );
            $replacement = sprintf(
                '%02d:%02d %s',
                $date['end']['hour'],
                $date['end']['minutes'],
                $date['end']['ampm']
            );
            break;
        case 'modern_events_calendar_event_location':
            $location_id = get_post_meta( $event_id, 'mec_location_id', true );
            $location = get_term( $location_id, 'mec_location' );
            $replacement = ( $location ? $location->name : '' );
            break;
        case 'modern_events_calendar_event_organizer':
            $organizer_id = get_post_meta( $event_id, 'mec_organizer_id', true );
            $organizer = get_term( $organizer_id, 'mec_organizer' );
            $replacement = ( $organizer ? $organizer->name : '' );
            break;
        case 'modern_events_calendar_event_cost':
            $replacement = get_post_meta( $event_id, 'mec_cost', true );
            break;
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_modern_events_calendar_get_trigger_tag_replacement', 10, 6 );