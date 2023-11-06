<?php
/**
 * Functions
 *
 * @package     AutomatorWP\ARMember\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Options callback for select2 fields assigned to plans
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_armember_options_cb_plan( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any membership plan', 'automatorwp-pro' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $plan_id ) {

            // Skip option none
            if( $plan_id === $none_value ) {
                continue;
            }

            $options[$plan_id] = automatorwp_armember_get_plan_title( $plan_id );
        }
    }

    return $options;

}

/**
 * Get the membership plan title
 *
 * @since 1.0.0
 *
 * @param int $plan_id
 *
 * @return string|null
 */
function automatorwp_armember_get_plan_title( $plan_id ) {

    // Empty title if no ID provided
    if( absint( $plan_id ) === 0 ) {
        return '';
    }

    if( class_exists( 'ARM_subscription_plans' ) ) {
		$obj_plans = new \ARM_subscription_plans();
	} else {
		$obj_plans = new \ARM_subscription_plans_Lite();
	}

    $membership_name = $obj_plans->arm_get_plan_name_by_id( $plan_id );

    return $membership_name;

}

/**
 * Get Membership plan
 *
 * @since 1.0.0
 *
 * @param int $plan_id
 *
 * @return string|null
 */
function automatorwp_armember_get_plan( ) {

    $membership_plans = array();

    if( class_exists( 'ARM_subscription_plans' ) ) {
		$obj_plans = new \ARM_subscription_plans();
	} else {
		$obj_plans = new \ARM_subscription_plans_Lite();
	}
    
    $all_plans = $obj_plans->arm_get_all_subscription_plans( 'arm_subscription_plan_id, arm_subscription_plan_name' );

    foreach ( $all_plans as $plan ){
    
        $membership_plans[] = array(
            'id' => $plan['arm_subscription_plan_id'],
            'name' => $plan['arm_subscription_plan_name'],
        );
        
    }

    return $membership_plans; 

}