(function( $ ) {

    var prefix = 'automatorwp-';
    var _prefix = 'automatorwp_activecampaign_';

    // On click authorize button
    $('body').on('click', '.automatorwp_settings #' + _prefix + 'authorize', function(e) {
        e.preventDefault();

        var button = $(this);
        var wrapper = button.parent();

        var url = $('#' + _prefix + 'url').val();
        var key = $('#' + _prefix + 'key').val();

        // Check if response div exists
        var response_wrap = wrapper.find('#' + _prefix + 'response');

        if( ! response_wrap.length ) {
            wrapper.append( '<div id="' + _prefix + 'response" style="display: none; margin-top: 10px;"></div>' );
            response_wrap = wrapper.find('#' + _prefix + 'response');
        }

        // Show error message if not correctly configured
        if( url.length === 0 || key.length === 0 ) {
            response_wrap.addClass( 'automatorwp-notice-error' );
            response_wrap.html( 'All fields are required to connect with ActiveCampaign' );
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
                action: 'automatorwp_activecampaign_authorize',
                nonce: automatorwp_activecampaign.nonce,
                url: url,
                key: key,
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

    // On click refresh button
    $('body').on('click', '.automatorwp_settings #' + _prefix + 'refresh', function(e) {
        e.preventDefault();

        var button = $(this);
        var wrapper = button.parent();

        var webhook = $('#' + _prefix + 'webhook').val();

        // Check if response div exists
        var response_wrap = wrapper.find('#' + _prefix + 'response');

        if( ! response_wrap.length ) {
            wrapper.append( '<div id="' + _prefix + 'response" style="display: none; margin-top: 10px;"></div>' );
            response_wrap = wrapper.find('#' + _prefix + 'response');
        }

        response_wrap.slideUp('fast');
        response_wrap.attr('class', '');

        // Show spinner
        $('<span class="spinner is-active" style="float: none;"></span>').insertAfter( button );

        // Disable button
        button.prop('disabled', true);

        $.post(
            ajaxurl,
            {
                action: 'automatorwp_activecampaign_refresh',
                nonce: automatorwp_activecampaign.nonce,
                webhook: webhook,
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

    // View webhook on triggers
    $('body').on('click', '.automatorwp-view-webhook', function(e) {
        e.preventDefault();

        $(this).parent().next().slideDown('fast');

    });

})( jQuery );