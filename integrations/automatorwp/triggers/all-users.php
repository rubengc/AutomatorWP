<?php
/**
 * All Users
 *
 * @package     AutomatorWP\Integrations\AutomatorWP\Triggers\All_Users
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_AutomatorWP_All_Users extends AutomatorWP_Integration_Trigger {

    public $integration = 'automatorwp';
    public $trigger = 'automatorwp_all_users';

    /**
     * The field conditions
     *
     * @since 1.0.0
     *
     * @var array $field_conditions
     */
    public $field_conditions = array();

    /**
     * The meta conditions
     *
     * @since 1.0.0
     *
     * @var array $meta_conditions
     */
    public $meta_conditions = array();

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => '',
            'select_option'     => '',
            /* translators: %1$s: Automation title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'Run automation on %1$s', 'automatorwp' ), '{all_users_conditions}' ),
            /* translators: %1$s: Automation title. */
            'log_label'         => sprintf( __( 'Run automation on %1$s', 'automatorwp' ), '{all_users_conditions}' ),
            'options'           => array(
                'all_users_conditions' => array(
                    'default' => __( 'all users', 'automatorwp' ),
                    'fields' => array(
                        'field_conditions' => array(
                            'name' => __( 'Field Conditions:', 'automatorwp' ),
                            'desc' => __( 'Set conditions to filter users by their fields.', 'automatorwp' ),
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
                                    'options_cb' => 'automatorwp_options_cb_user_fields',
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
                        'meta_conditions' => array(
                            'name' => __( 'Meta Conditions:', 'automatorwp' ),
                            'desc' => __( 'Set conditions to filter users by their metas.', 'automatorwp' ),
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
            ),
            'tags' => array_merge(
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Register the required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        parent::hooks();

        add_filter( 'automatorwp_get_automation_item_option_replacement', array( $this, 'dynamic_item_option_replacement' ), 10, 5 );
        add_filter( 'automatorwp_get_all_users_automation_sql', array( $this, 'get_sql' ), 10, 7 );

    }

    /**
     * Filters the option value for replacement on labels
     *
     * @since 1.0.0
     *
     * @param string    $value      The option value
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The item type (trigger|action)
     * @param string    $option     The option name
     * @param string    $context    The context this function is executed
     *
     * @return string
     */
    public function dynamic_item_option_replacement( $value, $object, $item_type, $option, $context ) {

        // Bail if not is our option
        if( $option !== 'all_users_conditions' ) {
            return $value;
        }

        ct_setup_table( "automatorwp_{$item_type}s" );

        // Setup vars
        $field_conditions = ct_get_object_meta( $object->id, 'field_conditions', true );
        $meta_conditions = ct_get_object_meta( $object->id, 'meta_conditions', true );

        $someting_in_fields = false;
        $someting_in_metas = false;

        // Check if there is any field condition
        if( is_array( $field_conditions ) ) {
            foreach( $field_conditions as $condition ) {
                if( ! empty( $condition['field'] ) && ! empty( $condition['condition'] ) && ! empty( $condition['value'] ) ) {
                    $someting_in_fields = true;
                    break;
                }
            }
        }

        // Check if there is any meta condition
        if( is_array( $meta_conditions ) ) {
            foreach( $meta_conditions as $condition ) {
                if( ! empty( $condition['meta_key'] ) && ! empty( $condition['condition'] ) && ! empty( $condition['meta_value'] ) ) {
                    $someting_in_fields = true;
                    break;
                }
            }
        }

        if( $someting_in_fields || $someting_in_metas ) {
            $value = __( 'some users', 'automatorwp' );
        }

        ct_reset_setup_table();

        return $value;

    }

    /**
     * Available filter to override the all users automation SQL
     *
     * @since 1.0.0
     *
     * @param string    $sql                The SQL query
     * @param stdClass  $automation         The automation object
     * @param stdClass  $trigger            The trigger object
     * @param bool      $count              True if is looking for the SQL to count the number of users
     * @param array     $trigger_options    The trigger's stored options
     * @param int       $users_per_loop     The automation users per loop option
     * @param int       $loop               The current loop
     */
    public function get_sql( $sql, $automation, $trigger, $count, $trigger_options, $users_per_loop, $loop ) {

        global $wpdb;

        // Setup vars
        $field_conditions = isset( $trigger_options['field_conditions'] ) ? $trigger_options['field_conditions'] : array();
        $meta_conditions = isset( $trigger_options['meta_conditions'] ) ? $trigger_options['meta_conditions'] : array();

        $joins = array();
        $where = array();

        // Set up the user field conditions
        if( is_array( $field_conditions ) ) {
            foreach( $field_conditions as $condition ) {

                if( ! isset( $condition['field'] ) ) {
                    continue;
                }

                // Sanitize
                $field = sanitize_text_field( $condition['field'] );
                $value = sanitize_text_field( $condition['value'] );

                if( ! empty( $field ) ) {
                    $where[] = automatorwp_utilities_parse_condition_to_sql( 'u.' . $field, $condition['condition'], $value );
                }
            }
        }

        // Set up the user meta conditions
        if( is_array( $meta_conditions ) ) {
            foreach( $meta_conditions as $condition ) {

                if( ! isset( $condition['meta_key'] ) ) {
                    continue;
                }

                // Sanitize
                $meta_key = sanitize_text_field( $condition['meta_key'] );
                $meta_value = sanitize_text_field( $condition['meta_value'] );

                if( ! empty( $meta_key ) ) {
                    $index = count( $joins );

                    $joins[] = "INNER JOIN {$wpdb->usermeta} AS um{$index} ON ( um{$index}.user_id = u.ID AND um{$index}.meta_key = '{$meta_key}' )";

                    $where[] = automatorwp_utilities_parse_condition_to_sql( "um{$index}.meta_value", $condition['condition'], $meta_value, false );
                }
            }
        }

        // Turn arrays into strings
        $joins = implode( ' ', $joins );
        $where = ( ! empty( $where ) ? 'WHERE ( ' . implode( ' ) AND ( ', $where ) . ' ) ' : '' );

        if( $count ) {
            // The count SQL query
            return "SELECT COUNT(*) FROM {$wpdb->users} AS u {$joins} {$where}";
        } else {
            $offset = $loop * $users_per_loop;

            // The normal SQL query
            return "SELECT u.ID FROM {$wpdb->users} AS u {$joins} {$where} LIMIT $offset, {$users_per_loop}";
        }

    }

}

new AutomatorWP_AutomatorWP_All_Users();