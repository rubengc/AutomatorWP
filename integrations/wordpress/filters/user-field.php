<?php
/**
 * User Field
 *
 * @package     AutomatorWP\Integrations\WordPress\Filters\User_Field
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_User_Field_Filter extends AutomatorWP_Integration_Filter {

    public $integration = 'wordpress';
    public $filter = 'wordpress_user_field';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_filter( $this->filter, array(
            'integration'       => $this->integration,
            'label'             => __( 'User field', 'automatorwp' ),
            'select_option'     => __( 'User <strong>field</strong>', 'automatorwp' ),
            /* translators: %1$s: Field name. %2$s: Condition. %3$s: Field value. */
            'edit_label'        => sprintf( __( '%1$s %2$s %3$s', 'automatorwp' ), '{field_name}', '{condition}', '{field_value}'  ),
            /* translators: %1$s: Field. %2$s: Condition. %3$s: Field value. */
            'log_label'         => sprintf( __( '%1$s %2$s %3$s', 'automatorwp' ), '{field_name}', '{condition}', '{field_value}' ),
            'options'           => array(
                'field_name' => array(
                    'from' => 'field_name',
                    'default' => __( 'field', 'automatorwp' ),
                    'fields' => array(
                        'field_name' => array(
                            'name' => __( 'Field:', 'automatorwp' ),
                            'type' => 'select',
                            'options' => array(
                                'ID'                => __( 'ID', 'automatorwp' ),
                                'user_login'        => __( 'Username', 'automatorwp' ),
                                'user_email'        => __( 'Email', 'automatorwp' ),
                                'user_url'          => __( 'URL', 'automatorwp' ),
                                'user_registered'   => __( 'Date registered', 'automatorwp' ),
                                'display_name'      => __( 'Display name', 'automatorwp' ),
                            ),
                            'default' => 'ID'
                        )
                    ),
                ),
                'condition' => automatorwp_utilities_condition_option(),
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
            'ID'                => __( 'ID', 'automatorwp' ),
            'user_login'        => __( 'username', 'automatorwp' ),
            'user_email'        => __( 'email', 'automatorwp' ),
            'user_url'          => __( 'URL', 'automatorwp' ),
            'user_registered'   => __( 'date registered', 'automatorwp' ),
            'display_name'      => __( 'display name', 'automatorwp' ),
        );

        // Shorthand
        $field_name = $filter_options['field_name'];
        $condition = $filter_options['condition'];
        $field_value = $filter_options['field_value'];

        // Bail if wrong configured
        if( empty( $field_name ) ) {
            $this->result = __( 'Filter not passed. Field option has not been configured.', 'automatorwp' );
            return false;
        }

        $user = get_userdata( $user_id );

        if( ! $user ) {
            $this->result = __( 'Filter not passed. Could not find the user.', 'automatorwp' );
            return false;
        }

        $user_field_value = $user->get( $field_name );

        // Don't deserve if meta value doesn't match with the user meta value
        if( ! automatorwp_condition_matches( $user_field_value, $field_value, $condition ) ) {
            /* translators: %1$s: Field name. %2$s: Field value. %3$s: Condition. %4$s: Field value. */
            $this->result = sprintf( __( 'Filter not passed. User %1$s has the value "%2$s" and does not meets the condition %3$s "%4$s".', 'automatorwp' ),
                $field_labels[$field_name],
                $user_field_value,
                automatorwp_utilities_get_condition_label( $condition ),
                $field_value
            );
            return false;
        }

        /* translators: %1$s: Field name. %2$s: Field value. %3$s: Condition. %4$s: Field value. */
        $this->result = sprintf( __( 'Filter passed. User %1$s has the value "%2$s" and meets the condition %3$s "%4$s".', 'automatorwp' ),
            $field_labels[$field_name],
            $user_field_value,
            automatorwp_utilities_get_condition_label( $condition ),
            $field_value
        );

        return $deserves_filter;

    }

}

new AutomatorWP_WordPress_User_Field_Filter();