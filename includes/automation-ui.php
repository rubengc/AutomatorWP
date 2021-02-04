<?php
/**
 * Automation UI
 *
 * @package     AutomatorWP\Automation_UI
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Render automation type dialog
 *
 * @since 1.3.0
 */
function automatorwp_render_automation_type_dialog() {

    if( ! isset( $_GET['page'] ) ) {
        return;
    }

    $allowed_pages = array(
        'automatorwp_automations',
        'edit_automatorwp_automations',
    );

    // Only render on allowed pages
    if( ! in_array( $_GET['page'], $allowed_pages ) ) {
        return;
    }

    $types = automatorwp_get_automation_types(); ?>

    <div class="automatorwp-automation-type-dialog-wrapper" style="display: none;">

        <div class="automatorwp-automation-type-dialog">
            <h2><?php _e( 'Automation type', 'automatorwp' ); ?></h2>
            <div class="automatorwp-automation-types">
                <?php foreach( $types as $type => $args ) : ?>
                    <div class="automatorwp-automation-type" data-type="<?php echo $type; ?>">
                        <img src="<?php echo $args['image']; ?>" alt="<?php echo $args['label']; ?>">
                        <strong><?php echo $args['label']; ?></strong>
                        <span><?php echo $args['desc']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="automatorwp-notice-warning">
                <?php echo __( '<strong>Note:</strong> Automation type cannot be changed later. ', 'automatorwp' ); ?>
            </div>
            <div class="automatorwp-automation-type-dialog-bottom">
                <div class="automatorwp-automation-type-dialog-buttons">
                    <button type="button" class="button button-primary automatorwp-automation-type-dialog-confirm"><?php _e( 'Confirm', 'automatorwp' ); ?></button>
                    <button type="button" class="button automatorwp-button-danger automatorwp-automation-type-dialog-cancel"><?php _e( 'Cancel', 'automatorwp' ); ?></button>
                </div>
            </div>
        </div>

    </div>

    <?php

}
add_action( 'admin_footer', 'automatorwp_render_automation_type_dialog' );

/**
 * Render automation type dialog
 *
 * @since 1.3.0
 *
 * @param string $classes
 *
 * @return string
 */
function automatorwp_automation_ui_admin_body_class( $classes ) {

    global $ct_table;

    if( ! isset( $_GET['page'] ) ) {
        return $classes;
    }

    // Only add class on allowed pages
    if( $_GET['page'] !== 'edit_automatorwp_automations' ) {
        return $classes;
    }

    if( $ct_table->name !== 'automatorwp_automations' ) {
        return $classes;
    }

    $primary_key = $ct_table->db->primary_key;

    if( ! isset( $_GET[$primary_key] ) ) {
        return $classes;
    }

    $automation_id = (int) $_GET[$primary_key];
    $automation = $ct_table->db->get( $automation_id );

    $classes .= ' edit-' . $automation->type . '-automation';

    return $classes;

}
add_filter( 'admin_body_class', 'automatorwp_automation_ui_admin_body_class' );

/**
 * Automation UI meta boxes
 *
 * @since  1.0.0
 */
function automatorwp_automation_ui_add_meta_boxes() {

    add_meta_box( 'automatorwp_triggers', __( 'Triggers', 'automatorwp' ), 'automatorwp_automation_ui_triggers_meta_box', 'automatorwp_automations', 'normal', 'default' );
    add_meta_box( 'automatorwp_actions', __( 'Actions', 'automatorwp' ), 'automatorwp_automation_ui_actions_meta_box', 'automatorwp_automations', 'normal', 'default' );

}
add_action( 'add_meta_boxes', 'automatorwp_automation_ui_add_meta_boxes' );

/**
 * Renders the triggers meta box
 *
 * @since  1.0.0
 *
 * @param stdClass $automation The automation object
 * @param string   $type       Type to render form
 */
function automatorwp_automation_ui_triggers_meta_box( $automation, $type ) {

    $triggers = automatorwp_get_automation_triggers( $automation->id );

    ?>
    <?php // Section title and subtitle ?>
    <div class="automatorwp-title"><?php _e( 'Triggers', 'automatorwp' ); ?></div>
    <div class="automatorwp-subtitle"><?php _e( 'When this happens...', 'automatorwp' ); ?></div>

    <?php // Sequential ?>
    <div class="automatorwp-sequential-field cmb2-switch">
        <label for="sequential"><?php _e( 'Sequential', 'automatorwp' ); ?></label>
        <div class="cmb-td">
            <input type="checkbox" id="sequential" name="sequential" class="automatorwp-auto-save" value="1" <?php checked( $automation->sequential, 1 ); ?> />
            <label for="sequential"><span class="cmb2-metabox-description"><?php _e( 'Check this option to force users to complete triggers in order.', 'automatorwp' ); ?></span></label>
        </div>
    </div>

    <?php // Triggers ?>
    <div class="automatorwp-automation-items automatorwp-triggers">

        <?php foreach( $triggers as $trigger ) : ?>

            <?php automatorwp_automation_item_edit_html( $trigger, 'trigger', $automation ); ?>

        <?php endforeach; ?>

    </div>

    <?php automatorwp_automation_ui_add_item_form( $automation, 'trigger' ); ?>

    <div class="automatorwp-automation-ui-anonymous-notice"><span class="dashicons dashicons-info"></span> <?php _e( 'Anonymous automations only support one trigger per automation.', 'automatorwp' ); ?></div>
    <button type="button" class="button automatorwp-button-success automatorwp-add-trigger"><span class="dashicons dashicons-plus"></span><?php _e( 'Add Trigger', 'automatorwp' ); ?></button>
    <?php
}

/**
 * Renders the actions meta box
 *
 * @since  1.0.0
 *
 * @param stdClass $automation The automation object
 * @param string   $type       Type to render form
 */
