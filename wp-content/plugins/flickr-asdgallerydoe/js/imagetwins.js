(function ($) {
    $(function () {
        /* 
        if ($('.report-image').length > 0) {
            $('.report-image').click(function (evt) {
                var $t = $(this);
                evt.preventDefault();
                $.post(ajaxurl, {
                    action: 'afg_report_images',
                    report: getParameterByName($(this).attr('href'), 'report' ),
                    nonce: getParameterByName($(this).attr('href'), 'nonce' )
                }, function (response) {
                    if ('1' === response.substr(response.length - 1)) {
                        $t.hide('slow');
                    } else {
                        $t.addClass('error'); 
                    }
                });
            });
        }
        */
    });
}(jQuery));

function getParameterByName(url, name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(url);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}