<?php
/**
 * Create User
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\Create_User
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Create_User extends AutomatorWP_Integration_Action {

    public $integration = 'wordpress';
    public $action = 'wordpress_create_user';

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
            'label'             => __( 'Create a user', 'automatorwp' ),
            'select_option'     => __( 'Create <strong>a user</strong>', 'automatorwp' ),
            /* translators: %1$s: User. */
            'edit_label'        => sprintf( __( 'Create a %1$s', 'automatorwp' ), '{user}' ),
            /* translators: %1$s: User. */
            'log_label'         => sprintf( __( 'Create a %1$s', 'automatorwp' ), '{user}' ),
            'options'           => array(
                'user' => array(
                    'default' => __( 'user', 'automatorwp' ),
                    'fields' => array(
                        'user_login' => array(
                            'name' => __( 'Username:', 'automatorwp' ),
                            'desc' => __( 'The user\'s login username.', 'automatorwp' ),
                            'type' => 'text',
                            'required'  => true,
                            'default' => ''
                        ),
                        'user_email' => array(
                            'name' => __( 'Email:', 'automatorwp' ),
                            'desc' => __( 'The user email address.', 'automatorwp' ),
                            'type' => 'text',
                            'required'  => true,
                            'default' => ''
                        ),
                        'first_name' => array(
                            'name' => __( 'First Name:', 'automatorwp' ),
                            'desc' => __( 'The user\'s first name.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'last_name' => array(
                            'name' => __( 'Last Name:', 'automatorwp' ),
                            'desc' => __( 'The user\'s last name.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'user_url' => array(
                            'name' => __( 'Website:', 'automatorwp' ),
                            'desc' => __( 'The user URL.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'user_pass' => array(
                            'name' => __( 'Password:', 'automatorwp' ),
                            'desc' => __( 'The user password. Leave blank to get password will get automatically generated.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'role' => array(
                            'name' => __( 'Role:', 'automatorwp' ),
                            'desc' => __( 'The user\'s role. By default, "subscriber".', 'automatorwp' )
                                . ' ' . automatorwp_toggleable_options_list( $role_options ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'send_user_notification' => array(
                            'name' => __( 'Send User Notification:', 'automatorwp' ),
                            'desc' => __( 'Send the new user an email about their account.', 'automatorwp' ),
                            'type' => 'checkbox',
                            'classes' => 'cmb2-switch'
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

        // Setup user fields
        $user_data = wp_parse_args( $action_options, array(
            'user_login'    => '',
            'user_email'    => '',
            'first_name'    => '',
            'last_name'     => '',
            'user_url'      => '',
            'user_pass'     => '',
            'role'          => 'subscriber',
        ) );

        // Check the user role
        $roles = automatorwp_get_editable_roles();

        // Bail if empty role to assign
        if( ! isset( $roles[$user_data['role']] ) ) {
            $user_data['role'] = 'subscriber';
        }

        // Generate the user password
        if( empty( $user_data['user_pass'] ) ) {
            $user_data['user_pass'] = wp_generate_password( 24 );
        }

        // Insert the user
        $this->user_id = wp_insert_user( $user_data );

        if( $this->user_id ) {

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

            // Send notification
            $notify = 'admin';

            if( (bool) $action_options['send_user_notification'] ) {
                $notify = 'both';
            }

            wp_send_new_user_notifications( $this->user_id, $notify );

            /**
             * Action triggered before the create new user action gets executed
             *
             * @since 1.2.6
             *
             * @param int       $new_user_id        The new user ID
             * @param stdClass  $action             The action object
             * @param int       $user_id            The user ID (user who triggered the automation)
             * @param array     $action_options     The action's stored options (with tags already passed, included on meta keys and values)
             * @param stdClass  $automation         The action's automation object
             */
            do_action( 'automatorwp_wordpress_create_user_executed', $this->user_id, $action, $user_id, $action_options, $automation );

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

        // Store user meta
        $log_meta['user_meta'] = $this->user_meta;

        // Store result
        if( $this->user_id ) {
            $log_meta['result'] = __( 'User created correctly', 'automatorwp' );
        } else if( is_wp_error( $this->user_id ) ) {
            $log_meta['result'] = $this->user_id->get_error_message();
        } else {
            $log_meta['result'] = __( 'Could not create user', 'automatorwp' );
        }

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

new AutomatorWP_WordPress_Create_User();