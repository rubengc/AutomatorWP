<?php
/**
 * Plain Condition
 *
 * @package     AutomatorWP\Integrations\WordPress\Filters\Flat_Condition
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Flat_Condition_Filter extends AutomatorWP_Integration_Filter {

    public $integration = 'automatorwp';
    public $filter = 'automatorwp_flat_condition';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_filter( $this->filter, array(
            'integration'       => $this->integration,
            'label'             => __( 'Flat condition', 'automatorwp' ),
            'select_option'     => __( 'Flat <strong>condition</strong>', 'automatorwp' ),
            /* translators: %1$s: Meta key. %2$s: Condition. %1$s: Meta value. */
            'edit_label'        => sprintf( __( '%1$s %2$s %3$s', 'automatorwp' ), '{first_value}', '{condition}', '{second_value}'  ),
            /* translators: %1$s: Meta key. %2$s: Condition. %1$s: Meta value. */
            'log_label'         => sprintf( __( '%1$s %2$s %3$s', 'automatorwp' ), '{first_value}', '{condition}', '{second_value}' ),
            'options'           => array(
                'first_value' => array(
                    'from' => 'first_value',
                    'default' => __( 'value 1', 'automatorwp' ),
                    'fields' => array(
                        'first_value' => array(
                            'name' => __( 'Value 1:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                    ),
                ),
                'condition' => automatorwp_utilities_condition_option(),
                'second_value' => array(
                    'from' => 'second_value',
                    'default' => __( 'value 2', 'automatorwp' ),
                    'fields' => array(
                        'second_value' => array(
                            'name' => __( 'Value 2:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                    ),
                ),
            ),
        ) );

    }

    /**
     * User deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_filter    True if user deserves filter, false otherwise
     * @param stdClass  $filter             The filter object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $filter_options     The filter's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    public function user_deserves_filter( $deserves_filter, $filter, $user_id, $event, $filter_options, $automation ) {

        // Shorthand
        $first_value = $filter_options['first_value'];
        $condition = $filter_options['condition'];
        $second_value = $filter_options['second_value'];

        // Don't deserve if meta value doesn't match with the user meta value
        if( ! automatorwp_condition_matches( $first_value, $second_value, $condition ) ) {
            $this->result = sprintf( __( 'Filter not passed. "%1$s" does not meets the condition %2$s "%3$s".', 'automatorwp' ),
                $first_value,
                automatorwp_utilities_get_condition_label( $condition ),
                $second_value
            );
            return false;
        }

        $this->result = sprintf( __( 'Filter passed. "%1$s" %2$s "%3$s".', 'automatorwp' ),
            $first_value,
            automatorwp_utilities_get_condition_label( $condition ),
            $second_value
        );

        return $deserves_filter;

    }

}

new AutomatorWP_WordPress_Flat_Condition_Filter();