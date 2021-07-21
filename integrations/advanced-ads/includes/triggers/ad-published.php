<?php
/**
 * Ad Published
 *
 * @package     AutomatorWP\Integrations\Advanced_Ads\Triggers\Ad_Published
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Advanced_Ads_Ad_Published extends AutomatorWP_Integration_Trigger {

    public $integration = 'advanced_ads';
    public $trigger = 'advanced_ads_ad_published';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User ad gets published', 'automatorwp' ),
            'select_option'     => __( 'User ad gets <strong>published</strong>', 'automatorwp' ),
            /* translators: %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'User ad gets published %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'User ad gets published', 'automatorwp' ),
            'action'            => 'advanced-ads-ad-status-published',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 1,
            'options'           => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param Advanced_Ads_Ad $ad
     */
    public function listener( $ad ) {

        $post = get_post( $ad->id );

        // Bail if post does not exists
        if( ! $post ) {
            return;
        }

        $user_id = absint( $post->post_author );

        // Bail if post does not has an author assigned
        if( absint( $user_id === 0 ) ) {
            return;
        }

        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
            'post_id'   => $ad->id,
        ) );

    }

}

new AutomatorWP_Advanced_Ads_Ad_Published();