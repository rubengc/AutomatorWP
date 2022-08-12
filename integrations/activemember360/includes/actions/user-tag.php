<?php
/**
 * User Tag
 *
 * @package     AutomatorWP\Integrations\ActiveMember360\Actions\User_Tag
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_ActiveMember360_User_Tag extends AutomatorWP_Integration_Action {

    public $integration = 'activemember360';
    public $action = 'activemember360_user_tag';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add tag to user', 'automatorwp' ),
            'select_option'     => __( 'Add <strong>tag</strong> to user', 'automatorwp' ),
            /* translators: %1$s: Tag name. */
            'edit_label'        => sprintf( __( 'Add %1$s to user', 'automatorwp' ), '{tag}' ),
            /* translators: %1$s: Tag name. */
            'log_label'         => sprintf( __( 'Add %1$s to user', 'automatorwp' ), '{tag}' ),
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
            ),
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
            'any' => __( 'all tags', 'automatorwp' ),
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
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    public function execute( $action, $user_id, $action_options, $automation ) {

        // Shorthand
        $tag_id = $action_options['tag'];

        // Get the user contact email
        $user = get_user_by('id', $user_id);
        $user_email = $user->user_email;

        // Check if user is a contact in ActiveCampaign
        $user_am360 = mbr_get_contact_by_email( $user_email );

        if ( empty( $user_am360 ) )  {
            return;
        }

        $tags = array();

        // Check specific tag
        if( $tag_id !== 'any' ) {

            $tags = array( $tag_id );

        }

        // Setup the data to sync
        $data = array(
            'contact_data' => array(
                'email' => $user_am360['email']
            )
        );

        // If adding to all tags, get all tags
        if( $tag_id === 'any' ) {

            $all_tags = apply_filters( 'mbr/site_tags/get', NULL );

            if( ! empty( $all_tags ) ) {
                $tags = array_keys( $all_tags );
            }
        }

        // Add tags to the user
        $data['assign_tags'] = $tags;

        // Sync user account
        apply_filters( 'mbr/api/sync_ctla', $data );

    }

}

new AutomatorWP_ActiveMember360_User_Tag();