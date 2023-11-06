<?php
/**
 * Anonymous Donation
 *
 * @package     AutomatorWP\Integrations\Charitable\Triggers\Anonymous_Donation
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Charitable_Anonymous_Donation extends AutomatorWP_Integration_Trigger {

    public $integration = 'charitable';
    public $trigger = 'charitable_anonymous_donation';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'anonymous'         => true,
            'label'             => __( 'Guest makes a donation', 'automatorwp' ),
            'select_option'     => __( 'Guest <strong>makes</strong> a donation', 'automatorwp' ),
            /* %1$s: Number of times. */
            'edit_label'        => sprintf( __( 'Guest makes a donation %1$s time(s)', 'automatorwp' ), '{times}' ),
            'log_label'         => __( 'Guest makes a donation', 'automatorwp' ),
            'action'            => 'charitable_donation_save',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags'              => array_merge(
                automatorwp_charitable_get_donation_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int     $donation_id The donation ID.
	 * @param WP_Post $post        Instance of `WP_Post`.
     */
    public function listener( $donation_id, $post ) {

        $donation = charitable_get_donation( $donation_id );
       
        $donor = $donation->get_donor_data();

        $user = get_user_by( 'email', $donor['email'] );

        // Bail if no user
        if ( !empty( $user ) ) {
            return;
        }
     
        // Get campaigns.
		$campaigns = $donation->get_campaign_donations();
        
        // Bail no campaigns.
		if ( empty( $campaigns ) ) {
			return;
		}

        foreach ( $campaigns as $campaign ) {
            $campaign_id = $campaign->campaign_id;
        }

        $campaign = charitable_get_campaign( $campaign_id );

        // Bail if not approved status
        if ( ! charitable_is_approved_status( get_post_status( $donation_id ) ) ) {
			return false;
		}

		$old_status = ! empty( $_POST['original_post_status'] ) ? $_POST['original_post_status'] : '';

        // Bail if same status
		if ( $old_status === $post->post_status ) {
			return;
		}
      
        // Trigger the become an affiliate
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'donation_id'   => $donation_id,
            'donation_title' => get_the_title( $donation_id ),
            'donation_amount' => $donation->get_amount_formatted(),
            'donation_status' => $donation->get_status_label(),
            'donation_payment_method' => $donation->get_gateway_label(),
            'donor_id' => $user_id,
            'donor_first_name' => $donor['first_name'],
            'donor_last_name' => $donor['last_name'],
            'donor_country' => $donor['country'],
            'donor_phone' => $donor['phone'],
            'campaign_id' => $campaign_id,
            'campaign_title' => $campaign->post_title,
            'campaign_goal' => charitable_format_money( $campaign->get_goal() ),
            'campaign_min_donation' => charitable_format_money( charitable_get_minimum_donation_amount( $campaign_id ) ),
            'campaign_end_date' => $campaign->get_end_date(),

        ) );

    }

    /**
     * Register the required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_anonymous_completed_trigger_log_meta', array( $this, 'log_meta' ), 10, 5 );

        parent::hooks();
    }

    /**
     * Trigger custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $trigger            The trigger object
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return array
     */
    function log_meta( $log_meta, $trigger, $event, $trigger_options, $automation ) {

        // Bail if action type don't match this action
        if( $trigger->type !== $this->trigger ) {
            return $log_meta;
        }

        $log_meta['donation_id'] = ( isset( $event['donation_id'] ) ? $event['donation_id'] : '' );
        $log_meta['donation_title'] = ( isset( $event['donation_title'] ) ? $event['donation_title'] : '' );
        $log_meta['donation_amount'] = ( isset( $event['donation_amount'] ) ? $event['donation_amount'] : 0.00 );
        $log_meta['donation_status'] = ( isset( $event['donation_status'] ) ? $event['donation_status'] : '' );
        $log_meta['donation_payment_method'] = ( isset( $event['donation_payment_method'] ) ? $event['donation_payment_method'] : '' );
        $log_meta['donor_id'] = ( isset( $event['donor_id'] ) ? $event['donor_id'] : '' );
        $log_meta['donor_first_name'] = ( isset( $event['donor_first_name'] ) ? $event['donor_first_name'] : '' );
        $log_meta['donor_last_name'] = ( isset( $event['donor_last_name'] ) ? $event['donor_last_name'] : '' );
        $log_meta['donor_country'] = ( isset( $event['donor_country'] ) ? $event['donor_country'] : '' );
        $log_meta['donor_phone'] = ( isset( $event['donor_phone'] ) ? $event['donor_phone'] : '' );
        $log_meta['campaign_id'] = ( isset( $event['campaign_id'] ) ? $event['campaign_id'] : '' );
        $log_meta['campaign_title'] = ( isset( $event['campaign_title'] ) ? $event['campaign_title'] : '' );
        $log_meta['campaign_goal'] = ( isset( $event['campaign_goal'] ) ? $event['campaign_goal'] : '' );
        $log_meta['campaign_min_donation'] = ( isset( $event['campaign_min_donation'] ) ? $event['campaign_min_donation'] : '' );
        $log_meta['campaign_end_date'] = ( isset( $event['campaign_end_date'] ) ? $event['campaign_end_date'] : '' );

        return $log_meta;

    }

}

new AutomatorWP_Charitable_Anonymous_Donation();