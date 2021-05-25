/**
 * Helper function to initialize select2 on fields
 *
 * @since 1.0.0
 *
 * @param {Object} $this
 */
function automatorwp_selector( $this ) {

    // Prevent load select2 on widgets lists
    if( $this.closest('#available-widgets').length ) {
        return;
    }

    var select2_args = {
        theme: 'default automatorwp-select2',
        placeholder: ( $this.data('placeholder') ? $this.data('placeholder') : automatorwp_admin_functions.selector_placeholder ),
        allowClear: true,
        multiple: ( $this[0].hasAttribute('multiple') ),
    };

    $this.automatorwp_select2( select2_args );

}

/**
 * Helper function to initialize select2 post selector on fields
 *
 * @since 1.0.0
 *
 * @param {Object} $this
 */
function automatorwp_post_selector( $this ) {

    // Prevent load select2 on widgets lists
    if( $this.closest('#available-widgets').length ) {
        return;
    }

    var post_types = $this.data('post-type').split(',');

    var select2_args = {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    action: 'automatorwp_get_posts',
                    nonce: automatorwp_admin_functions.nonce,
                    post_type: post_types,
                    post_type_cb: $this.data('post-type-cb'),
                    option_none: $this.data('option-none'),
                    option_none_value: $this.data('option-none-value'),
                    option_none_label: $this.data('option-none-label'),
                    option_custom: $this.data('option-custom'),
                    option_custom_value: $this.data('option-custom-value'),
                    option_custom_label: $this.data('option-custom-label'),
                };
            },
            processResults: automatorwp_select2_posts_process_results
        },
        escapeMarkup: function ( markup ) { return markup; },
        templateResult: ( post_types.length === 1 ? automatorwp_select2_posts_template_result : automatorwp_select2_post_types_template_result ),
        theme: 'default automatorwp-select2',
        placeholder: ( $this.data('placeholder') ? $this.data('placeholder') : automatorwp_admin_functions.post_selector_placeholder ),
        allowClear: true,
        multiple: ( $this[0].hasAttribute('multiple') )
    };

    $this.automatorwp_select2( select2_args );

}

/**
 * Helper function to initialize select2 term selector on fields
 *
 * @since 1.0.0
 *
 * @param {Object} $this
 */
function automatorwp_term_selector( $this ) {

    // Prevent load select2 on widgets lists
    if( $this.closest('#available-widgets').length ) {
        return;
    }

    var select2_args = {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    action: 'automatorwp_get_terms',
                    nonce: automatorwp_admin_functions.nonce,
                    taxonomy: $this.data('taxonomy'),
                    option_none: $this.data('option-none'),
                    option_none_value: $this.data('option-none-value'),
                    option_none_label: $this.data('option-none-label'),
                    option_custom: $this.data('option-custom'),
                    option_custom_value: $this.data('option-custom-value'),
                    option_custom_label: $this.data('option-custom-label'),
                };
            },
            processResults: automatorwp_select2_terms_process_results
        },
        escapeMarkup: function ( markup ) { return markup; },
        templateResult: automatorwp_select2_terms_template_result,
        theme: 'default automatorwp-select2',
        placeholder: ( $this.data('placeholder') ? $this.data('placeholder') : automatorwp_admin_functions.term_selector_placeholder ),
        allowClear: true,
        multiple: ( $this[0].hasAttribute('multiple') )
    };

    $this.automatorwp_select2( select2_args );

}

/**
 * Helper function to initialize select2 user selector on fields
 *
 * @since 1.0.0
 *
 * @param {Object} $this
 */
function automatorwp_user_selector( $this ) {

    // Prevent load select2 on widgets lists
    if( $this.closest('#available-widgets').length ) {
        return;
    }

    var select2_args = {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    action: 'automatorwp_get_users',
                    nonce: automatorwp_admin_functions.nonce,
                };
            },
            processResults: automatorwp_select2_users_process_results
        },
        escapeMarkup: function ( markup ) { return markup; },
        templateResult: automatorwp_select2_users_template_result,
        theme: 'default automatorwp-select2',
        placeholder: ( $this.data('placeholder') ? $this.data('placeholder') : automatorwp_admin_functions.user_selector_placeholder ),
        allowClear: true,
        multiple: ( $this[0].hasAttribute('multiple') )
    };

    $this.automatorwp_select2( select2_args );

}

/**
 * Helper function to initialize select2 object selector on fields
 *
 * @since 1.0.0
 *
 * @param {Object} $this
 */
