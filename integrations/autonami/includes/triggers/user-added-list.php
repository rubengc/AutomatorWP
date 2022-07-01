<?php
/**
 * User Added List
 *
 * @package     AutomatorWP\Integrations\Autonami\Triggers\User_Added_List
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Autonami_User_Added_List extends AutomatorWP_Integration_Trigger {

    public $integration = 'autonami';
    public $trigger = 'autonami_user_added_list';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User added to list', 'automatorwp' ),
            'select_option'     => __( 'User added to <strong>list</strong>', 'automatorwp' ),
            /* translators: %1$s: List. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User added to %1$s %2$s time(s)', 'automatorwp' ), '{list}', '{times}' ),
            /* translators: %1$s: List. */
            'log_label'         => sprintf( __( 'User added to %1$s', 'automatorwp' ), '{list}' ),
            'action'            => 'bwfan_contact_added_to_lists',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'list' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'list',
                    'name'              => __( 'List:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any list', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_autonami_get_lists',
                    'options_cb'        => 'automatorwp_autonami_options_cb_list',
                    'default'           => 'any'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_autonami_list_tags(),
                automatorwp_autonami_email_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param BWFCRM_Lists $lists
     * @param BWFCRM_Contact $contact
     */
    public function listener( $lists, $contact ) {

        // Get contact email
        $email = $contact->contact->get_email();
		$user = get_user_by( 'email', $email );

        // Make sure contact has an user ID assigned
        if ( $user->ID === 0 ) {
            return;
        }
    
        if ( !is_array ( $lists ) ){

            // Trigger the user added to list
            automatorwp_trigger_event( array(
                'trigger'           => $this->trigger,
                'user_id'           => $user->ID,
                'list_id'           => $lists->get_id(),
                'contact_email'     => $email,
            ) );

        } else {

            foreach ( $lists as $list ){

                // Trigger the user added to list
                automatorwp_trigger_event( array(
                    'trigger'           => $this->trigger,
                    'user_id'           => $user->ID,
                    'list_id'           => $list->get_id(),
                    'contact_email'     => $email,
                ) );
    
            }

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

        // Don't deserve if list is not received
        if( ! isset( $event['list_id'] ) ) {
            return false;
        }

        // Don't deserve if list doesn't match with the trigger option
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

        $log_meta['contact_email'] = ( isset( $event['contact_email'] ) ? $event['contact_email'] : '' );
        $log_meta['list_id'] = ( isset( $event['list_id'] ) ? $event['list_id'] : '' );

        return $log_meta;

    }

}

new AutomatorWP_Autonami_User_Added_List();