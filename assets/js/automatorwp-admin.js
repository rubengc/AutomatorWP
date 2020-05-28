(function ( $ ) {

    // Selector Control
    $('.automatorwp-selector select').each(function() { automatorwp_selector( $(this) ); });

    // Post Selector Control
    $('.automatorwp-post-selector select').each(function() { automatorwp_post_selector( $(this) ); });

    // Term Selector Control
    $('.automatorwp-term-selector select').each(function() { automatorwp_term_selector( $(this) ); });

    // User Selector Control
    $('.automatorwp-user-selector select').each(function() { automatorwp_user_selector( $(this) ); });

    // Object Selector Control
    $('.automatorwp-object-selector select').each(function() { automatorwp_object_selector( $(this) ); });

    // Ajax Selector Control
    $('.automatorwp-ajax-selector select').each(function() { automatorwp_ajax_selector( $(this) ); });

    // Update items positions at init to avoid any order error
    automatorwp_update_items_position( $('.automatorwp-automation-items.automatorwp-triggers') );
    automatorwp_update_items_position( $('.automatorwp-automation-items.automatorwp-actions') );

    // Save and activate
    $('body').on('click', '.automatorwp-save-and-activate input', function(e) {
        e.preventDefault();

        $('select#status').val('active');

        $('#ct-save').trigger('click');
    });

    // Tooltip
    $('body').on('mouseover', '.automatorwp-tooltip', function(e) {
        $(this).prev('.cmb2-metabox-description').addClass('automatorwp-tooltip-dialog').fadeIn('fast');
    });

    $('body').on('mouseout', '.automatorwp-tooltip', function(e) {
        $(this).prev('.cmb2-metabox-description').fadeOut('fast');
    });

    // Make automation items sortable
    $('.automatorwp-automation-items').sortable({
        cursor: 'grabbing',
        cancel: '.automatorwp-option-form-container, .automatorwp-no-grab',
        // When the items order is updated
        update : function ( e, ui ) {

            // Event target refers to .automatorwp-triggers or .automatorwp-actions
            automatorwp_update_items_position( $(e.target) );

        }
    });

    // Toggleable options list
    $('body').on('click', '.automatorwp-toggleable-options-list-toggle', function(e) {
        e.preventDefault();

        var $this = $(this);
        var show_text = $this.data( 'show-text' );
        var hide_text = $this.data( 'hide-text' );
        var list = $this.closest('.cmb-td').find('.automatorwp-toggleable-options-list');

        // Toggle options list visitibility and change toggle text
        if( $this.text() === show_text ) {
            $this.text(hide_text);
            list.slideDown('fast');
        } else {
            $this.text(show_text);
            list.slideUp('fast');
        }

    });

    // Sequential
    $('body').on('change', 'input#sequential', function(e) {

        if( $(this).prop('checked') ) {
            $('.automatorwp-triggers .automatorwp-automation-item-position').show();
        } else {
            $('.automatorwp-triggers .automatorwp-automation-item-position').hide();
        }

    });

    // Delete item
    $('body').on('click', '.automatorwp-automation-item-action-delete', function(e) {

        var item = $(this).closest('.automatorwp-automation-item');
        var items_list =$(this).closest('.automatorwp-automation-items');
        var item_type = item.hasClass('automatorwp-trigger') ? 'trigger' : 'action';
        var id = item.find('input.id').val();

        // Hide this item
        item.slideUp('fast', function() {
            // Removes this item
            item.remove();

            // Update items position
            automatorwp_update_items_position( items_list );
        });

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'automatorwp_delete_automation_item',
                nonce: automatorwp_admin.nonce,
                id: id,
                item_type: item_type
            },
            success: function( response ) {

                if( response.success ) {

                    // Remove trigger tags
                    if( item_type === 'trigger' ) {
                        $('.automatorwp-automation-tag-selector optgroup[data-id="' + id + '"]').remove();
                    }

                } else {

                }
            },
            error: function( response ) {

            }
        });


    });

    // -----------------------------------------------
    // Add Trigger/Action
    // -----------------------------------------------

    // Add trigger/action
    $('body').on('click', '.automatorwp-add-trigger, .automatorwp-add-action', function(e) {
        e.preventDefault();

        var type = $(this).hasClass('automatorwp-add-trigger') ? 'trigger' : 'action';

        $('<div class="automatorwp-automation-item automatorwp-' + type + '" style="display: none;">'
            + $(this).closest('.inside').find('.automatorwp-add-item-form').html()
        + '</div>').appendTo('.automatorwp-' + type + 's').slideDown('fast');
    });

    // Select an integration
    $('body').on('click', '.automatorwp-automation-item .automatorwp-select-integration .automatorwp-integration', function(e) {
        e.preventDefault();

        var item = $(this).closest('.automatorwp-automation-item');
        var select = item.find('.automatorwp-integration-choices[data-integration="' + $(this).data('integration') + '"]');

        // Hide the integration select
        item.find('.automatorwp-select-integration').hide();

        // Update integration icon and label
        item.find('.automatorwp-automation-item-details .automatorwp-integration-icon').append('<img src="' + $(this).data('icon') + '" />');
        item.find('.automatorwp-automation-item-content').prepend('<div class="automatorwp-integration-label">' + $(this).data('label') + '</div>');

        // Show the trigger select
        select.show();
        item.find('.automatorwp-integration-choices-container').show();

        $('<div class="automatorwp-select2-dropdown-container"></div>').insertAfter(select);

        var dropdown_container = select.next('.automatorwp-select2-dropdown-container');

        select.automatorwp_select2( {
            theme: 'default automatorwp-select2',
            placeholder: select.data('placeholder'),
            allowClear: false,
            multiple: false,
            closeOnSelect: false,
            dropdownParent: dropdown_container,
            escapeMarkup: function(markup) { return markup; },
            templateResult: function(data) {
                // Custom template result to allow HTML markup on select items
                var text = data.text;

                if( data.element !== undefined ) {

                    // Check if option has something on data-text attribute
                    var custom_text = $(data.element).data('text');

                    if( custom_text !== undefined && custom_text.length ) {
                        text = custom_text;
                    }

                }

                return text;
            },
            templateSelection: function(data) { return data.text; }
        } ).on('select2:open', function(e) {
            // Set the search input placeholder attribute
            dropdown_container.find('input.select2-search__field').attr('placeholder', select.data('placeholder'));
        }).on('select2:closing', function(e) {
            // Prevent to close dropdown
            e.preventDefault();
        }).on('select2:closed', function(e) {
            // Prevent to close dropdown
            select.select2('open');
        }).on('select2:selecting', function (e) {
            // Prevent change value if disabled
            if ( select.prop('disabled') ) {
                e.preventDefault();
            }
        });

        select.automatorwp_select2( 'open' );
    });

    // Select a trigger/action
    $('body').on('change', '.automatorwp-automation-item .automatorwp-integration-choices', function(e) {
        e.preventDefault();

        if( $(this).prop('disabled') ) {
            return;
        }

        var item = $(this).closest('.automatorwp-automation-item');
        var items_list = item.closest('.automatorwp-automation-items');
        var automation_id = $('#object_id').val();
        var type = $(this).val();
        var item_type = item.hasClass('automatorwp-trigger') ? 'trigger' : 'action';

        if( ! type.length ) {
            return;
        }

        var last_position = items_list.find('.automatorwp-automation-item input.position').last();
        var position = 0;

        if( last_position.length ) {
            position = parseInt( last_position.val() ) + 1;
        } else {
            position = parseInt( items_list.find('.automatorwp-automation-item').length - 1 );
        }

        // Disable the dropdown
        $(this).prop('disabled', true);

        // Show spinner
        item.find('.automatorwp-spinner').show();

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'automatorwp_add_automation_item',
                nonce: automatorwp_admin.nonce,
                automation_id: automation_id,
                item_type: item_type,
                type: type,
                position: position
            },
            success: function( response ) {

                if( response.success ) {
                    var new_item = $( response.data.edit_html );

                    // Append the new item
                    new_item.appendTo( item.closest('.automatorwp-automation-items') );

                    // If sequential enabled, show the item position
                    if( item_type === 'trigger' && $('#sequential').prop('checked') ) {
                        item.closest('.automatorwp-automation-items').find('.automatorwp-automation-item-position').show();
                    }

                    // Add tags to all tags selectors
                    if( item_type === 'trigger' && response.data.tags_html.length ) {
                        $( response.data.tags_html ).appendTo('.automatorwp-automation-tag-selector');
                    }

                    // Removes the new item form
                    item.remove();

                    // Make javascript features work on the new item
                    automatorwp_initialize_form_fields( new_item );

                } else {

                }
            },
            error: function( response ) {

            }
        });
    });

    // Cancel select a trigger/action
    $('body').on('click', '.automatorwp-automation-item .automatorwp-cancel-choice-select', function(e) {
        e.preventDefault();

        var item = $(this).closest('.automatorwp-automation-item');

        item.slideUp('fast', function() {
            item.remove();
        });
    });

    // On click recommended integrations
    $('body').on('click', '.automatorwp-recommended-integrations-label a', function(e) {
        e.preventDefault();

        $(this).closest('.automatorwp-recommended-integrations').find('.automatorwp-integrations').slideToggle('fast');
    });

    // -----------------------------------------------
    // Options
    // -----------------------------------------------

    // Click on an option
    $('body').on('click', '.automatorwp-automation-item-label > .automatorwp-option', function(e) {

        var item = $(this).closest('.automatorwp-automation-item');
        var option = $(this).data('option');
        var option_form = item.find('.automatorwp-option-form-container[data-option="' + option + '"]');

        if( option_form.hasClass('automatorwp-option-form-active') ) {

            // Hide this option form
            option_form.removeClass('automatorwp-option-form-active').slideUp('fast');

        } else {

            // Hide any other option form
            item.find('.automatorwp-option-form-active').removeClass('automatorwp-option-form-active').slideUp('fast');

            // Show this option form
            option_form.addClass('automatorwp-option-form-active').slideDown('fast');

        }

    });

    // Save option
    $('body').on('click', '.automatorwp-save-option-form', function(e) {

        var button = $(this);

        if( button.prop('disabled') ) {
            return;
        }

        var item = button.closest('.automatorwp-automation-item');
        var form = button.closest('.automatorwp-option-form-container');
        var id = item.find('input.id').val();
        var item_type = item.hasClass('automatorwp-trigger') ? 'trigger' : 'action';

        // Show spinner
        form.find('.automatorwp-spinner').show();

        // Disable save and cancel button
        button.prop('disabled', true);
        button.next('.automatorwp-cancel-option-form').prop('disabled', true);

        // Get the option form values
        var data = automatorwp_get_option_form_values( form );

        // Append the request data
        data.action = 'automatorwp_update_item_option';
        data.nonce = automatorwp_admin.nonce;
        data.id = id;
        data.item_type = item_type;
        data.option_name = form.data('option');

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: data,
            success: function( response ) {

                if( response.success ) {

                    // Hide the option form
                    form.removeClass('automatorwp-option-form-active').slideUp('fast');

                    // Update the item label
                    item.find('.automatorwp-automation-item-label').html( response.data.edit_html );

                    if( item_type === 'trigger' ) {
                        // Update the option display on tags selector
                        $('.automatorwp-automation-tag-selector optgroup[data-id="' + id + '"]').attr('label', response.data.edit_html );

                        // Update option tags
                        if( response.data.tags_html.length ) {
                            $('.automatorwp-automation-tag-selector optgroup[data-id="' + id + '"]').html( $(response.data.tags_html).html() );

                            // To get the options correctly updated and working is required to destroy all Select2 and reinitialize them
                            $('.automatorwp-automation-tag-selector').each(function() {
                                $(this).automatorwp_select2('destroy');

                                automatorwp_tags_selector( $(this) );
                            });
                        }
                    }

                }

                // Hide the spinner
                form.find('.automatorwp-spinner').hide();

                // Enable save and cancel button
                button.prop('disabled', false);
                button.next('.automatorwp-cancel-option-form').prop('disabled', false);

            },
            error: function( response ) {

            }
        });

    });

    // Cancel option edit
    $('body').on('click', '.automatorwp-cancel-option-form', function(e) {

        // Hide the option form
        $(this).closest('.automatorwp-option-form-container').removeClass('automatorwp-option-form-active').slideUp('fast');

    });

    // -----------------------------------------------
    // Options fields
    // -----------------------------------------------

    // Make radios work through javascript
    $('body').on('change', '.automatorwp-option-form input[type="radio"]', function(e) {
        $(this).closest('ul').find('input[type="radio"]:not([value="' + $(this).val() + '"])').prop('checked', false);
    });

    // Fix radio label click
    $('body').on('click', '.automatorwp-option-form input[type="radio"] + label', function(e) {
        e.preventDefault();
        $(this).prev('input').prop('checked', true).change();
    });

    // Fix checkbox label click
    $('body').on('click', '.automatorwp-option-form input[type="checkbox"] + label', function(e) {
        e.preventDefault();
        $(this).prev('input').prop( 'checked', ( ! $(this).prev('input').prop('checked') ) ).change();
    });

    // Fix repeatable fields selector
    $('.automatorwp-option-form .cmb-repeat-table').each(function() {
        var item_id = $(this).closest('.automatorwp-automation-item').find('input.id').val();
        var new_id = $(this).attr('id') + '-' + item_id;

        $(this).attr('id', new_id);
        $(this).parent().find('.cmb-add-row-button').attr('data-selector', new_id);
    });

    // Fix group fields selector
    $('.automatorwp-option-form .cmb-repeatable-group').each(function() {
        var item_id = $(this).closest('.automatorwp-automation-item').find('input.id').val();
        var new_id = $(this).attr('id') + '-' + item_id;

        $(this).attr('id', new_id);
        $(this).find('.cmb-add-group-row').attr('data-selector', new_id);
        $(this).find('.cmb-remove-group-row').attr('data-selector', new_id);
    });

    // On add a new group row, reinitialize fields
    $('body').on('cmb2_add_row', '.cmb-repeatable-group', function( e, row, cmb ) {

        // Remove Select2 element
        row.find('.select2').remove();

        // Reset Select2 data
        row.find('.select2-hidden-accessible')
            .removeClass('select2-hidden-accessible')
            .removeAttr('data-select2-id'); // For fields without id, select2 assigns this attribute as id

        automatorwp_initialize_form_fields( row );
    });

    // -----------------------------------------------
    // Tags
    // -----------------------------------------------

    // Tags Selector Control
    $('.automatorwp-automation-tag-selector').each(function() { automatorwp_tags_selector( $(this) ); });

    // On focus an action option input
    $('body').on('focus', '.automatorwp-action input, .automatorwp-action textarea', function(e) {
        $(this).data('focused', true);
    });

    // On change tag selector
    $('body').on('change', '.automatorwp-automation-tag-selector', function(e) {
        var input = $(this).parent().find( 'input, textarea' );
        var value = $(this).val();

        if( value && value.length ) {
            value = '{' + value + '}';

            // Support for wysiwyg field
            if( $(this).closest('.cmb-row').hasClass('cmb-type-wysiwyg') ) {

                input = $(this).parent().find( 'textarea.wp-editor-area' );

                var editor = window.tinyMCE.get( input.attr('id') );

                if ( editor && ! editor.isHidden() ) {
                    // Insert content to the HTML editor
                    editor.execCommand( 'mceInsertContent', false, value );

                    return;
                } else if ( typeof QTags !== 'undefined' ) {
                    // If quick tags are available, insert the HTML into its content.
                    QTags.insertContent( value );

                    return;
                }
            }

            // if input hasn't been focused yet, then set caret at end
            if( input.data('focused') !== true ) {
                automatorwp_set_caret_at_end( input );
            }

            // Insert content at caret
            automatorwp_insert_at_caret( input, value );

            // Restore tag selector value to allow select again the same option
            $(this).val('').trigger('change');

        }
    });

    // Make tags selector dropdown option groups toggleables
    $('body').on('click', '.automatorwp-tags-select2 .select2-dropdown li[role="group"] > strong', function(e) {
        $(this).next('ul').slideToggle('fast');
    });

})( jQuery );

