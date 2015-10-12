(function ($) {
    $(function () {
        // Check to see if the Ajax Notification is visible
        if ($('.ban-image').length > 0) {
            // If so, we need to setup an event handler to trigger it's dismissal
            $('.ban-image').click(function (evt) {
                var $t = $(this);
                evt.preventDefault();
                // Initiate a request to the server-side
                $.post(ajaxurl, {
                    action: 'afg_ban_images',
                    report: getParameterByName($(this).attr('href'), 'report' ),
                    nonce: getParameterByName($(this).attr('href'), 'nonce' )
                }, function (response) {
                    // If the response was successful (that is, 1 was returned), hide the notification;
                    // Otherwise, we'll change the class name of the notification
                    if ('1' === response.substr(response.length - 1)) {
                        $t.hide('slow');
                    } else {
                        $t.addClass('error'); 
                    } // end if
                });
            });
        } // end if
    });
}(jQuery));

function getParameterByName(url, name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(url);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}