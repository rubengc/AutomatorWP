<?php
/**
 * Update User
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\Update_User
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Update_User extends AutomatorWP_Integration_Action {

    public $integration = 'wordpress';
    public $action = 'wordpress_update_user';

    /**
     * The new inserted user ID
     *
     * @since 1.0.0
     *
     * @var int|WP_Error $user_id
     */
    public $user_id = 0;

    /**
     * The post meta
     *
     * @since 1.0.0
     *
     * @var array $user_meta
     */
    public $user_meta = array();

    /**
     * The action result
     *
     * @since 1.0.0
     *
     * @var array $result
     */
    public $result = array();

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        $role_options = array();
        $editable_roles = apply_filters( 'editable_roles', wp_roles()->roles );

        foreach( $editable_roles as $role => $details ) {
            /* translators: %1$s: Role key (subscriber, editor). %2$s: Role name (Subscriber, Editor). */
            $role_options[] = sprintf( __( '<code>%1$s</code> for %2$s', 'automatorwp' ), $role, translate_user_role( $details['name'] ) );
        }

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Update a user', 'automatorwp' ),
            'select_option'     => __( 'Update a <strong>user</strong>', 'automatorwp' ),
            /* translators: %1$s: User. */
            'edit_label'        => sprintf( __( 'Update a %1$s', 'automatorwp' ), '{user}' ),
            /* translators: %1$s: User. */
            'log_label'         => sprintf( __( 'Update a %1$s', 'automatorwp' ), '{user}' ),
            'options'           => array(
                'user' => array(
                    'default' => __( 'user', 'automatorwp' ),
                    'fields' => array(
                        'user_id' => array(
                            'name' => __( 'User ID:', 'automatorwp' ),
                            'desc' => __( 'The user\'s ID to update.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to use the ID of the user that completes the automation.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'user_login' => array(
                            'name' => __( 'Username:', 'automatorwp' ),
                            'desc' => __( 'The user\'s login username.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'user_email' => array(
                            'name' => __( 'Email:', 'automatorwp' ),
                            'desc' => __( 'The user email address.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'first_name' => array(
                            'name' => __( 'First Name:', 'automatorwp' ),
                            'desc' => __( 'The user\'s first name.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'last_name' => array(
                            'name' => __( 'Last Name:', 'automatorwp' ),
                            'desc' => __( 'The user\'s last name.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'user_url' => array(
                            'name' => __( 'Website:', 'automatorwp' ),
                            'desc' => __( 'The user URL.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'user_pass' => array(
                            'name' => __( 'Password:', 'automatorwp' ),
                            'desc' => __( 'The user password.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'role' => array(
                            'name' => __( 'Role:', 'automatorwp' ),
                            'desc' => __( 'The user\'s role.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' )
                                . ' ' . automatorwp_toggleable_options_list( $role_options ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'user_meta' => array(
                            'name' => __( 'User Meta:', 'automatorwp' ),
                            'desc' => __( 'The user meta values keyed by their user meta key.', 'automatorwp' ),
                            'type' => 'group',
                            'classes' => 'automatorwp-fields-table',
                            'options'     => array(
                                'add_button'        => __( 'Add meta', 'automatorwp' ),
                                'remove_button'     => '<span class="dashicons dashicons-no-alt"></span>',
                            ),
                            'fields' => array(
                                'meta_key' => array(
                                    'name' => __( 'Meta Key:', 'automatorwp' ),
                                    'type' => 'text',
                                    'default' => ''
                                ),
                                'meta_value' => array(
                                    'name' => __( 'Meta Value:', 'automatorwp' ),
                                    'type' => 'text',
                                    'default' => ''
                                ),
                            ),
                        ),
                    )
                )
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

        global $wpdb;

        // Shorthand
        $user_login = $action_options['user_login'];
        $user_email = $action_options['user_email'];
        $role = $action_options['role'];

        $user_data = array();
        $this->result = array();


        // User ID
        if( empty( $action_options['user_id'] ) ) {
            $action_options['user_id'] = $user_id;
        }

        $this->user_id = absint( $action_options['user_id'] );
        $user = get_userdata( $this->user_id );

        // Bail if could not find the user
        if( ! $user ) {
            $this->result[] = sprintf( __( 'User not found by the ID %1$s.', 'automatorwp' ), $this->user_id );
            return;
        }

        $user_data['ID'] = $this->user_id;

        // User login
        if( ! empty( $user_login ) ) {
            if ( ! validate_username( $user_login ) ) {
                $this->result[] = sprintf( __( 'Invalid username: %1$s.', 'automatorwp' ), $user_login );
            } elseif ( username_exists( $user_login ) !== false && username_exists( $user_login ) !== $this->user_id ) {
                $this->result[] = sprintf( __( 'Username "%1$s" already exists.', 'automatorwp' ), $user_login );
            } else {
                // Update the user login
                $wpdb->update( $wpdb->users, array( 'user_login' => $user_login ), array( 'ID' => $this->user_id ) );
            }
        }

        // User email
        if( ! empty( $user_email ) ) {
            if ( ! is_email( $user_email ) ) {
                $this->result[] = sprintf( __( 'Invalid email address: %1$s.', 'automatorwp' ), $user_email );
            } elseif ( email_exists( $user_email ) !== false && email_exists( $user_email ) !== $this->user_id ) {
                $this->result[] = sprintf( __( 'Email address "%1$s" already exists.', 'automatorwp' ), $user_email );
            } else {
                $user_data['user_email'] = $user_email;
            }
        }

        // Role
        $roles = automatorwp_get_editable_roles();

        if( ! isset( $roles[$role] ) ) {
            $this->result[] = sprintf( __( 'Invalid role: %1$s.', 'automatorwp' ), $role );
        } else {
            $user_data['role'] = $role;
        }

        // The rest fields
        $user_fields = array(
            'first_name',
            'last_name',
            'user_url',
            'user_pass',
        );

        foreach( $user_fields as $user_field ) {
            if( ! empty( $action_options[$user_field] ) ) {
                $user_data[$user_field] = $action_options[$user_field];
            }
        }

        // User meta
        if( is_array( $action_options['user_meta'] ) ) {

            foreach( $action_options['user_meta'] as $i => $meta ) {

                // Parse automation tags replacements to both, key and value
                $meta_key = automatorwp_parse_automation_tags( $automation->id, $user_id, $meta['meta_key'] );
                $meta_value = automatorwp_parse_automation_tags( $automation->id, $user_id, $meta['meta_value'] );

                // Sanitize
                $meta_key = sanitize_text_field( $meta_key );
                $meta_value = sanitize_text_field( $meta_value );

                // Update user meta
                update_user_meta( $this->user_id, $meta_key, $meta_value );

                $this->user_meta[$meta_key] = $meta_value;

                // Update action options to be passed on upcoming hooks
                $action_options['user_meta'][$i] = array(
                    'meta_key' => $meta_key,
                    'meta_value' => $meta_value,
                );

            }

        }

        // Update the user
        $update_user = wp_update_user( $user_data );

        if ( is_wp_error( $update_user ) ) {
            $this->result[] = sprintf( __( 'Failed to update user, reason: %1$s', 'automatorwp' ), $update_user->get_error_message() );
        }

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

        // Store user fields
        $user_fields = array(
            'user_login',
            'user_email',
            'first_name',
            'last_name',
            'user_url',
            'user_pass',
            'role',
        );

        foreach( $user_fields as $user_field ) {
            $log_meta[$user_field] = $action_options[$user_field];
        }

        // Store user ID
        $log_meta['user_id'] = $this->user_id;

        // Store user meta
        $log_meta['user_meta'] = $this->user_meta;

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

        $log_fields['user_info'] = array(
            'name' => __( 'User Information', 'automatorwp' ),
            'desc' => __( 'Information about the user created.', 'automatorwp' ),
            'type' => 'title',
        );

        $log_fields['user_login'] = array(
            'name' => __( 'User ID:', 'automatorwp' ),
            'desc' => __( 'The user\'s ID updated.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['user_login'] = array(
            'name' => __( 'Username:', 'automatorwp' ),
            'desc' => __( 'The user\'s login username.', 'automatorwp' ),
            'type' => 'text',
        );

         $log_fields['user_email'] = array(
            'name' => __( 'Email:', 'automatorwp' ),
            'desc' => __( 'The user email address.', 'automatorwp' ),
            'type' => 'text',
            'default' => ''
        );

         $log_fields['first_name'] = array(
            'name' => __( 'First Name:', 'automatorwp' ),
            'desc' => __( 'The user\'s first name.', 'automatorwp' ),
            'type' => 'text',
            'default' => ''
        );

         $log_fields['last_name'] = array(
            'name' => __( 'Last Name:', 'automatorwp' ),
            'desc' => __( 'The user\'s last name.', 'automatorwp' ),
            'type' => 'text',
            'default' => ''
        );

         $log_fields['user_url'] = array(
            'name' => __( 'Website:', 'automatorwp' ),
            'desc' => __( 'The user URL.', 'automatorwp' ),
            'type' => 'text',
            'default' => ''
        );

         $log_fields['user_pass'] = array(
            'name' => __( 'Password:', 'automatorwp' ),
            'desc' => __( 'The user password.', 'automatorwp' ),
            'type' => 'text',
            'default' => ''
        );

         $log_fields['role'] = array(
            'name' => __( 'Role:', 'automatorwp' ),
            'desc' => __( 'The user\'s role.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['user_meta'] = array(
            'name' => __( 'User Meta:', 'automatorwp' ),
            'desc' => __( 'The user meta values keyed by their user meta key.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;
    }

}

new AutomatorWP_WordPress_Update_User();