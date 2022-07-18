<?php
/**
 * @package      RGC\CMB2\Field_JS_Controls
 * @author       Ruben Garcia (RubenGC) <rubengcdev@gmail.com>, GamiPress <contact@gamipress.com>
 * @copyright    Copyright (c) Tsunoa
 *
 * Plugin Name: CMB2 Field JS Controls
 * Plugin URI: https://github.com/rubengc/cmb2-field-js-controls
 * GitHub Plugin URI: https://github.com/rubengc/cmb2-field-js-controls
 * Description: Show any field similar to Wordpress publishing actions (Post/Page post_status, visibility and post_date submit box field).
 * Version: 1.0.0
 * Author: Tsunoa
 * Author URI: https://tsunoa.com/
 * License: GPLv2+
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Prevent CMB2 autoload adding "RGC_" at start
if( !class_exists( 'RGC_CMB2_Field_JS_Controls' ) ) {

    /**
     * Class RGC_CMB2_Field_JS_Controls
     */
    class RGC_CMB2_Field_JS_Controls {

        /**
         * Current version number
         */
        const VERSION = '1.0.0';

        /**
         * Initialize the plugin by hooking into CMB2
         */
        public function __construct() {
            add_action( 'admin_enqueue_scripts', array( $this, 'setup_admin_scripts' ) );

            // TODO: Find a way to add this content if field has parameter 'js_controls' => true
        }

        /**
         * @param  array        $field_args     Current field args
         * @param  CMB2_Field   $field          Current field object
         */
        public function before_row( $field_args, $field ) {
            $field_args = $this->parse_field_args( $field_args );

            if( ! empty( $field_args['js_controls']['icon'] ) ) {
                $icon = ( ( strpos( $field_args['js_controls']['icon'], 'dashicons-' ) !== false ) ? 'dashicons ' : '' ) . $field_args['js_controls']['icon'];
            }

            $id_attr_prefix = 'cmb-field-js-controls-' . $field_args['id'];

            ?>
            <div id="<?php echo $id_attr_prefix; ?>-before" class="cmb-field-js-controls-before">

                <?php if( isset( $icon ) ) : ?>
                    <span id="<?php echo $id_attr_prefix; ?>-icon" class="cmb-field-js-controls-icon <?php echo $icon; ?>"></span>
                <?php endif; ?>

                <span id="<?php echo $id_attr_prefix; ?>-label" class="cmb-field-js-controls-label"><?php echo $field_args['name']; ?>:</span>

                <div id="<?php echo $id_attr_prefix; ?>-value" class="cmb-field-js-controls-value"><?php $field->render_column(); ?></div>

                <?php if( $field_args['js_controls']['edit_button'] ) : ?>
                    <a href="#<?php echo $field_args['id']; ?>" id="<?php echo $id_attr_prefix; ?>-edit" class="cmb-field-js-controls-edit hide-if-no-js"><?php echo $field_args['js_controls']['edit_button']; ?></a>
                <?php endif; ?>
            </div>
            <?php
        }

        /**
         * @param  array        $field_args     Current field args
         * @param  CMB2_Field   $field          Current field object
         */
        public function after_row( $field_args, $field ) {
            $field_args = $this->parse_field_args( $field_args );

            $id_attr_prefix = 'cmb-field-js-controls-' . $field_args['id'];

            ?>
            <div id="<?php echo $id_attr_prefix; ?>-after" class="cmb-field-js-controls-after hide-if-js">
                <?php if( $field_args['js_controls']['save_button'] ) : ?>
                    <a href="#<?php echo $field_args['id']; ?>"
                       id="<?php echo $id_attr_prefix; ?>-save"
                       class="cmb-field-js-controls-save <?php echo esc_attr( $field_args['js_controls']['save_button_classes'] ); ?>"
                    ><?php echo $field_args['js_controls']['save_button']; ?></a>
                <?php endif; ?>

                <?php if( $field_args['js_controls']['cancel_button'] ) : ?>
                    <a href="#<?php echo $field_args['id']; ?>"
                       id="<?php echo $id_attr_prefix; ?>-cancel"
                       class="cmb-field-js-controls-cancel <?php echo esc_attr( $field_args['js_controls']['cancel_button_classes'] ); ?>"
                    ><?php echo $field_args['js_controls']['cancel_button']; ?></a>
                <?php endif; ?>
            </div>
            <?php
        }

        /**
         * @param   array   $field_args     Current field args
         * @return  array
         */
        private function parse_field_args( $field_args ) {
            $field_args['js_controls'] = array_merge(
                array(
                    'icon'          => '',
                    'edit_button'   => __( 'Edit' ),
                    'save_button'   => __( 'OK' ),
                    'save_button_classes'   => 'button',
                    'cancel_button' => __( 'Cancel' ),
                    'cancel_button_classes' => '',
                ),
                ( ( isset( $field_args['js_controls'] ) && is_array( $field_args['js_controls'] ) ) ? $field_args['js_controls'] : array() )
            );

            return $field_args;
        }

        /**
         * Enqueue scripts and styles
         */
        public function setup_admin_scripts() {
            wp_register_script( 'cmb-js-controls-event-manager', plugins_url( 'js/event-manager.min.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );
            wp_register_script( 'cmb-js-controls', plugins_url( 'js/js-controls.js', __FILE__ ), array( 'jquery', 'cmb-js-controls-event-manager' ), self::VERSION, true );

            wp_enqueue_script( 'cmb-js-controls-event-manager' );
            wp_enqueue_script( 'cmb-js-controls' );

            wp_enqueue_style( 'cmb-js-controls', plugins_url( 'css/js-controls.css', __FILE__ ), array(), self::VERSION );
        }

    }

    //$cmb2_field_js_controls = new RGC_CMB2_Field_JS_Controls();

    // TODO: Temporal solution to output html content
    function js_controls_before( $field_args, $field ) {
        $cmb2_field_js_controls = new RGC_CMB2_Field_JS_Controls();

        $cmb2_field_js_controls->before_row( $field_args, $field );
    }

    function js_controls_after( $field_args, $field ) {
        $cmb2_field_js_controls = new RGC_CMB2_Field_JS_Controls();

        $cmb2_field_js_controls->after_row( $field_args, $field );
    }

    $cmb2_field_js_controls = new RGC_CMB2_Field_JS_Controls();
}