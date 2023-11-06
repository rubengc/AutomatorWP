<?php
/**
 * File Downloaded
 *
 * @package     AutomatorWP\Integrations\WP_All_Import\Triggers\Import_Success
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WP_All_Import_Success extends AutomatorWP_Integration_Trigger {

    public $integration = 'wp_all_import';
    public $trigger = 'wp_all_import_success';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User completes an import', 'automatorwp' ),
            'select_option'     => __( 'User <strong>completes</strong> an import', 'automatorwp' ),
            /* translators: %1$s: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User completes an import %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User completes an import', 'automatorwp' ),
            'action'            => 'pmxi_after_xml_import',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_wp_all_import_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int       $import_id
     * @param object    $import
     * 
     */
    public function listener( $import_id, $import ) {
        
        $user_id = get_current_user_id();

        // Bail if user is not logged
        if ($user_id === 0) {
            return;
        }

        if ( empty( $import_id ) ) {
			return false;
		}

        $history = automatorwp_wp_all_import_get_services( $import_id );

        foreach ( $history as $tags ) {
            $history_id = $tags['id'];
            $import_type = $tags['type'];
            $import_time_run = $tags['time_run'];
            $import_date = $tags['date'];
            $import_summary = $tags['summary'];
        }

        // Trigger import completed
        automatorwp_trigger_event( array(
            'trigger'           => $this->trigger,
            'user_id'           => $user_id,
            'import_id'         => $import_id,
            'history_id'        => $history_id,
            'import_type'       => $import_type,
            'import_time_run'   => $import_time_run,
            'import_date'       => $import_date,
            'import_summary'    => $import_summary,
        ) );
     
    }

    /**
     * Register the required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 6 );

        parent::hooks();
    }

    /**
     * Trigger custom log meta
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
    function log_meta( $log_meta, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Bail if action type don't match this action
        if( $trigger->type !== $this->trigger ) {
            return $log_meta;
        }

        $log_meta['import_id'] = ( isset( $event['import_id'] ) ? $event['import_id'] : '' );
        $log_meta['history_id'] = ( isset( $event['history_id'] ) ? $event['history_id'] : '' );
        $log_meta['import_type'] = ( isset( $event['import_type'] ) ? $event['import_type'] : '' );
        $log_meta['import_time_run'] = ( isset( $event['import_time_run'] ) ? $event['import_time_run'] : '' );
        $log_meta['import_date'] = ( isset( $event['import_date'] ) ? $event['import_date'] : '' );
        $log_meta['import_summary'] = ( isset( $event['import_summary'] ) ? $event['import_summary'] : '' );

        return $log_meta;

    }

}


new AutomatorWP_WP_All_Import_Success();