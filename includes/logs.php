<?php
/**
 * Log
 *
 * @package     AutomatorWP\Log
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Get log registered types
 *
 * @since  1.0.0
 *
 * @return array
 */
function automatorwp_get_log_types() {

    return apply_filters( 'automatorwp_log_types', array(
        'automation' => __( 'Automation', 'automatorwp' ),
        'anonymous' => __( 'Anonymous', 'automatorwp' ),
        'trigger' => __( 'Trigger', 'automatorwp' ),
        'action' => __( 'Action', 'automatorwp' ),
    ) );

}

/**
 * Insert a new log
 *
 * @since 1.0.0
 *
 * @param array $log_data   The log data to insert
 * @param array $log_meta   The log meta data to insert
 *
 * @return int|WP_Error     The log ID on success. The value 0 or WP_Error on failure.
 */
function automatorwp_insert_log( $log_data = array(), $log_meta = array() ) {

    global $wpdb;

    $log_data = wp_parse_args( $log_data, array(
        'title'     => '',
        'type'      => '',
        'object_id' => 0,
        'user_id'   => 0,
        'post_id'   => 0,
        'date'      => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
    ) );

    $ct_table = ct_setup_table( 'automatorwp_logs' );

    // Store log entry
    $log_id = $ct_table->db->insert( $log_data );

    // If log correctly inserted, insert all meta data received
    if( $log_id && ! empty( $log_meta ) ) {

        $metas = array();

        foreach( $log_meta as $meta_key => $meta_value ) {
            // Sanitize vars
            $meta_key = sanitize_key( $meta_key );
            $meta_key = wp_unslash( $meta_key );
            $meta_value = wp_unslash( $meta_value );
            $meta_value = esc_sql( $meta_value );
            $meta_value = sanitize_meta( $meta_key, $meta_value, $ct_table->name );
            $meta_value = maybe_serialize( $meta_value );

            // Setup the insert value
            $metas[] = $wpdb->prepare( '%d, %s, %s', array( $log_id, $meta_key, $meta_value ) );
        }

        $logs_meta = AutomatorWP()->db->logs_meta;
        $metas = implode( '), (', $metas );

        // Since the log is recently inserted, is faster to run a single query to insert all metas instead of insert them one-by-one
        $wpdb->query( "INSERT INTO {$logs_meta} (id, meta_key, meta_value) VALUES ({$metas})" );

    }

    ct_reset_setup_table();

    // Flush cache to prevent meta data cached values
    wp_cache_flush();

    return $log_id;

}

/**
 * Get the log object data
 *
 * @since 1.0.0
 *
 * @param int       $log_id         The log ID
 * @param string    $output         Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which correspond to
 *                                  a object, an associative array, or a numeric array, respectively. Default OBJECT.
 * @return array|stdClass|null
 */
function automatorwp_get_log_object( $log_id, $output = OBJECT ) {

    ct_setup_table( 'automatorwp_logs' );

    $log = ct_get_object( $log_id );

    ct_reset_setup_table();

    return $log;

}

/**
 * Get the log object data
 *
 * @since 1.0.0
 *
 * @param int       $log_id         The log ID
 * @param string    $meta_key       Optional. The meta key to retrieve. By default, returns
 *                                  data for all keys. Default empty.
 * @param bool      $single         Optional. Whether to return a single value. Default false.
 *
 * @return mixed                    Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function automatorwp_get_log_meta( $log_id, $meta_key = '', $single = false ) {

    ct_setup_table( 'automatorwp_logs' );

    $meta_value = ct_get_object_meta( $log_id, $meta_key, $single );

    ct_reset_setup_table();

    return $meta_value;

}

/**
 * Get the log integration icon HTML markup
 *
 * @since 1.0.0
 *
 * @param stdClass $log The log object
 */
function automatorwp_get_log_integration_icon( $log ) {

    if( in_array( $log->type, array( 'trigger', 'action' ) ) ) {

        // Get the trigger or action
        ct_setup_table( "automatorwp_{$log->type}s" );
        $object = ct_get_object( $log->object_id );
        ct_reset_setup_table();

        $type_args = automatorwp_automation_item_type_args( $object, $log->type );

        if( $type_args ) {
            $integration = automatorwp_get_integration( $type_args['integration'] );

            if( $integration ) :

                if( $log->type === 'action' && $object->type === 'automatorwp_anonymous_user' ) {
                    $integration['icon'] = AUTOMATORWP_URL . 'assets/img/automatorwp-anonymous.svg';
                }?>

                <div class="automatorwp-integration-icon">
                    <img src="<?php echo esc_attr( $integration['icon'] ); ?>" title="<?php echo esc_attr( $integration['label'] ); ?>" alt="<?php echo esc_attr( $integration['label'] ); ?>">
                </div>

            <?php endif;
        } else { ?>

            <div class="automatorwp-integration-icon">
                <img src="<?php echo esc_attr( AUTOMATORWP_URL . 'assets/img/integration-missing.svg' ); ?>" title="<?php echo esc_attr( __( 'Missing plugin', 'automatorwp' ) ); ?>">
            </div>

        <?php }

    } else {

        $icon = AUTOMATORWP_URL . 'includes/integrations/automatorwp/assets/automatorwp.svg';

        if( $log->type === 'anonymous' ) {
            $icon = AUTOMATORWP_URL . 'assets/img/automatorwp-anonymous.svg';
        }

        /**
         * Available filter to override log default icon
         *
         * @since 1.2.4
         *
         * @param string    $icon   The icon URL
         * @param stdClass  $log    The log object
         *
         * @return string
         */
        $icon = apply_filters( 'automatorwp_get_log_default_icon', $icon, $log );

        /**
         * Available filter to override log default icon title
         *
         * @since 1.2.4
         *
         * @param string    $title  The icon title attribute
         * @param stdClass  $log    The log object
         *
         * @return string
         */
        $title = apply_filters( 'automatorwp_get_log_default_icon_title', 'AutomatorWP', $log ); ?>

        <div class="automatorwp-integration-icon">
            <img src="<?php echo esc_attr( $icon ); ?>" title="<?php echo esc_attr( $title ); ?>">
        </div>

    <?php }

}