function automatorwp_object_selector( $this ) {

    // Prevent load select2 on widgets lists
    if( $this.closest('#available-widgets').length ) {
        return;
    }

    var select2_args = {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    action: 'automatorwp_get_objects',
                    nonce: automatorwp_admin_functions.nonce,
                    table: $this.data('table'),
                    option_none: $this.data('option-none'),
                    option_none_value: $this.data('option-none-value'),
                    option_none_label: $this.data('option-none-label'),
                    option_custom: $this.data('option-custom'),
                    option_custom_value: $this.data('option-custom-value'),
                    option_custom_label: $this.data('option-custom-label'),
                };
            },
            processResults: automatorwp_select2_objects_process_results
        },
        escapeMarkup: function ( markup ) { return markup; },
        templateResult: automatorwp_select2_objects_template_result,
        theme: 'default automatorwp-select2',
        placeholder: ( $this.data('placeholder') ? $this.data('placeholder') : automatorwp_admin_functions.object_selector_placeholder ),
        allowClear: true,
        multiple: ( $this[0].hasAttribute('multiple') )
    };

    $this.automatorwp_select2( select2_args );

}

/**
 * Helper function to initialize select2 ajax selector on fields
 *
 * @since 1.0.0
 *
 * @param {Object} $this
 */
function automatorwp_ajax_selector( $this ) {

    // Prevent load select2 on widgets lists
    if( $this.closest('#available-widgets').length ) {
        return;
    }

    var select2_args = {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {

                var data = {
                    q: params.term,
                    page: params.page || 1,
                    action: $this.data('action'),
                    nonce: automatorwp_admin_functions.nonce,
                    table: $this.data('table'),
                    option_none: $this.data('option-none'),
                    option_none_value: $this.data('option-none-value'),
                    option_none_label: $this.data('option-none-label'),
                    option_custom: $this.data('option-custom'),
                    option_custom_value: $this.data('option-custom-value'),
                    option_custom_label: $this.data('option-custom-label'),
                };

                /**
                 * Allow external functions to add their own data
                 *
                 * @since 1.0.0
                 *
                 * @param Object    data
                 * @param Node      element
                 */
                $this.trigger( 'automatorwp_ajax_selector_data', [ data, $this ] );

                return data;
            },
            processResults: automatorwp_select2_ajax_process_results
        },
        escapeMarkup: function ( markup ) { return markup; },
        templateResult: automatorwp_select2_ajax_template_result,
        theme: 'default automatorwp-select2',
        placeholder: ( $this.data('placeholder') ? $this.data('placeholder') : '' ),
        allowClear: true,
        multiple: ( $this[0].hasAttribute('multiple') )
    };

    $this.automatorwp_select2( select2_args );

}

/**
 * Custom formatting for posts on select2
 *
 * @since 1.0.0
 *
 * @param {Object} item
 *
 * @return {string}
 */
function automatorwp_select2_posts_template_result( item ) {

    if( item.post_title !== undefined ) {
        return item.post_title + ( ! isNaN( item.ID ) ? '<span class="result-description align-right">ID: ' + item.ID + '</span>': '' );
    }

    return item.text + ( ! isNaN( item.id ) ? '<span class="result-description align-right">ID: ' + item.id + '</span>': '' );

}

/**
 * Custom formatting for posts on select2 with multiples post types
 *
 * @since 1.0.0
 *
 * @param {Object} item
 *
 * @return {string}
 */
function automatorwp_select2_post_types_template_result( item ) {

    if( item.post_type !== undefined ) {

        var post_type_label = item.post_type;

        if( automatorwp_post_type_exists( item.post_type ) ) {
            post_type_label = automatorwp_get_post_type_label( item.post_type );
        }

        return item.post_title
            + '<span class="result-description">'
            + post_type_label + '<span class="align-right">' + 'ID: ' + item.ID + '</span>'
            + '</span>';
    }

    // Fallback oon posts template result
    return automatorwp_select2_posts_template_result( item );

}

/**
 * Custom formatting for terms on select2
 *
 * @since 1.0.0
 *
 * @param {Object} item
 *
 * @return {string}
 */
function automatorwp_select2_terms_template_result( item ) {

    if( item.name !== undefined ) {
        return item.name + ( ! isNaN( item.term_id ) ? '<span class="result-description align-right">ID: ' + item.term_id + '</span>': '' );
    }

    return item.text + ( ! isNaN( item.id ) ? '<span class="result-description align-right">ID: ' + item.id + '</span>': '' );

}

