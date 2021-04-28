<?php
/**
 * Send Message
 *
 * @package     AutomatorWP\Integrations\WPAdverts\Triggers\Send_Message
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WPAdverts_Send_Message extends AutomatorWP_Integration_Trigger {

    public $integration = 'wpadverts';
    public $trigger = 'wpadverts_send_message';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User sends a message to an advert author', 'automatorwp' ),
            'select_option'     => __( 'User <strong>sends a message</strong> to an advert author', 'automatorwp' ),
            /* translators: %1$s: Post Title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User sends a message to %1$s author time(s)', 'automatorwp' ), '{post}', '{times}' ),
            /* translators: %1$s: Post Title. */
            'log_label'         => sprintf( __( 'User sends a message to %1$s author', 'automatorwp' ), '{post}' ),
            'action'            => 'adext_contact_form_send',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Advert:', 'automatorwp' ),
                    'option_none_label' => __( 'any advert', 'automatorwp' ),
                    'post_type' => 'advert'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags( __( 'Advert', 'automatorwp' ) ),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int           $post_id
     * @param Adverts_Form  $form
     */
    public function listener( $post_id, $form ) {

        $post = get_post( $post_id );

        // Bail if post not exists
        if( ! $post ) {
            return;
        }

        // Bail if not is an advert
        if( $post->post_type !== 'advert' ) {
            return;
        }

        // First try to get the user from the message email
        $email = $form->get_value( 'message_email' );
        $user = get_user_by_email( $email );

        if( $user ) {
            $user_id = $user->ID;
        }

        if( $user_id === 0 ) {
            $user_id = get_current_user_id();
        }

        // Bail if can't find the user ID
        if( $user_id === 0 ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger' => $this->trigger,
            'user_id' => $user_id,
            'post_id' => $post->ID,
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
        if( ! isset( $event['post_id'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['post_id'], $trigger_options['post'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WPAdverts_Send_Message();