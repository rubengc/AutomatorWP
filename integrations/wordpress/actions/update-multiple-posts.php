<?php
/**
 * Update Multiple Posts
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\Update_Multiple_Posts
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Update_Multiple_Posts extends AutomatorWP_Integration_Action {

    public $integration = 'wordpress';
    public $action = 'wordpress_update_multiple_posts';

    /**
     * The post field conditions
     *
     * @since 1.0.0
     *
     * @var array $field_conditions
     */
    public $field_conditions = array();

    /**
     * The post meta conditions
     *
     * @since 1.0.0
     *
     * @var array $meta_conditions
     */
    public $meta_conditions = array();

    /**
     * The post meta
     *
     * @since 1.0.0
     *
     * @var array $post_meta
     */
    public $post_meta = array();

    /**
     * Store the action result
     *
     * @since 1.0.0
     *
     * @var string $result
     */
    public $result = '';

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
            'label'             => __( 'Update multiple posts', 'automatorwp' ),
            'select_option'     => __( 'Update <strong>multiple posts</strong>', 'automatorwp' ),
            /* translators: %1$s: Multiple Posts.  %2$s: Data. */
            'edit_label'        => sprintf( __( 'Update %1$s with %2$s', 'automatorwp' ), '{conditions}', '{post}' ),
            /* translators: %1$s: Multiple Posts. %2$s: Data. */
            'log_label'         => sprintf( __( 'Update %1$s with %2$s', 'automatorwp' ), '{conditions}', '{post}' ),
            'options'           => array(
                'conditions' => array(
                    'default' => __( 'multiple posts', 'automatorwp' ),
                    'fields' => array(
                        'post_field_conditions' => array(
                            'name' => __( 'Field Conditions:', 'automatorwp' ),
                            'desc' => __( 'Set conditions to filter posts to update by post fields.', 'automatorwp' ),
                            'type' => 'group',
                            'classes' => 'automatorwp-fields-table',
                            'options'     => array(
                                'add_button'        => __( 'Add condition', 'automatorwp' ),
                                'remove_button'     => '<span class="dashicons dashicons-no-alt"></span>',
                            ),
                            'fields' => array(
                                'field' => array(
                                    'name' => __( 'Field:', 'automatorwp' ),
                                    'type' => 'select',
                                    'options_cb' => 'automatorwp_options_cb_post_fields',
                                    'option_none' => true,
                                    'option_none_value' => '',
                                    'option_none_label' => __( 'Choose a field', 'automatorwp' ),
                                    'default' => ''
                                ),
                                'condition' => automatorwp_utilities_condition_field(),
                                'value' => array(
                                    'name' => __( 'Value:', 'automatorwp' ),
                                    'type' => 'text',
                                    'default' => ''
                                ),
                            ),
                        ),
                        'post_meta_conditions' => array(
                            'name' => __( 'Meta Conditions:', 'automatorwp' ),
                            'desc' => __( 'Set conditions to filter posts to update by post metas.', 'automatorwp' ),
                            'type' => 'group',
                            'classes' => 'automatorwp-fields-table',
                            'options'     => array(
                                'add_button'        => __( 'Add condition', 'automatorwp' ),
                                'remove_button'     => '<span class="dashicons dashicons-no-alt"></span>',
                            ),
                            'fields' => array(
                                'meta_key' => array(
                                    'name' => __( 'Meta Key:', 'automatorwp' ),
                                    'type' => 'text',
                                    'default' => ''
                                ),
                                'condition' => automatorwp_utilities_condition_field(),
                                'meta_value' => array(
                                    'name' => __( 'Meta Value:', 'automatorwp' ),
                                    'type' => 'text',
                                    'default' => ''
                                ),
                            ),
                        ),
                    )
                ),
                'post' => array(
                    'default' => __( 'data', 'automatorwp' ),
                    'fields' => array(
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

        $this->result = '';
        $this->field_conditions = array();
        $this->meta_conditions = array();
        $this->post_meta = array();

        global $wpdb;

        // Filter the posts
        $field_conditions = $action_options['post_field_conditions'];
        $meta_conditions = $action_options['post_meta_conditions'];

        $joins      = array();
        $where      = array();

        // Setup the post field conditions
        foreach( $field_conditions as $condition ) {

            // Parse automation tags replacements to both, key and value
            $field = automatorwp_parse_automation_tags( $automation->id, $user_id, $condition['field'] );
            $value = automatorwp_parse_automation_tags( $automation->id, $user_id, $condition['value'] );

            // Sanitize
            $field = sanitize_text_field( $field );
            $value = sanitize_text_field( $value );

            if( ! empty( $field ) ) {
                $where[] = automatorwp_utilities_parse_condition_to_sql( 'p.' . $field, $condition['condition'], $value );

                $this->field_conditions[] = array(
                    'field' => $field,
                    'condition' => $condition['condition'],
                    'value' => $value,
                );
            }
        }

        // Setup the post meta conditions
        foreach( $meta_conditions as $condition ) {

            // Parse automation tags replacements to both, key and value
            $meta_key = automatorwp_parse_automation_tags( $automation->id, $user_id, $condition['meta_key'] );
            $meta_value = automatorwp_parse_automation_tags( $automation->id, $user_id, $condition['meta_value'] );

            // Sanitize
            $meta_key = sanitize_text_field( $meta_key );
            $meta_value = sanitize_text_field( $meta_value );

            if( ! empty( $meta_key ) ) {
                $index = count( $joins );

                $joins[] = "INNER JOIN {$wpdb->postmeta} AS pm{$index} ON ( pm{$index}.post_id = p.ID AND pm{$index}.meta_key = '{$meta_key}' )";

                $where[] = automatorwp_utilities_parse_condition_to_sql( "pm{$index}.meta_value", $condition['condition'], $meta_value, false );

                $this->meta_conditions[] = array(
                    'meta_key' => $meta_key,
                    'condition' => $condition['condition'],
                    'meta_value' => $meta_value,
                );
            }
        }

        // Turn arrays into strings
        $joins = implode( ' ', $joins );
        $where = ( ! empty( $where ) ? 'WHERE ( ' . implode( ' ) AND ( ', $where ) . ' ) ' : '' );

        $sql = "SELECT p.ID
            FROM {$wpdb->posts} AS p
            {$joins}
            {$where}";

        $post_ids = $wpdb->get_col( $sql );

        // Bail if not posts found
        if( count( $post_ids ) === 0 ) {
            $this->result = __( 'No posts found to be updated.', 'automatorwp' );
            return;
        }

        // Parse the metas to update
        if( is_array( $action_options['post_meta'] ) ) {

            foreach( $action_options['post_meta'] as $i => $meta ) {

                // Parse automation tags replacements to both, key and value
                $meta_key = automatorwp_parse_automation_tags( $automation->id, $user_id, $meta['meta_key'] );
                $meta_value = automatorwp_parse_automation_tags( $automation->id, $user_id, $meta['meta_value'] );

                // Sanitize
                $meta_key = sanitize_text_field( $meta_key );
                $meta_value = sanitize_text_field( $meta_value );

                $this->post_meta[$meta_key] = $meta_value;

                // Update action options to be passed on upcoming hooks
                $action_options['post_meta'][$i] = array(
                    'meta_key' => $meta_key,
                    'meta_value' => $meta_value,
                );

            }

        }

        // Update the posts
        foreach( $post_ids as $post_id ) {

            // Setup post data
            $post_data = array();

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

            $post_data['ID'] = $post_id;

            // Update the post
            wp_update_post( $post_data );

            // Update the post metas
            foreach( $this->post_meta as $meta_key => $meta_value ) {
                update_post_meta( $post_id, $meta_key, $meta_value );
            }
        }

        $this->result = sprintf( __( '%d posts updated.', 'automatorwp' ), count( $post_ids ) );

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

        // Store the filters applied
        $log_meta['field_conditions'] = $this->field_conditions;
        $log_meta['field_conditions_parsed'] = '';

        foreach( $this->field_conditions as $condition ) {
            $log_meta['field_conditions_parsed'] .= sprintf( '%s %s %s',
                $condition['field'],
                automatorwp_utilities_get_condition_label( $condition['condition'] ),
                $condition['value']
            ) . "<br>";
        }

        $log_meta['meta_conditions'] = $this->meta_conditions;
        $log_meta['meta_conditions_parsed'] = '';

        foreach( $this->meta_conditions as $condition ) {
            $log_meta['meta_conditions_parsed'] .= sprintf( '%s %s %s',
                $condition['meta_key'],
                automatorwp_utilities_get_condition_label( $condition['condition'] ),
                $condition['meta_value']
            ) . "<br>";
        }

        // Store result
        $log_meta['result'] = $this->result;

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

        $log_fields['posts_found_info'] = array(
            'name' => __( 'Posts Filtered', 'automatorwp' ),
            'desc' => __( 'Information about the filters applied and the posts found.', 'automatorwp' ),
            'type' => 'title',
        );

        $log_fields['field_conditions_parsed'] = array(
            'name' => __( 'Field conditions:', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['meta_conditions_parsed'] = array(
            'name' => __( 'Meta conditions:', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['post_info'] = array(
            'name' => __( 'Data Updated', 'automatorwp' ),
            'desc' => __( 'Information about the data updated to all posts found.', 'automatorwp' ),
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

        return $log_fields;
    }

}

new AutomatorWP_WordPress_Update_Multiple_Posts();