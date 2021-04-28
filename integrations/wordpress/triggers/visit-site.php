<?php
/**
 * Visit Site
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Visit_Site
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Visit_Site extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_visit_site';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User daily visits the site', 'automatorwp' ),
            'select_option'     => __( 'User <strong>daily visits</strong> the site', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User daily visits the site %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User daily visits the site', 'automatorwp' ),
            'action'            => 'init',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'times' => automatorwp_utilities_times_option()
            ),
            'tags' => array(
                'times' => automatorwp_utilities_times_tag( true )
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     */
    public function listener() {

        $user_id = get_current_user_id();

        // Bail if user is not logged in
        if( $user_id === 0 ) {
            return;
        }

        // Get the user last visit
        $last_visit = get_user_meta( $user_id, '_automatorwp_last_visit', true );

        if( ! empty( $last_visit ) ) {

            $today = date( 'Y-m-d', current_time( 'timestamp' ) );
            $last_visit = date( 'Y-m-d', strtotime( $last_visit ) );

            // Bail if user has visited the site today
            if( $last_visit === $today ) {
                return;
            }

        }

        // Update the user last visit for next check
        update_user_meta( $user_id, '_automatorwp_last_visit', date( 'Y-m-d 00:00:00', current_time( 'timestamp' ) ) );

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $user_id,
        ) );

    }

}

new AutomatorWP_WordPress_Visit_Site();