/**
 * Custom results processing for posts on select2
 *
 * @since 1.0.0
 *
 * @param {Object} response
 * @param {Object} params
 *
 * @return {string}
 */
function automatorwp_select2_posts_process_results( response, params ) {

    if( response === null ) {
        return { results: [] };
    }

    var formatted_results = [];

    // Paginated responses will come with results and more_results keys
    var results = ( response.data.results !== undefined ? response.data.results : response.data );

    results.forEach( function( item ) {

        // Extend select2 keys (id and text) with given keys (ID, post_title and optionally post_type, site_id and site_name)
        formatted_results.push( jQuery.extend({
            id: item.ID,
            //text: item.post_title + ' (ID: ' + item.ID + ')',
            text: item.post_title,
        }, item ) );

    } );

    return {
        results: formatted_results,
        pagination: {
            more: ( response.data.more_results !== undefined ? response.data.more_results : false )
        }
    };

}

/**
 * Custom results processing for terms on select2
 *
 * @since 1.0.0
 *
 * @param {Object} response
 * @param {Object} params
 *
 * @return {string}
 */
function automatorwp_select2_terms_process_results( response, params ) {

    if( response === null ) {
        return { results: [] };
    }

    var formatted_results = [];

    // Paginated responses will come with results and more_results keys
    var results = ( response.data.results !== undefined ? response.data.results : response.data );

    results.forEach( function( item ) {

        // Extend select2 keys (id and text) with given keys (ID, post_title and optionally post_type, site_id and site_name)
        formatted_results.push( jQuery.extend({
            id: item.term_id,
            //text: item.post_title + ' (ID: ' + item.ID + ')',
            text: item.name,
        }, item ) );

    } );

    return {
        results: formatted_results,
        pagination: {
            more: ( response.data.more_results !== undefined ? response.data.more_results : false )
        }
    };

}

/**
 * Custom formatting for users on select2
 *
 * @since 1.0.0
 *
 * @param {Object} item
 *
 * @return {string}
 */
function automatorwp_select2_users_template_result( item ) {

    if( item.display_name !== undefined ) {

        return item.display_name + ( ! isNaN( item.ID ) ? '<span class="result-description align-right">ID: ' + item.ID + '</span>': '' );

    }

    return item.text + ( ! isNaN( item.id ) ? '<span class="result-description align-right">ID: ' + item.id + '</span>': '' );

}

/**
 * Custom results processing for users on select2
 *
 * @since 1.0.0
 *
 * @param {Object} response
 * @param {Object} params
 *
 * @return {string}
 */
function automatorwp_select2_users_process_results( response, params ) {

    if( response === null ) {
        return { results: [] };
    }

    var formatted_results = [];

    // Paginated responses will come with results and more_results keys
    var results = ( response.data.results !== undefined ? response.data.results : response.data );

    results.forEach( function( item ) {

        // Extend select2 keys (id and text) with given keys (ID, post_title and optionally post_type)
        formatted_results.push( jQuery.extend({
            id: item.ID,
            text: item.user_login,
            //text: item.user_login + ' (#' + item.ID + ')',
        }, item ) );

    } );

    return {
        results: formatted_results,
        pagination: {
            more: ( response.data.more_results !== undefined ? response.data.more_results : false )
        }
    };

}

/**
 * Custom formatting for objects on select2
 *
 * @since 1.0.0
 *
 * @param {Object} item
 *
 * @return {string}
 */
function automatorwp_select2_objects_template_result( item ) {

    return item.text + ( ! isNaN( item.id ) ? '<span class="result-description align-right">ID: ' + item.id + '</span>': '' );

}

/**
 * Custom results processing for objects on select2
 *
 * @since 1.0.0
 *
 * @param {Object} response
 * @param {Object} params
 *
 * @return {string}
 */
function automatorwp_select2_objects_process_results( response, params ) {

    if( response === null ) {
        return { results: [] };
    }

    var formatted_results = [];

    // Paginated responses will come with results and more_results keys
    var results = ( response.data.results !== undefined ? response.data.results : response.data );

    results.forEach( function( item ) {

        // Extend select2 keys (id and text) with given keys (id and text)
        formatted_results.push( jQuery.extend({
            id: item.id,
            text: item.text,
        }, item ) );

    } );

    return {
        results: formatted_results,
        pagination: {
            more: ( response.data.more_results !== undefined ? response.data.more_results : false )
        }
    };

}

/**
 * Custom formatting for objects on select2
 *
 * @since 1.0.0
 *
 * @param {Object} item
 *
 * @return {string}
 */
