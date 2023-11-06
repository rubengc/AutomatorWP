<?php
/**
 * Tags
 *
 * @package     AutomatorWP\WP_WP_All_Import\Tags
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Download tags
 *
 * @since 1.0.0
 *
 * @return array
 */
function automatorwp_wp_all_import_tags() {

    return array(
        'import_id' => array(
            'label'     => __( 'Import ID', 'automatorwp' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'history_id' => array(
            'label'     => __( 'History ID', 'automatorwp' ),
            'type'      => 'integer',
            'preview'   => '123',
        ),
        'import_type' => array(
            'label'     => __( 'Import type', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The import type',
        ),
        'import_time_run' => array(
            'label'     => __( 'Time run', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'The time run in seconds',
        ),
        'import_date' => array(
            'label'     => __( 'Import date', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'Import date',
        ),
        'import_summary' => array(
            'label'     => __( 'Import summary', 'automatorwp' ),
            'type'      => 'text',
            'preview'   => 'Import summary',
        ),
    );

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
function automatorwp_wp_all_import_get_trigger_download_tag_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {


    $trigger_args = automatorwp_get_trigger( $trigger->type );

    // Skip if trigger is not from this integration
    if( $trigger_args['integration'] !== 'wp_all_import' ) {
        return $replacement;
    }

    switch( $tag_name ) {
        case 'import_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'import_id', true );
            break;
        case 'history_id':
            $replacement = automatorwp_get_log_meta( $log->id, 'history_id', true );
            break;      
        case 'import_type':
            $replacement = automatorwp_get_log_meta( $log->id, 'import_type', true );
            break; 
        case 'import_time_run':
            $replacement = automatorwp_get_log_meta( $log->id, 'import_time_run', true );
            break; 
        case 'import_date':
            $replacement = automatorwp_get_log_meta( $log->id, 'import_date', true );
            break; 
        case 'import_summary':
            $replacement = automatorwp_get_log_meta( $log->id, 'import_summary', true );
            break;      
    }

    return $replacement;

}
add_filter( 'automatorwp_get_trigger_tag_replacement', 'automatorwp_wp_all_import_get_trigger_download_tag_replacement', 10, 6 );