/**
 * Update elements positions
 *
 * @since 1.0.0
 *
 * @param {Object} element
 */
function automatorwp_update_items_position( element ) {

    var $ = $ || jQuery;

    var items = element.find('.automatorwp-automation-item');
    var items_order = {};
    var items_order_for_tags = [];

    // Loop through each item
    items.each(function( index, value ) {

        // Write it's current position to our hidden input value
        $(this).find('input.position').val( index );

        // Update item position
        $(this).find('.automatorwp-automation-item-position').html( ( index + 1 ) + '.');

        items_order[$(this).find('input.id').val()] = index;

        // Setup a custom array for tags
        items_order_for_tags.push( $(this).find('input.id').val() );

    });

    if( Object.entries(items_order).length ) {

        var item_type = element.hasClass('automatorwp-triggers') ? 'trigger' : 'action';

        // Update automation items order trough ajax
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'automatorwp_update_automation_items_order',
                nonce: automatorwp_admin.nonce,
                items_order: items_order,
                item_type: item_type,
            },
            success: function( response ) {

                if( response.success ) {

                } else {

                }
            },
            error: function( response ) {

            }
        });

        if( item_type === 'trigger' ) {

            // Update tags selector optgroups order
            $.each(items_order_for_tags, function( index, id ) {

                $('.automatorwp-automation-tag-selector').each(function() {
                    $(this).find('optgroup[data-id="' + id + '"]').appendTo(this);
                });

            });
        }

    }

}

