<?php
/**
 * Status Change
 *
 * @package     AutomatorWP\Integrations\FluentCRM\Triggers\Status_Change
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_FluentCRM_Status_Change extends AutomatorWP_Integration_Trigger {

    public $integration = 'fluentcrm';
    public $trigger = 'fluentcrm_status_change';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User status changes to a status', 'automatorwp' ),
            'select_option'     => __( 'User status changes to <strong>a status</strong>', 'automatorwp' ),
            /* translators: %1$s: List. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User status changes to %1$s %2$s time(s)', 'automatorwp' ), '{status}', '{times}' ),
            /* translators: %1$s: List. */
            'log_label'         => sprintf( __( 'User status changes to %1$s', 'automatorwp' ), '{status}' ),
            'action'            => array(
                'fluentcrm_subscriber_status_to_subscribed',
                'fluentcrm_subscriber_status_to_pending',
                'fluentcrm_subscriber_status_to_unsubscribed',
                'fluentcrm_subscriber_status_to_bounced',
                'fluentcrm_subscriber_status_to_complained',
            ),
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'status' => array(
                    'from' => 'status',
                    'fields' => array(
                        'status' => array(
                            'name' => __( 'Status:', 'automatorwp' ),
                            'type' => 'select',
                            'options' => array(
                                'any'           => __( 'any status', 'automatorwp' ),
                                'subscribed'    => __( 'Subscribed', 'automatorwp' ),
                                'pending'       => __( 'Pending', 'automatorwp' ),
                                'unsubscribed'  => __( 'Unsubscribed', 'automatorwp' ),
                                'bounced'       => __( 'Bounced', 'automatorwp' ),
                                'complained'    => __( 'Complained', 'automatorwp' ),
                            ),
                            'default' => 'any'
                        ),
                    )
                ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_fluentcrm_contact_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param \FluentCrm\App\Models\Subscriber $subscriber
     * @param string $old_status
     */
    public function listener( $subscriber, $old_status ) {

        $user_id = automatorwp_fluentcrm_get_subscriber_user_id( $subscriber );

        // Make sure subscriber has a user ID assigned
        if ( $user_id === 0 ) {
            return;
        }

        // Trigger the status change
        automatorwp_trigger_event( array(
            'trigger'           => $this->trigger,
            'user_id'           => $user_id,
            'status'            => $subscriber->status,
            'subscriber_email'  => $subscriber->email,
        ) );

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

        // Don't deserve if status is not received
        if( ! isset( $event['status'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( $trigger_options['status'] !== 'any' && $trigger_options['status'] !== $event['status'] ) {
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

        $log_meta['subscriber_email'] = ( isset( $event['subscriber_email'] ) ? $event['subscriber_email'] : '' );

        return $log_meta;

    }

}

new AutomatorWP_FluentCRM_Status_Change();