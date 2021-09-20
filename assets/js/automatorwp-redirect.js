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
        url: automatorwp_redirect.ajaxurl,
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

/**
 * Check if request URL or its data should be excluded from the redirect check
 *
 * @since 1.6.9
 *
 * @param {string} url
 * @param {string} data
 *
 * @return {boolean}
 */
function automatorwp_redirect_is_url_excluded( url, data ) {

    if( url === undefined ) {
        url = '';
    }

    if( data === undefined ) {
        data = url;
    }

    // Check for excluded urls
    var excluded_url = false;

    automatorwp_redirect.excluded_urls.forEach( function ( to_match ) {
        if( url.includes( to_match ) ) {
            excluded_url = true;
        }
    } );

    if( excluded_url ) {
        return true;
    }

    // Check for excluded data
    var excluded_data = false;

    automatorwp_redirect.excluded_data.forEach( function ( to_match ) {
        if( data.includes( to_match ) ) {
            excluded_data = true;
        }
    } );

    if( excluded_data ) {
        return true;
    }

    // If is an ajax call, check for excluded ajax actions
    if( url.includes('admin-ajax.php') ) {

        var excluded_action = false;

        automatorwp_redirect.excluded_ajax_actions.forEach( function ( to_match ) {
            if( data.includes( 'action=' + to_match ) ) {
                excluded_action = true;
            }
        } );

        if( excluded_action ) {
            return true;
        }

    }

    return false;

}

(function ( $ ) {

    // Listen for any ajax success
    $(document).ajaxSuccess( function ( event, request, settings ) {

        // Bail if URL is excluded
        if( automatorwp_redirect_is_url_excluded( settings.url, settings.data ) ) {
            return;
        }

        var status = parseInt( request.status );

        if ( status === 200 ) {
            automatorwp_check_for_redirect();
        }

    }) ;

})( jQuery );