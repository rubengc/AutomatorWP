var automatorwp_redirect_in_progress = false;

/**
 * Server side check to meet if user should get redirected
 *
 * @since 1.4.3
 */
function automatorwp_check_for_redirect() {

    var $ = jQuery;

    var user_id = parseInt( automatorwp_redirect.user_id );

    if( user_id === 0 || isNaN( user_id ) ) {
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

            // Sanitization check
            if( ! automatorwp_is_response_valid_for_redirect( response ) ) {
                automatorwp_redirect_in_progress = false;
                return;
            }

            // Try to redirect
            if( ! automatorwp_redirect_to_url( response.data.redirect_url ) ) {
                automatorwp_redirect_in_progress = false;
            }

        },
        error: function( response ) {

            // Sanitization check
            if( ! automatorwp_is_response_valid_for_redirect( response ) ) {
                automatorwp_redirect_in_progress = false;
                return;
            }

            // Try to redirect
            if( ! automatorwp_redirect_to_url( response.data.redirect_url ) ) {
                automatorwp_redirect_in_progress = false;
            }

        }
    });

}

/**
 * Check if the response object has all required properties
 *
 * @since 2.4.1
 *
 * @param {object} response
 */
function automatorwp_is_response_valid_for_redirect( response ) {

    if( response === undefined ) {
        return false;
    }

    if( response.data === undefined ) {
        return false;
    }

    if( response.data.redirect_url === undefined ) {
        return false;
    }

    return true;

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

    if ( typeof data !== 'string' ) {
        data = url;
    }

    // Check for excluded urls
    var excluded_url = false;

    automatorwp_redirect.excluded_urls.forEach( function ( to_match ) {
        if( url !== undefined && url.includes( to_match ) || url === to_match ) {
            excluded_url = true;
        }
    } );

    if( excluded_url ) {
        return true;
    }

    // Check for excluded data
    var excluded_data = false;

    automatorwp_redirect.excluded_data.forEach( function ( to_match ) {
        if( data !== undefined && data.includes( to_match ) ) {
            excluded_data = true;
        }
    } );

    if( excluded_data ) {
        return true;
    }

    // If is an ajax call, check for excluded ajax actions
    if( url !== undefined && url.includes('admin-ajax.php') ) {

        var excluded_action = false;

        automatorwp_redirect.excluded_ajax_actions.forEach( function ( to_match ) {
            if( data !== undefined && data.includes( 'action=' + to_match ) ) {
                excluded_action = true;
            }

            if( url !== undefined && url.includes( 'action=' + to_match ) ) {
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