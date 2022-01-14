<?php
/**
 * User Deleted
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\User_Deleted
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_User_Deleted extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_user_deleted';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User gets deleted', 'automatorwp' ),
            'select_option'     => __( 'User gets <strong>deleted</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => __( 'User gets deleted', 'automatorwp' ),
            'log_label'         => __( 'User gets deleted', 'automatorwp' ),
            'action'            => 'delete_user',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                // No options
            ),
            'tags' => array(
                // No tags
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int      $user_id  ID of the user to delete.
     * @param int|null $reassign ID of the user to reassign posts and links to.
     *                           Default null, for no reassignment.
     * @param WP_User  $user     WP_User object of the user to delete.
     */
    public function listener( $user_id, $reassign, $user ) {

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $user_id,
        ) );

    }

}

new AutomatorWP_WordPress_User_Deleted();