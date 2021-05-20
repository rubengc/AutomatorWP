<?php
/**
 * Trigger Filter
 *
 * @package     AutomatorWP\Integrations\Filter\Triggers\Trigger_Filter
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Trigger_Filter extends AutomatorWP_Integration_Trigger {

    public $integration = 'filter';
    public $trigger = 'filter';

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
            'edit_label'        => '{operator} {filter}',
            'log_label'         => '{operator} {filter}',
            'options'           => array(
                'operator' => array(
                    'from' => 'operator',
                    'fields' => array(
                        'operator' => array(
                            'name' => __( 'Operator:', 'automatorwp' ),
                            'type' => 'select',
                            'options'  => array(
                                'and' => __( 'AND', 'automatorwp' ),
                                'or' => __( 'OR', 'automatorwp' ),
                            ),
                            'default' => 'and'
                        ),
                    )
                ),
                'filter' => array(
                    'from' => 'filter',
                    'fields' => array(
                        'filter' => array(
                            'name' => __( 'Filter:', 'automatorwp' ),
                            'type' => 'automatorwp_select_filter',
                            'classes' => 'automatorwp-filter-selector',
                            'options_cb'  => 'automatorwp_options_cb_filters',
                            'default' => 'any'
                        ),
                    )
                )
            ),
            'tags' => array()
        ) );

    }

}

new AutomatorWP_Trigger_Filter();