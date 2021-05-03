<?php
/**
 * Anonymous User
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\Anonymous_User
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Anonymous_User extends AutomatorWP_Integration_Action {

    public $integration = 'automatorwp';
    public $action = 'automatorwp_anonymous_user';

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
     * Defines if new user has been created by this action
     *
     * @since 1.0.0
     *
     * @var bool $new_user
     */
    public $new_user = false;

    /**
     * The action result
     *
     * @since 1.0.0
     *
     * @var string $result
     */
    public $result = '';

    /**
     * The action result (detailed)
     *
     * @since 1.0.0
     *
     * @var array $detailed_result
     */
    public $detailed_result = '';

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
            'label'             => '',
            'select_option'     => '',
            /* translators: %1$s: User. */
            'edit_label'        => sprintf( __( 'Actions will be run on %1$s', 'automatorwp' ), '{user}' ),
            /* translators: %1$s: User. */
            'log_label'         => sprintf( __( 'Actions will be run on %1$s', 'automatorwp' ), '{user}' ),
            'options'           => array(
                'user' => array(
                    'from' => 'run_actions_on',
                    'fields' => array(
                        'run_actions_on' => array(
                            'name' => __( 'Run actions on:', 'automatorwp' ),
                            'type' => 'select',
                            'required'  => true,
                            'options' => array(
                                '' => '',
                                'existing_user' => __( 'Existing user', 'automatorwp' ),
                                'new_user' => __( 'New user', 'automatorwp' ),
                            ),
                            'default' => ''
                        ),

                        // Existing user fields

                        'search_field' => array(
                            'name' => __( 'Field to search the user:', 'automatorwp' ),
                            'desc' => __( 'The field by which to search the user.', 'automatorwp' ),
                            'type' => 'select',
                            'required'  => true,
                            'options' => array(
                                'id' => __( 'ID', 'automatorwp' ),
                                'email' => __( 'Email', 'automatorwp' ),
                                'login' => __( 'Username', 'automatorwp' ),
                            ),
                            'default' => 'id'
                        ),
                        'search_field_value' => array(
                            'name' => __( 'Field value:', 'automatorwp' ),
                            'desc' => __( 'Value of the field to search the user.', 'automatorwp' ),
                            'type' => 'text',
                            'required'  => true,
                            'default' => ''
                        ),
                        'existing_user_not_exists' => array(
                            'name' => __( 'What to do if the user doesn\'t exist:', 'automatorwp' ),
                            'type' => 'radio',
                            'required'  => true,
                            'options' => array(
                                'abort' => __( 'Do not run the actions', 'automatorwp' ),
                                'new_user' => __( 'Create a new user', 'automatorwp' ),
                            ),
                            'default' => 'abort',
                        ),

                        // New user fields

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
                        'new_user_exists' => array(
                            'name' => __( 'What to do if the user already exists:', 'automatorwp' ),
                            'type' => 'radio',
                            'required'  => true,
                            'options' => array(
                                'abort' => __( 'Do not run the actions', 'automatorwp' ),
                                'existing_user' => __( 'Select existing user', 'automatorwp' ),
                            ),
                            'default' => 'abort',
                        ),
                        'new_user_search_field' => array(
                            'name' => __( 'Field to search the user:', 'automatorwp' ),
                            'desc' => __( 'The field by which to search the user if during creation is detected that the user already exists.', 'automatorwp' ),
                            'type' => 'select',
                            'required'  => true,
                            'options' => array(
                                'email' => __( 'Email', 'automatorwp' ),
                                'login' => __( 'Username', 'automatorwp' ),
                            ),
                            'default' => 'email',
                        ),
                    )
                )
            ),
        ) );

    }

    /**
     * Register required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Dynamic edit label
        add_filter( 'automatorwp_get_anonymous_automation_user_id', array( $this, 'get_user_id' ), 10, 4 );

        // Dynamic edit label
        add_filter( 'automatorwp_parse_automation_item_edit_label', array( $this, 'dynamic_edit_label' ), 10, 5 );

        // Dynamic log label
        add_filter( 'automatorwp_parse_automation_item_log_label', array( $this, 'dynamic_log_label' ), 10, 5 );

        // Log meta data
        add_filter( 'automatorwp_user_completed_action_log_meta', array( $this, 'log_meta' ), 10, 5 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );
        add_filter( 'automatorwp_log_fields', array( $this, 'anonymous_log_fields' ), 10, 5 );

        parent::hooks();
    }

    /**
     * Decide to which user ID will be assigned to the automation
     *
     * @since 1.0.0
     *
     * @param int       $user_id            The user ID
     * @param stdClass  $action             The action object
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     *
     * @return int|false                    The user ID, false otherwise
     */
    public function get_user_id( $user_id, $action, $action_options, $automation ) {

        $run_actions_on = $action_options['run_actions_on'];
        $this->user_id = $user_id;
        $this->result = '';
        $this->detailed_result = '';
        $this->new_user = false;

        $user_fields_labels = array(
            'id' => __( 'ID', 'automatorwp' ),
            'email' => __( 'email', 'automatorwp' ),
            'login' => __( 'username', 'automatorwp' ),
        );

        // Bail if not defined where run the actions on
        if( ! in_array( $run_actions_on, array( 'existing_user', 'new_user' ) ) ) {
            return $this->user_id;
        }

        if( $run_actions_on === 'existing_user' ) {
            // Existing user

            // Shorthand
            $search_field = $action_options['search_field'];
            $search_field_value = $action_options['search_field_value'];
            $existing_user_not_exists = $action_options['existing_user_not_exists'];

            // Check that the search field is a correct one
            if( in_array( $search_field, array( 'id', 'email', 'login' ) ) ) {

                $user = get_user_by( $search_field, $search_field_value );

                // If user found, update the user ID and result
                if( $user ) {

                    /* translators: %1$s: User field (id, email or username). */
                    $this->result = sprintf( __( 'Existing user found by %1$s', 'automatorwp' ), $user_fields_labels[$search_field] );

                    /* translators: %1$s: User field (id, email or username). %2$s: User field value. */
                    $this->detailed_result = sprintf( __( 'Existing user found by the %1$s %2$s', 'automatorwp' ), $user_fields_labels[$search_field], $search_field_value );

                    // Assign the user found
                    $this->user_id = $user->ID;

                }

            }

            if( ! $this->user_id ) {

                // If user not found, decide if abort or create a new one
                switch( $existing_user_not_exists ) {
                    case 'new_user':
                        // Try to create a new user from the action options
                        $new_user_id = $this->create_user( $action, $action_options, $automation );

                        if( is_wp_error( $new_user_id ) ) {

                            /* translators: %1$s: User field (id, email or username). */
                            $this->result = sprintf( __( 'Existing user not found by %1$s, could not create user', 'automatorwp' ), $user_fields_labels[$search_field] );

                            /* translators: %1$s: User field (id, email or username). %2$s: User field value. */
                            $this->detailed_result = sprintf( __( 'Existing user not found by the %1$s %2$s, could not create user, reason: %3$s', 'automatorwp' ), $user_fields_labels[$search_field], $search_field_value, $new_user_id->get_error_message() );

                        } else{
                            /* translators: %1$s: User field (id, email or username). */
                            $this->result = sprintf( __( 'Existing user not found by %1$s, new user created', 'automatorwp' ), $user_fields_labels[$search_field] );

                            /* translators: %1$s: User field (id, email or username). %2$s: User field value. */
                            $this->detailed_result = sprintf( __( 'Existing user not found by the %1$s %2$s, new user created', 'automatorwp' ), $user_fields_labels[$search_field], $search_field_value );

                            // Assign the new created user
                            $this->user_id = $new_user_id;
                            $this->new_user = true;
                        }
                        break;
                    case 'abort':
                    default:
                        // Abort

                        /* translators: %1$s: User field (id, email or username). */
                        $this->result = sprintf( __( 'Existing user not found by %1$s, execution aborted', 'automatorwp' ), $user_fields_labels[$search_field] );

                        /* translators: %1$s: User field (id, email or username). %2$s: User field value. */
                        $this->detailed_result = sprintf( __( 'Existing user not found by the %1$s %2$s, execution aborted', 'automatorwp' ), $user_fields_labels[$search_field], $search_field_value );
                        break;
                }

            }

        }else if( $run_actions_on === 'new_user' ) {
            // New user action

            // Shorthand
            $new_user_exists = $action_options['new_user_exists'];
            $new_user_search_field = $action_options['new_user_search_field'];
            $found_by = 'email';

            // Check if the new user exists
            switch( $new_user_search_field ) {
                case 'login':
                    $user_id = username_exists( $action_options['user_login'] );
                    $found_by = 'login';

                    if( ! $user_id ) {
                        $user_id = email_exists( $action_options['user_email'] );
                        $found_by = 'email';
                    }
                    break;
                case 'email':
                default:
                    $user_id = email_exists( $action_options['user_email'] );
                    $found_by = 'email';

                    if( ! $user_id ) {
                        $user_id = username_exists( $action_options['user_login'] );
                        $found_by = 'login';
                    }
                    break;
            }

            if( $user_id ) {

                // If user found, decide if abort or use this one
                switch( $new_user_exists ) {
                    case 'existing_user':
                        /* translators: %1$s: User field (id, email or username). */
                        $this->result = sprintf( __( 'New user found by %1$s, selecting user', 'automatorwp' ), $user_fields_labels[$found_by] );

                        /* translators: %1$s: User field (id, email or username). %2$s: User field value. */
                        $this->detailed_result = sprintf( __( 'New user found by the %1$s %2$s, selecting user', 'automatorwp' ), $user_fields_labels[$found_by], $action_options['user_' . $found_by] );

                        // Assign the user found
                        $this->user_id = $user_id;
                        break;
                    case 'abort':
                    default:
                        // Abort

                        /* translators: %1$s: User field (id, email or username). */
                        $this->result = sprintf( __( 'New user found by %1$s, execution aborted', 'automatorwp' ), $user_fields_labels[$found_by] );

                        /* translators: %1$s: User field (id, email or username). %2$s: User field value. */
                        $this->detailed_result = sprintf( __( 'New user found by the %1$s %2$s, execution aborted', 'automatorwp' ), $user_fields_labels[$found_by], $action_options['user_' . $found_by] );

                    break;
                }

            } else {

                // Try to create a new user from the action options
                $new_user_id = $this->create_user( $action, $action_options, $automation );

                if( is_wp_error( $new_user_id ) ) {

                    $this->result = __( 'Could not create user', 'automatorwp' );

                    /* translators: %1$s: Error message. */
                    $this->detailed_result = sprintf( __( 'Could not create user, reason: %1$s', 'automatorwp' ), $new_user_id->get_error_message() );

                } else {

                    $this->result = __( 'New user created', 'automatorwp' );
                    $this->detailed_result = __( 'New user created', 'automatorwp' );

                    // Assign the new created user
                    $this->user_id = $new_user_id;
                    $this->new_user = true;

                }

            }

        }

        // Only insert a log entry if the user doesn't gets created
        if( ! $this->user_id ) {
            automatorwp_insert_log(
            array(
                'title'     => $this->result,
                'type'      => 'anonymous',
                'object_id' => $automation->id,
                'user_id'   => ( $this->user_id ?  $this->user_id : 0 ),
                'post_id'   => 0,
                'date'      => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
            ),
            array(
                'new_user'  => ( $this->new_user ? 1 : 0 ),
                'result'    => $this->detailed_result,
            ) );
        }

        return $this->user_id;

    }

    /**
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     *
     * @return int|WP_Error                    The new user ID, false otherwise
     */
    public function create_user( $action, $action_options, $automation ) {

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
        $user_id = wp_insert_user( $user_data );

        if( $user_id ) {

            if( is_array( $action_options['user_meta'] ) ) {

                foreach( $action_options['user_meta'] as $i => $meta ) {

                    // Parse automation tags replacements to both, key and value
                    $meta_key = automatorwp_parse_automation_tags( $automation->id, 0, $meta['meta_key'] );
                    $meta_value = automatorwp_parse_automation_tags( $automation->id, 0, $meta['meta_value'] );

                    // Sanitize
                    $meta_key = sanitize_text_field( $meta_key );
                    $meta_value = sanitize_text_field( $meta_value );

                    // Update user meta
                    update_user_meta( $user_id, $meta_key, $meta_value );

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

            wp_send_new_user_notifications( $user_id, $notify );

            /**
             * Action triggered before create the new user for an anonymous automation
             *
             * @since 1.3.0
             *
             * @param int       $user_id            The new user ID
             * @param stdClass  $action             The action object
             * @param array     $action_options     The action's stored options (with tags already passed, included on meta keys and values)
             * @param stdClass  $automation         The action's automation object
             */
            do_action( 'automatorwp_anonymous_user_created', $user_id, $action, $action_options, $automation );

        }

        return $user_id;

    }

    /**
     * Custom edit/log label
     *
     * @since 1.0.0
     *
     * @param string    $label      The edit label
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The item type (trigger|action)
     * @param string    $context    The context this function is executed
     * @param array     $type_args  The type parameters
     *
     * @return string
     */
    public function dynamic_edit_label( $label, $object, $item_type, $context, $type_args ) {

        // Bail if action type don't match this action
        if( $object->type !== $this->action ) {
            return $label;
        }

        // Get the operation value
        ct_setup_table( "automatorwp_{$item_type}s" );

        // Update the edit label
        if( $context === 'edit' ) {

            // Get the action info
            $run_actions_on = ct_get_object_meta( $object->id, 'run_actions_on', true );
            $search_field = ct_get_object_meta( $object->id, 'search_field', true );
            $search_field_value = ct_get_object_meta( $object->id, 'search_field_value', true );
            $existing_user_not_exists = ct_get_object_meta( $object->id, 'existing_user_not_exists', true );
            $user_email = ct_get_object_meta( $object->id, 'user_email', true );
            $new_user_exists = ct_get_object_meta( $object->id, 'new_user_exists', true );

            // Setup labels
            $run_actions_on_labels = array(
                '' => '',
                'existing_user' => __( 'Existing user', 'automatorwp' ),
                'new_user' => __( 'New user', 'automatorwp' ),
            );

            $run_actions_on_images = array(
                '' => '',
                'existing_user' => AUTOMATORWP_URL . 'assets/img/existing-user.svg',
                'new_user' => AUTOMATORWP_URL . 'assets/img/new-user.svg',
            );

            $run_actions_on_label = $run_actions_on_labels[$run_actions_on];

            $existing_user_no_exists_labels = array(
                '' => '',
                'abort' => __( 'Do not run the actions', 'automatorwp' ),
                'new_user' => __( 'Create a new user', 'automatorwp' ),
            );

            $existing_user_no_exists_label = strtolower( $existing_user_no_exists_labels[$existing_user_not_exists] );

            $new_user_exists_labels = array(
                '' => '',
                'abort' => __( 'Do not run the actions', 'automatorwp' ),
                'existing_user' => __( 'Select existing user', 'automatorwp' ),
            );

            $new_user_exists_label = strtolower( $new_user_exists_labels[$new_user_exists] );

            ob_start(); ?>
            <div class="automatorwp-anonymous-user-label"><?php _e( 'Actions will be run on...', 'automatorwp' ); ?></div>

            <div class="automatorwp-anonymous-user-resume" <?php if( $run_actions_on === '' ) : ?>style="display: none"<?php endif; ?>>

                <div><img src="<?php echo $run_actions_on_images[$run_actions_on]; ?>" alt="<?php echo $run_actions_on_label; ?>"><strong><?php echo $run_actions_on_label; ?></strong></div>

                <?php if( $run_actions_on === 'existing_user' ) : ?>

                    <div><?php printf( __( 'Where %1$s matches with %2$s', 'automatorwp' ), "<strong>{$search_field}</strong>", "<strong>{$search_field_value}</strong>" ); ?></div>
                    <div><?php printf( __( 'If user not found then %1$s', 'automatorwp' ), "<strong>{$existing_user_no_exists_label}</strong>" ); ?></div>

                <?php elseif( $run_actions_on === 'new_user' ) : ?>

                    <div><?php printf( __( 'With the email %1$s', 'automatorwp' ), "<strong>{$user_email}</strong>" ); ?></div>
                    <div><?php printf( __( 'If user already exists then %1$s', 'automatorwp' ), "<strong>{$new_user_exists_label}</strong>" ); ?></div>

                <?php endif; ?>

                <button type="button" class="button button-primary automatorwp-anonymous-user-change-button"><?php _e( 'Change', 'automatorwp' ); ?></button>
            </div>

            <div class="automatorwp-anonymous-user-choices" <?php if( $run_actions_on !== '' ) : ?>style="display: none"<?php endif; ?>>

                <div class="automatorwp-anonymous-user-choice automatorwp-anonymous-user-choice-existing-user" data-choice="existing_user">
                    <img src="<?php echo $run_actions_on_images['existing_user']; ?>" alt="">
                    <strong><?php _e( 'Existing user', 'automatorwp' ); ?></strong>
                </div>

                <div class="automatorwp-anonymous-user-choice automatorwp-anonymous-user-choice-new-user" data-choice="new_user">
                    <img src="<?php echo $run_actions_on_images['new_user']; ?>" alt="">
                    <strong><?php _e( 'New user', 'automatorwp' ); ?></strong>
                </div>

            </div>

            <?php $choices_html = ob_get_clean();

            return $choices_html;
        }

        ct_reset_setup_table();

        return $label;

    }

    /**
     * Custom log label
     *
     * @since 1.0.0
     *
     * @param string    $label      The edit label
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The item type (trigger|action)
     * @param string    $context    The context this function is executed
     * @param array     $type_args  The type parameters
     *
     * @return string
     */
    public function dynamic_log_label( $label, $object, $item_type, $context, $type_args ) {

        // Bail if action type don't match this action
        if( $object->type !== $this->action ) {
            return $label;
        }

        return $this->result;

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

        // Store all actions options
        foreach( $action_options as $option_key => $option_value ) {

            // Skip user meta
            if( $option_key === 'user_meta' ) {
                continue;
            }

            $log_meta[$option_key] = $option_value;
        }

        // Store user meta
        $log_meta['user_meta'] = $this->user_meta;

        // Store result
        $log_meta['user_id'] = ( $this->user_id ? $this->user_id : 0 );
        $log_meta['new_user'] = ( $this->new_user ? 1 : 0 );
        $log_meta['result'] = $this->detailed_result;

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

        $log_fields['run_actions_on'] = array(
            'name' => __( 'Run actions on:', 'automatorwp' ),
            'type' => 'select',
            'options' => array(
                '' => '',
                'existing_user' => __( 'Existing user', 'automatorwp' ),
                'new_user' => __( 'New user', 'automatorwp' ),
            ),
        );

        $run_actions_on = automatorwp_get_log_meta( $log->id, 'run_actions_on', true );

        if( $run_actions_on === 'existing_user' ) {

            $log_fields['search_field'] = array(
                'name' => __( 'Field to search the user:', 'automatorwp' ),
                'desc' => __( 'The field by which to search the user.', 'automatorwp' ),
                'type' => 'text',
            );

            $log_fields['search_field_value'] = array(
                'name' => __( 'Field value:', 'automatorwp' ),
                'desc' => __( 'Value of the field to search the user.', 'automatorwp' ),
                'type' => 'text',
            );

            $log_fields['existing_user_not_exists'] = array(
                'name' => __( 'What to do if the user doesn\'t exist:', 'automatorwp' ),
                'type' => 'select',
                'options' => array(
                    'abort' => __( 'Do not run the actions', 'automatorwp' ),
                    'new_user' => __( 'Create a new user', 'automatorwp' ),
                ),
            );

        }

        $new_user = automatorwp_get_log_meta( $log->id, 'new_user', true );

        if( $new_user ) {

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

        }

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;
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
    public function anonymous_log_fields( $log_fields, $log, $object ) {

        // Bail if log is not assigned to an anonymous entry
        if( $log->type !== 'anonymous' ) {
            return $log_fields;
        }

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;

    }

}

new AutomatorWP_WordPress_Anonymous_User();