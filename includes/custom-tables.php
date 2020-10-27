<?php
/**
 * Custom Tables
 *
 * @package     AutomatorWP\Custom_Tables
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Custom Tables
require_once AUTOMATORWP_DIR . 'includes/custom-tables/automations.php';
require_once AUTOMATORWP_DIR . 'includes/custom-tables/triggers.php';
require_once AUTOMATORWP_DIR . 'includes/custom-tables/actions.php';
require_once AUTOMATORWP_DIR . 'includes/custom-tables/logs.php';

/**
 * Register all custom database Tables
 *
 * @since   1.0.0
 *
 * @return void
 */
function automatorwp_register_custom_tables() {

    // Automations
    ct_register_table( 'automatorwp_automations', array(
        'singular' => __( 'Automation', 'automatorwp' ),
        'plural' => __( 'Automations', 'automatorwp' ),
        'show_ui' => true,
        'show_in_rest' => true,
        'rest_base' => 'automatorwp-automations',
        'version' => 2,
        'supports' => array( 'meta' ),
        'views' => array(
            'list' => array(
                'menu_title' => __( 'Automations', 'automatorwp' ),
                'parent_slug' => 'automatorwp',
            ),
        ),
        'schema' => array(
            'id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),
            'title' => array(
                'type' => 'text',
            ),
            'type' => array(
                'type' => 'text',
            ),
            'user_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'sequential' => array(
                'type' => 'tinyint',
                'length' => '1',
                'default' => '0',
            ),
            'times_per_user' => array(
                'type' => 'int',
                'length' => '11',
                'default' => '1',
            ),
            'times' => array(
                'type' => 'int',
                'length' => '11',
                'default' => '0',
            ),
            'status' => array(
                'type' => 'varchar',
                'length' => '50',
            ),
            'date' => array(
                'type' => 'datetime',
                'default' => '0000-00-00 00:00:00'
            ),
        ),
    ) );

    // Triggers
    ct_register_table( 'automatorwp_triggers', array(
        'singular' => __( 'Trigger', 'automatorwp' ),
        'plural' => __( 'Triggers', 'automatorwp' ),
        'show_ui' => false,
        'show_in_rest' => true,
        'rest_base' => 'automatorwp-triggers',
        'version' => 1,
        'supports' => array( 'meta' ),
        'schema' => array(
            'id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),
            'automation_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'title' => array(
                'type' => 'text',
            ),
            'type' => array(
                'type' => 'text',
            ),
            'status' => array(
                'type' => 'varchar',
                'length' => '50',
            ),
            'position' => array(
                'type' => 'int',
                'length' => '11',
                'default' => '0',
            ),
            'date' => array(
                'type' => 'datetime',
                'default' => '0000-00-00 00:00:00'
            ),
        ),
    ) );

    // Actions
    ct_register_table( 'automatorwp_actions', array(
        'singular' => __( 'Action', 'automatorwp' ),
        'plural' => __( 'Actions', 'automatorwp' ),
        'show_ui' => false,
        'show_in_rest' => true,
        'rest_base' => 'automatorwp-actions',
        'version' => 1,
        'supports' => array( 'meta' ),
        'schema' => array(
            'id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),
            'automation_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'title' => array(
                'type' => 'text',
            ),
            'type' => array(
                'type' => 'text',
            ),
            'status' => array(
                'type' => 'varchar',
                'length' => '50',
            ),
            'position' => array(
                'type' => 'int',
                'length' => '11',
                'default' => '0',
            ),
            'date' => array(
                'type' => 'datetime',
                'default' => '0000-00-00 00:00:00'
            ),
        ),
    ) );

    // Logs
    ct_register_table( 'automatorwp_logs', array(
        'singular' => __( 'Log', 'automatorwp' ),
        'plural' => __( 'Logs', 'automatorwp' ),
        'show_ui' => true,
        'show_in_rest' => true,
        'rest_base' => 'automatorwp-logs',
        'version' => 1,
        'supports' => array( 'meta' ),
        'views' => array(
            'list' => array(
                'menu_title' => __( 'Logs', 'automatorwp' ),
                'parent_slug' => 'automatorwp',
            ),
            'add' => false,
        ),
        'schema' => array(
            'id' => array(
                'type' => 'bigint',
                'length' => '20',
                'auto_increment' => true,
                'primary_key' => true,
            ),
            'title' => array(
                'type' => 'text',
            ),
            'type' => array(
                'type' => 'text',
            ),
            'object_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'user_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'post_id' => array(
                'type' => 'bigint',
                'length' => '20',
                'key' => true,
            ),
            'date' => array(
                'type' => 'datetime',
                'default' => '0000-00-00 00:00:00'
            ),
        ),
    ) );

}
add_action( 'ct_init', 'automatorwp_register_custom_tables' );

/**
 * Helper function to generate a where from a CT Query
 *
 * @since 1.0.0
 *
 * @param array     $query_vars The query vars
 * @param string    $field_id   The field id
 * @param string    $field_type The field type (string|integer)
 *
 * @return string
 */
function automatorwp_custom_table_where( $query_vars, $field_id, $field_type ) {

    global $ct_table;

    $table_name = $ct_table->db->table_name;

    $where = '';

    // Shorthand
    $qv = $query_vars;

    // Type
    if( isset( $qv[$field_id] ) && ! empty( $qv[$field_id] ) ) {

        $value = $qv[$field_id];

        if( is_array( $value ) ) {
            // Multiples values

            if( $field_type === 'string' ) {

                // Join values by a comma-separated list of strings
                $value = "'" . implode( "', '", $value ) . "'";

                $where .= " AND {$table_name}.{$field_id} IN ( {$value} )";

            } else if( $field_type === 'integer' ) {

                // Join values by a comma-separated list of integers
                $value = "'" . implode( ", ", $value ) . "'";

                $where .= " AND {$table_name}.{$field_id} IN ( {$value} )";

            }
        } else {
            // Single value

            if( $field_type === 'string' ) {

                $where .= " AND {$table_name}.{$field_id} = '{$value}'";

            } else if( $field_type === 'integer' ) {

                $value = (int) $value;

                $where .= " AND {$table_name}.{$field_id} = {$value}";

            }
        }

    }

    return $where;

}