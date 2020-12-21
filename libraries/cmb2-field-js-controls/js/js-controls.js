(function( $ ) {
    // Edit link
    $('a.cmb-field-js-controls-edit').on( 'click', function( e ) {
        e.preventDefault();

        var field_row = $(this).parent().next('.cmb-row');
        var field = $($(this).attr('href'));

        if ( field_row.is( ':hidden' ) ) {
            // Store previous value to restore if cancel is clicked
            $(this).attr('data-value', field.val());

            // Show field row and js controls after
            field_row.slideDown( 'fast' );
            field_row.next('.cmb-field-js-controls-after').show();

            // Hide this
            $(this).hide();
        }
    });

    // Save link
    $('a.cmb-field-js-controls-save').on( 'click', function( e ) {
        e.preventDefault();

        var field_row = $(this).parent().prev('.cmb-row');
        var field = $($(this).attr('href'));

        if ( ! field_row.is( ':hidden' ) ) {
            // Update value to show
            var value_container = field_row.prev('.cmb-field-js-controls-before').find('.cmb-field-js-controls-value');
            var field_type = 'text';

            if( field_row[0].hasAttribute('data-fieldtype') ) {
                field_type = field_row.attr('data-fieldtype');
            }

            value_container.html( cmb_js_controls_display_field( field, field_type ) );

            // Remove previous value attribute
            field_row.prev('.cmb-field-js-controls-before').find('.cmb-field-js-controls-edit').removeAttr('data-value');

            // Hide field row and show js controls before
            field_row.slideUp( 'fast' );
            field_row.prev('.cmb-field-js-controls-before').find('.cmb-field-js-controls-edit').show();

            // Hide this
            $(this).parent().hide();
        }
    });

    // Cancel link
    $('a.cmb-field-js-controls-cancel').on( 'click', function( e ) {
        e.preventDefault();

        var field_row = $(this).parent().prev('.cmb-row');
        var field = $($(this).attr('href'));

        if ( ! field_row.is( ':hidden' ) ) {
            // Restore previous value
            field.val( field_row.prev('.cmb-field-js-controls-before').find('.cmb-field-js-controls-edit').attr('data-value') );

            // Remove previous value attribute
            field_row.prev('.cmb-field-js-controls-before').find('.cmb-field-js-controls-edit').removeAttr('data-value');

            // Hide field row and show js controls before
            field_row.slideUp( 'fast' );
            field_row.prev('.cmb-field-js-controls-before').find('.cmb-field-js-controls-edit').show();

            // Hide this
            $(this).parent().hide();
        }
    });
})(jQuery);

function cmb_js_controls_display_field( field, field_type ) {
    var field_id = field.attr('id');
    var value = field.val();
    var output = undefined;

    // Filter by cmb_js_controls_display_{field_id}
    output = wp.hooks.applyFilters( 'cmb_js_controls_display_' + field_id, output, field, field_type );

    if( output !== undefined && output.length ) {
        return output;
    }

    // Filter by cmb_js_controls_display_{field_type}
    output = wp.hooks.applyFilters( 'cmb_js_controls_display_' + field_type, output, field );

    if( output !== undefined && output.length ) {
        return output;
    }

    // Built-in CMB2 fields

    // select
    if( field_type === 'select' && field.find('option[value="' + field.val() + '"]').length ) {
        return field.find('option[value="' + field.val() + '"]').html();
    }

    // colorpicker
    if( field_type === 'colorpicker' ) {
        return '<span class="cmb2-colorpicker-swatch"><span style="background-color:' + field.val() + '"></span> ' + field.val() + '</span>';
    }

    // radio
    if( field_type === 'radio' ) {
        // TODO: Get label text
    }

    // CMB2 custom fields
    if( field_type === 'post_ajax_search' || field_type === 'user_ajax_search' || field_type === 'term_ajax_search' ) {
        var anchor = field.closest('.cmb-row').prev('.cmb-field-js-controls-before').find('.cmb-field-js-controls-value .cmb-column a');

        if( anchor.length ) {

            var href = anchor.attr('href')
                .replace(/(post=)[^\&]+/, '$1' + field.val())   // Post parameter
                .replace(/(user=)[^\&]+/, '$1' + field.val())   // User parameter
                .replace(/(term=)[^\&]+/, '$1' + field.val());  // Term parameter

            return '<a href="' + href + '" class="edit-link">' + field.next('input').val() + '</a>'
        } else {
            return field.next('input').val();
        }
    }

    return field.val();
}