/**
 * Turns the given element into a tags selector
 *
 * @since 1.0.0
 *
 * @param {Object} element
 */
function automatorwp_tags_selector( element ) {

    var $ = $ || jQuery;

    element.automatorwp_select2({
        theme: 'default automatorwp-select2 automatorwp-tags-select2',
        escapeMarkup: function(markup) { return markup; },
        templateResult: function(data) {
            // Custom template result to allow HTML markup on select items
            var text = data.text;

            if( data.element !== undefined ) {
                // Check if option has something on label attribute
                var label = $(data.element).attr('label');

                if( label !== undefined && label.length ) {
                    text = label;
                }

                // Check if option has something on data-text attribute
                var custom_text = $(data.element).data('text');

                if( custom_text !== undefined && custom_text.length ) {
                    text = custom_text;
                }

                // Check if option has an icon
                var icon = $(data.element).data('icon');

                if( icon !== undefined ) {
                    text = '<img src="' + icon + '" />' + text;
                }

            }

            return text;
        },
        matcher: automatorwp_select2_optgroup_matcher,
        dropdownPosition: 'below',
    });

    element.val('').trigger('change');

}

/**
 * Get form fields values
 *
 * @since 1.0.0
 *
 * @param {Object} form
 *
 * @return {Object}
 */