function automatorwp_select2_ajax_template_result( item ) {

    return item.text + ( ! isNaN( item.id ) ? '<span class="result-description align-right">ID: ' + item.id + '</span>': '' );

}

/**
 * Custom results processing for objects on select2
 *
 * @since 1.0.0
 *
 * @param {Object} response
 * @param {Object} params
 *
 * @return {string}
 */
function automatorwp_select2_ajax_process_results( response, params ) {

    if( response === null ) {
        return { results: [] };
    }

    var formatted_results = [];

    // Paginated responses will come with results and more_results keys
    var results = ( response.data.results !== undefined ? response.data.results : response.data );

    results.forEach( function( item ) {

        // Extend select2 keys (id and text) with given keys (id and text)
        formatted_results.push( jQuery.extend({
            id: item.id,
            text: item.text,
        }, item ) );

    } );

    return {
        results: formatted_results,
        pagination: {
            more: ( response.data.more_results !== undefined ? response.data.more_results : false )
        }
    };

}

/**
 * Custom search matcher for selects with groups on select2
 *
 * @since 1.0.0
 *
 * @param {Object} params
 * @param {Object} data
 *
 * @return {mixed}
 */
function automatorwp_select2_optgroup_matcher( params, data ) {

    // Initialize required vars
    data.parentText = data.parentText   || '';

    // Always return the object if there is nothing to compare
    if ( params.term === undefined ) {
        return data;
    }

    if ( params.term.trim() === '' ) {
        return data;
    }

    // Do a recursive check for options with children
    if ( data.children && data.children.length > 0 ) {

        // Clone the data object if there are children
        // This is required as we modify the object to remove any non-matches
        var match = jQuery.extend( true, {}, data );

        // Check each child of the option
        for ( var c = data.children.length - 1; c >= 0; c-- ) {

            var child = data.children[c];
            child.parentText += data.parentText + " " + data.text;

            var matches = automatorwp_select2_optgroup_matcher( params, child );

            // If there wasn't a match, remove the object in the array
            if (matches == null) {
                match.children.splice( c, 1 );
            }

        }

        // If any children matched, return the new object
        if ( match.children.length > 0 ) {
            return match;
        }

        // If there were no matching children, check just the plain object
        return automatorwp_select2_optgroup_matcher( params, match );

    }

    // If the typed-in term matches the text of this term, or the text from any
    // parent term, then it's a match.
    var original = ( data.parentText + ' ' + data.text ).toUpperCase();
    var term = params.term.toUpperCase();


    // Check if the text contains the term
    if ( original.indexOf( term ) > -1 ) {
        return data;
    }

    // If it doesn't contain the term, don't return anything
    return null;
}

/**
 * Check if post type has been registered
 *
 * @since 1.0.0
 *
 * @param {string} post_type
 *
 * @return {boolean}
 */
function automatorwp_post_type_exists( post_type ) {
    return ( automatorwp_admin_functions.post_type_labels[post_type] !== undefined )
}

/**
 * Get the post type label (singular name)
 *
 * @since 1.0.0
 *
 * @param {string} post_type
 *
 * @return {string}
 */
function automatorwp_get_post_type_label( post_type ) {

    var label = '';

    if( automatorwp_post_type_exists( post_type ) ) {
        label = automatorwp_admin_functions.post_type_labels[post_type];
    }

    return label;
}

/**
 * Check if taxonomy has been registered
 *
 * @since 1.0.0
 *
 * @param {string} taxonomy
 *
 * @return {boolean}
 */
function automatorwp_taxonomy_exists( taxonomy ) {
    return ( automatorwp_admin_functions.taxonomy_labels[taxonomy] !== undefined )
}

/**
 * Get the taxonomy label (singular name)
 *
 * @since 1.0.0
 *
 * @param {string} taxonomy
 *
 * @return {string}
 */
function automatorwp_get_taxonomy_label( taxonomy ) {

    var label = '';

    if( automatorwp_taxonomy_exists( taxonomy ) ) {
        label = automatorwp_admin_functions.taxonomy_labels[taxonomy];
    }

    return label;
}

/**
 * Function to turn an object or a JSON object to a CSV file and force the download (Used on import/export tools)
 *
 * @since 1.0.0
 *
 * @param {Object} data
 * @param {string} filename
 */
function automatorwp_download_csv( data, filename ) {

    // Convert JSON to CSV
    var csv = automatorwp_object_to_csv( data );

    automatorwp_download_file( csv, filename, 'csv' );

}