function automatorwp_automation_ui_actions_meta_box( $automation, $type ) {

    $actions = automatorwp_get_automation_actions( $automation->id );

    ?>
    <?php // Section title and subtitle ?>
    <div class="automatorwp-title"><?php _e( 'Actions', 'automatorwp' ); ?></div>
    <div class="automatorwp-subtitle"><?php _e( 'Do this...', 'automatorwp' ); ?></div>

    <?php // Actions ?>
    <div class="automatorwp-automation-items automatorwp-actions">

        <?php $actions = automatorwp_check_anonymous_user_action( $automation, $actions ); ?>

        <?php foreach( $actions as $action ) : ?>

            <?php automatorwp_automation_item_edit_html( $action, 'action', $automation ); ?>

        <?php endforeach; ?>

    </div>

    <?php automatorwp_automation_ui_add_item_form( $automation, 'action' ); ?>

    <button type="button" class="button automatorwp-button-success automatorwp-add-action"><span class="dashicons dashicons-plus"></span><?php _e( 'Add Action', 'automatorwp' ); ?></button>

    <?php
}

/**
 * Automation UI add item form
 *
 * @since 1.0.0
 *
 * @param stdClass  $automation The automation object
 * @param string    $item_type  The item type (trigger|action)
 */
function automatorwp_automation_ui_add_item_form( $automation, $item_type ) {

    $choices_filters = array();

    if( $item_type === 'trigger' ) {
        $choices_filters['anonymous'] = (bool) ( $automation->type === 'anonymous' );
    }

    ?>

    <div class="automatorwp-add-item-form" style="display: none;">

        <div class="automatorwp-automation-item-details">
            <div class="automatorwp-integration-icon"></div>
        </div>

        <div class="automatorwp-automation-item-content">

            <div class="automatorwp-select-integration">

                <div class="automatorwp-select-integration-label"><?php _e( 'Select an integration', 'automatorwp' ); ?></div>

                <div class="automatorwp-integrations">

                    <?php foreach( AutomatorWP()->integrations as $integration => $args ) : ?>

                        <?php switch ( $item_type ) {
                            case 'trigger':
                                $choices = automatorwp_get_integration_triggers( $integration, $choices_filters );
                                break;
                            case 'action':
                                $choices = automatorwp_get_integration_actions( $integration, $choices_filters );
                                break;
                            default:
                                $choices = array();
                                break;
                        }

                        // Skip integrations without triggers or actions
                        if( ! empty( $choices ) ) : ?>

                            <div class="automatorwp-integration"
                                 data-integration="<?php echo esc_attr( $integration ); ?>"
                                 data-label="<?php echo esc_attr( $args['label'] ); ?>"
                                 data-icon="<?php echo esc_attr( $args['icon'] ); ?>">
                                <div class="automatorwp-integration-icon">
                                    <img src="<?php echo esc_attr( $args['icon'] ); ?>" alt="<?php echo esc_attr( $args['label'] ); ?>">
                                </div>
                                <div class="automatorwp-integration-label"><?php echo $args['label']; ?></div>
                            </div>

                        <?php endif; ?>

                        <?php
                        /**
                         * Available action to extend integration markup
                         *
                         * @since 1.0.0
                         *
                         * @param string    $integration        The integration name
                         * @param array     $integration_args   Integration arguments
                         * @param stdClass  $automation         The automation object
                         * @param string    $item_type          The item type
                         */
                        do_action( 'automatorwp_automation_ui_after_integration_choice', $integration, $args, $automation, $item_type ); ?>

                    <?php endforeach; ?>

                </div>

            </div>

            <div class="automatorwp-integration-choices-container" style="display: none;">

                <?php if ( $item_type === 'trigger' ) : ?>

                    <div class="automatorwp-select-trigger-label"><?php _e( 'Select a trigger', 'automatorwp' ); ?></div>

                    <?php foreach( AutomatorWP()->integrations as $integration => $args ) : ?>

                        <select class="automatorwp-integration-choices"
                                data-integration="<?php echo esc_attr( $integration ); ?>"
                                data-placeholder="<?php echo esc_attr( __( 'Search for triggers', 'automatorwp' ) ); ?>"
                                style="display: none;">

                                <option value=""></option>
                            <?php foreach( automatorwp_get_integration_triggers( $integration, $choices_filters ) as $trigger => $args ) :

                                // Skip no label items
                                if( empty( $args['select_option'] ) && empty( $args['label'] ) ) {
                                    continue;
                                } ?>
                                <option value="<?php echo esc_attr( $trigger ); ?>" data-text="<?php echo esc_attr( $args['select_option'] ); ?>"><?php echo $args['label']; ?></option>
                            <?php endforeach; ?>

                            <?php
                            /**
                             * Available action to extend integration triggers choices
                             *
                             * @since 1.2.4
                             *
                             * @param string    $integration        The integration name
                             * @param array     $integration_args   Integration arguments
                             * @param stdClass  $automation         The automation object
                             * @param string    $item_type          The item type
                             */
                            do_action( 'automatorwp_automation_ui_after_integration_triggers_choices', $integration, $args, $automation, $item_type ); ?>

                        </select>

                    <?php endforeach; ?>

                <?php elseif ( $item_type === 'action' ) : ?>

                    <div class="automatorwp-select-action-label"><?php _e( 'Select an action', 'automatorwp' ); ?></div>

                    <?php foreach( AutomatorWP()->integrations as $integration => $args ) : ?>

                        <select class="automatorwp-integration-choices"
                                data-integration="<?php echo esc_attr( $integration ); ?>"
                                data-placeholder="<?php echo esc_attr( __( 'Search for actions', 'automatorwp' ) ); ?>"
                                style="display: none;">

                            <option value=""></option>
                            <?php foreach( automatorwp_get_integration_actions( $integration, $choices_filters ) as $action => $args ) :
                                // Skip no label items
                                if( empty( $args['select_option'] ) && empty( $args['label'] ) ) {
                                    continue;
                                } ?>
                                <option value="<?php echo esc_attr( $action ); ?>" data-text="<?php echo esc_attr( $args['select_option'] ); ?>"><?php echo $args['label']; ?></option>
                            <?php endforeach; ?>

                            <?php
                            /**
                             * Available action to extend integration actions choices
                             *
                             * @since 1.2.4
                             *
                             * @param string    $integration        The integration name
                             * @param array     $integration_args   Integration arguments
                             * @param stdClass  $automation         The automation object
                             * @param string    $item_type          The item type
                             */
                            do_action( 'automatorwp_automation_ui_after_integration_actions_choices', $integration, $args, $automation, $item_type ); ?>

                        </select>

                    <?php endforeach; ?>

                <?php endif; ?>

                <button type="button" class="button automatorwp-button-danger automatorwp-cancel-choice-select"><?php _e( 'Cancel', 'automatorwp' ); ?></button>

                <div class="automatorwp-spinner" style="display: none;">
                    <span class="spinner is-active"></span>
                    <span class="spinner-label"><?php _e( 'Saving...', 'automatorwp' ); ?></span>
                </div>

            </div>

            <?php automatorwp_automation_ui_integrations_recommendations( $item_type ); ?>

        </div>

    </div>

    <?php

}

