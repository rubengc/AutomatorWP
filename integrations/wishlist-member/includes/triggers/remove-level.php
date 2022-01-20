<?php
/**
 * Remove Level
 *
 * @package     AutomatorWP\Integrations\WishList_Member\Triggers\Remove_Level
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WishList_Member_Remove_Level extends AutomatorWP_Integration_Trigger {

    public $integration = 'wishlist_member';
    public $trigger = 'wishlist_member_remove_level';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User gets removed from a level', 'automatorwp' ),
            'select_option'     => __( 'User gets <strong>removed</strong> from a level', 'automatorwp' ),
            /* translators: %1$s: Level. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User gets removed from %1$s %2$s time(s)', 'automatorwp' ), '{level}', '{times}' ),
            /* translators: %1$s: Level. */
            'log_label'         => sprintf( __( 'User gets removed from %1$s', 'automatorwp' ), '{level}' ),
            'action'            => 'wishlistmember_remove_user_levels',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'level' => array(
                    'from' => 'level',
                    'fields' => array(
                        'level' => array(
                            'name' => __( 'Level:', 'automatorwp' ),
                            'type' => 'select',
                            'options_cb' => 'automatorwp_wishlist_member_levels_options_cb',
                            'default' => 'any'
                        )
                    )
                ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int   $user_id    The user ID
     * @param array $levels_ids Levels removed to the user
     */
    public function listener( $user_id, $levels_ids ) {

        // Trigger the level removed
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'levels_ids'    => $levels_ids,
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

        // Don't deserve if levels IDs are not received
        if( ! isset( $event['levels_ids'] ) ) {
            return false;
        }

        // Don't deserve if level doesn't match with the trigger option
        if( $trigger_options['level'] !== 'any' && ! in_array( $trigger_options['level'], $event['levels_ids'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WishList_Member_Remove_Level();