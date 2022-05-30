<?php
/**
 * User Points
 *
 * @package     AutomatorWP\Integrations\GamiPress\Actions\User_Points
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_GamiPress_User_Points extends AutomatorWP_Integration_Action {

    public $integration = 'gamipress';
    public $action = 'gamipress_user_points';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Award points to user', 'automatorwp' ),
            'select_option'     => __( 'Award <strong>points</strong> to user', 'automatorwp' ),
            /* translators: %1$s: Points amount. %2$s: Post title. %3$s: User. */
            'edit_label'        => sprintf( __( 'Award %1$s %2$s to %3$s', 'automatorwp' ), '{points}', '{points_type}', '{user}' ),
            /* translators: %1$s: Points amount. %2$s: Post title. %3$s: User. */
            'log_label'         => sprintf( __( 'Award %1$s %2$s to %3$s', 'automatorwp' ), '{points}', '{points_type}', '{user}' ),
            'options'           => array(
                'points' => array(
                    'from' => 'points',
                    'fields' => array(
                        'points' => array(
                            'name' => __( 'Points amount:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => '1'
                        ),
                        'earning_text' => array(
                            'name' => __( 'User earning text:', 'automatorwp' ),
                            'desc' => __( 'Enter the text for the user earning entry. Leave blank to do not register a new user earning entry.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        )
                    )
                ),
                'points_type' => array(
                    'from' => 'points_type',
                    'default' => __( 'Choose a points type', 'automatorwp' ),
                    'fields' => array(
                        'points_type' => array(
                            'name' => __( 'Points Type:', 'automatorwp' ),
                            'type' => 'select',
                            'option_none' => false,
                            'options_cb' => 'automatorwp_gamipress_points_types_options_cb'
                        )
                    )
                ),
                'user' => array(
                    'from' => 'user',
                    'default' => __( 'user', 'automatorwp' ),
                    'fields' => array(
                        'user' => array(
                            'name' => __( 'User ID:', 'automatorwp' ),
                            'desc' => __( 'User ID that will receive this points. Leave blank to award the points to the user that completes the automation.', 'automatorwp' ),
                            'type' => 'input',
                            'default' => ''
                        ),
                    )
                ),
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
        $points = absint( $action_options['points'] );
        $points_type = $action_options['points_type'];
        $earning_text = $action_options['earning_text'];
        $user_id_to_award = absint( $action_options['user'] );

        if( $user_id_to_award === 0 ) {
            $user_id_to_award = $user_id;
        }

        $user = get_userdata( $user_id_to_award );

        // Bail if user does not exists
        if( ! $user ) {
            return;
        }

        // Bail if no points to award
        if( $points === 0 ) {
            return;
        }

        // Bail if post is not a points type
        if( ! gamipress_get_points_type( $points_type ) ) {
            return;
        }

        // Award the points
        gamipress_award_points_to_user( $user_id_to_award, $points, $points_type );

        if( ! empty( $earning_text ) ) {
            // Insert the custom user earning
            gamipress_insert_user_earning( $user_id, array(
                'title'	        => $earning_text,
                'user_id'	    => $user_id_to_award,
                'post_id'	    => gamipress_get_points_type_id( $points_type ),
                'post_type' 	=> 'points-type',
                'points'	    => $points,
                'points_type'	=> $points_type,
                'date'	        => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
            ) );
        }

    }

}

new AutomatorWP_GamiPress_User_Points();