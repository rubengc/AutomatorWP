(function( $ ) {

    var prefix = 'automatorwp-zoom-';
    var _prefix = 'automatorwp_zoom_';

    // On click authorize button
    $('body').on('click', '.automatorwp_settings #' + _prefix + 'meetings_authorize', function(e) {
        e.preventDefault();

        var button = $(this);
        var wrapper = button.parent();
        var platform = button.attr('id').replace( _prefix, '' ).replace( '_authorize', '' );

        // Update prefix
        var box_prefix = _prefix + platform + '_';

        var client_id = $('#' + box_prefix + 'client_id').val();
        var client_secret = $('#' + box_prefix + 'client_secret').val();

        // Check if response div exists
        var response_wrap = wrapper.find('#' + box_prefix + 'response');

        if( ! response_wrap.length ) {
            wrapper.append( '<div id="' + box_prefix + 'response" style="display: none; margin-top: 10px;"></div>' );
            response_wrap = wrapper.find('#' + box_prefix + 'response');
        }

        // Show error message if not correctly configured
        if( client_id.length === 0 || client_secret.length === 0 ) {
            response_wrap.addClass( 'automatorwp-notice-error' );
            response_wrap.html( 'All fields are required to connect with Zoom' );
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
                action: 'automatorwp_zoom_authorize',
                nonce: automatorwp_zoom.nonce,
                client_id: client_id,
                client_secret: client_secret,
                platform: platform,
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

})( jQuery );