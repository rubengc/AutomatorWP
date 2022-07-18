<?php
/**
 * User Exists
 *
 * @package     AutomatorWP\Integrations\WordPress\Filters\User_Exists
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_User_Exists_Filter extends AutomatorWP_Integration_Filter {

    public $integration = 'wordpress';
    public $filter = 'wordpress_user_exists';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_filter( $this->filter, array(
            'integration'       => $this->integration,
            'label'             => __( 'User with field exists or does not exists', 'automatorwp' ),
            'select_option'     => __( 'User with field <strong>exists</strong> or <strong>does not exists</strong>', 'automatorwp' ),
            /* translators: %1$s: Field name. %2$s: Field value. %3$s: Operator. */
            'edit_label'        => sprintf( __( 'User with %1$s %2$s %3$s', 'automatorwp' ), '{field_name}', '{field_value}', '{condition}'  ),
            /* translators: %1$s: Field name. %2$s: Field value. %3$s: Operator. */
            'log_label'         => sprintf( __( 'User with %1$s %2$s %3$s', 'automatorwp' ), '{field_name}', '{field_value}', '{condition}' ),
            'options'           => array(
                'field_name' => array(
                    'from' => 'field_name',
                    'default' => __( 'field', 'automatorwp' ),
                    'fields' => array(
                        'field_name' => array(
                            'name' => __( 'Field:', 'automatorwp' ),
                            'type' => 'select',
                            'options' => array(
                                'email'     => __( 'Email', 'automatorwp' ),
                                'login'     => __( 'Username', 'automatorwp' ),
                            ),
                            'default' => 'email'
                        )
                    ),
                ),
                'field_value' => array(
                    'from' => 'field_value',
                    'default' => __( 'value', 'automatorwp' ),
                    'fields' => array(
                        'field_value' => array(
                            'name' => __( 'Value:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                    ),
                ),
                'condition' => array(
                    'from' => 'condition',
                    'default' => __( 'exists', 'automatorwp' ),
                    'fields' => array(
                        'condition' => array(
                            'name' => __( 'Condition:', 'automatorwp' ),
                            'type' => 'select',
                            'options' => array(
                                'exists'     => __( 'exists', 'automatorwp' ),
                                'not_exists' => __( 'does not exists', 'automatorwp' ),
                            ),
                            'default' => 'exists'
                        )
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

        $field_labels = array(
            'email'        => __( 'email', 'automatorwp' ),
            'login'        => __( 'username', 'automatorwp' ),
        );

        // Shorthand
        $field_name = $filter_options['field_name'];
        $field_value = $filter_options['field_value'];
        $condition = $filter_options['condition'];

        if( empty( $condition ) ) {
            $condition = 'exists';
        }

        // Bail if wrong configured
        if( empty( $field_name ) ) {
            $this->result = __( 'Filter not passed. Field option has not been configured.', 'automatorwp' );
            return false;
        }

        // Bail if wrong configured
        if( empty( $field_value ) ) {
            $this->result = __( 'Filter not passed. Field value option is empty.', 'automatorwp' );
            return false;
        }

        // Try to find the user
        $user = get_user_by( $field_name, $field_value );

        if( $condition === 'exists' ) {

            if( ! $user ) {
                /* translators: %1$s: Field name. %2$s: Field value. */
                $this->result = sprintf( __( 'Filter not passed. User with %1$s "%2$s" not found.', 'automatorwp' ),
                    $field_labels[$field_name],
                    $field_value
                );
                return false;
            }

            /* translators: %1$s: Field name. %2$s: Field value. */
            $this->result = sprintf( __( 'Filter passed. User with %1$s "%2$s" found.', 'automatorwp' ),
                $field_labels[$field_name],
                $field_value
            );

            return $deserves_filter;

        } else if( $condition === 'not_exists' ) {

            if( $user ) {
                /* translators: %1$s: Field name. %2$s: Field value. */
                $this->result = sprintf( __( 'Filter not passed. User with %1$s "%2$s" found.', 'automatorwp' ),
                    $field_labels[$field_name],
                    $field_value
                );
                return false;
            }

            /* translators: %1$s: Field name. %2$s: Field value. */
            $this->result = sprintf( __( 'Filter passed. User with %1$s "%2$s" not found.', 'automatorwp' ),
                $field_labels[$field_name],
                $field_value
            );

            return $deserves_filter;

        }

        return $deserves_filter;

    }

}

new AutomatorWP_WordPress_User_Exists_Filter();