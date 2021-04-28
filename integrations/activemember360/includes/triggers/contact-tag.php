<?php
/**
 * Contact Tag
 *
 * @package     AutomatorWP\Integrations\ActiveMember360\Triggers\Contact_Tag
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_ActiveMember360_Contact_Tag extends AutomatorWP_Integration_Trigger {

    public $integration = 'activemember360';
    public $trigger = 'activemember360_contact_tag';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User has tag', 'automatorwp' ),
            'select_option'     => __( 'User has <strong>tag</strong>', 'automatorwp' ),
            /* translators: %1$s: Tag name. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User has %1$s %2$s time(s)', 'automatorwp' ), '{tag}', '{times}' ),
            /* translators: %1$s: Tag name. */
            'log_label'         => sprintf( __( 'User has %1$s', 'automatorwp' ), '{tag}' ),
            'action'            => 'init',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'tag' => array(
                    'from' => 'tag',
                    'fields' => array(
                        'tag' => array(
                            'name' => __( 'Tag:', 'automatorwp' ),
                            'type' => 'select',
                            'classes' => 'automatorwp-selector',
                            'options_cb' => array( $this, 'tags_options_cb' ),
                            'default' => 'any'
                        )
                    )
                ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Get tags options
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function tags_options_cb() {

        $options = array(
            'any' => __( 'any tag', 'automatorwp' ),
        );

        // Get site tags
        $tags = apply_filters( 'mbr/site_tags/get', NULL );

        if( ! empty( $tags ) ) {
            foreach( $tags as $tag_id => $tag_name ) {
                $options[$tag_id] = $tag_name;
            }
        }

        return $options;

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     */
    public function listener() {

        // Bail if in admin area
        if( is_admin() ) {
            return;
        }

        $user_id = get_current_user_id();

        // Bail if user is not logged in
        if( $user_id === 0 ) {
            return;
        }

        // Bail if site or user hasn't any tags
        if( empty( MBR()->MBRTAGS ) ) {
            return;
        }

        $remote_logged_in = apply_filters( 'mbr/contact_id', NULL );

        // Bail if user account is not remote logged in
        if ( empty( $remote_logged_in ) ) {
            return;
        }

        // Trigger the user has tag
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
        ) );

    }

    /**
     * User deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if user deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    public function user_deserves_trigger( $deserves_trigger, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Shorthand
        $tag = $trigger_options['tag'];

        // Don't deserve if tag doesn't match with the trigger option
        if( $tag !== 'any' && ! mbr_has_tags( $trigger_options['tag'], 'any' ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_ActiveMember360_Contact_Tag();