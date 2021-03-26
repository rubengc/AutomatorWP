(function ( $ ) {

    var request = window.location.search;
    request = request.replace( '?page=automatorwp_import_automation&', '' );

    $.ajax({
        url: ajaxurl,
        method: 'POST',
        data: {
            action: 'automatorwp_import_automation_from_url',
            nonce: automatorwp_import_automation.nonce,
            request: request,
        },
        success: function( response ) {

            $('.automatorwp-import-automation-loader').hide();
            $('.automatorwp-import-automation-icon-default').hide();

            if( response.success ) {

                $('.automatorwp-import-automation-icon-success').show();

                // Show the success message
                $('.automatorwp-import-automation-text')
                    .addClass('automatorwp-import-automation-success')
                    .html( response.data.message );

                // Redirect to the new automation
                if( response.data.redirect_url !== undefined && response.data.redirect_url.length ) {
                    window.location.href = response.data.redirect_url;
                }
            } else {

                $('.automatorwp-import-automation-icon-error').show();

                // Show the error message
                $('.automatorwp-import-automation-text')
                    .addClass('automatorwp-import-automation-error')
                    .html( response.data );
            }
        },
        error: function( response ) {

            $('.automatorwp-import-automation-loader').hide();
            $('.automatorwp-import-automation-icon-default').hide();
            $('.automatorwp-import-automation-icon-error').show();

            // Show the error message
            $('.automatorwp-import-automation-text')
                .addClass('automatorwp-import-automation-error')
                .html( response.data );
        }
    });

})( jQuery );