/**
 * Get the object type args
 *
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 *
 * @return array|false
 */
function automatorwp_automation_item_type_args( $object, $item_type ) {

    $type_args = array();

    if( $item_type === 'trigger' ) {
        $type_args = automatorwp_get_trigger( $object->type );
    } else if( $item_type === 'action' ) {
        $type_args = automatorwp_get_action( $object->type );
    }

    return $type_args;

}

/**
 * Renders the trigger/action edit HTML
 *
 * @since  1.0.0
 *
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param stdClass  $automation The automation object
 */
function automatorwp_automation_item_edit_html( $object, $item_type, $automation ) {

    // Check item type
    if( ! in_array( $item_type, array( 'trigger', 'action' ) ) ) {
        return;
    }

    // Check type args
    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        automatorwp_automation_missing_integration_item_edit_html( $object, $item_type, $automation );
        return;
    }

    // Check integration
    $integration = automatorwp_get_integration( $type_args['integration'] );

    if( ! $integration ) {
        automatorwp_automation_missing_integration_item_edit_html( $object, $item_type, $automation );
        return;
    }

    if( $item_type === 'action' && $object->type === 'automatorwp_anonymous_user' ) {
        $integration['icon'] = AUTOMATORWP_URL . 'assets/img/automatorwp-anonymous.svg';
    }

    // Setup the item classes
    $classes = array(
        'automatorwp-automation-item',
        'automatorwp-' . $item_type,
        'automatorwp-' . $item_type . '-' . str_replace( '_', '-', $object->type ),
    );

    // Setup the item actions
    $actions = array(
        'delete' => array(
            'label' => __( 'Delete', 'automatorwp'),
            'icon' => 'trash'
        )
    );

    if( $automation->type === 'anonymous' ) {
        if( $item_type === 'trigger' ) {
            $classes[] = 'automatorwp-no-grab';
        } else if( $object->type === 'automatorwp_anonymous_user' ) {
            $classes[] = 'automatorwp-no-grab';
            unset( $actions['delete'] );
        }
    }

    /**
     * Filter to modify the CSS classes of the trigger/action edit HTML
     *
     * @since  1.3.0
     *
     * @param array     $classes    The trigger/action CSS classes
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The item type (trigger|action)
     * @param stdClass  $automation The automation object
     *
     * @return array
     */
    $classes = apply_filters( 'automatorwp_automations_ui_item_edit_html_classes', $classes, $object, $item_type, $automation );

    /**
     * Filter to modify the actions of the trigger/action edit HTML
     *
     * @since  1.3.0
     *
     * @param array     $actions    The trigger/action actions
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The item type (trigger|action)
     * @param stdClass  $automation The automation object
     *
     * @return array
     */
    $actions = apply_filters( 'automatorwp_automations_ui_item_edit_html_actions', $actions, $object, $item_type, $automation );

    ?>
    <div id="automatorwp-item-<?php echo esc_attr( $object->id ); ?>" class="<?php echo implode( ' ', $classes ); ?>">

        <div class="automatorwp-automation-item-details">
            <div class="automatorwp-integration-icon">
                <img src="<?php echo esc_attr( $integration['icon'] ); ?>" title="<?php echo esc_attr( $integration['label'] ); ?>" alt="<?php echo esc_attr( $integration['label'] ); ?>">
            </div>
        </div>

        <div class="automatorwp-automation-item-content">

            <?php if( ! empty( $actions ) ) : ?>
                <div class="automatorwp-automation-item-actions">
                    <?php foreach( $actions as $action => $action_args ) : ?>
                    <div class="automatorwp-automation-item-action automatorwp-automation-item-action-<?php echo $action; ?>" title="<?php echo esc_attr( $action_args['label'] ); ?>"><span class="dashicons dashicons-<?php echo $action_args['icon']; ?>"></span></div>
                    <?php endforeach; ?>

                </div>
            <?php endif; ?>

            <div class="automatorwp-integration-label"><?php echo $integration['label']; ?></div>

            <div class="automatorwp-automation-item-position" style="<?php echo ( $automation->sequential ? '' : 'display: none;' ); ?>"><?php echo $object->position + 1; ?>.</div>
            <div class="automatorwp-automation-item-label"><?php echo automatorwp_parse_automation_item_edit_label( $object, $item_type ); ?></div>

            <?php
            /**
             * After item label
             *
             * @since 1.0.0
             *
             * @param stdClass  $object     The trigger/action object
             * @param string    $item_type  The object type (trigger|action)
             */
            do_action( 'automatorwp_automation_ui_after_item_label', $object, $item_type ); ?>

            <?php // Render the options form ?>
            <?php foreach( $type_args['options'] as $option => $args ) : ?>

                <div class="automatorwp-option-form-container" data-option="<?php echo esc_attr( $option ); ?>" data-from="<?php echo esc_attr( ( isset( $args['from'] ) ? $args['from'] : '' ) ); ?>" style="display: none;">

                    <?php
                    /**
                     * After option from
                     *
                     * @since 1.0.0
                     *
                     * @param stdClass  $object     The trigger/action object
                     * @param string    $item_type  The object type (trigger|action)
                     * @param string    $option     The option key
                     * @param array     $args       The option arguments
                     */
                    do_action( 'automatorwp_automation_ui_after_option_form', $object, $item_type, $option, $args ); ?>

                    <?php
                    // Get the option form
                    $cmb2 = automatorwp_get_automation_item_option_form( $object, $item_type, $option, $automation );

                    if( $cmb2 ) {

                        ct_setup_table( "automatorwp_{$item_type}s" );

                        // Render the form
                        CMB2_Hookup::enqueue_cmb_css();
                        CMB2_Hookup::enqueue_cmb_js();
                        $cmb2->show_form();

                        ct_reset_setup_table();
                    }
                    ?>

                    <?php
                    /**
                     * Before option from
                     *
                     * @since 1.0.0
                     *
                     * @param stdClass  $object     The trigger/action object
                     * @param string    $item_type  The object type (trigger|action)
                     * @param string    $option     The option key
                     * @param array     $args       The option arguments
                     */
                    do_action( 'automatorwp_automation_ui_before_option_form', $object, $item_type, $option, $args ); ?>

                    <button type="button" class="button button-primary automatorwp-save-option-form"><?php _e( 'Save', 'automatorwp' ); ?></button>
                    <button type="button" class="button automatorwp-button-danger automatorwp-cancel-option-form"><?php _e( 'Cancel', 'automatorwp' ); ?></button>

                    <div class="automatorwp-spinner" style="display: none;">
                        <span class="spinner is-active"></span>
                        <span class="spinner-label"><?php _e( 'Saving...', 'automatorwp' ); ?></span>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <?php // Hidden fields ?>
        <input type="hidden" class="id" value="<?php echo esc_attr( $object->id ); ?>"/>
        <input type="hidden" class="type" value="<?php echo esc_attr( $object->type ); ?>"/>
        <input type="hidden" class="status" value="<?php echo esc_attr( $object->status ); ?>"/>
        <input type="hidden" class="position" value="<?php echo esc_attr( $object->position ); ?>"/>

    </div>
    <?php

}

