<?php
/**
 * Post Meta Update
 *
 * @package     AutomatorWP\Integrations\WordPress\Triggers\Post_Meta_Update
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Post_Meta_Update extends AutomatorWP_Integration_Trigger {

    public $integration = 'wordpress';
    public $trigger = 'wordpress_post_meta_update';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'Post meta gets updated with a value', 'automatorwp' ),
            'select_option'     => __( 'Post <strong>meta gets updated</strong> with a value', 'automatorwp' ),
            /* translators: %1$s: Post title. %2$s: Key. %3$s: Value. %4$s: Number of times. */
            'edit_label'        => sprintf( __( '%1$s meta %2$s gets updated with %3$s %4$s time(s)', 'automatorwp' ), '{post}', '{meta_key}', '{meta_value}', '{times}' ),
            /* translators: %1$s: Post title. %2$s: Key. %3$s: Value. */
            'log_label'         => sprintf( __( '%1$s meta %2$s gets updated with %3$s', 'automatorwp' ), '{post}', '{meta_key}', '{meta_value}' ),
            'action'            => 'updated_post_meta',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 4,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'post_type'             => 'any',
                    'option_none_label'     => __( 'Any post', 'automatorwp' ),
                ) ),
                'meta_key' => array(
                    'from' => 'meta_key',
                    /* translators: Refers to meta key */
                    'default' => __( 'with any key', 'automatorwp' ),
                    'fields' => array(
                        'meta_key' => array(
                            'name' => __( 'Meta key:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                    )
                ),
                'meta_value' => array(
                    'from' => 'meta_value',
                    'default' => __( 'any value', 'automatorwp' ),
                    'fields' => array(
                        'meta_value' => array(
                            'name' => __( 'Meta value:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                    )
                ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                array(
                    'updated_meta_key' => array(
                        'label'     => __( 'Updated meta key', 'automatorwp' ),
                        'type'      => 'text',
                        'preview'   => __( 'Key of the updated meta', 'automatorwp' ),
                    ),
                    'updated_meta_value' => array(
                        'label'     => __( 'Updated meta value', 'automatorwp' ),
                        'type'      => 'text',
                        'preview'   => __( 'Value of the updated meta', 'automatorwp' ),
                    ),
                ),
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
     * @param int    $meta_id     ID of updated metadata entry.
     * @param int    $object_id   ID of the object metadata is for.
     * @param string $meta_key    Metadata key.
     * @param mixed  $meta_value  Metadata value. Serialized if non-scalar.
     */
    public function listener( $meta_id, $object_id, $meta_key, $meta_value ) {

        $post = get_post( $object_id );

        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $post->post_author,
            'post_id'       => $object_id,
            'meta_key'      => $meta_key,
            'meta_value'    => $meta_value,
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
        if( ! isset( $event['post_id'] ) && ! isset( $event['meta_key'] ) && ! isset( $event['meta_value'] ) ) {
            return false;
        }

        $post = get_post( absint( $event['post_id'] ) );

        // Don't deserve if post doesn't exists
        if( ! $post ) {
            return false;
        }

        // Don't deserve if key doesn't matches with the trigger option
        if( $trigger_options['meta_key'] !== '' && $trigger_options['meta_key'] !== $event['meta_key'] ) {
            return false;
        }

        // Don't deserve if value doesn't matches with the trigger option
        if( $trigger_options['meta_value'] !== '' && $trigger_options['meta_value'] !== $event['meta_value'] ) {
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
            case 'updated_meta_key':
                $replacement = automatorwp_get_log_meta( $log->id, 'updated_meta_key', true );
                break;
            case 'updated_meta_value':
                $replacement = automatorwp_get_log_meta( $log->id, 'updated_meta_value', true );
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

        $log_meta['updated_meta_key'] = ( isset( $event['updated_meta_key'] ) ? $event['updated_meta_key'] : '' );
        $log_meta['updated_meta_value'] = ( isset( $event['updated_meta_value'] ) ? $event['updated_meta_value'] : '' );

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

        $log_fields['updated_meta_key'] = array(
            'name' => __( 'Updated meta key', 'automatorwp' ),
            'desc' => __( 'Key of the updated meta.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['updated_meta_value'] = array(
            'name' => __( 'Updated meta value', 'automatorwp' ),
            'desc' => __( 'Value of the updated meta.', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;

    }

}

new AutomatorWP_WordPress_Post_Meta_Update();