<?php
/**
 * Add Contact Tag
 *
 * @package     AutomatorWP\Integrations\Autonami\Actions\Add_Contact_Tag
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly

if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Autonami_Add_Contact_Tag extends AutomatorWP_Integration_Action {

    public $integration = 'autonami';
    public $action = 'autonami_add_contact_tag';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add a tag to contact', 'automatorwp' ),
            'select_option'     => __( 'Add a <strong>tag</strong> to contact', 'automatorwp' ),
            /* translators: %1$s: Tag. %2$s: Contact */
            'edit_label'        => sprintf( __( 'Add tag %1$s to %2$s', 'automatorwp' ), '{tag}', '{contact}' ),
            /* translators: %1$s: Tag. */
            'log_label'         => sprintf( __( 'Add tag %1$s', 'automatorwp' ), '{tag}' ),
            'options'           => array(
                'tag' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'tag',
                    'option_default'    => __( 'Select a tag', 'automatorwp' ),
                    'name'              => __( 'Tag:', 'automatorwp' ),
                    'option_none'       => false,
                    'action_cb'         => 'automatorwp_autonami_get_tags',
                    'options_cb'        => 'automatorwp_autonami_options_cb_tag',
                    'placeholder'       => __( 'Select a tag', 'automatorwp' ),
                    'default'           => ''
                ) ),
                'contact' => array(
                    'from' => 'email',
                    'default' => __( 'contact', 'automatorwp' ),
                    'fields' => array(
                        'email' => array(
                            'name' => __( 'Contact Email:', 'automatorwp' ),
                            'desc' => __( 'The contact email address.', 'automatorwp' ),
                            'type' => 'text',
                            'required'  => true,
                            'default' => ''
                        ),
                     ) )
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

        // Shorthand
        $tag_id = $action_options['tag'];
        $contact_email = $action_options['email'];
        $this->result = '';

        // Bail if empty tag to assign
        if( empty( $tag_id ) ) {
            return;
        }

        $tag = array(
			array(
				'id' => $tag_id,
			),
		);

       
        $contact = new BWFCRM_Contact( $contact_email );

        // Bail if contact not exists
        if ( ! $contact->is_contact_exists() ){
            return;
        }

		$response = $contact->add_tags( $tag );

        if ( empty ( $response ) ){
            $this->result = __( 'Contact already has the tag', 'automatorwp' );
            return;
        } else{
            $this->result = __( 'Tag added successfully', 'automatorwp' );
        }

    }

    /**
     * Register required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_action_log_meta', array( $this, 'log_meta' ), 10, 5 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

        parent::hooks();

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

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;
    }

}

new AutomatorWP_Autonami_Add_Contact_Tag();