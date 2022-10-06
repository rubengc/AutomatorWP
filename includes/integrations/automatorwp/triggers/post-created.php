<?php
/**
 * Post Created
 *
 * @package     AutomatorWP\Integrations\AutomatorWP\Triggers\Post_Created
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_AutomatorWP_Post_Created extends AutomatorWP_Integration_Trigger {

    public $integration = 'automatorwp';
    public $trigger = 'automatorwp_post_created';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'Post gets created through an automation', 'automatorwp' ),
            'select_option'     => __( 'Post gets created through <strong>an automation</strong>', 'automatorwp' ),
            /* translators: %1$s: Automation title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'Post gets created through %1$s %2$s time(s)', 'automatorwp' ), '{automation}', '{times}' ),
            /* translators: %1$s: Automation title. */
            'log_label'         => sprintf( __( 'Post gets created through %1$s', 'automatorwp' ), '{automation}' ),
            'action'            => 'automatorwp_wordpress_create_post_executed',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 5,
            'options'           => array(
                'automation' => automatorwp_utilities_automation_option(),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int       $post_id            The post ID
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID (user who triggered the automation)
     * @param array     $action_options     The action's stored options (with tags already passed, included on meta keys and values)
     * @param stdClass  $automation         The action's automation object
     */
    public function listener( $post_id, $action, $user_id, $action_options, $automation ) {

        $post = get_post( $post_id );

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'post_id' => $post_id,
            'user_id' => $post->post_author,
            'automation_id' => $automation->id,
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

        // Don't deserve if automation is not received
        if( ! isset( $event['automation_id'] ) ) {
            return false;
        }

        $automation = automatorwp_get_automation_object( absint( $event['automation_id'] ) );

        // Don't deserve if automation doesn't exists
        if( ! $automation ) {
            return false;
        }

        $automation_id = absint( $trigger_options['automation'] );

        // Don't deserve if automation doesn't match with the trigger option
        if( $automation_id !== 0 && absint( $automation->id ) !== $automation_id ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_AutomatorWP_Post_Created();