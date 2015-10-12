jQuery(document).ready(function ($){
    var acs_action = 'jg_autocompletesearch';
    $("#pageshortname").autocomplete({
        source: function(req, response){
            $("#pageshortname").parent().find('.error').html(' ');
            $.getJSON(JGSearch.url+'?callback=?&action='+acs_action, req, response);
        },
        minLength: 3
    });
});    