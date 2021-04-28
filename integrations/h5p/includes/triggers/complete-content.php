<?php
/**
 * Complete Content
 *
 * @package     AutomatorWP\Integrations\H5P\Triggers\Complete_Content
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_H5P_Complete_Content extends AutomatorWP_Integration_Trigger {

    public $integration = 'h5p';
    public $trigger = 'h5p_complete_content';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User completes a content', 'automatorwp' ),
            'select_option'     => __( 'User completes <strong>a content</strong>', 'automatorwp' ),
            /* translators: %1$s: Content title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User completes %1$s %2$s time(s)', 'automatorwp' ), '{content}', '{times}' ),
            /* translators: %1$s: Content title. */
            'log_label'         => sprintf( __( 'User completes %1$s', 'automatorwp' ), '{content}' ),
            'action'            => 'h5p_alter_user_result',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 4,
            'options'           => array(
                'content' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'content',
                    'name'              => __( 'Content:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any content', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_h5p_get_contents',
                    'options_cb'        => 'automatorwp_h5p_options_cb_content',
                    'default'           => 'any'
                ) ),
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
     * @param object    $data      Has the following properties score,max_score,opened,finished,time
     * @param int       $result_id  Only set if updating result
     * @param int       $content_id Identifier of the H5P Content
     * @param int       $user_id    Identifier of the User
     */
    public function listener( $data, $result_id, $content_id, $user_id ) {

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'content_id'    => $content_id,
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

        // Don't deserve if content is not received
        if( ! isset( $event['content_id'] ) ) {
            return false;
        }

        $content_id = absint( $event['content_id'] );

        // Don't deserve if content doesn't exists
        if( $content_id === 0 ) {
            return false;
        }

        $required_content_id = absint( $trigger_options['content'] );

        // Don't deserve if content doesn't match with the trigger option
        if( $required_content_id !== 0 && $content_id !== $required_content_id ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_H5P_Complete_Content();