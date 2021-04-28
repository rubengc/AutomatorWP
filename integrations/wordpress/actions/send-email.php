<?php
/**
 * Send Email
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\Send_Email
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Send_Email extends AutomatorWP_Integration_Action {

    public $integration = 'wordpress';
    public $action = 'wordpress_send_email';

    /**
     * Store the action result
     *
     * @since 1.0.0
     *
     * @var bool $result
     */
    public $result = false;

    /**
     * Register required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Log meta data
        add_filter( 'automatorwp_user_completed_action_log_meta', array( $this, 'log_meta' ), 10, 5 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

        parent::hooks();
    }

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Send an email', 'automatorwp' ),
            'select_option'     => __( 'Send <strong>an email</strong>', 'automatorwp' ),
            /* translators: %1$s: Email. */
            'edit_label'        => sprintf( __( 'Send an email to %1$s', 'automatorwp' ), '{email}' ),
            /* translators: %1$s: Email. */
            'log_label'         => sprintf( __( 'Send an email to %1$s', 'automatorwp' ), '{email}' ),
            'options'           => array(
                'email' => array(
                    'from' => 'to',
                    'default' => __( 'user', 'automatorwp' ),
                    'fields' => array(
                        'from' => array(
                            'name' => __( 'From:', 'automatorwp' ),
                            'desc' => __( 'Leave empty to use default WordPress email.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'to' => array(
                            'name' => __( 'To:', 'automatorwp' ),
                            'desc' => __( 'Email address(es) to send the email. Accepts single or comma-separated list of emails.', 'automatorwp' )
                                . '<br>' . __( 'Leave empty to use the email of the user that completes the automation.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'cc' => array(
                            'name' => __( 'CC:', 'automatorwp' ),
                            'desc' => __( 'Email address(es) that will receive a copy of this email. Accepts single or comma-separated list of emails.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'bcc' => array(
                            'name' => __( 'BCC:', 'automatorwp' ),
                            'desc' => __( 'Email address(es) that will receive a copy of this email. Accepts single or comma-separated list of emails.', 'automatorwp' ),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'subject' => array(
                            'name' => __( 'Subject:', 'automatorwp' ),
                            'desc' => __( 'Email\'s subject.', 'automatorwp' ),
                            'type' => 'text',
                            'required'  => true,
                            'default' => ''
                        ),
                        'content' => array(
                            'name' => __( 'Content:', 'automatorwp' ),
                            'desc' => __( 'Email\'s content.', 'automatorwp' ),
                            'type' => 'wysiwyg',
                            'required'  => true,
                            'default' => ''
                        ),
                    )
                )
            ),
        ) );

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
        $to = $action_options['to'];

        // Setup to
        if( empty( $to ) ) {
            $user = get_userdata( $user_id );
            $to = $user->user_email;
        }

        // Send the email
        $this->result = automatorwp_send_email( array(
            // Email parameters
            'from'              => $action_options['from'],
            'to'                => $to,
            'cc'                => $action_options['cc'],
            'bcc'               => $action_options['bcc'],
            'subject'           => $action_options['subject'],
            'message'           => $action_options['content'],
            // Custom parameters
            'action'            => $action,
            'user_id'           => $user_id,
            'action_options'    => $action_options,
            'automation'        => $automation
        ) );

    }

    /**
     * Action custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     *
     * @return array
     */
    public function log_meta( $log_meta, $action, $user_id, $action_options, $automation ) {

        // Bail if action type don't match this action
        if( $action->type !== $this->action ) {
            return $log_meta;
        }

        $log_meta['from']       = $action_options['from'];
        $log_meta['to']         = $action_options['to'];
        $log_meta['cc']         = $action_options['cc'];
        $log_meta['bcc']        = $action_options['bcc'];
        $log_meta['subject']    = $action_options['subject'];
        $log_meta['content']    = $action_options['content'];
        $log_meta['result']     = ( $this->result ? __( 'Sent', 'automatorwp' ) : __( 'Not sent', 'automatorwp' ) );

        return $log_meta;
    }

    /**
     * Action custom log fields
     *
     * @since 1.0.0
     *
     * @param array     $log_fields The log fields
     * @param stdClass  $log        The log object
     * @param stdClass  $object     The trigger/action/automation object attached to the log
     *
     * @return array
     */
    public function log_fields( $log_fields, $log, $object ) {

        // Bail if log is not assigned to an action
        if( $log->type !== 'action' ) {
            return $log_fields;
        }

        // Bail if action type don't match this action
        if( $object->type !== $this->action ) {
            return $log_fields;
        }

        $log_fields['email_info'] = array(
            'name' => __( 'Sending Information', 'automatorwp' ),
            'desc' => __( 'Information about email sent.', 'automatorwp' ),
            'type' => 'title',
        );

        $log_fields['from'] = array(
            'name' => __( 'From:', 'automatorwp' ),
            'desc' => __( 'Email sender.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['to'] = array(
            'name' => __( 'To:', 'automatorwp' ),
            'desc' => __( 'Email recipient.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['cc'] = array(
            'name' => __( 'CC:', 'automatorwp' ),
            'desc' => __( 'Carbon copy emails.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['bcc'] = array(
            'name' => __( 'BCC:', 'automatorwp' ),
            'desc' => __( 'Blind carbon copy emails.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['subject'] = array(
            'name' => __( 'Subject:', 'automatorwp' ),
            'desc' => __( 'Email\'s subject.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['content'] = array(
            'name' => __( 'Content:', 'automatorwp' ),
            'desc' => __( 'Email\'s content.', 'automatorwp' ),
            'type' => 'text',
            'wpautop' => true,
        );

        $log_fields['result'] = array(
            'name' => __( 'Sending result:', 'automatorwp' ),
            'desc' => __( 'If sending result is "Not sent" you need to check if your server\'s wp_mail() function is correctly configured.', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;
    }

}

new AutomatorWP_WordPress_Send_Email();