/**
 * Creates the anonymous user action if not exists
 *
 * @since  1.3.0
 *
 * @param stdClass  $automation The automation object
 * @param array     $actions The automation actions
 */
function automatorwp_check_anonymous_user_action( $automation, $actions ) {

    if( $automation->type === 'anonymous' ) {

        $create_user_action = false;

        // Check if the first action is the action required for anonymous automations
        if( ! isset( $actions[0] ) ) {
            $create_user_action = true;
        }

        if( isset( $actions[0] ) && $actions[0]->type !== 'automatorwp_anonymous_user' ) {
            $create_user_action = true;
        }

        if( $create_user_action ) {
            ct_setup_table( 'automatorwp_actions' );

            $action_data = array(
                'automation_id' => $automation->id,
                'title'         => sprintf( __( 'Actions will be run on %1$s', 'automatorwp' ), '{user}' ),
                'type'          => 'automatorwp_anonymous_user',
                'status'        => 'active',
                'position'      => '0',
                'date'          => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
            );

            // Insert the new action
            $action_id = ct_insert_object( $action_data );

            if( $action_id ) {
                $action_data['id'] = $action_id;

                $action_data = (object) $action_data;

                // Prepend the new action at start of the actions list
                array_unshift( $actions, $action_data );
            }

            ct_reset_setup_table();
        }

    }

    return $actions;

}

/**
 * Renders the trigger/action missing integration edit HTML
 *
 * @since  1.1.2
 *
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param stdClass  $automation The automation object
 */
function automatorwp_automation_missing_integration_item_edit_html( $object, $item_type, $automation ) {

    if( $item_type === 'trigger' ) {
        $warning_message = __( 'Trigger disabled because plugin associated couldn\'t be found. Please, re-install the plugin associated or remove this trigger.', 'automatorwp' );
    } else {
        $warning_message = __( 'Action disabled because plugin associated couldn\'t be found. Please, re-install the plugin associated or remove this action.', 'automatorwp' );

    }

    ?>
    <div id="automatorwp-item-<?php echo esc_attr( $object->id ); ?>" class="automatorwp-automation-item automatorwp-automation-missing-integration-item automatorwp-<?php echo esc_attr( $item_type ); ?>">

        <div class="automatorwp-automation-item-details">
            <div class="automatorwp-integration-icon">
                <img src="<?php echo esc_attr( AUTOMATORWP_URL . 'assets/img/integration-missing.svg' ); ?>" title="<?php echo esc_attr( __( 'Missing plugin', 'automatorwp' ) ); ?>">
            </div>
        </div>

        <div class="automatorwp-automation-item-content">

            <div class="automatorwp-automation-item-actions">
                <div class="automatorwp-automation-item-action automatorwp-automation-item-action-delete" title="<?php echo esc_attr( __( 'Delete', 'automatorwp') ); ?>"><span class="dashicons dashicons-trash"></span></div>
            </div>

            <div class="automatorwp-integration-label"><?php echo __( 'Missing plugin', 'automatorwp' ); ?></div>

            <div class="automatorwp-automation-item-position" style="<?php echo ( $automation->sequential ? '' : 'display: none;' ); ?>"><?php echo $object->position + 1; ?>.</div>
            <div class="automatorwp-automation-item-label"><?php echo $object->title; ?></div>

            <div class="automatorwp-notice-warning"><?php echo $warning_message; ?></div>

            <?php
            /**
             * After missing integration item label
             *
             * @since 1.1.2
             *
             * @param stdClass  $object     The trigger/action object
             * @param string    $item_type  The object type (trigger|action)
             */
            do_action( 'automatorwp_automation_ui_after_missing_integration_item_label', $object, $item_type ); ?>

        </div>

        <?php // Hidden fields ?>
        <input type="hidden" class="id" value="<?php echo esc_attr( $object->id ); ?>"/>
        <input type="hidden" class="type" value="<?php echo esc_attr( $object->type ); ?>"/>
        <input type="hidden" class="status" value="<?php echo esc_attr( $object->status ); ?>"/>
        <input type="hidden" class="position" value="<?php echo esc_attr( $object->position ); ?>"/>

    </div>
    <?php

}

