(function ( $ ) {
    
    var _prefix = 'automatorwp_mailchimp_';

    // Save credentials
    $('body').on('click', '#automatorwp_save_mailchimp_oauth', function( e ) {

        e.preventDefault();

        var button = $(this);
        var wrapper = button.parent();

        // Check if response div exists
        var status_wrap = wrapper.find('#awp_mailchimp_oauth_status');

        var data = {
            action: 'automatorwp_mailchimp_save_oauth_credentials',
            nonce: automatorwp_mailchimp.nonce,
            api_key: $('#' + _prefix + 'api_key').val(),
            server_prefix: $('#' + _prefix + 'server_prefix').val(),
        };

        $.post(ajaxurl, data, function (response ) {
            if( response.success ) {
                status_wrap.css("width", "fit-content");
                status_wrap.addClass( 'automatorwp-notice-success' );
                status_wrap.html( 'Credentials Saved!' );
                status_wrap.slideDown('fast');
            } else {
                if ( data.api_key.length == 0 || data.server_prefix.length == 0 ) {
                    status_wrap.css("width", "fit-content");
                    status_wrap.addClass( 'automatorwp-notice-error' );
                    status_wrap.html( 'All fields are required to connect with Mailchimp' );
                    status_wrap.slideDown('fast');
                } else {
                    status_wrap.css("width", "fit-content");
                    status_wrap.addClass( 'automatorwp-notice-error' );
                    status_wrap.html( 'Either Credentials are invalid or Mailchimp API is not accessible' );
                    status_wrap.slideDown('fast');
                }
            }
        });
      
    });

    // Delete credentials
    $('body').on('click', '#automatorwp_remove_mailchimp_oauth', function( e ) {

        e.preventDefault();

        $( '#awp_mailchimp_oauth_status' ).text( "Removing Credentials...(wait)" );

        var data = {
            action: 'automatorwp_mailchimp_delete_oauth_credentials',
            nonce: automatorwp_mailchimp.nonce,
        };

        $.post(ajaxurl, data, function (response ) {
            $( '#awp_mailchimp_oauth_status' ).text( "Credentials Removed! Refresh this page. " );
            setTimeout(function () { location.reload(); }, 100);
        });

    });

       // On change audience
       $('body').on('change', '.automatorwp-action-mailchimp-user-add-tag .cmb2-id-audience select, '
           + '.automatorwp-action-mailchimp-create-send-campaign .cmb2-id-audience select', function(e) {
        var list = $(this).closest('.cmb-row');
        var tag_list = list.next('.cmb2-id-tags');

        var list_id = $(this).val();

        var first_change = list.hasClass('is-option-change');

        if( list_id === 'any' || list_id === '' ) {
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

        list.removeClass('is-option-change');
    });

    // On click on an option, check if form contains the list selector
    $('body').on('click', '.automatorwp-automation-item-label > .automatorwp-option', function(e) {

        var item = $(this).closest('.automatorwp-automation-item');
        var option = $(this).data('option');
        var option_form = item.find('.automatorwp-option-form-container[data-option="' + option + '"]');
        var list_selector = option_form.find('.cmb2-id-audience');

        if( list_selector !== undefined ) {
            list_selector.addClass('is-option-change');
            list_selector.find('select.select2-hidden-accessible').trigger('change');
        }

    });

})( jQuery );