/**
 * Function to force the download of the given content (Used on import/export tools)
 *
 * @since 1.0.0
 *
 * @param {string} content
 * @param {string} filename
 * @param {string} extension
 * @param {string} mime_type
 * @param {string} charset
 */
function automatorwp_download_file( content, filename, extension, mime_type = '', charset = '' ) {

    if( mime_type === undefined || mime_type === '' )
        mime_type = 'text/' + extension;

    if( charset === undefined || charset === '' )
        charset = 'utf-8';

    // Setup the file name
    var file = ( filename.length ? filename + '.' + extension : 'file.' + extension );

    var blob = new Blob( [content], { type: mime_type + ';charset=' + charset + ';' } );

    if (navigator.msSaveBlob) {

        // IE 10+
        navigator.msSaveBlob( blob, file );

    } else {

        var link = document.createElement("a");

        // Hide the link element
        link.style.visibility = 'hidden';

        // Check if browser supports HTML5 download attribute
        if ( link.download !== undefined ) {

            // Build the URL object
            var url = URL.createObjectURL( blob );

            // Update link attributes
            link.setAttribute( "href", url );
            link.setAttribute( "download", file );

            // Append the link element and trigger the click event
            document.body.appendChild( link );

            link.click(); // NOTE: Is not a jQuery element, so is safe to use click()

            // Finally remove the link element
            document.body.removeChild( link );

        }
    }

}

/**
 * Format an object into a CSV line
 *
 * @since 1.0.0
 *
 * @param {Object} obj
 *
 * @return {string}
 */
function automatorwp_object_to_csv( obj ) {

    // Convert JSON to Object
    var array = typeof obj !== 'object' ? JSON.parse( obj ) : obj;
    var str = '';

    for ( var i = 0; i < array.length; i++ ) {

        var line = '';

        for ( var index in array[i] ) {

            // Separator
            if ( line !== '' ) {
                line += ',';
            }

            // Build a new line
            line += '"' + array[i][index] + '"';
        }

        // Append the line break
        str += line + '\r\n';

    }

    return str;

}

/**
 * Insert content at caret position
 *
 * @since 1.0.0
 *
 * @param {Object} input
 * @param {String} content
 */
function automatorwp_insert_at_caret(input, content) {

    input.each(function() {

        if ( document.selection ) {
            // Internet Explorer

            this.focus(); // NOTE: Is not a jQuery element, so is safe to use focus()
            var sel = document.selection.createRange();
            sel.text = content;
            this.focus();

        } else if ( this.selectionStart || this.selectionStart == '0' ) {
            // Firefox/Chrome/Opera

            var startPos = this.selectionStart;
            var endPos = this.selectionEnd;
            var scrollTop = this.scrollTop;
            this.value = this.value.substring(0, startPos) +
                content + this.value.substring(endPos,this.value.length);
            this.focus();
            this.selectionStart = startPos + content.length;
            this.selectionEnd = startPos + content.length;
            this.scrollTop = scrollTop;

        } else {
            // If can't determine browser, only append at the end

            this.value += content;
            this.focus();

        }
    });

}

/**
 * Set caret position at the end of the input content
 *
 * @since 1.0.0
 *
 * @param {Object} input
 */
function automatorwp_set_caret_at_end( input ) {

    // Get the input node
    input = input[0];

    var length = input.value.length;

    // For IE Only
    if ( document.selection ) {

        // Set focus
        input.focus(); // NOTE: Is not a jQuery element, so is safe to use focus()

        // Use IE Ranges
        var oSel = document.selection.createRange();

        // Reset position to 0 & then set at end
        oSel.moveStart('character', -length);
        oSel.moveStart('character', length);
        oSel.moveEnd('character', 0);
        oSel.select();

    } else if ( input.selectionStart || input.selectionStart == '0' ) {

        // Firefox/Chrome
        input.selectionStart = length;
        input.selectionEnd = length;
        input.focus(); // NOTE: Is not a jQuery element, so is safe to use focus()
    }

}

/**
 * Helper function to get a parameter from an URL
 *
 * @since 1.0.0
 *
 * @param {String} url
 * @param {String} param
 * @param default_value
 *
 * @return {String}
 */
function automatorwp_get_url_param( url, param, default_value = false ) {

    var results = new RegExp('[\?&]' + param + '=([^&#]*)').exec( url );

    return results[1] || default_value;

}

/**
 * Helper function to check if given URL is a valid one
 *
 * @since 1.0.0
 *
 * @param {String} url
 *
 * @return {boolean}
 */
function automatorwp_is_valid_url( url ) {
    var result = url.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
    return ( result !== null )
}