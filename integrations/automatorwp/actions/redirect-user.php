<?php
/**
 * Redirect User
 *
 * @package     AutomatorWP\Integrations\WordPress\Actions\Redirect_User
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WordPress_Redirect_User extends AutomatorWP_Integration_Action {

    public $integration = 'automatorwp';
    public $action = 'automatorwp_redirect_user';

    /**
     * URL to redirect
     *
     * @since 1.0.0
     *
     * @var string $url
     */
    public $url = '';

    /**
     * The action result
     *
     * @since 1.0.0
     *
     * @var string $result
     */
    public $result = '';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Redirect user to url', 'automatorwp' ),
            'select_option'     => __( 'Redirect user to <strong>url</strong>', 'automatorwp' ),
            /* translators: %1$s: URL. */
            'edit_label'        => sprintf( __( 'Redirect user to %1$s', 'automatorwp' ), '{url}' ),
            /* translators: %1$s: URL. */
            'log_label'         => sprintf( __( 'Redirect user to %1$s', 'automatorwp' ), '{url}' ),
            'options'           => array(
                'url' => array(
                    'from' => 'url',
                    'default' => __( 'url', 'automatorwp' ),
                    'fields' => array(
                        'url' => array(
                            'name' => __( 'URL:', 'automatorwp' ),
                            'desc' => __( 'The url to redirect.', 'automatorwp' ),
                            'type' => 'text',
                            'required'  => true,
                            'attributes'  => array(
                                'placeholder' => 'https://'
                            ),
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

        // Setup URL
        $this->url = esc_url( $action_options['url'] );

        // Restore replaced ampersands (&)
        $this->url = str_replace('#038;', '&', $this->url);
        $this->url = str_replace('&&', '&', $this->url);

        // Validate last URL
        if ( ! filter_var( $this->url, FILTER_VALIDATE_URL ) ) {
            $this->result = sprintf( __( '%s is not a valid URL.', 'automatorwp' ), $this->url );
            $this->url = '';
            return;
        }

        // Override others wp_redirect() calls
        add_filter( 'wp_redirect', array( $this, 'wp_redirect' ), 10, 2 );

        if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {           
            // If doing an ajax, cron or rest request, update an internal option for this user
            update_option( 'automatorwp_redirect_url_' . $user_id, $this->url, false );       
        } else { ?>
            <script type="text/javascript">
                setTimeout( function () {
                    document.location.href = '<?php echo $this->url ?>';
                }, 100 );
            </script>
        <?php }

        $this->result = __( 'User redirected successfully.', 'automatorwp' );

    }

    /**
     * Override others wp_redirect() calls
     *
     * @since 1.0.0
     *
     * @param string $location The path or URL to redirect to.
     * @param int    $status   The HTTP response status code to use.
     *
     * @return string
     */
    public function wp_redirect( $location, $status ) {

        if( ! empty( $this->url ) && filter_var( $this->url, FILTER_VALIDATE_URL ) ) {
            $location = $this->url;
        }

        return $location;

    }

    /**
     * Register required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Ajax handler
        add_action( 'wp_ajax_automatorwp_check_for_redirect', array( $this, 'ajax_check_for_redirect' ) );

        // Log meta data
        add_filter( 'automatorwp_user_completed_action_log_meta', array( $this, 'log_meta' ), 10, 5 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

        parent::hooks();
    }

    /**
     * Check if user should get redirected
     *
     * @since 1.0.0
     */
    public function ajax_check_for_redirect() {
        
        // Security check, forces to die if not security passed
        check_ajax_referer( 'automatorwp', 'nonce' );

        $user_id = absint( $_REQUEST['user_id'] );
        
        // Get the redirect URL for this user
        $url = get_option( 'automatorwp_redirect_url_' . $user_id, '' );

        $url = esc_url( $url, null, 'edit' );

        if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
            $url = '';
        }

        // Delete the redirect URL option
        delete_option( 'automatorwp_redirect_url_' . $user_id );

        // Return the URL
        wp_send_json_success( array(
            'redirect_url' => $url
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

        $log_meta['url'] = $action_options['url'];
        $log_meta['result'] = $this->result;

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

        $log_fields['url'] = array(
            'name' => __( 'URL:', 'automatorwp' ),
            'desc' => __( 'The url where user has been redirected.', 'automatorwp' ),
            'type' => 'text',
        );

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp' ),
            'type' => 'text',
        );

        return $log_fields;
    }

}

new AutomatorWP_WordPress_Redirect_User();