function automatorwp_get_option_form_values( form ) {

    var $ = $ || jQuery;
    var data = {};

    form.find('input:not([type="button"]):not([type="submit"]), select, textarea').each(function() {

        var option = $(this).data('option');
        var value = $(this).val();

        // Skip pattern fields
        if( $(this).closest('.cmb-row.empty-row').length ) {
            return;
        }

        if( $(this).attr('type') === 'checkbox' || $(this).attr('type') === 'radio' ) {

            if( $(this).closest('.cmb2-checkbox-list').length ) {
                // Multicheck
                value = [];
                $(this).closest('.cmb2-checkbox-list').find('input:checked').each(function() {
                    value.push($(this).val());
                });
            } else if( ! $(this).prop('checked') ) {
                // Checkbox and radio
                return;
            }

        } else if( $(this).hasClass('wp-editor-area') ) {
            // Wysiwyg

            option = $(this).attr('name');

            // Force save tinyMCE editor to get textarea value updated
            if( window.tinyMCE.editors.length && window.tinyMCE.editors[$(this).attr('id')] ) {
                window.tinyMCE.editors[$(this).attr('id')].save();
                value = $(this).val();
            }
        }

        if( option !== undefined ) {

            // Repeatable and group fields
            if( option.indexOf('[') !== -1 ) {
                var parts = option.split('[');
                option = parts[0];

                if( data[option] === undefined ) {
                    data[option] = []
                }

                if( parts[1] !== ']' ) {

                    var option_key = parts[1].replace(']', '');
                    var index = $(this).closest('.cmb-repeatable-grouping').data('iterator');

                    if( data[option][index] === undefined ) {
                        data[option][index] = {}
                    }

                    // Group field
                    data[option][index][option_key] = value;
                } else {
                    // Repeatable
                    data[option].push(value);
                }

            } else {
                // Single option value
                data[option] = value;
            }
        }
    });

    return data;

}

