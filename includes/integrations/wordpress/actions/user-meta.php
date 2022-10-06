<?php
/**
 * User Meta
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\User_Meta
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_User_Meta extends AutomatorWP_Integration_Action {

    public $integration = 'wordpress';
    public $action = 'wordpress_user_meta';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Set, insert, increment or decrement user meta', 'automatorwp' ),
            'select_option'     => __( 'Set, insert, increment or decrement <strong>user meta</strong>', 'automatorwp' ),
            /* translators: %1$s: Operation (Set, insert, increment or decrement). %2$s: Meta value. %3$s: Meta key. */
            'edit_label'        => sprintf( __( '%1$s user meta value %2$s for meta key %3$s', 'automatorwp' ), '{operation}', '{meta_value}', '{meta_key}' ),
            /* translators: %1$s: Operation (Set, insert, increment or decrement). %2$s: Meta value. %3$s: Meta key. */
            'log_label'         => sprintf( __( '%1$s user meta value %2$s for meta key %3$s', 'automatorwp' ), '{operation}', '{meta_value}', '{meta_key}' ),
            'options'           => array(
                'operation' => array(
                    'from' => 'operation',
                    'fields' => array(
                        'operation' => array(
                            'name' => __( 'Operation:', 'automatorwp' ),
                            'desc' => __( 'Operation defines how the meta value will be applied. The available options are:', 'automatorwp' )
                                . '<br><br>' . __( '<strong>Set:</strong> The new value will be set as the meta value.', 'automatorwp' )
                                . '<br>' . __( 'Example: If old value is "Word" and new value is "Press", the final meta value will be "Press".', 'automatorwp' )
                                . '<br><br>' . __( '<strong>Insert:</strong> The new value will be inserted to the current meta value (for arrays, the new value will be inserted at the end, for other types, the new value will be appended).', 'automatorwp' )
                                . '<br>' . __( 'Example for arrays: If old value is array( "Word" ) and new value is "Press", the final meta value will be array( "Word", "Press" ).', 'automatorwp' )
                                . '<br>' . __( 'Example for other types: If old value is "Word" and new value is "Press", the final meta value will be "WordPress".', 'automatorwp' )
                                . '<br><br>' . __( '<strong>Increment:</strong> For numeric values, the current meta value will be incremented the same amount as the new value.', 'automatorwp' )
                                . '<br>' . __( 'Example: If old value is "5" and new value is "1", the final meta value will be "6".', 'automatorwp' )
                                . '<br><br>' . __( '<strong>Decrement:</strong> For numeric values, the current meta value will be decremented the same amount as the new value.', 'automatorwp' )
                                . '<br>' . __( 'Example: If old value is "5" and new value is "1", the final meta value will be "4".', 'automatorwp' ),
                            'type' => 'select',
                            'options' => array(
                                'set'       => __( 'Set', 'automatorwp' ),
                                'insert'    => __( 'Insert', 'automatorwp' ),
                                'increment' => __( 'Increment', 'automatorwp' ),
                                'decrement' => __( 'Decrement', 'automatorwp' ),
                            ),
                            'default' => 'set'
                        ),
                    )
                ),
                'meta_value' => array(
                    'from' => 'meta_value',
                    'default' => __( 'value', 'automatorwp' ),
                    'fields' => array(
                        'meta_value' => array(
                            'name' => __( 'Meta value:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                    )
                ),
                'meta_key' => array(
                    'from' => 'meta_key',
                    /* translators: Refers to meta key */
                    'default' => __( 'key', 'automatorwp' ),
                    'fields' => array(
                        'meta_key' => array(
                            'name' => __( 'Meta key:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
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

        // Dynamic edit and log labels
        add_filter( 'automatorwp_parse_automation_item_edit_label', array( $this, 'dynamic_label' ), 10, 5 );
        add_filter( 'automatorwp_parse_automation_item_log_label', array( $this, 'dynamic_label' ), 10, 5 );

        parent::hooks();

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
    public function dynamic_label( $label, $object, $item_type, $context, $type_args ) {

        // Bail if action type don't match this action
        if( $object->type !== $this->action ) {
            return $label;
        }

        // Get the operation value
        ct_setup_table( "automatorwp_{$item_type}s" );
        $operation = ct_get_object_meta( $object->id, 'operation', true );
        ct_reset_setup_table();

        // Update the edit and log labels
        if( in_array( $operation, array( 'increment', 'decrement' ) ) ) {
            /* translators: %1$s: Operation (Set, insert, increment or decrement). %2$s: Meta value. %3$s: Meta key. */
            return sprintf( __( '%1$s user meta value by %2$s for meta key %3$s', 'automatorwp' ), '{operation}', '{meta_value}', '{meta_key}' );
        }

        return $label;

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

        // Shorthand
        $operation = $action_options['operation'];
        $meta_key = sanitize_title( $action_options['meta_key'] );
        $meta_value = sanitize_text_field( $action_options['meta_value'] );

        // Bail if empty meta key
        if( empty( $meta_key ) ) {
            return;
        }

        // Get the current meta value
        $value = get_user_meta( $user_id, $meta_key, true );

        // For increment and decrement, is required to turn values into a numeric value
        if( in_array( $operation, array( 'increment', 'decrement' ) ) ) {

            if( strpos( $meta_value, '.' ) !== false ) {
                // Treat values as float
                $value = (float) $value;
                $meta_value = (float) $meta_value;
            } else {
                // Treat values as int
                $value = (int) $value;
                $meta_value = (int) $meta_value;
            }

        }

        switch ( $operation ) {
            case 'set':
                // Override old meta value
                $value = $meta_value;
                break;
            case 'insert':
                if( is_array( $value ) ) {
                    // If value is an array, append the new value
                    $value[] = $meta_value;
                } else {
                    // If not, concat the new value
                    $value .= $meta_value;
                }
                break;
            case 'increment':
                // Increase meta value
                $value += $meta_value;
                break;
            case 'decrement':
                // Decrease meta value
                $value -= $meta_value;
                break;
        }

        // Update the user meta value
        update_user_meta( $user_id, $meta_key, $value );

    }

}

new AutomatorWP_WordPress_User_Meta();