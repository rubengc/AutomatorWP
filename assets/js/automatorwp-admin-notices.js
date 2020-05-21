(function ( $ ) {

    // Hide review notice
    $('body').on('click', '.automatorwp-hide-review-notice', function(e) {

        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            data: {
                action: 'automatorwp_hide_review_notice',
                nonce: automatorwp_admin_notices.nonce,
            },
            success: function(response) {
                // Hide the notice on success
                $('.automatorwp-review-notice').slideUp('fast');
            }
        });

    });

})( jQuery );