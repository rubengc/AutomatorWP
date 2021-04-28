<?php
/**
 * Earn Points
 *
 * @package     AutomatorWP\Integrations\GamiPress\Triggers\Earn_Points
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_GamiPress_Earn_Points extends AutomatorWP_Integration_Trigger {

    public $integration = 'gamipress';
    public $trigger = 'gamipress_earn_points';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User earns points', 'automatorwp' ),
            'select_option'     => __( 'User earns <strong>points</strong>', 'automatorwp' ),
            /* translators: %1$s: Points amount. %2$s: Post title. */
            'edit_label'        => sprintf( __( 'User earns %1$s %2$s', 'automatorwp' ), '{points}', '{points_type}' ),
            /* translators: %1$s: Points amount. %2$s: Post title. */
            'log_label'         => sprintf( __( 'User earns %1$s %2$s', 'automatorwp' ), '{points}', '{points_type}' ),
            'action'            => 'gamipress_update_user_points',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 8,
            'options'           => array(
                'points' => array(
                    'from' => 'points',
                    'default' => __( 'any amount of', 'automatorwp' ),
                    'fields' => array(
                        'points' => array(
                            'name' => __( 'Points amount:', 'automatorwp' ),
                            'desc' => __( 'Leave blank for any amount of points.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        )
                    )
                ),
                'points_type' => array(
                    'from' => 'points_type',
                    'fields' => array(
                        'points_type' => array(
                            'name' => __( 'Points Type:', 'automatorwp' ),
                            'type' => 'select',
                            'option_none' => true,
                            'option_none_label' => __( 'points of any type', 'automatorwp' ),
                            'options_cb' => 'automatorwp_gamipress_points_types_options_cb',
                            'default' => 'any',
                        )
                    )
                ),
            ),
            'tags' => array_merge(
                array(
                    'points_earned' => array(
                        'label'     => __( 'Points earned', 'automatorwp' ),
                        'type'      => 'integer',
                        'preview'   => __( '100', 'automatorwp' ),
                    ),
                ),
                automatorwp_utilities_post_tags()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int       $user_id        The user ID
     * @param int       $new_points     Points amount earned
     * @param int       $total_points   User points balance
     * @param int       $admin_id       The admin that awarded those rank
     * @param int       $achievement_id The achievement ID
     * @param string    $points_type    The points type slug
     * @param string    $reason         Reason of this award
     * @param string    $log_type       The log type
     */
    public function listener( $user_id, $new_points, $total_points, $admin_id, $achievement_id, $points_type, $reason, $log_type ) {

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'points_earned' => $new_points,
            'points'        => $total_points,
            'points_type'   => $points_type,
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

        // Don't deserve if points is not received
        if( ! isset( $event['points'] ) ) {
            return false;
        }

        // Don't deserve if points type is not received
        if( ! isset( $event['points_type'] ) ) {
            return false;
        }

        // Ensure points amount required as integer
        $trigger_options['points'] = absint( $trigger_options['points'] );

        if( $trigger_options['points'] > 0 ) {

            // Don't deserve if points earned are lower than trigger option
            if( $event['points'] < $trigger_options['points'] ) {
                return false;
            }

        }

        // Don't deserve if points type doesn't match with the trigger option
        if( $trigger_options['points_type'] !== 'any' && $trigger_options['points_type'] !== $event['points_type'] ) {
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

        // Trigger tags replacements
        add_filter( 'automatorwp_trigger_tags_replacements', array( $this, 'tags_replacements' ), 10, 4 );

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

        $log_meta['points_earned'] = ( isset( $event['points_earned'] ) ? $event['points_earned'] : 0 );

        return $log_meta;

    }

    /**
     * Filter to setup custom trigger tags replacements
     *
     * Note: Post and times tags replacements are already passed
     *
     * @since 1.0.0
     *
     * @param array     $replacements   The trigger replacements
     * @param stdClass  $trigger        The trigger object
     * @param int       $user_id        The user ID
     * @param stdClass  $log            The last trigger log object
     *
     * @return array
     */
    public function tags_replacements( $replacements, $trigger, $user_id, $log ) {

        // Bail if trigger type don't match this trigger
        if( $trigger->type !== $this->trigger ) {
            return $replacements;
        }

        // Times replacement by default
        $replacements['points_earned'] = ct_get_object_meta( $log->id, 'points_earned', true );

        return $replacements;

    }

}

new AutomatorWP_GamiPress_Earn_Points();