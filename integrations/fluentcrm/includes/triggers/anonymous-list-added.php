<?php
/**
 * Anonymous List Added
 *
 * @package     AutomatorWP\Integrations\FluentCRM\Triggers\Anonymous_List_Added
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_FluentCRM_Anonymous_List_Added extends AutomatorWP_Integration_Trigger {

    public $integration = 'fluentcrm';
    public $trigger = 'fluentcrm_anonymous_list_added';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'anonymous'         => true,
            'label'             => __( 'Contact gets added to list', 'automatorwp' ),
            'select_option'     => __( 'Contact gets added to <strong>list</strong>', 'automatorwp' ),
            /* translators: %1$s: List. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'Contact gets added to %1$s %2$s time(s)', 'automatorwp' ), '{list}', '{times}' ),
            /* translators: %1$s: List. */
            'log_label'         => sprintf( __( 'Contact gets added to %1$s', 'automatorwp' ), '{list}' ),
            'action'            => 'fluentcrm_contact_added_to_lists',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'list' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'list',
                    'name'              => __( 'List:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any list', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_fluentcrm_get_lists',
                    'options_cb'        => 'automatorwp_fluentcrm_options_cb_list',
                    'default'           => 'any'
                ) ),
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
     * @param array $lists_ids
     * @param \FluentCrm\App\Models\Subscriber $subscriber
     */
    public function listener( $lists_ids, $subscriber ) {

        $user_id = automatorwp_fluentcrm_get_subscriber_user_id( $subscriber );

        // Make sure subscriber has not a user ID assigned
        if ( $user_id !== 0 ) {
            return;
        }

        foreach( $lists_ids as $list_id ) {
            // Trigger the list added
            automatorwp_trigger_event( array(
                'trigger'           => $this->trigger,
                'list_id'           => $list_id,
                'subscriber_email'  => $subscriber->email,
            ) );
        }

    }

    /**
     * Guest deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if guest deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if guest deserves trigger, false otherwise
     */
    public function anonymous_deserves_trigger( $deserves_trigger, $trigger, $event, $trigger_options, $automation ) {

        // Don't deserve if post is not received
        if( ! isset( $event['list_id'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( $trigger_options['list'] !== 'any' && absint( $trigger_options['list'] ) !== absint( $event['list_id'] ) ) {
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
        add_filter( 'automatorwp_anonymous_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 5 );

        parent::hooks();
    }

    /**
     * Trigger custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    function log_meta( $log_meta, $trigger, $event, $trigger_options, $automation ) {

        // Bail if action type don't match this action
        if( $trigger->type !== $this->trigger ) {
            return $log_meta;
        }

        $log_meta['subscriber_email'] = ( isset( $event['subscriber_email'] ) ? $event['subscriber_email'] : '' );

        return $log_meta;

    }

}

new AutomatorWP_FluentCRM_Anonymous_List_Added();