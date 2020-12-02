<?php
/**
 * Create Post
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\Create_Post
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Create_Post extends AutomatorWP_Integration_Action {

    public $integration = 'wordpress';
    public $action = 'wordpress_create_post';

    /**
     * The new inserted post ID
     *
     * @since 1.0.0
     *
     * @var int|WP_Error $post_id
     */
    public $post_id = 0;

    /**
     * The post meta
     *
     * @since 1.0.0
     *
     * @var array $post_meta
     */
    public $post_meta = array();

    /**
     * Register required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log post ID
        add_filter( 'automatorwp_user_completed_action_post_id', array( $this, 'post_id' ), 10, 6 );

        // Log meta data
        add_filter( 'automatorwp_user_completed_action_log_meta', array( $this, 'log_meta' ), 10, 5 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

        parent::hooks();
    }

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        $post_type_options = array();

        foreach( get_post_types( array(), 'objects' ) as $post_type ) {
            /* translators: %1$s: Post type key (post, page). %2$s: Post type name (Post, Page). */
            $post_type_options[] = sprintf( __( '<code>%1$s</code> for %2$s', 'automatorwp' ), $post_type->name, $post_type->labels->name );
        }

        $post_status_options = array();

        foreach( get_post_statuses() as $post_status => $post_status_label ) {
            /* translators: %1$s: Post status key (draft, pending). %2$s: Post status label (Draft, Pending Review). */
            $post_status_options[] = sprintf( __( '<code>%1$s</code> for %2$s', 'automatorwp' ), $post_status, $post_status_label );
        }

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Create a post', 'automatorwp' ),
            'select_option'     => __( 'Create <strong>a post</strong>', 'automatorwp' ),
            /* translators: %1$s: Post. */
            'edit_label'        => sprintf( __( 'Create a %1$s', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Post. */
            'log_label'         => sprintf( __( 'Create a %1$s', 'automatorwp' ), '{post}' ),
            'options'           => array(
                'post' => array(
                    'default' => __( 'post', 'automatorwp' ),
                    'fields' => array(
                        'post_title' => array(
                            'name' => __( 'Title:', 'automatorwp' ),
                            'desc' => __( 'The post title.', 'automatorwp' ),
                            'type' => 'text',
                            'required'  => true,
                            'default' => ''
                        ),
                        'post_type' => array(
                            'name' => __( 'Type:', 'automatorwp' ),
                            'desc' => __( 'The post type. By default, "post".', 'automatorwp' )
                                . ' ' . automatorwp_toggleable_options_list( $post_type_options ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'post_status' => array(
                            'name' => __( 'Status:', 'automatorwp' ),
                            'desc' => __( 'The post status. By default, "draft".', 'automatorwp' )
                                . ' ' . automatorwp_toggleable_options_list( $post_status_options ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'post_date' => array(
                            'name' => __( 'Date:', 'automatorwp' ),
                            'desc' => __( 'The date of the post. Supports "YYYY-MM-DD HH:MM:SS" and "YYYY-MM-DD" formats. By default, the date at the moment the automation gets completed.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'post_author' => array(
                            'name' => __( 'Author:', 'automatorwp' ),
                            'desc' => __( 'The ID of the user who added this post. By default, ID of user who completes this automation.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'post_content' => array(
                            'name' => __( 'Content:', 'automatorwp' ),
                            'desc' => __( 'The post content. By default, empty.', 'automatorwp' ),
                            'type' => 'wysiwyg',
                            'default' => ''
                        ),
                        'post_excerpt' => array(
                            'name' => __( 'Excerpt:', 'automatorwp' ),
                            'desc' => __( 'The post excerpt. By default, empty.', 'automatorwp' ),
                            'type' => 'textarea',
                            'default' => ''
                        ),
                        'post_parent' => array(
                            'name' => __( 'Parent:', 'automatorwp' ),
                            'desc' => __( 'The post parent. By default, none.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'menu_order' => array(
                            'name' => __( 'Menu order:', 'automatorwp' ),
                            'desc' => __( 'The post menu order. By default, 0.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'post_password' => array(
                            'name' => __( 'Password:', 'automatorwp' ),
                            'desc' => __( 'The password to access this post. By default, empty.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'post_meta' => array(
                            'name' => __( 'Post Meta:', 'automatorwp' ),
                            'desc' => __( 'The post meta values keyed by their post meta key.', 'automatorwp' ),
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

        // Setup post data
        $post_data = wp_parse_args( $action_options, array(
            'post_title'    => '',
            'post_type'     => 'post',
            'post_status'   => 'draft',
            'post_date'     => '',
            'post_author'   => '',
            'post_content'  => '',
            'post_excerpt'  => '',
            'post_parent'   => '',
            'menu_order'    => '0',
            'post_password' => '',
        ) );

        // Format post date
        if( ! empty( $post_data['post_date'] ) ) {
            $post_data['post_date'] = date( 'Y-m-d H:i:s', strtotime( $post_data['post_date'] ) );
        }

        // Format post date
        if( absint( $post_data['post_author'] ) === 0 ) {
            $post_data['post_author'] = $user_id;
        }

        // Insert the post
        $this->post_id = wp_insert_post( $post_data );

        if( $this->post_id ) {

            if( is_array( $action_options['post_meta'] ) ) {

                foreach( $action_options['post_meta'] as $i => $meta ) {

                    // Parse automation tags replacements to both, key and value
                    $meta_key = automatorwp_parse_automation_tags( $automation->id, $user_id, $meta['meta_key'] );
                    $meta_value = automatorwp_parse_automation_tags( $automation->id, $user_id, $meta['meta_value'] );

                    // Sanitize
                    $meta_key = sanitize_text_field( $meta_key );
                    $meta_value = sanitize_text_field( $meta_value );

                    // Update post meta
                    update_post_meta( $this->post_id, $meta_key, $meta_value );

                    $this->post_meta[$meta_key] = $meta_value;

                    // Update action options to be passed on upcoming hooks
                    $action_options['post_meta'][$i] = array(
                        'meta_key' => $meta_key,
                        'meta_value' => $meta_value,
                    );

                }

            }

            /**
             * Action triggered before the create new user action gets executed
             *
             * @since 1.2.6
             *
             * @param int       $post_id            The new post ID
             * @param stdClass  $action             The action object
             * @param int       $user_id            The user ID (user who triggered the automation)
             * @param array     $action_options     The action's stored options (with tags already passed, included on meta keys and values)
             * @param stdClass  $automation         The action's automation object
             */
            do_action( 'automatorwp_wordpress_create_post_executed', $this->post_id, $action, $user_id, $action_options, $automation );

        }

    }

    /**
     * Action custom log post ID
     *
     * @since 1.0.0
     *
     * @param int       $post_id            The post ID, by default 0
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     *
     * @return int
     */
    public function post_id( $post_id, $action, $user_id, $event, $action_options, $automation ) {

        // Bail if action type don't match this action
        if( $action->type !== $this->action ) {
            return $post_id;
        }

        if( $this->post_id ) {
            $post_id = $this->post_id;
        }

        return $post_id;

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

        // Store post fields
        $post_fields = array(
            'post_title',
            'post_type',
            'post_status',
            'post_date',
            'post_author',
            'post_content',
            'post_excerpt',
            'post_parent',
            'menu_order',
            'post_password',
        );

        foreach( $post_fields as $post_field ) {
            $log_meta[$post_field] = $action_options[$post_field];
        }

        // Store post meta
        $log_meta['post_meta'] = $this->post_meta;

        // Store result
        if( $this->post_id ) {
            $log_meta['result'] = __( 'Post created correctly', 'automatorwp' );
        } else if( is_wp_error( $this->post_id ) ) {
            $log_meta['result'] = $this->post_id->get_error_message();
        } else {
            $log_meta['result'] = __( 'Could not create post', 'automatorwp' );
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

        $log_fields['post_info'] = array(
            'name' => __( 'Post Information', 'automatorwp' ),
            'desc' => __( 'Information about the post created.', 'automatorwp' ),
            'type' => 'title',
        );

        $log_fields['post_title'] = array(
            'name'      => __( 'Title:', 'automatorwp' ),
            'desc'      => __( 'The post title.', 'automatorwp' ),
            'type'      => 'text',
        );

        $log_fields['post_type'] = array(
            'name' => __( 'Type:', 'automatorwp' ),
            'desc' => __( 'The post type.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['post_status'] = array(
            'name' => __( 'Status:', 'automatorwp' ),
            'desc' => __( 'The post status.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['post_date'] = array(
            'name' => __( 'Date:', 'automatorwp' ),
            'desc' => __( 'The date of the post.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['post_author'] = array(
            'name' => __( 'Author:', 'automatorwp' ),
            'desc' => __( 'The ID of the user who added this post.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['post_content'] = array(
            'name' => __( 'Content:', 'automatorwp' ),
            'desc' => __( 'The post content.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['post_excerpt'] = array(
            'name' => __( 'Excerpt:', 'automatorwp' ),
            'desc' => __( 'The post excerpt.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['post_parent'] = array(
            'name' => __( 'Parent:', 'automatorwp' ),
            'desc' => __( 'The post parent.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['menu_order'] = array(
            'name' => __( 'Menu order:', 'automatorwp' ),
            'desc' => __( 'The post menu order.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['post_password'] = array(
            'name' => __( 'Password:', 'automatorwp' ),
            'desc' => __( 'The password to access this post.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['post_meta'] = array(
            'name' => __( 'Post Meta:', 'automatorwp' ),
            'desc' => __( 'The post meta values keyed by their post meta key.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;
    }

}

new AutomatorWP_WordPress_Create_Post();