/**
 * Parses the trigger/action edit label
 *
 * @since  1.0.0
 *
 * @param stdClass  $object     The trigger object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $context    The context this function is executed
 *
 * @return string
 */
function automatorwp_parse_automation_item_edit_label( $object, $item_type, $context = 'edit' ) {

    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        return '';
    }

    /**
     * Filter to dynamically change the edit label
     *
     * @since 1.0.0
     *
     * @param string    $label      The edit label
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The item type (trigger|action)
     * @param string    $context    The context this function is executed
     * @param array     $type_args  The type parameters
     *
     * @return string
     */
    $label = apply_filters( 'automatorwp_parse_automation_item_edit_label', $type_args['edit_label'], $object, $item_type, $context, $type_args );

    return automatorwp_parse_automation_item_label( $object, $item_type, $label, $context );

}

/**
 * Parses the trigger/action log label
 *
 * @since  1.0.0
 *
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $context    The context this function is executed
 *
 * @return string
 */
function automatorwp_parse_automation_item_log_label( $object, $item_type, $context = 'edit' ) {

    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        return '';
    }

    /**
     * Filter to dynamically change the log label
     *
     * @since 1.0.0
     *
     * @param string    $label      The edit label
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The item type (trigger|action)
     * @param string    $context    The context this function is executed
     * @param array     $type_args  The type parameters
     *
     * @return string
     */
    $label = apply_filters( 'automatorwp_parse_automation_item_log_label', $type_args['log_label'], $object, $item_type, $context, $type_args );

    return automatorwp_parse_automation_item_label( $object, $item_type, $label, $context );

}

/**
 * Parses the trigger/action label given
 *
 * @since  1.0.0
 *
 * @param stdClass  $object     The trigger object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $label      The label to parse
 * @param string    $context    The context this function is executed
 *
 * @return string
 */
function automatorwp_parse_automation_item_label( $object, $item_type, $label, $context = 'edit' ) {

    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        return '';
    }

    $replacements = array();

    foreach( $type_args['options'] as $option => $args ) {
        $replacements['{' . $option . '}'] = automatorwp_get_automation_item_option_replacement( $object, $item_type, $option, $context );
    }

    /**
     * Trigger/action label replacements
     *
     * @since 1.0.0
     *
     * @param array     $replacements   The replacements to apply
     * @param stdClass  $object         The trigger object
     * @param string    $item_type      The item type (trigger|action)
     * @param string    $label          The label to parse
     * @param string    $context        The context this function is executed
     *
     * @return array
     */
    $replacements = apply_filters( 'automatorwp_parse_automation_item_label_replacements', $replacements, $object, $item_type, $label, $context );

    $tags = array_keys( $replacements );

    $label_parsed = str_replace( $tags, $replacements, $label );

    /**
     * Trigger/action label parsed
     *
     * @since 1.0.0
     *
     * @param string    $label_parsed   The label parsed
     * @param stdClass  $object         The trigger object
     * @param string    $item_type      The item type (trigger|action)
     * @param string    $label          The originallabel to parse
     * @param string    $context        The context this function is executed
     * @param array     $tags           The tags applied
     * @param array     $replacements   The replacements applied
     *
     * @return string
     */
    return apply_filters( 'automatorwp_parse_automation_item_label', $label_parsed, $object, $item_type, $label, $context, $tags, $replacements );

}

/**
 * Get the option replacement
 *
 * @since 1.0.0
 *
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $option     The option name
 * @param string    $context    The context this function is executed
 *
 * @return string
 */
function automatorwp_get_automation_item_option_replacement( $object, $item_type, $option, $context = 'edit' ) {

    // Check item type
    if( ! in_array( $item_type, array( 'trigger', 'action' ) ) ) {
        return false;
    }

    // Check type args
    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        return '';
    }

    // Bail if this type hasn't any option
    if( ! isset( $type_args['options'][$option] ) ) {
        return '';
    }

    $option_args = $type_args['options'][$option];

    $field_id = ( isset( $option_args['from'] ) ? $option_args['from'] : '' );
    $value = '';

    // If not isset the from field, try to get a default value
    if( ! isset( $option_args['fields'][$field_id] ) ) {

        if( isset( $option_args['default'] ) && ! empty( $option_args['default'] ) ) {
            $value = $option_args['default'];
        }

    } else {

        ct_setup_table( "automatorwp_{$item_type}s" );

        $field = $option_args['fields'][$field_id];
        $value = ct_get_object_meta( $object->id, $field_id, true );

        if( empty( $value ) && isset( $field['default'] ) ) {
            $value = $field['default'];
        }

        // Select field
        if( in_array( $field['type'], array( 'select', 'automatorwp_select' ) ) ) {

            $options = array();

            // Try to get the field options from field args
            if( isset( $field['options'] ) ) {
                $options = $field['options'];
            } else if( isset( $field['options_cb'] ) && is_callable( $field['options_cb'] ) ) {

                $field['value'] = $value;
                $field['escaped_value'] = $value;
                $field['args'] = $field;

                $options = call_user_func( $field['options_cb'], (object) $field );
            }

            if( isset( $options[$value] ) ) {
                $value = $options[$value];
            }
        }

        // Fallback to default option if exists
        if( empty( $value ) && isset( $option_args['default'] ) && ! empty( $option_args['default'] ) ) {
            $value = $option_args['default'];
        }

        ct_reset_setup_table();

    }

    /**
     * Filters the option value for replacement on labels
     *
     * @since 1.0.0
     *
     * @param string    $value      The option value
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The item type (trigger|action)
     * @param string    $option     The option name
     * @param string    $context    The context this function is executed
     *
     * @return string
     */
    $value = apply_filters( 'automatorwp_get_automation_item_option_replacement', $value, $object, $item_type, $option, $context );

    if( $context === 'edit' ) {

        $option_class = 'button button-primary';

        /**
         * Filters the option button class
         *
         * @since 1.2.4
         *
         * @param string    $option_class   The option class, by default "button button-primary"
         * @param stdClass  $object         The trigger/action object
         * @param string    $item_type      The item type (trigger|action)
         * @param string    $option         The option name
         * @param string    $context        The context this function is executed
         *
         * @return string
         */
        $option_class = apply_filters( 'automatorwp_get_automation_item_option_button_class', $option_class, $object, $item_type, $option, $context );

        $value = '<span class="' . esc_attr__( $option_class ) . ' automatorwp-option" data-option="' . $option . '">' . $value . '</span>';
    }

    return $value;

}

