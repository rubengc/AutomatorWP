(function( $ ) {

    var prefix = 'automatorwp-clickup-';
    var _prefix = 'automatorwp_clickup_';

    // On click authorize button
    $('body').on('click', '.automatorwp_settings #' + _prefix + 'authorize', function(e) {
        e.preventDefault();

        var button = $(this);
        var wrapper = button.parent();

        var token = $('#' + _prefix + 'token').val();

        // Check if response div exists
        var response_wrap = wrapper.find('#' + _prefix + 'response');

        if( ! response_wrap.length ) {
            wrapper.append( '<div id="' + _prefix + 'response" style="display: none; margin-top: 10px;"></div>' );
            response_wrap = wrapper.find('#' + _prefix + 'response');
        }

        // Show error message if not correctly configured
        if( token.length === 0 ) {
            response_wrap.addClass( 'automatorwp-notice-error' );
            response_wrap.html( 'API token is required to connect with ClickUp' );
            response_wrap.slideDown('fast');
            return;
        }

        response_wrap.slideUp('fast');
        response_wrap.attr('class', '');

        // Show spinner
        wrapper.append('<span class="spinner is-active" style="float: none;"></span>');

        // Disable button
        button.prop('disabled', true);

        $.post(
            ajaxurl,
            {
                action: 'automatorwp_clickup_authorize',
                nonce: automatorwp_clickup.nonce,
                token: token,
            },
            function( response ) {

                // Add class automatorwp-notice-success on successful unlock, if not will add the class automatorwp-notice-error
                response_wrap.addClass( 'automatorwp-notice-' + ( response.success === true ? 'success' : 'error' ) );
                response_wrap.html( ( response.data.message !== undefined ? response.data.message : response.data ) );
                response_wrap.slideDown('fast');

                // Hide spinner
                wrapper.find('.spinner').remove();

                // Redirect on success
                if( response.success === true && response.data.redirect_url !== undefined ) {
                    window.location = response.data.redirect_url;
                    return;
                }
                

                // Enable button
                button.prop('disabled', false);

            }
        );

        
    });

    // On change team
    $('body').on('change', '.automatorwp-action-clickup-create-list .cmb2-id-team select, '
        + '.automatorwp-action-clickup-add-comment-task .cmb2-id-team select, '
        + '.automatorwp-action-clickup-add-tag-task .cmb2-id-team select, '
        + '.automatorwp-action-clickup-create-task .cmb2-id-team select, '
        + '.automatorwp-action-clickup-remove-task .cmb2-id-team select, '
        + '.automatorwp-action-clickup-remove-tag-task .cmb2-id-team select', function(e, first_change) {
    var team = $(this).closest('.cmb-row');
    var space_team = team.next('.cmb2-id-space');

    var team_id = $(this).val();
		
	if( first_change === undefined ) {
		first_change = false;
	}
		
    if( team_id === 'any' || team_id === '' || team_id === null ) {
        // Hide the term selector
        if( first_change ) {
            space_team.hide();
        } else {
            space_team.slideUp('fast');
        }
    } else {
        var space_selector = space_team.find('select.select2-hidden-accessible');

        // Remove Select2 element
        space_selector.next('.select2').remove();

        // Update the team (since we do not use the table attribute, lets to use it as team)
        space_selector.data( 'table', team_id );

        if( ! first_change ) {
            // Update the the term value
            space_selector.val('');
        }

        // Reset the selector
        space_selector.removeAttr('data-select2-id');

        // Init it again
        automatorwp_ajax_selector( space_selector );

        // Show the term selector
        if( first_change ) {
            space_team.show();
        } else {
            space_team.slideDown('fast');
        }
    }

    });

    // On change space
    $('body').on('change', '.automatorwp-action-clickup-create-list .cmb2-id-space select, '
        + '.automatorwp-action-clickup-add-comment-task .cmb2-id-space select, '
        + '.automatorwp-action-clickup-add-tag-task .cmb2-id-space select, '
        + '.automatorwp-action-clickup-create-task .cmb2-id-space select, '
        + '.automatorwp-action-clickup-remove-task .cmb2-id-space select, '
        + '.automatorwp-action-clickup-remove-tag-task .cmb2-id-space select', function(e, first_change) {
        var space = $(this).closest('.cmb-row');
        
        var folder_space = space.next('.cmb2-id-folder');
    
        var space_id = $(this).val();
		
		if( first_change === undefined ) {
			first_change = false;
		}
        
        if( space_id === 'any' || space_id === '' || space_id === null ) {
            // Hide the term selector
            if( first_change ) {
                folder_space.hide();
            } else {
                folder_space.slideUp('fast');
            }
        } else {
            var folder_selector = folder_space.find('select.select2-hidden-accessible');
            
            // Remove Select2 element
            folder_selector.next('.select2').remove();
    
            // Update the space (since we do not use the table attribute, lets to use it as space)
            folder_selector.data( 'table', space_id );

            if( ! first_change ) {
                // Update the the term value
                folder_selector.val('');
            }

            // Reset the selector
            folder_selector.removeAttr('data-select2-id');
    
            // Init it again
            automatorwp_ajax_selector( folder_selector );
    
            // Show the term selector
            if( first_change ) {
                folder_space.show();
            } else {
                folder_space.slideDown('fast');
            }
        }
    
        });

        // On change folder
    $('body').on('change', '.automatorwp-action-clickup-create-list .cmb2-id-folder select, '
        + '.automatorwp-action-clickup-add-comment-task .cmb2-id-folder select, '
        + '.automatorwp-action-clickup-add-tag-task .cmb2-id-folder select, '
        + '.automatorwp-action-clickup-create-task .cmb2-id-folder select, '
        + '.automatorwp-action-clickup-remove-task .cmb2-id-folder select, '
        + '.automatorwp-action-clickup-remove-tag-task .cmb2-id-folder select', function(e, first_change) {
        var folder = $(this).closest('.cmb-row');
        var list_folder = folder.next('.cmb2-id-list');
    
        var folder_id = $(this).val();
		
		if( first_change === undefined ) {
			first_change = false;
		}
        
        if( folder_id === 'any' || folder_id === '' || folder_id === null ) {
            // Hide the term selector
            if( first_change ) {
                list_folder.hide();
            } else {
                list_folder.slideUp('fast');
            }
        } else {
            var list_selector = list_folder.find('select.select2-hidden-accessible');
    
            // Remove Select2 element
            list_selector.next('.select2').remove();
    
            // Update the folder (since we do not use the table attribute, lets to use it as folder)
            list_selector.data( 'table', folder_id );

            // Reset the selector
            list_selector.removeAttr('data-select2-id');
    
            // Init it again
            automatorwp_ajax_selector( list_selector );
    
            // Show the term selector
            if( first_change ) {
                list_folder.show();
            } else {
                list_folder.slideDown('fast');
            }
        }
    
        });

    // On change list
    $('body').on('change', '.automatorwp-action-clickup-add-comment-task .cmb2-id-list select, '
        + '.automatorwp-action-clickup-add-tag-task .cmb2-id-list select, '
        + '.automatorwp-action-clickup-remove-task .cmb2-id-list select, '
        + '.automatorwp-action-clickup-remove-tag-task .cmb2-id-list select', function(e, first_change) {
        var list = $(this).closest('.cmb-row');
        var task_list = list.next('.cmb2-id-task');

        var list_id = $(this).val();
		
		if( first_change === undefined ) {
			first_change = false;
		}

        if( list_id === 'any' || list_id === '' || list_id === null ) {
            // Hide the term selector
            if( first_change ) {
                task_list.hide();
            } else {
                task_list.slideUp('fast');
            }
        } else {
            var task_selector = task_list.find('select.select2-hidden-accessible');

            // Remove Select2 element
            task_selector.next('.select2').remove();

            // Update the list (since we do not use the table attribute, lets to use it as list)
            task_selector.data( 'table', list_id );

            // Reset the selector
            task_selector.removeAttr('data-select2-id');

            // Init it again
            automatorwp_ajax_selector( task_selector );

            // Show the term selector
            if( first_change ) {
                task_list.show();
            } else {
                task_list.slideDown('fast');
            }
        }

        });   
	
	// On change task
    $('body').on('change', '.automatorwp-action-clickup-add-comment-task .cmb2-id-task select, '
        + '.automatorwp-action-clickup-add-tag-task .cmb2-id-task select, '
        + '.automatorwp-action-clickup-remove-task .cmb2-id-task select, '
        + '.automatorwp-action-clickup-remove-tag-task .cmb2-id-task select', function(e, first_change) {
        var task = $(this).closest('.cmb-row');
        var tag_list = task.next('.cmb2-id-tag');

        var task_id = $(this).val();
		
		if( first_change === undefined ) {
			first_change = false;
		}

        if( task_id === 'any' || task_id === '' || task_id === null ) {
            // Hide the term selector
            if( first_change ) {
                tag_list.hide();
            } else {
                tag_list.slideUp('fast');
            }
        } else {
            var tag_selector = tag_list.find('select.select2-hidden-accessible');

            // Remove Select2 element
            tag_selector.next('.select2').remove();

            // Update the list (since we do not use the table attribute, lets to use it as list)
            tag_selector.data( 'table', list_id );

            // Reset the selector
            tag_selector.removeAttr('data-select2-id');

            // Init it again
            automatorwp_ajax_selector( tag_selector );

            // Show the term selector
            if( first_change ) {
                tag_list.show();
            } else {
                tag_list.slideDown('fast');
            }
        }

        });

    // On click on an option, check if form contains the team selector
    $('body').on('click', '.automatorwp-automation-item-label > .automatorwp-option', function(e) {

    var item = $(this).closest('.automatorwp-automation-item[class*="clickup"]');
    var option = $(this).data('option');
    var option_form = item.find('.automatorwp-option-form-container[data-option="' + option + '"]');

    var team_selector = option_form.find('.cmb2-id-team');
				
    if( team_selector !== undefined ) {
        team_selector.find('select.select2-hidden-accessible').trigger('change', [true]);
    }

    var space_selector = option_form.find('.cmb2-id-space');

    if( space_selector !== undefined ) {
        space_selector.find('select.select2-hidden-accessible').trigger('change', [true]);
    }

    var folder_selector = option_form.find('.cmb2-id-folder');

    if( folder_selector !== undefined ) {
        folder_selector.find('select.select2-hidden-accessible').trigger('change', [true]);
    }
    
    var list_selector = option_form.find('.cmb2-id-list');

    if( list_selector !== undefined ) {
        list_selector.find('select.select2-hidden-accessible').trigger('change', [true]);
    }
		
	var task_selector = option_form.find('.cmb2-id-task');

    if( task_selector !== undefined ) {
        task_selector.find('select.select2-hidden-accessible').trigger('change', [true]);
    }

    });

    

})( jQuery );