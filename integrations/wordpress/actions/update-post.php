<?php
/**
 * Update Post
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\Update_Post
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Update_Post extends AutomatorWP_Integration_Action {

    public $integration = 'wordpress';
    public $action = 'wordpress_update_post';

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
            'label'             => __( 'Update a post', 'automatorwp' ),
            'select_option'     => __( 'Update <strong>a post</strong>', 'automatorwp' ),
            /* translators: %1$s: Post. */
            'edit_label'        => sprintf( __( 'Update %1$s', 'automatorwp' ), '{post}' ),
            /* translators: %1$s: Post. */
            'log_label'         => sprintf( __( 'Update %1$s', 'automatorwp' ), '{post}' ),
            'options'           => array(
                'post' => array(
                    'default' => __( 'a post', 'automatorwp' ),
                    'from' => 'post_id',
                    'fields' => array(
                        'post_id' => automatorwp_utilities_post_field( array(
                            'name'                  => __( 'Post to update:', 'automatorwp' ),
                            'post_type'             => 'any',
                            'placeholder'           => __( 'Select a post', 'automatorwp' ),
                            'option_none_label'     => __( 'a post', 'automatorwp' ),
                            'option_custom'         => true,
                            'option_custom_desc'    => __( 'Post ID', 'automatorwp' ),
                        ) ),
                        'post_id_custom' => automatorwp_utilities_custom_field( array(
                            'option_custom_desc'    => __( 'Post ID', 'automatorwp' ),
                        ) ),
                        'post_title' => array(
                            'name' => __( 'Title:', 'automatorwp' ),
                            'desc' => __( 'The post title.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'post_name' => array(
                            'name' => __( 'URL slug:', 'automatorwp' ),
                            'desc' => __( 'The last part of the URL.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' )
                                . ' ' . sprintf( __( '<a href="" target="_blank">Read about permalinks</a>', 'automatorwp' ), 'https://wordpress.org/support/article/writing-posts/#post-field-descriptions' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'post_type' => array(
                            'name' => __( 'Type:', 'automatorwp' ),
                            'desc' => __( 'The post type.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' )
                                . ' ' . automatorwp_toggleable_options_list( $post_type_options ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'post_status' => array(
                            'name' => __( 'Status:', 'automatorwp' ),
                            'desc' => __( 'The post status.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' )
                                . ' ' . automatorwp_toggleable_options_list( $post_status_options ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'post_date' => array(
                            'name' => __( 'Date:', 'automatorwp' ),
                            'desc' => __( 'The date of the post. Supports "YYYY-MM-DD HH:MM:SS" and "YYYY-MM-DD" formats.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'post_author' => array(
                            'name' => __( 'Author:', 'automatorwp' ),
                            'desc' => __( 'The ID of the user who added this post.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'post_content' => array(
                            'name' => __( 'Content:', 'automatorwp' ),
                            'desc' => __( 'The post content.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' ),
                            'type' => 'wysiwyg',
                            'default' => ''
                        ),
                        'post_excerpt' => array(
                            'name' => __( 'Excerpt:', 'automatorwp' ),
                            'desc' => __( 'The post excerpt.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' ),
                            'type' => 'textarea',
                            'default' => ''
                        ),
                        'post_parent' => array(
                            'name' => __( 'Parent:', 'automatorwp' ),
                            'desc' => __( 'The post parent.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'menu_order' => array(
                            'name' => __( 'Menu order:', 'automatorwp' ),
                            'desc' => __( 'The post menu order.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'post_password' => array(
                            'name' => __( 'Password:', 'automatorwp' ),
                            'desc' => __( 'The password to access this post.', 'automatorwp' )
                                . ' ' . __( 'Leave empty to no update this field.', 'automatorwp' ),
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

        $post_id = absint( $action_options['post_id'] );
        $this->post_id = $post_id;

        // Bail if not post ID provided
        if( $post_id === 0 ) {
            return;
        }

        // Setup post data
        $post_data = array(
            'ID'    => $post_id,
        );

        $post_fields = array(
            'post_title',
            'post_name',
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
            if( ! empty( $action_options[$post_field] ) ) {
                $post_data[$post_field] = $action_options[$post_field];
            }
        }

        $post_data['ID'] = $this->post_id;

        // Update the post
        wp_update_post( $post_data );

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
            'post_name',
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
            $log_meta['result'] = __( 'Post updated successfully', 'automatorwp' );
        } else if( is_wp_error( $this->post_id ) ) {
            $log_meta['result'] = $this->post_id->get_error_message();
        } else {
            $log_meta['result'] = __( 'Could not update the post', 'automatorwp' );
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

        $log_fields['post_name'] = array(
            'name'      => __( 'URL slug:', 'automatorwp' ),
            'desc'      => __( 'The post URL slug.', 'automatorwp' ),
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

new AutomatorWP_WordPress_Update_Post();