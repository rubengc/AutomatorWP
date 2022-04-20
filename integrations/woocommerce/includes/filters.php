<?php
/**
 * Filters
 *
 * @package     AutomatorWP\Integrations\WooCommerce\Filters
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Columns rendering for logs list view
 *
 * @since  1.0.0
 *
 * @param string $column_name
 * @param integer $object_id
 */
function automatorwp_woocommerce_manage_logs_custom_column( $column_name, $object_id ) {

    if( $column_name !== 'post_id' ) {
        return;
    }

    // Setup vars
    $log = ct_get_object( $object_id );

    // Bail if not is a trigger log entry
    if( $log->type !== 'trigger' ) {
        return;
    }

    $trigger = automatorwp_get_trigger_object( $log->object_id );

    if( ! $trigger ) {
        return;
    }

    $type_args = automatorwp_automation_item_type_args( $trigger, $log->type );

    if( ! $type_args ) {
        return;
    }

    // Bail if trigger is not from this integration
    if( $type_args['integration'] !== 'woocommerce' ) {
        return;
    }

    // Get order associated to this log entry
    $order_id = (int) automatorwp_get_log_meta( $log->id, 'order_id', true );

    // Bail if order ID is equal to the post ID
    if( $log->post_id === $order_id ) {
        return;
    }

    $order = get_post( $order_id );

    if ( $order ){
        ?>
        <br>
        <a href="<?php echo get_edit_post_link( $order->ID ); ?>"><?php echo sprintf( __( 'Order #%s', 'automatorwp'), $order->ID ); ?></a>
        <?php
    }

}
add_filter( 'manage_automatorwp_logs_custom_column', 'automatorwp_woocommerce_manage_logs_custom_column', 99, 2 );