/*
jQuery(document).ready(function(){
    jQuery('#ajaxbhfflickr .error').hide();  
    function uploadFiles(event){
        event.stopPropagation();
        event.preventDefault();
        jQuery('#ajaxbhfflickr .error').hide();  
        jQuery('#ajaxbhfflickr .error').empty();  
        var error = 0;
        var name = jQuery("input#yourname").val();           
        if (name == "") {  
            jQuery("#name_error").html( 'Please enter your name' ).show();  
            jQuery("input#yourname").focus(); 
            error = 1;
        }  
        var email = jQuery("input#youremail").val();  
        if (email == "") {  
            jQuery("#email_error").html( 'Please enter your email address' ).show();  
            jQuery("input#youremail").focus();  
            error = 1;
        }
        var file = jQuery("input#yourfile").val();  
        if (file == "") {  
            jQuery("#file_error").html( 'Please choose a picture to upload' ).show();  
            jQuery("input#yourfile").focus();  
            error = 1;
        }           
        if (!jQuery("#tsandcs").is(':checked')) {  
            jQuery("#tsandcs_error").html( 'You must accept the terms and conditions' ).show();  
            jQuery("input#tsandcs").focus();  
            error = 1;
        }    
        if (error != 0) return false;  
        jQuery.ajax({
            url: jQuery('#ajaxbhfflickr').attr('action'),
            type: 'POST',
            data: {
                action: 'ajaxbhf_flickr_send',
                yourfile: jQuery("input#yourfile").val(),
                yourname: jQuery("input#yourname").val(),
                youremail: jQuery("input#youremail").val(),
                tsandcs: jQuery("#some_id").attr("checked") ? 1 : 0,
            },
            cache: false,
            success:function(data, textStatus, XMLHttpRequest){
                var id = '#ajaxbhfflickr';
                //jQuery(id).replaceWith(data);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert(errorThrown);
            }
        });        
    }
    jQuery('#ajaxbhfflickr').on('submit', uploadFiles);
});
*/