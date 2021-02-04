var automatorwp_redirect_in_progress = false;

/**
 * Server side check to meet if user should get redirected
 *
 * @since 1.4.3
 */
function automatorwp_check_for_redirect() {

    var $ = jQuery;

    if( automatorwp_redirect.user_id === 0 ) {
        return;
    }

    if( automatorwp_redirect_in_progress ) {
        return;
    }

    automatorwp_redirect_in_progress = true;

    $.ajax({
        url: ajaxurl,
        method: 'POST',
        data: {
            action: 'automatorwp_check_for_redirect',
            nonce: automatorwp_redirect.nonce,
            user_id: automatorwp_redirect.user_id,
        },
        success: function( response ) {

            if( ! automatorwp_redirect_to_url( response.data.redirect_url ) ) {
                automatorwp_redirect_in_progress = false;
            }

        },
        error: function( response ) {

            if( ! automatorwp_redirect_to_url( response.data.redirect_url ) ) {
                automatorwp_redirect_in_progress = false;
            }

        }
    });

}

/**
 * Server side check to meet if user should get redirected
 *
 * @since 1.4.3
 *
 * @param {string} url
 */
function automatorwp_redirect_to_url( url ) {

    if( url === undefined ) {
        url = '';
    }

    if( ! url.length ) {
        return false;
    }

    document.location.href = url;

    return true;

}

(function ( $ ) {

    // Listen for any ajax success
    $(document).ajaxSuccess( function ( event, request ) {

        var status = parseInt( request.status );

        if ( status === 200 ) {
            automatorwp_check_for_redirect();
        }

    }) ;

})( jQuery );