<?php
/**
 * Contact Added
 *
 * @package     AutomatorWP\Integrations\Jetpack_CRM\Triggers\Contact_Added
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Jetpack_CRM_Contact_Added extends AutomatorWP_Integration_Trigger {

    public $integration = 'jetpack_crm';
    public $trigger = 'jetpack_crm_contact_added';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'New contact is added', 'automatorwp' ),
            'select_option'     => __( 'New contact is <strong>added</strong>', 'automatorwp' ),
            /* translators: %1$s: List. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'New contact is added %1$s time(s)', 'automatorwp' ), '{times}' ),
            /* translators: %1$s: List. */
            'log_label'         => __( 'New contact is added', 'automatorwp' ),
            'action'            => 'zbs_new_customer',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                //automatorwp_jetpack_crm_contact_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param array $obj
     */
    public function listener( $obj ) {

        $user_id = get_current_user_id( );

        // Bail if not user
        if ( $user_id === 0 ) {
            return;
        }

        // Trigger the contact added
        automatorwp_trigger_event( array(
            'trigger'           => $this->trigger,
            'user_id'           => $user_id
        ) );
        

    }

}

new AutomatorWP_Jetpack_CRM_Contact_Added();