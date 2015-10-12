jQuery.noConflict();

(function($){
   
    $(window).load(function(){
        $(document).ready(function(){  
        if (typeof Modernizr === 'object'){    
            yepnope({ 
              test : Modernizr.inputtypes.datetimeLocal,
              nope : {
                'css': ajaxsiteroot.url + '/wp-content/plugins/justgiving/js/ui/jquery-ui.min.css',
                'js': ajaxsiteroot.url + '/wp-includes/js/jquery/ui/datepicker.min.js'
              },
              callback: { // executed once files are loaded
                'js': function() { 
                    date_obj = new Date();
                    date_obj_hours = date_obj.getHours();
                    date_obj_mins = date_obj.getMinutes();

                    if (date_obj_mins < 10) { date_obj_mins = "0" + date_obj_mins; }

                    if (date_obj_hours > 11) {
                        date_obj_hours = date_obj_hours - 12;
                        date_obj_am_pm = " PM";
                    } else {
                        date_obj_am_pm = " AM";
                    }

                    date_obj_time = "'"+date_obj_hours+":"+date_obj_mins+date_obj_am_pm+"'";        
                    $( "input[type=datetime-local]" ).datepicker({
                        beforeShow: function(input) {
                            $(input).css({
                                "position": "relative",
                                "z-index": 999999
                            });
                        },
                        dateFormat : 'dd/mm/yy '+ date_obj_time ,
                        showAnim: false,
                        minDate: "+0d",
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "-150:+0"
                    }).focus(function () {
                        $(this).blur()
                    });   
                }}
            }); 
            $("#geocomplete").geocomplete({
                details: "fieldset.location",
                detailsAttribute: "data-geo",
                types: ["geocode", "establishment"]
            });
        }
        });
    });
})(jQuery);   