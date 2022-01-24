<?php
/**
 * User Field Update
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\User_Field_Update
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_User_Field_Update extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_user_field_update';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User profile field gets updated', 'automatorwp' ),
            'select_option'     => __( 'User <strong>profile field</strong> gets updated', 'automatorwp' ),
            /* translators: %1$s: Profile Field. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User %1$s gets updated %2$s time(s)', 'automatorwp' ), '{profile_field}', '{times}' ),
            /* translators: %1$s: Profile Field. */
            'log_label'         => sprintf( __( 'User %1$s gets updated', 'automatorwp' ), '{profile_field}' ),
            'action'            => 'profile_update',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'profile_field' => array(
                    'from' => 'profile_field',
                    'default' => __( 'profile field', 'automatorwp' ),
                    'fields' => array(
                        'profile_field' => array(
                            'name' => __( 'Profile field:', 'automatorwp' ),
                            'type' => 'select',
                            'options' => array(
                                ''              => __( 'any profile field', 'automatorwp' ),
                                'user_login'    => __( 'Username', 'automatorwp' ),
                                'user_email'    => __( 'Email', 'automatorwp' ),
                                'display_name'  => __( 'Display name', 'automatorwp' ),
                                'user_nicename' => __( 'Nicename', 'automatorwp' ),
                                'user_pass'     => __( 'Password', 'automatorwp' ),
                                'user_url'      => __( 'Website', 'automatorwp' ),
                            ),
                            'default' => ''
                        ),
                    )
                ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                array(
                    'new_field_value' => array(
                        'label'     => __( 'New field value', 'automatorwp' ),
                        'type'      => 'text',
                        'preview'   => __( 'New value of the updated field', 'automatorwp' ),
                    ),
                    'old_field_value' => array(
                        'label'     => __( 'Old field value', 'automatorwp' ),
                        'type'      => 'text',
                        'preview'   => __( 'Old value of the updated field', 'automatorwp' ),
                    ),
                ),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int     $user_id       User ID.
     * @param WP_User $old_user_data Object containing user's data prior to update.
     */
    public function listener( $user_id, $old_user_data ) {

        // Setup vars
        $user_fields = array(
            'user_login',
            'user_email',
            'display_name',
            'user_nicename',
            'user_pass',
            'user_url',
        );

        $new_user_data = get_userdata( $user_id );

        foreach( $user_fields as $user_field ) {

            // Skip field if not updated
            if( $new_user_data->$user_field === $old_user_data->$user_field ) {
                continue;
            }

            automatorwp_trigger_event( array(
                'trigger'           => $this->trigger,
                'user_id'           => $user_id,
                'profile_field'     => $user_field,
                'new_field_value'   => $new_user_data->$user_field,
                'old_field_value'   => $old_user_data->$user_field,
            ) );

        }

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
        if( ! isset( $event['profile_field'] ) ) {
            return false;
        }

        // Don't deserve if profile field doesn't matches with the trigger option
        if( $trigger_options['profile_field'] !== '' && $trigger_options['profile_field'] !== $event['profile_field'] ) {
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

        // Tags replacement
        add_filter( 'automatorwp_get_trigger_tag_replacement', array( $this, 'tags_replacement' ), 10, 6 );

        // Log meta data
        add_filter( 'automatorwp_user_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 6 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

        parent::hooks();
    }

    /**
     * Trigger custom tags replacement
     *
     * @since 1.0.0
     *
     * @param string    $replacement    The tag replacement
     * @param string    $tag_name       The tag name (without "{}")
     * @param stdClass  $trigger        The trigger object
     * @param int       $user_id        The user ID
     * @param string    $content        The content to parse
     * @param stdClass  $log            The last trigger log object
     *
     * @return string
     */
    function tags_replacement( $replacement, $tag_name, $trigger, $user_id, $content, $log ) {

        // Bail if action type don't match this action
        if( $trigger->type !== $this->trigger ) {
            return $replacement;
        }

        switch( $tag_name ) {
            case 'new_field_value':
                $replacement = automatorwp_get_log_meta( $log->id, 'new_field_value', true );
                break;
            case 'old_field_value':
                $replacement = automatorwp_get_log_meta( $log->id, 'old_field_value', true );
                break;
        }

        return $replacement;

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

        $log_meta['profile_field'] = ( isset( $event['profile_field'] ) ? $event['profile_field'] : '' );
        $log_meta['new_field_value'] = ( isset( $event['new_field_value'] ) ? $event['new_field_value'] : '' );
        $log_meta['old_field_value'] = ( isset( $event['old_field_value'] ) ? $event['old_field_value'] : '' );

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

        // Bail if log is not assigned to an trigger
        if( $log->type !== 'trigger' ) {
            return $log_fields;
        }

        // Bail if trigger type don't match this trigger
        if( $object->type !== $this->trigger ) {
            return $log_fields;
        }

        $log_fields['profile_field'] = array(
            'name' => __( 'Profile field', 'automatorwp' ),
            'desc' => __( 'Profile field updated.', 'automatorwp' ),
            'type' => 'select',
            'options' => array(
                'user_login'    => __( 'Username', 'automatorwp' ),
                'user_email'    => __( 'Email', 'automatorwp' ),
                'display_name'  => __( 'Display name', 'automatorwp' ),
                'user_nicename'  => __( 'Nicename', 'automatorwp' ),
                'user_pass'     => __( 'Password', 'automatorwp' ),
                'user_url'      => __( 'Website', 'automatorwp' ),
            ),
        );

        $log_fields['new_field_value'] = array(
            'name' => __( 'New field value', 'automatorwp' ),
            'desc' => __( 'New value of the updated field.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['old_field_value'] = array(
            'name' => __( 'Old field value', 'automatorwp' ),
            'desc' => __( 'Old value of the updated field.', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;

    }

}

new AutomatorWP_WordPress_User_Field_Update();