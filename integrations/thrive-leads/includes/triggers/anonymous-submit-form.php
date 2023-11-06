<?php
/**
 * Anonymous Submit Form
 *
 * @package     AutomatorWP\Integrations\Thrive_Leads\Triggers\Anonymous_Submit_Form
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Thrive_Leads_Anonymous_Submit_Form extends AutomatorWP_Integration_Trigger {

    public $integration = 'thrive_leads';
    public $trigger = 'thrive_leads_anonymous_submit_form';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'anonymous'         => true,
            'label'             => __( 'Guest submits a form', 'automatorwp' ),
            'select_option'     => __( 'Guest submits <strong>a form</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'Guest submits %1$s %2$s time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'Guest submits %1$s', 'automatorwp' ), '{post}' ),
            'action'            => 'tcb_api_form_submit',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'post' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'post',
                    'name'              => __( 'Form:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any form', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_thrive_leads_get_forms',
                    'options_cb'        => 'automatorwp_thrive_leads_options_cb_form',
                    'default'           => 'any'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_thrive_leads_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param array     $post
     */
    public function listener( $post ) {

        // Login is required
        if ( is_user_logged_in() ) {
            return;
        }

        $user_id = get_current_user_id();

        $form_id = $post['thrive_leads']['tl_data']['_key'];
        $form_name = $post['thrive_leads']['tl_data']['form_name'];
        $group_id = $post['thrive_leads']['tl_data']['main_group_id'];
        $group_name = $post['thrive_leads']['tl_data']['main_group_name'];
        
        // Trigger submit form event
        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'form_id' => $form_id,
            'form_name' => $form_name,
            'group_id' => $group_id,
            'group_name' => $group_name,
        ) );

    }

    /**
     * User deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if user deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    public function anonymous_deserves_trigger( $deserves_trigger, $trigger, $event, $trigger_options, $automation ) {

        // Don't deserve if post is not received
        if( ! isset( $event['form_id'] ) ) {
            return false;
        }

        // Bail if post doesn't match with the trigger option
        if( $trigger_options['post'] !== 'any' && absint( $event['form_id'] ) !== absint( $trigger_options['post'] ) ) {
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

        $log_meta['form_id'] = ( isset( $event['form_id'] ) ? $event['form_id'] : 0 );
        $log_meta['form_name'] = ( isset( $event['form_name'] ) ? $event['form_name'] : '' );
        $log_meta['group_id'] = ( isset( $event['group_id'] ) ? $event['group_id'] : 0 );
        $log_meta['group_name'] = ( isset( $event['group_name'] ) ? $event['group_name'] : '' );


        return $log_meta;

    }

}

new AutomatorWP_Thrive_Leads_Anonymous_Submit_Form();