/**
 * Gets a CMB2 object from a trigger option
 *
 * @since 1.0.0
 *
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $option     Option form to render
 * @param stdClass  $automation The automation object
 *
 * @return CMB2|false
 */
function automatorwp_get_automation_item_option_form( $object, $item_type, $option, $automation ) {

    // Check item type
    if( ! in_array( $item_type, array( 'trigger', 'action' ) ) ) {
        return false;
    }

    // Check type args
    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        return false;
    }

    // Bail if this type hasn't any option
    if( ! isset( $type_args['options'][$option] ) ) {
        return false;
    }

    $args = $type_args['options'][$option];

    ct_setup_table( "automatorwp_{$item_type}s" );

    // Setup the CMB2 form
    $cmb2 = new CMB2( array(
        'id'        => $option .'_form',
        'object_types' => array( 'automatorwp_triggers', 'automatorwp_actions' ),
        'classes'   => 'automatorwp-form automatorwp-option-form',
        'hookup'    => false,
    ), $object->id );

    // Setup the options fields
    foreach ( $args['fields'] as $field_id => $field ) {

        $field['id'] = $field_id;

        if( $field['type'] === 'group' ) {
            // Group fields

            // Setup field arguments on each group field
            foreach ( $field['fields'] as $field_group_id => $field_group ) {

                $field_group['id'] = $field_group_id;

                $field['fields'][$field_group_id] = automatorwp_automation_item_option_field_args( $object, $item_type, $option, $automation, $field_id . '[' .$field_group_id . ']', $field_group );

            }

        } else {
            // Single fields

            $field = automatorwp_automation_item_option_field_args( $object, $item_type, $option, $automation, $field_id, $field );

        }

        // Add the field to the form
        $cmb2->add_field( $field );

    }

    ct_reset_setup_table();

    return $cmb2;

}

/**
 * Gets a CMB2 object from a trigger option
 *
 * @since 1.0.0
 *
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $option     Option form to render
 * @param stdClass  $automation The automation object
 * @param string    $field_id   The field ID
 * @param array     $field      The field parameters
 *
 * @return array
 */
function automatorwp_automation_item_option_field_args( $object, $item_type, $option, $automation, $field_id, $field ) {

    $repeatable = ( isset( $field['repeatable'] ) && $field['repeatable'] === true );

    // Prevent to render field names to avoid conflicts on the main form
    $field['attributes']['name'] = '';

    // Update id attribute to avoid id collisions
    $field['attributes']['id'] = $field_id . '-' . $object->id;
    $field['attributes']['data-option'] = $field_id . ( $repeatable ? '[]' : '' );

    // Setup the fields tags selector
    if( $item_type === 'action' ) {

        // Set a specific sanitization callback for fields that support HTML and URLs
        if( in_array( $field['type'], array( 'textarea', 'wysiwyg', 'oembed' ) ) ) {
            $field['sanitization_cb'] = 'automatorwp_textarea_sanitization_cb';
        }

        // Check if field type is compatible with tags selector
        if( in_array( $field['type'], array( 'text', 'textarea', 'wysiwyg' ) ) ) {
            $field['after_field'] = automatorwp_get_tags_selector_html( $automation, $object, $item_type );
        }

    }

    // If field is required, update its label with the required mark
    if( isset( $field['required'] ) && $field['required'] && isset( $field['name'] ) ) {
        $field['name'] .= '<span class="automatorwp-field-required">*</span>';
    }

    /**
     * Filter available to process custom field parameters
     *
     * @since 1.0.0
     *
     * @param array     $field      The field parameters
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The item type (trigger|action)
     * @param string    $option     Option form to render
     * @param stdClass  $automation The automation object
     * @param string    $field_id   The field ID
     *
     * @return array
     */
    return apply_filters( 'automatorwp_automation_item_option_field_args', $field, $object, $item_type, $option, $automation, $field_id );

}

/**
 * Automation UI add-ons recommendations
 *
 * @since 1.1.2
 *
 * @param string $item_type The item type (trigger|action)
 */
