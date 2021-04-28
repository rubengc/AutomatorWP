<?php
/**
 * Add User Membership
 *
 * @package     AutomatorWP\Integrations\Paid_Memberships_Pro\Actions\Add_User_Membership
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Paid_Memberships_Pro_Add_User_Membership extends AutomatorWP_Integration_Action {

    public $integration = 'paid_memberships_pro';
    public $action = 'paid_memberships_pro_add_user_membership';

    /**
     * The action result
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
            'label'             => __( 'Add user to membership level', 'automatorwp' ),
            'select_option'     => __( 'Add user to <strong>membership level</strong>', 'automatorwp' ),
            /* translators: %1$s: Post title. */
            'edit_label'        => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{membership}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'Add user to %1$s', 'automatorwp' ), '{membership}' ),
            'options'           => array(
                'membership' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'membership',
                    'name'              => __( 'Membership Level:', 'automatorwp' ),
                    'option_default'    => __( 'membership level', 'automatorwp' ),
                    'option_none'       => false,
                    'option_custom'         => true,
                    'option_custom_label'   => __( 'Membership Level ID', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_paid_memberships_pro_get_memberships',
                    'options_cb'        => 'automatorwp_paid_memberships_pro_options_cb_membership',
                    'default'           => 'any'
                ) ),
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
        $membership_id = absint( $action_options['membership'] );

        // Bail if membership ID not provided
        if( $membership_id === 0 ) {
            return;
        }

        $current_level = pmpro_getMembershipLevelForUser( $user_id );

        // Bail if user is already on this membership
        if ( ! empty( $current_level ) && absint( $current_level->ID ) === $membership_id ) {
            $this->result = __( 'User is already on this membership level.', 'automatorwp' );
            return;
        }

        $new_level = pmpro_changeMembershipLevel( $membership_id, $user_id );

        if( ! $new_level ) {
            $this->result = __( 'Could not add user to membership level.', 'automatorwp' );
            return;
        }

        $this->result = __( 'User added to membership level successfully.', 'automatorwp' );

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

        // Store result
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

new AutomatorWP_Paid_Memberships_Pro_Add_User_Membership();