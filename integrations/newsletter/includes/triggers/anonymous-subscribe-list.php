<?php
/**
 * Anonymous Subscribe List
 *
 * @package     AutomatorWP\Integrations\Newsletter\Triggers\Anonymous_Subscribe_List
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Newsletter_Anonymous_Subscribe_List extends AutomatorWP_Integration_Trigger {

    public $integration = 'newsletter';
    public $trigger = 'newsletter_anonymous_subscribe_list';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'anonymous'         => true,
            'label'             => __( 'Guest subscribes to a list', 'automatorwp' ),
            'select_option'     => __( 'Guest subscribes to a <strong>list</strong>', 'automatorwp' ),
            /* translators: %1$s: List title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'Guest subscribes to %1$s %2$s time(s)', 'automatorwp' ), '{list}', '{times}' ),
            /* translators: %1$s: List title. */
            'log_label'         => sprintf( __( 'Guest subscribes to %1$s', 'automatorwp' ), '{list}' ),
            'action'            => 'newsletter_user_post_subscribe',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'list' => array(
                    'from' => 'list',
                    'fields' => array(
                        'list' => array(
                            'name' => __( 'List:', 'automatorwp' ),
                            'type' => 'select',
                            'classes' => 'automatorwp-selector',
                            'options_cb' => array( $this, 'lists_options_cb' ),
                            'default' => 'any'
                        )
                    )
                ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                array(
                    'newsletter_user_email' => array(
                        'label'     => __( 'User email', 'automatorwp' ),
                        'type'      => 'text',
                        'preview'   => __( 'contact@automatorwp.com', 'automatorwp' ),
                    ),
                ),
                automatorwp_utilities_post_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Get lists options
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function lists_options_cb() {

        $options = array(
            'any' => __( 'any list', 'automatorwp' ),
        );

        if ( class_exists( '\Newsletter' ) ) {

            $lists = \Newsletter::instance()->get_lists();

            if ( ! empty( $lists ) ) {
                foreach ( $lists as $list ) {
                    $options['list_' . $list->id] = $list->name;
                }
            }

        }

        return $options;

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param object $user
     */
    public function listener( $user ) {

        global $wpdb;

        $user_id = $user->id;
        $user_email = $user->email;

        // Get the last user log
        $logs_table = $wpdb->prefix . 'newsletter_user_logs';
        $log = $wpdb->get_row( "SELECT MAX(id), data FROM {$logs_table} WHERE user_id = {$user_id} AND source = 'subscribe'" );

        // Bail if log not found
        if ( $log === null ) {
            return;
        }

        // Check if there is the log data
        if ( ! isset( $log->data ) && $log->data !== null  ) {
            return;
        }

        if ( $log->data === null  ) {
            return;
        }

        // Get the lists subscribed
        $lists = json_decode( $log->data, true );

        foreach( $lists as $list_id => $status ) {

            // Possible statuses are:
            // 1 - Subscribed
            // 0 - Not subscribed
            // C - Confirmed

            // Bail if user not subscribed to this list
            if( $status !== '1' ) {
                continue;
            }

            // Trigger the list subscribed
            automatorwp_trigger_event( array(
                'trigger'       => $this->trigger,
                'user_id'       => $user_id,
                'user_email'    => $user_email,
                'list_id'       => $list_id,
            ) );
        }

    }

    /**
     * User deserves check
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
    public function user_deserves_trigger( $deserves_trigger, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Don't deserve if post is not received
        if( ! isset( $event['list_id'] ) ) {
            return false;
        }

        // Shorthand
        $list_id = $trigger_options['list'];

        // Don't deserve if list doesn't match with the trigger option
        if( $list_id !== 'any' && $list_id !== $event['list_id'] ) {
            return false;
        }

        return $deserves_trigger;

    }

    /**
     * Register the required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 6 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

        // Trigger tags replacements
        add_filter( 'automatorwp_trigger_tags_replacements', array( $this, 'tags_replacements' ), 10, 4 );

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

        $log_meta['user_email'] = ( isset( $event['user_email'] ) ? $event['user_email'] : '' );
        $log_meta['list_id'] = ( isset( $event['list_id'] ) ? $event['list_id'] : '' );

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

        // Bail if log is not assigned to an trigger
        if( $log->type !== 'trigger' ) {
            return $log_fields;
        }

        // Bail if trigger type don't match this trigger
        if( $object->type !== $this->trigger ) {
            return $log_fields;
        }

        $log_fields['user_email'] = array(
            'name' => __( 'User email', 'automatorwp' ),
            'desc' => __( 'Email used to get subscribed.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['list_id'] = array(
            'name' => __( 'List subscribed', 'automatorwp' ),
            'desc' => __( 'List subscribed.', 'automatorwp' ),
            'type' => 'text',
            'before_field' => array( $this, 'list_display_cb' ),
        );

        return $log_fields;

    }

    /**
     * Callback used to render list on logs
     *
     * @since 1.0.0
     *
     * @param array         $field_args
     * @param CMB2_Field    $field
     */
    public function list_display_cb( $field_args, $field ) {

        $list_id = $value = $field->value();

        $lists = $this->lists_options_cb();

        echo ( isset( $lists[$list_id] ) ? $lists[$list_id] : sprintf( __( 'List with ID %s not found.', 'automatorwp' ), $list_id ) );

    }

    /**
     * Filter to setup custom trigger tags replacements
     *
     * Note: Post and times tags replacements are already passed
     *
     * @since 1.0.0
     *
     * @param array     $replacements   The trigger replacements
     * @param stdClass  $trigger        The trigger object
     * @param int       $user_id        The user ID
     * @param stdClass  $log            The last trigger log object
     *
     * @return array
     */
    public function tags_replacements( $replacements, $trigger, $user_id, $log ) {

        // Bail if trigger type don't match this trigger
        if( $trigger->type !== $this->trigger ) {
            return $replacements;
        }

        // Times replacement by default
        $replacements['newsletter_user_email'] = ct_get_object_meta( $log->id, 'user_email', true );

        return $replacements;

    }

}

new AutomatorWP_Newsletter_Anonymous_Subscribe_List();