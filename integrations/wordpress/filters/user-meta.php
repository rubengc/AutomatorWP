<?php
/**
 * User Meta
 *
 * @package     AutomatorWP\Integrations\WordPress\Filters\User_Meta
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_User_Meta_Filter extends AutomatorWP_Integration_Filter {

    public $integration = 'wordpress';
    public $filter = 'wordpress_user_meta';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_filter( $this->filter, array(
            'integration'       => $this->integration,
            'label'             => __( 'User meta', 'automatorwp' ),
            'select_option'     => __( 'User <strong>meta</strong>', 'automatorwp' ),
            /* translators: %1$s: Meta key. %2$s: Condition. %1$s: Meta value. */
            'edit_label'        => sprintf( __( '%1$s %2$s %3$s', 'automatorwp' ), '{meta_key}', '{condition}', '{meta_value}'  ),
            /* translators: %1$s: Meta key. %2$s: Condition. %1$s: Meta value. */
            'log_label'         => sprintf( __( '%1$s %2$s %3$s', 'automatorwp' ), '{meta_key}', '{condition}', '{meta_value}' ),
            'options'           => array(
                'meta_key' => array(
                    'from' => 'meta_key',
                    'default' => __( 'key', 'automatorwp' ),
                    'fields' => array(
                        'meta_key' => array(
                            'name' => __( 'Meta key:', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        )
                    ),
                ),
                'condition' => automatorwp_utilities_condition_option(),
                'meta_value' => array(
                    'from' => 'meta_value',
                    'default' => __( 'value', 'automatorwp' ),
                    'fields' => array(
                        'meta_value' => array(
                            'name' => __( 'Meta value:', 'automatorwp' ),
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
        $meta_key = $filter_options['meta_key'];
        $condition = $filter_options['condition'];
        $meta_value = $filter_options['meta_value'];

        // Bail if wrong configured
        if( empty( $meta_key ) ) {
            $this->result = __( 'Filter not passed. Meta key option has not been configured.', 'automatorwp' );
            return false;
        }

        $user_meta_value = get_user_meta( $user_id, $meta_key, true );

        // Don't deserve if meta value doesn't match with the user meta value
        if( ! automatorwp_condition_matches( $user_meta_value, $meta_value, $condition ) ) {
            /* translators: %1$s: Meta key. %2$s: Meta value. %3$s: Condition. %4$s: Meta value. */
            $this->result = sprintf( __( 'Filter not passed. User meta "%1$s" has the value "%2$s" and does not meets the condition %3$s "%4$s".', 'automatorwp' ),
                $meta_key,
                $user_meta_value,
                automatorwp_utilities_get_condition_label( $condition ),
                $meta_value
            );
            return false;
        }

        /* translators: %1$s: Meta key. %2$s: Meta value. %3$s: Condition. %4$s: Meta value. */
        $this->result = sprintf( __( 'Filter passed. User meta "%1$s" has the value "%2$s" and meets the condition %3$s "%4$s".', 'automatorwp' ),
            $meta_key,
            $user_meta_value,
            automatorwp_utilities_get_condition_label( $condition ),
            $meta_value
        );

        return $deserves_filter;

    }

}

new AutomatorWP_WordPress_User_Meta_Filter();