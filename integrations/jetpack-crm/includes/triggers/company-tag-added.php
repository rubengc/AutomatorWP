<?php
/**
 * Company Tag Added
 *
 * @package     AutomatorWP\Integrations\Jetpack_CRM\Triggers\Company_Tag_Added
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Jetpack_CRM_Company_Tag_Added extends AutomatorWP_Integration_Trigger {

    public $integration = 'jetpack_crm';
    public $trigger = 'jetpack_crm_company_tag_added';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'Tag added to company', 'automatorwp' ),
            'select_option'     => __( '<strong>Tag</strong> added to company', 'automatorwp' ),
            /* translators: %1$s: Tag. %2$s: Number of times. */
            'edit_label'        => sprintf( __( '%1$s added to company %2$s time(s)', 'automatorwp' ), '{tag}', '{times}' ),
            /* translators: %1$s: Tag. */
            'log_label'         => sprintf( __( '%1$s added to company', 'automatorwp' ), '{tag}' ),
            'action'            => 'zbs_tag_added_to_objid',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'tag' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'tag',
                    'name'              => __( 'Tag:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any tag', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_jetpack_crm_get_company_tags',
                    'options_cb'        => 'automatorwp_jetpack_crm_options_cb_tag',
                    'default'           => 'any'
                ) ),
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
     * @param int $tagID
     * @param int $objtype
     * @param int $objid
     */
    public function listener( $tagID, $objtype, $objid ) {

        $user_id = get_current_user_id();

        // Make sure subscriber has a user ID assigned
        if ( $user_id === 0 ) {
            return;
        }

        // Trigger the tag added
        automatorwp_trigger_event( array(
            'trigger'           => $this->trigger,
            'user_id'           => $user_id,
            'tag_id'            => $tagID,
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

        // Don't deserve if post is not received
        if( ! isset( $event['tag_id'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( $trigger_options['tag'] !== 'any' && absint( $trigger_options['tag'] ) !== absint( $event['tag_id'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }    
  

}

new AutomatorWP_Jetpack_CRM_Company_Tag_Added();