function automatorwp_automation_ui_integrations_recommendations( $item_type ) {

    $integrations = automatorwp_get_recommended_integrations();

    // If not recommendations, show a generic message
    if ( is_wp_error( $integrations ) ||  empty( $integrations ) ) { ?>

        <div class="automatorwp-more-integrations">
            <span><?php if ( $item_type === 'trigger' ) : _e( 'Looking for more triggers?', 'automatorwp' ); elseif ( $item_type === 'action' ) : _e( 'Looking for more actions?', 'automatorwp' ); endif; ?></span>
            <a href="https://automatorwp.com/add-ons/" target="_blank"><?php _e( 'View all add-ons', 'automatorwp' ); ?></a>
        </div>

        <?php
        return;
    }
    ?>

    <div class="automatorwp-recommended-integrations">

        <div class="automatorwp-recommended-integrations-label">
            <span><?php printf( _n( '%d plugin of your site can be connected with AutomatorWP.', '%d plugins of your site can be connected with AutomatorWP.', count( $integrations ), 'automatorwp' ), count( $integrations ) ); ?></span>
            <a href="#"><?php _e( 'View plugins', 'automatorwp' ); ?></a>
        </div>

        <div class="automatorwp-integrations" style="display: none;">

            <?php foreach ( $integrations as $integration ) :

                // Setup the triggers and actions information
                $triggers_and_actions = array();

                if( count( $integration->triggers ) ) {
                    $triggers_and_actions[] = sprintf( _n( '%d trigger', '%d triggers', count( $integration->triggers ), 'automatorwp' ), count( $integration->triggers ) );
                }

                if( count( $integration->actions ) ) {
                    $triggers_and_actions[] = sprintf( _n( '%d action', '%d actions', count( $integration->actions ), 'automatorwp' ), count( $integration->actions ) );
                }

                // Setup the add-on slug for the add-on URL
                $slug = str_replace( '_', '-', $integration->code ); ?>

                <a class="automatorwp-integration"
                     href="https://automatorwp.com/add-ons/<?php echo $slug; ?>/"
                     target="_blank"
                     data-integration="<?php echo esc_attr( $integration->code ); ?>"
                     data-label="<?php echo esc_attr( $integration->title ); ?>"
                     data-icon="<?php echo esc_attr( $integration->icon ); ?>">
                    <div class="automatorwp-integration-icon">
                        <img src="<?php echo esc_attr( $integration->icon ); ?>" alt="<?php echo esc_attr( $integration->title ); ?>">
                    </div>
                    <div class="automatorwp-integration-label"><?php echo $integration->title; ?></div>
                    <div class="automatorwp-integration-triggers-and-actions"><?php echo implode( ', ', $triggers_and_actions ); ?></div>
                </a>

            <?php endforeach; ?>

        </div>

    </div>

    <?php

}

/**
 * Get recommended integrations
 *
 * @since 1.1.2
 *
 * @return array|WP_Error Object with recommended integrations
 */
function automatorwp_get_recommended_integrations() {

    $integrations = automatorwp_integrations_api();

    if( is_wp_error( $integrations ) ) {
        return $integrations;
    }

    $recommended_integrations = array();

    foreach ( $integrations as $integration ) {

        // Skip integration if can't determine its class
        if( empty( $integration->integration_class ) ) {
            continue;
        }

        // Skip integration if already installed
        if( class_exists( $integration->integration_class ) ) {
            continue;
        }

        // Skip integration if free version already installed
        if( class_exists( $integration->integration_class . '_Integration' ) ) {
            continue;
        }

        // Skip integration if hasn't defined any way to meet if plugin is installed
        if( empty( $integration->required_class )
            && empty( $integration->required_function )
            && empty( $integration->required_constant ) ) {
            continue;
        }

        // Skip if integrated plugin is not installed
        if( ! empty( $integration->required_class ) && ! class_exists( $integration->required_class ) ) {
            continue;
        }

        // Skip if integrated plugin is not installed
        if( ! empty( $integration->required_function ) && ! function_exists( $integration->required_function ) ) {
            continue;
        }

        // Skip if integrated plugin is not installed
        if( ! empty( $integration->required_constant ) && ! defined( $integration->required_constant ) ) {
            continue;
        }

        $recommended_integrations[] = $integration;

    }

    return $recommended_integrations;

}

/**
 * Function to contact with the AutomatorWP integrations API
 *
 * @since  1.1.2
 *
 * @return object|WP_Error Object with AutomatorWP integrations
 */
function automatorwp_integrations_api() {

    // If a integrations api request has been cached already, then use cached integrations
    if ( false !== ( $res = get_transient( 'automatorwp_integrations_api' ) ) ) {
        return $res;
    }

    $url = $http_url = 'http://automatorwp.com/wp-json/automatorwp/integrations';

    if ( $ssl = wp_http_supports( array( 'ssl' ) ) ) {
        $url = set_url_scheme( $url, 'https' );
    }

    $http_args = array(
        'timeout' => 15,
    );

    $request = wp_remote_get( $url, $http_args );

    if ( $ssl && is_wp_error( $request ) ) {
        trigger_error(
            sprintf(
                __( 'An unexpected error occurred. Something may be wrong with automatorwp.com or this server&#8217;s configuration. If you continue to have problems, please try to <a href="%s">contact us</a>.', 'automatorwp' ),
                'https://automatorwp.com/contact-us/'
            ) . ' ' . __( '(WordPress could not establish a secure connection to automatorwp.com. Please contact your server administrator.)' ),
            headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE
        );

        $request = wp_remote_get( $http_url, $http_args );
    }

    if ( is_wp_error( $request ) ) {
        $res = new WP_Error( 'automatorwp_integrations_api_failed',
            sprintf(
                __( 'An unexpected error occurred. Something may be wrong with automatorwp.com or this server&#8217;s configuration. If you continue to have problems, please try to <a href="%s">contact us</a>.', 'automatorwp' ),
                'https://automatorwp.com/contact-us/'
            ),
            $request->get_error_message()
        );
    } else {
        $res = json_decode( $request['body'] );

        $res = (array) $res;

        // Set a transient for 1 week with api integrations
        set_transient( 'automatorwp_integrations_api', $res, ( 24 * 7 ) * HOUR_IN_SECONDS );
    }

    return $res;

}

/**
 * Inform about integration pro choices
 *
 * @since 1.2.4
 *
 * @param string    $integration_name   The integration name
 * @param array     $args               Integration arguments
 * @param stdClass  $automation         The automation object
 * @param string    $item_type          The item type
 */
