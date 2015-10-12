(function ($) {
    $(function () {
        // If so, we need to setup an event handler to trigger it's dismissal
        $('.og-grid').on('click', '.report-image' ,function(evt){
        
            var $t = $(this);
            evt.preventDefault();
            // Initiate a request to the server-side
            $.post(ajaxafgflickr.ajaxurl, {
                action: 'afg_report_images',
                report: getParameterByName($(this).attr('href'), 'report' ),
            }, function (response) {
                // If the response was successful (that is, 1 was returned), hide the notification;
                // Otherwise, we'll change the class name of the notification
                if ('1' === response.substr(response.length - 1)) {
                    $t.hide('slow');
                    $('body').find('.og-expanded .og-expander').html('<div class="og-expander-inner"><span class="og-close"></span><h3>Inappropriate image? Give it a yellow card.</h3><p>Thank you for raising the yellow card to this image. Our referees have been notified and will give their ruling very soon.</p><p><a href="#" class="report-close">Close this message and return to gallery</a></p></div>');
                    $('body').find('.og-expanded .og-expander').height($('body').find('.og-expanded .og-expander-inner').height() + 40);
                    $('body').scrollTop($('body').find('.og-expanded .og-expander-inner').offset().top);
                } else {
                    $t.addClass('error'); 
                } // end if
            });
        });
    });
}(jQuery));

function getParameterByName(url, name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(url);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}