/**
 * Initialize form fields
 *
 * @since 1.0.0
 *
 * @param {Object} form
 */
function automatorwp_initialize_form_fields( form ) {

    var $ = $ || jQuery;

    // Selector Control
    form.find('.automatorwp-selector select').each(function() { automatorwp_selector( $(this) ); });

    // Post Selector Control
    form.find('.automatorwp-post-selector select').each(function() { automatorwp_post_selector( $(this) ); });

    // Term Selector Control
    form.find('.automatorwp-term-selector select').each(function() { automatorwp_term_selector( $(this) ); });

    // User Selector Control
    form.find('.automatorwp-user-selector select').each(function() { automatorwp_user_selector( $(this) ); });

    // Object Selector Control
    form.find('.automatorwp-object-selector select').each(function() { automatorwp_object_selector( $(this) ); });

    // Ajax Selector Control
    form.find('.automatorwp-ajax-selector select').each(function() { automatorwp_ajax_selector( $(this) ); });

    // Tags selector
    form.find('.automatorwp-automation-tag-selector').each(function() { automatorwp_tags_selector( $(this) ); });

    // Fix repeatable fields selector
    form.find('.cmb-repeat-table').each(function() {
        var item_id = $(this).closest('.automatorwp-automation-item').find('input.id').val();
        var new_id = $(this).attr('id') + '-' + item_id;

        $(this).attr('id', new_id);
        $(this).parent().find('.cmb-add-row-button').attr('data-selector', new_id);
    });

    // Fix group fields selector
    form.find('.cmb-repeatable-group').each(function() {
        var item_id = $(this).closest('.automatorwp-automation-item').find('input.id').val();
        var new_id = $(this).attr('id') + '-' + item_id;

        $(this).attr('id', new_id);
        $(this).find('.cmb-add-group-row').attr('data-selector', new_id);
        $(this).find('.cmb-remove-group-row').attr('data-selector', new_id);
    });

    // Init CMB2 callbacks
    form
        // Repeatable content
        .on( 'click', '.cmb-add-group-row', function(e) {
            window.CMB2.addGroupRow.apply(this, [e] );
        })
        .on( 'click', '.cmb-add-row-button', function(e) {
            window.CMB2.addAjaxRow.apply(this, [e] );
        })
        .on( 'click', '.cmb-remove-group-row', function(e) {
            window.CMB2.removeGroupRow.apply(this, [e] );
        })
        .on( 'click', '.cmb-remove-row-button', function(e) {
            window.CMB2.removeAjaxRow.apply(this, [e] );
        })
        // Reset titles when removing a row
        .on( 'cmb2_remove_row', '.cmb-repeatable-group', function(e) {
            window.CMB2.resetTitlesAndIterator.apply(this, [e] );
        })
        .on( 'click', '.cmbhandle, .cmbhandle + .cmbhandle-title', function(e) {
            window.CMB2.toggleHandle.apply(this, [e] );
        });

    // Init wysiwyg editors
    form.find( '.cmb-type-wysiwyg' ).each( function() {

        var $this = $(this);

        var textarea_id = $this.find('textarea').attr('id');
        var textarea = $this.find('textarea').clone();

        // Remove CMB2 HTML and add a single textarea to be converted as editor
        $this.find('.wp-editor-wrap').remove();
        $this.find('.cmb-td').append(textarea);

        wp.editor.initialize( textarea_id, {
            mediaButtons: true,
            tinymce: {
                wpautop: true,
                toolbar1: 'formatselect bold italic bullist numlist blockquote alignleft aligncenter alignright link unlink wp_more fullscreen wp_adv',
                toolbar2: 'strikethrough hr forecolor pastetext removeformat charmap outdent indent undo redo wp_help'
            },
            quicktags: {
                buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,,code,more,close'
            }
        });

    } );

    if ( 'undefined' !== typeof window.QTags ) {
        window.QTags._buttonsInit();
    }

}