function automatorwp_automation_ui_integration_pro_choice( $integration_name, $args, $automation, $item_type ) {

    $integrations = automatorwp_integrations_api();

    if( is_wp_error( $integrations ) ) {
        return;
    }

    foreach ( $integrations as $integration ) {
        // Break if found the integration
        if( $integration->code === $integration_name ) {
            break;
        }
    }

    // Bail if integration if already installed
    if( class_exists( $integration->integration_class ) ) {
        return;
    }

    // Get integration items
    $items = array();

    if( $item_type === 'trigger' ) {

        $choices = automatorwp_get_integration_triggers( $integration_name );

        // Don't list if already listed
        if( count( $choices ) ) {
            return;
        }

        $items = $integration->triggers;

    } else if( $item_type === 'action' ) {

        $choices = automatorwp_get_integration_actions( $integration_name );

        // Don't list if already listed
        if( count( $choices ) ) {
            return;
        }

        $items = $integration->actions;
    }

    // Check the free and pro elements
    $has_free = false;
    $has_pro = false;

    foreach( $items as $item ) {

        // For triggers, only get anonymous choices
        if( $item_type === 'trigger' && $automation->type === 'anonymous' && ! $item->anonymous ) {
            continue;
        }

        if( $item->free ) {
            $has_free = true;
        } else {
            $has_pro = true;
        }

    }

    if( ! $has_free && $has_pro ) : ?>

        <div class="automatorwp-integration automatorwp-integration-pro"
             data-integration="<?php echo esc_attr( $integration_name ); ?>"
             data-label="<?php echo esc_attr( $args['label'] ); ?>"
             data-icon="<?php echo esc_attr( $args['icon'] ); ?>">
            <span class="automatorwp-integration-pro-badge">PRO</span>
            <div class="automatorwp-integration-icon">
                <img src="<?php echo esc_attr( $args['icon'] ); ?>" alt="<?php echo esc_attr( $args['label'] ); ?>">
            </div>
            <div class="automatorwp-integration-label"><?php echo $args['label']; ?></div>
        </div>

    <?php endif;

}
add_action( 'automatorwp_automation_ui_after_integration_choice', 'automatorwp_automation_ui_integration_pro_choice', 10, 4 );

/**
 * Inform about integration triggers pro choices
 *
 * @since 1.2.4
 *
 * @param string    $integration_name   The integration name
 * @param array     $args               Integration arguments
 * @param stdClass  $automation         The automation object
 * @param string    $item_type          The item type
 */
function automatorwp_automation_ui_integration_triggers_pro_choices( $integration_name, $args, $automation, $item_type ) {

    $integrations = automatorwp_integrations_api();

    if( is_wp_error( $integrations ) ) {
        return;
    }

    foreach ( $integrations as $integration ) {
        // Break if found the integration
        if( $integration->code === $integration_name ) {
            break;
        }
    }

    // Bail if integration is already installed
    if( class_exists( $integration->integration_class ) ) {
        return;
    }

    // Get the list of already listed triggers
    $choices = automatorwp_get_integration_triggers( $integration_name );

    foreach( $integration->triggers as $i => $trigger ) {

        // For triggers, only get anonymous choices
        if( $automation->type === 'anonymous' && ! $trigger->anonymous ) {
            continue;
        }

        // Skip free triggers
        if( $trigger->free ) {
            continue;
        }

        // Skip if integration is already installed
        if( property_exists( $trigger, 'required_class' ) && ! empty( $trigger->required_class ) ) {
            if( class_exists( $trigger->required_class ) ) {
                continue;
            }
        }

        $already_listed = false;

        foreach( $choices as $choice ) {
            // Check if trigger has been already listed
            if( $choice['label'] === $trigger->label ) {
                $already_listed = true;
                break;
            }
        }

        // Skip triggers already listed
        if( $already_listed ) {
            continue;
        } ?>
            <option value="<?php echo esc_attr( $integration_name ) . '_' . $i; ?>" disabled="disabled"><?php echo $trigger->label; ?></option>

        <?php
    }

}
add_action( 'automatorwp_automation_ui_after_integration_triggers_choices', 'automatorwp_automation_ui_integration_triggers_pro_choices', 10, 4 );

/**
 * Inform about integration actions pro choices
 *
 * @since 1.2.4
 *
 * @param string    $integration_name   The integration name
 * @param array     $args               Integration arguments
 * @param stdClass  $automation         The automation object
 * @param string    $item_type          The item type
 */
function automatorwp_automation_ui_integration_actions_pro_choices( $integration_name, $args, $automation, $item_type ) {

    $integrations = automatorwp_integrations_api();

    if( is_wp_error( $integrations ) ) {
        return;
    }

    foreach ( $integrations as $integration ) {
        // Break if found the integration
        if( $integration->code === $integration_name ) {
            break;
        }
    }

    // Bail if integration is already installed
    if( class_exists( $integration->integration_class ) ) {
        return;
    }

    // Get the list of already listed actions
    $choices = automatorwp_get_integration_actions( $integration_name );

    foreach( $integration->actions as $i => $action ) {
        // Skip actions already listed
        if( $action->free ) {
            continue;
        }

        // Skip if integration is already installed
        if( property_exists( $action, 'required_class' ) && ! empty( $action->required_class ) ) {
            if( class_exists( $action->required_class ) ) {
                continue;
            }
        }

        $already_listed = false;

        foreach( $choices as $choice ) {
            // Check if action has been already listed
            if( $choice['label'] === $action->label ) {
                $already_listed = true;
                break;
            }
        }

        // Skip actions already listed
        if( $already_listed ) {
            continue;
        } ?>
        <option value="<?php echo esc_attr( $integration_name ) . '_' . $i; ?>" disabled="disabled"><?php echo $action->label; ?></option>

        <?php
    }

}
add_action( 'automatorwp_automation_ui_after_integration_actions_choices', 'automatorwp_automation_ui_integration_actions_pro_choices', 10, 4 );