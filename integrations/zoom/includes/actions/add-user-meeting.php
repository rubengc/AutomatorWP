<?php
/**
 * Add User Meeting
 *
 * @package     AutomatorWP\Integrations\Zoom\Actions\Add_User_Meeting
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Zoom_Add_User_Meeting extends AutomatorWP_Integration_Action {

    public $integration = 'zoom';
    public $action = 'zoom_add_user_meeting';

    /**
     * Registrant First Name
     *
     * @since 1.0.0
     *
     * @var string $registrant_first_name
     */
    public $registrant_first_name = '';

    /**
     * Registrant Last Name
     *
     * @since 1.0.0
     *
     * @var string $registrant_last_name
     */
    public $registrant_last_name = '';

    /**
     * Registrant Email
     *
     * @since 1.0.0
     *
     * @var string $registrant_email
     */
    public $registrant_email = '';

    /**
     * Registrant Status
     *
     * @since 1.0.0
     *
     * @var string $registrant_status
     */
    public $registrant_status = '';

    /**
     * Meeting ID
     *
     * @since 1.0.0
     *
     * @var string $meeting_id
     */
    public $meeting_id = '';

    /**
     * Registrant ID
     *
     * @since 1.0.0
     *
     * @var string $registrant_id
     */
    public $registrant_id = '';

    /**
     * Join URL
     *
     * @since 1.0.0
     *
     * @var string $join_url
     */
    public $join_url = '';

    /**
     * Store the action result
     *
     * @since 1.0.0
     *
     * @var string $result
     */
    public $result = '';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add user to meeting', 'automatorwp' ),
            'select_option'     => __( 'Add user to <strong>meeting</strong>', 'automatorwp' ),
            /* translators: %1$s: Meeting. */
            'edit_label'        => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{meeting}' ),
            /* translators: %1$s: Meeting. */
            'log_label'         => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{meeting}' ),
            'options'           => array(
                'meeting' => array(
                    'from' => 'meeting',
                    'default' => __( 'meeting', 'automatorwp' ),
                    'fields' => array(
                        'meeting' => automatorwp_utilities_ajax_selector_field( array(
                            'field'             => 'meeting',
                            'option_default'    => __( 'meeting', 'automatorwp' ),
                            'placeholder'       => __( 'Select a meeting', 'automatorwp' ),
                            'name'              => __( 'Meeting:', 'automatorwp' ),
                            'action_cb'         => 'automatorwp_zoom_get_meetings',
                            'options_cb'        => 'automatorwp_zoom_options_cb_meetings',
                            'default'           => ''
                        ) ),
                        'registrant_status' => array(
                            'name' => __( 'Status:', 'automatorwp' ),
                            'desc' => __( 'The registrant status.', 'automatorwp' ),
                            'type' => 'select',
                            'options' => array(
                                'approved'  => __( 'Approved', 'automatorwp' ),
                                'denied'    => __( 'Denied', 'automatorwp' ),
                                'pending'   => __( 'Pending', 'automatorwp' ),
                            ),
                        ),
                    )
                )
            ),
        ) );

    }

    /**
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    public function execute( $action, $user_id, $action_options, $automation ) {

        $prefix = '_automatorwp_zoom_meetings_';

        $this->registrant_first_name = '';
        $this->registrant_last_name = '';
        $this->registrant_email = '';
        $this->registrant_status = '';
        $this->meeting_id = '';
        $this->registrant_id = '';
        $this->join_url = '';
        $this->result = '';

        // Shorthand
        $meeting_id = $action_options['meeting'];

        // Bail if empty meeting to assign
        if( empty( $meeting_id ) ) {
            $this->result = __( 'Could not add registrant to meeting, missing meeting', 'automatorwp' );
            return;
        }

        $params = automatorwp_zoom_get_request_parameters( 'meetings' );

        // Bail if the authorization has not been setup from settings
        if( $params === false ) {
            $this->result = __( 'Could not add registrant to meeting, Zoom authentication failed', 'automatorwp' );
            return;
        }

        $user = get_userdata( $user_id );

        $this->registrant_first_name = $user->first_name;
        $this->registrant_last_name = $user->last_name;
        $this->registrant_email = $user->user_email;
        $this->registrant_status = $action_options['registrant_status'];

        if( $this->registrant_status === '' ) {
            $this->registrant_status = 'approved';
        }

        // Setup the request parameters
        $body_params = array(
            'first_name' => $this->registrant_first_name,
            'last_name'  => $this->registrant_last_name,
            'email'      => $this->registrant_email,
            'status'     => $this->registrant_status,
        );

        // Force auto approval
        if( $this->registrant_status === 'approved' ) {
            $body_params['auto_approve'] = true;
        }

        $params['body'] = json_encode( $body_params );

        // Setup the URL
        $url = 'https://api.zoom.us/v2/meetings/' . $meeting_id . '/registrants';

        // Execute the request
        $response = wp_remote_post( $url, $params );

        if ( is_wp_error( $response ) ) {
            $this->result = sprintf( __( 'Could not add registrant to meeting, error received: %1$s', 'automatorwp' ), $response->get_error_message() );
            return;
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( $response['body'], true, 512, JSON_BIGINT_AS_STRING );

        if ( $response_code === 201 ) {

            if ( isset( $body['join_url'] ) ) {

                // Update user metas with information from this meeting
                update_user_meta( $user_id, $prefix . $meeting_id . '_id', $body['id'] );
                update_user_meta( $user_id, $prefix . $meeting_id . '_registrant_id', $body['registrant_id'] );
                update_user_meta( $user_id, $prefix . $meeting_id . '_join_url', $body['join_url'] );

                // Update trigger vars with information from this meeting
                $this->meeting_id = $body['id'];
                $this->registrant_id = $body['registrant_id'];
                $this->join_url = $body['join_url'];
                $this->result = __( 'Registrant successfully registered', 'automatorwp' );
            }
        } else {
            $this->result = sprintf( __( 'Could not add registrant to meeting, error received: %1$s', 'automatorwp' ), $body['message'] );
        }

    }

    /**
     * Register required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Configuration notice
        add_filter( 'automatorwp_automation_ui_after_item_label', array( $this, 'configuration_notice' ), 10, 2 );

        // Log meta data
        add_filter( 'automatorwp_user_completed_action_log_meta', array( $this, 'log_meta' ), 10, 5 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

        parent::hooks();
    }

    /**
     * Configuration notice
     *
     * @since 1.0.0
     *
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The object type (trigger|action)
     */
    public function configuration_notice( $object, $item_type ) {

        // Bail if action type don't match this action
        if( $item_type !== 'action' ) {
            return;
        }

        if( $object->type !== $this->action ) {
            return;
        }

        $params = automatorwp_zoom_get_request_parameters( 'meetings' );

        // Warn user if the authorization has not been setup from settings
        if( $params === false ) : ?>
            <div class="automatorwp-notice-warning" style="margin-top: 10px; margin-bottom: 0;">
                <?php echo sprintf(
                    __( 'You need to configure the <a href="%s" target="_blank">Zoom Meetings settings</a> to get this action to work.', 'automatorwp' ),
                    get_admin_url() . 'admin.php?page=automatorwp_settings&tab=opt-tab-zoom'
                ); ?>
                <?php echo sprintf(
                    __( '<a href="%s" target="_blank">Documentation</a>', 'automatorwp' ),
                    'https://automatorwp.com/docs/zoom/'
                ); ?>
            </div>
        <?php endif;

    }

    /**
     * Action custom log meta
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
    public function log_meta( $log_meta, $action, $user_id, $action_options, $automation ) {

        // Bail if action type don't match this action
        if( $action->type !== $this->action ) {
            return $log_meta;
        }

        // Store the action's result
        $log_meta['registrant_first_name'] = $this->registrant_first_name;
        $log_meta['registrant_last_name'] = $this->registrant_last_name;
        $log_meta['registrant_email'] = $this->registrant_email;
        $log_meta['registrant_status'] = $this->registrant_status;
        $log_meta['meeting_id'] = $this->meeting_id;
        $log_meta['registrant_id'] = $this->registrant_id;
        $log_meta['join_url'] = $this->join_url;
        $log_meta['result'] = $this->result;

        return $log_meta;
    }

    /**
     * Action custom log fields
     *
     * @since 1.0.0
     *
     * @param array     $log_fields The log fields
     * @param stdClass  $log        The log object
     * @param stdClass  $object     The trigger/action/automation object attached to the log
     *
     * @return array
     */
    public function log_fields( $log_fields, $log, $object ) {

        // Bail if log is not assigned to an action
        if( $log->type !== 'action' ) {
            return $log_fields;
        }

        // Bail if action type don't match this action
        if( $object->type !== $this->action ) {
            return $log_fields;
        }

        $log_fields['registrant_first_name'] = array(
            'name' => __( 'Registrant First Name:', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['registrant_last_name'] = array(
            'name' => __( 'Registrant Last Name:', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['registrant_email'] = array(
            'name' => __( 'Registrant Email:', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['registrant_status'] = array(
            'name' => __( 'Registrant Status:', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['meeting_id'] = array(
            'name' => __( 'Meeting ID:', 'automatorwp' ),
            'desc' => __( 'Unique identifier for the meeting.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['registrant_id'] = array(
            'name' => __( 'Registrant ID:', 'automatorwp' ),
            'desc' => __( 'Unique identifier assigned to the registrant.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['join_url'] = array(
            'name' => __( 'Join URL:', 'automatorwp' ),
            'desc' => __( 'Unique URL for this registrant to join the meeting.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;
    }

}

new AutomatorWP_Zoom_Add_User_Meeting();