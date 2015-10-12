
var scriptPath = function () {
    var scripts = document.getElementsByTagName('SCRIPT');
    var path = '';
    if(scripts && scripts.length>0) {
        for(var i in scripts) {
            if(scripts[i].src && scripts[i].src.match(/\/uplflickr\.js/)) {
                var matcha  = scripts[i].src.match(/(.*)\/uplflickr\.js/gmi);
                path = matcha[0].replace('js/uplflickr.js', '');
                break;
            }
        }
    }
    return path;
};   

function updateURLParameter(url, param, paramVal){
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (i=0; i<tempArray.length; i++){
            if(tempArray[i].split('=')[0] != param){
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }
    var rows_txt = '';
    if (paramVal != '') rows_txt = temp + "" + param + "=" + paramVal;
    return baseURL + "?" + newAdditionalURL + rows_txt;
} 

jQuery.noConflict();
(function( $ ) {

  $(document).ready(function( ){ 
    $('.flickr-gallery-bottom-buttons').hide();
    $(window).scroll(function() {
        if ($(this).scrollTop() >= 300) {
            $('.flickr-gallery-bottom-buttons').show();
        }
    });
    if ($('#ajaxafgflickr').length > 0){
        var url = "http://ajax.aspnetcdn.com/ajax/jquery.validate/1.12.0/jquery.validate.min.js";
        $.getScript( url, function() {
            setupValidation();
        });
    }
               
    function setupValidation (){
        var patterns = {
            letters: /^[\sa-z'-]+$/i,
            ukpostcode: /^([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9]?[A-Za-z])))) {0,1}[0-9][A-Za-z]{2})$/,
            telephone: /^([\+(][+0-9()]{1,5}([ )\-])?)?([\(]{1}[0-9]{3}[\)])?([0-9 \-]{1,256}([ \s\-])?)((x|ext|extension)?[0-9]{1,4}?)$/i,
            mobile: /^((0|\+44|00\d{2})7(5|6|7|8|9){1}\d{2}\s?\d{6})$/,
            decimal: /^\d*(\.[0-9]{1,2})?$/i,
            mastercard: /^5[1-5]\d{14}$/,
            visa: /(^4\d{12}$)|(^4[0-8]\d{14}$)|(^(49)[^013]\d{13}$)|(^(49030)[0-1]\d{10}$)|(^(49033)[0-4]\d{10}$)|(^(49110)[^12]\d{10}$)|(^(49117)[0-3]\d{10}$)|(^(49118)[^0-2]\d{10}$)|(^(493)[^6]\d{12}$)/,
            maestro: /(^(5[0678])\d{11,18}$)|(^(6[^05])\d{11,18}$)|(^(601)[^1]\d{9,16}$)|(^(6011)\d{9,11}$)|(^(6011)\d{13,16}$)|(^(65)\d{11,13}$)|(^(65)\d{15,18}$)|(^(49030)[2-9](\d{10}$|\d{12,13}$))|(^(49033)[5-9](\d{10}$|\d{12,13}$))|(^(49110)[1-2](\d{10}$|\d{12,13}$))|(^(49117)[4-9](\d{10}$|\d{12,13}$))|(^(49118)[0-2](\d{10}$|\d{12,13}$))|(^(4936)(\d{12}$|\d{14,15}$))/,
            securitycode: /[0-9]{3}/,
            alphanums: /^[a-zA-Z0-9]*$/,
            postcode: /^[ a-zA-Z0-9]*$/,
            words: /^[ a-zA-Z0-9'-]*$/,
            groupid: /^[A-Za-z]{2}[0-9]{4}$/,
            url: /^[0-9a-z-]+$/i,
            emails: /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/,
            addressline: /^[A-Za-z0-9\-_’'‘ÆÐ??????Œ?Þ??æð??????œ??ßþ??A?ÇÐ?EHI?LØOS?T?TUUY??a?çd?ehi?løos?t?tuuy??ÁÀÂÄAAAÃÅ?AÆ???CCCCÇD?Ð?ÐÉÈEÊËEEEE????GGGGG?áàâäaaaãå?aæ???ccccçd?d?ðéèeêëeeee????ggggg?H?HIÍÌIÎÏIIIII??JK?LLLL?'NNN¨NÑN?ÓÒÔÖOOOÕO?Ø?OŒh?hiíìiîïiiiii??jk??llll??nn¨nñn?óòôöoooõo?ø?oœRRRSSŠS???TT?TÞÚÙÛÜUUUUUUU?U??W??Ý?YŸ???ZZŽ?rrr?ssšs??ßtt?tþúùûüuuuuuuu?u??w??ý?yÿ???zzž?]+[A-Za-z0-9\-_ ’'‘ÆÐ??????Œ?Þ??æð??????œ??ßþ??A?ÇÐ?EHI?LØOS?T?TUUY??a?çd?ehi?løos?t?tuuy??ÁÀÂÄAAAÃÅ?AÆ???CCCCÇD?Ð?ÐÉÈEÊËEEEE????GGGGG?áàâäaaaãå?aæ???ccccçd?d?ðéèeêëeeee????ggggg?H?HIÍÌIÎÏIIIII??JK?LLLL?'NNN¨NÑN?ÓÒÔÖOOOÕO?Ø?OŒh?hiíìiîïiiiii??jk??llll??nn¨nñn?óòôöoooõo?ø?oœRRRSSŠS???TT?TÞÚÙÛÜUUUUUUU?U??W??Ý?YŸ???ZZŽ?rrr?ssšs??ßtt?tþúùûüuuuuuuu?u??w??ý?yÿ???zzž?]*[A-Za-z]+[A-Za-z0-9\-_ ’'‘ÆÐ??????Œ?Þ??æð??????œ??ßþ??A?ÇÐ?EHI?LØOS?T?TUUY??a?çd?ehi?løos?t?tuuy??ÁÀÂÄAAAÃÅ?AÆ???CCCCÇD?Ð?ÐÉÈEÊËEEEE????GGGGG?áàâäaaaãå?aæ???ccccçd?d?ðéèeêëeeee????ggggg?H?HIÍÌIÎÏIIIII??JK?LLLL?'NNN¨NÑN?ÓÒÔÖOOOÕO?Ø?OŒh?hiíìiîïiiiii??jk??llll??nn¨nñn?óòôöoooõo?ø?oœRRRSSŠS???TT?TÞÚÙÛÜUUUUUUU?U??W??Ý?YŸ???ZZŽ?rrr?ssšs??ßtt?tþúùûüuuuuuuu?u??w??ý?yÿ???zzž?]*$/,
            // As addressline but accepts full stop as well
            addressline1: /^[A-Za-z0-9\-_’'‘.ÆÐ??????Œ?Þ??æð??????œ??ßþ??A?ÇÐ?EHI?LØOS?T?TUUY??a?çd?ehi?løos?t?tuuy??ÁÀÂÄAAAÃÅ?AÆ???CCCCÇD?Ð?ÐÉÈEÊËEEEE????GGGGG?áàâäaaaãå?aæ???ccccçd?d?ðéèeêëeeee????ggggg?H?HIÍÌIÎÏIIIII??JK?LLLL?'NNN¨NÑN?ÓÒÔÖOOOÕO?Ø?OŒh?hiíìiîïiiiii??jk??llll??nn¨nñn?óòôöoooõo?ø?oœRRRSSŠS???TT?TÞÚÙÛÜUUUUUUU?U??W??Ý?YŸ???ZZŽ?rrr?ssšs??ßtt?tþúùûüuuuuuuu?u??w??ý?yÿ???zzž?]+[A-Za-z0-9\-_ ’'‘.ÆÐ??????Œ?Þ??æð??????œ??ßþ??A?ÇÐ?EHI?LØOS?T?TUUY??a?çd?ehi?løos?t?tuuy??ÁÀÂÄAAAÃÅ?AÆ???CCCCÇD?Ð?ÐÉÈEÊËEEEE????GGGGG?áàâäaaaãå?aæ???ccccçd?d?ðéèeêëeeee????ggggg?H?HIÍÌIÎÏIIIII??JK?LLLL?'NNN¨NÑN?ÓÒÔÖOOOÕO?Ø?OŒh?hiíìiîïiiiii??jk??llll??nn¨nñn?óòôöoooõo?ø?oœRRRSSŠS???TT?TÞÚÙÛÜUUUUUUU?U??W??Ý?YŸ???ZZŽ?rrr?ssšs??ßtt?tþúùûüuuuuuuu?u??w??ý?yÿ???zzž?]*[A-Za-z]+[A-Za-z0-9\-_ ’'‘.ÆÐ??????Œ?Þ??æð??????œ??ßþ??A?ÇÐ?EHI?LØOS?T?TUUY??a?çd?ehi?løos?t?tuuy??ÁÀÂÄAAAÃÅ?AÆ???CCCCÇD?Ð?ÐÉÈEÊËEEEE????GGGGG?áàâäaaaãå?aæ???ccccçd?d?ðéèeêëeeee????ggggg?H?HIÍÌIÎÏIIIII??JK?LLLL?'NNN¨NÑN?ÓÒÔÖOOOÕO?Ø?OŒh?hiíìiîïiiiii??jk??llll??nn¨nñn?óòôöoooõo?ø?oœRRRSSŠS???TT?TÞÚÙÛÜUUUUUUU?U??W??Ý?YŸ???ZZŽ?rrr?ssšs??ßtt?tþúùûüuuuuuuu?u??w??ý?yÿ???zzž?]*$/,
            firstname: /^[A-Za-z\-_’ '‘.ÆÐ??????Œ?Þ??æð??????œ??ßþ??A?ÇÐ?EHI?LØOS?T?TUUY??a?çd?ehi?løos?t?tuuy??ÁÀÂÄAAAÃÅ?AÆ???CCCCÇD?Ð?ÐÉÈEÊËEEEE????GGGGG?áàâäaaaãå?aæ???ccccçd?d?ðéèeêëeeee????ggggg?H?HIÍÌIÎÏIIIII??JK?LLLL?'NNN¨NÑN?ÓÒÔÖOOOÕO?Ø?OŒh?hiíìiîïiiiii??jk??llll??nn¨nñn?óòôöoooõo?ø?oœRRRSSŠS???TT?TÞÚÙÛÜUUUUUUU?U??W??Ý?YŸ???ZZŽ?rrr?ssšs??ßtt?tþúùûüuuuuuuu?u??w??ý?yÿ???zzž?]+[A-Za-z\-_’ '‘.ÆÐ??????Œ?Þ??æð??????œ??ßþ??A?ÇÐ?EHI?LØOS?T?TUUY??a?çd?ehi?løos?t?tuuy??ÁÀÂÄAAAÃÅ?AÆ???CCCCÇD?Ð?ÐÉÈEÊËEEEE????GGGGG?áàâäaaaãå?aæ???ccccçd?d?ðéèeêëeeee????ggggg?H?HIÍÌIÎÏIIIII??JK?LLLL?'NNN¨NÑN?ÓÒÔÖOOOÕO?Ø?OŒh?hiíìiîïiiiii??jk??llll??nn¨nñn?óòôöoooõo?ø?oœRRRSSŠS???TT?TÞÚÙÛÜUUUUUUU?U??W??Ý?YŸ???ZZŽ?rrr?ssšs??ßtt?tþúùûüuuuuuuu?u??w??ý?yÿ???zzž?]*[A-Za-z]+[A-Za-z\-_’ '‘.ÆÐ??????Œ?Þ??æð??????œ??ßþ??A?ÇÐ?EHI?LØOS?T?TUUY??a?çd?ehi?løos?t?tuuy??ÁÀÂÄAAAÃÅ?AÆ???CCCCÇD?Ð?ÐÉÈEÊËEEEE????GGGGG?áàâäaaaãå?aæ???ccccçd?d?ðéèeêëeeee????ggggg?H?HIÍÌIÎÏIIIII??JK?LLLL?'NNN¨NÑN?ÓÒÔÖOOOÕO?Ø?OŒh?hiíìiîïiiiii??jk??llll??nn¨nñn?óòôöoooõo?ø?oœRRRSSŠS???TT?TÞÚÙÛÜUUUUUUU?U??W??Ý?YŸ???ZZŽ?rrr?ssšs??ßtt?tþúùûüuuuuuuu?u??w??ý?yÿ???zzž?]*$/,
            surname: /^[A-Za-z\-_’ '‘ÆÐ??????Œ?Þ??æð??????œ??ßþ??A?ÇÐ?EHI?LØOS?T?TUUY??a?çd?ehi?løos?t?tuuy??ÁÀÂÄAAAÃÅ?AÆ???CCCCÇD?Ð?ÐÉÈEÊËEEEE????GGGGG?áàâäaaaãå?aæ???ccccçd?d?ðéèeêëeeee????ggggg?H?HIÍÌIÎÏIIIII??JK?LLLL?'NNN¨NÑN?ÓÒÔÖOOOÕO?Ø?OŒh?hiíìiîïiiiii??jk??llll??nn¨nñn?óòôöoooõo?ø?oœRRRSSŠS???TT?TÞÚÙÛÜUUUUUUU?U??W??Ý?YŸ???ZZŽ?rrr?ssšs??ßtt?tþúùûüuuuuuuu?u??w??ý?yÿ???zzž?]+[A-Za-z\-_’ '‘ÆÐ??????Œ?Þ??æð??????œ??ßþ??A?ÇÐ?EHI?LØOS?T?TUUY??a?çd?ehi?løos?t?tuuy??ÁÀÂÄAAAÃÅ?AÆ???CCCCÇD?Ð?ÐÉÈEÊËEEEE????GGGGG?áàâäaaaãå?aæ???ccccçd?d?ðéèeêëeeee????ggggg?H?HIÍÌIÎÏIIIII??JK?LLLL?'NNN¨NÑN?ÓÒÔÖOOOÕO?Ø?OŒh?hiíìiîïiiiii??jk??llll??nn¨nñn?óòôöoooõo?ø?oœRRRSSŠS???TT?TÞÚÙÛÜUUUUUUU?U??W??Ý?YŸ???ZZŽ?rrr?ssšs??ßtt?tþúùûüuuuuuuu?u??w??ý?yÿ???zzž?]*[A-Za-z]+[A-Za-z\-_’ '‘ÆÐ??????Œ?Þ??æð??????œ??ßþ??A?ÇÐ?EHI?LØOS?T?TUUY??a?çd?ehi?løos?t?tuuy??ÁÀÂÄAAAÃÅ?AÆ???CCCCÇD?Ð?ÐÉÈEÊËEEEE????GGGGG?áàâäaaaãå?aæ???ccccçd?d?ðéèeêëeeee????ggggg?H?HIÍÌIÎÏIIIII??JK?LLLL?'NNN¨NÑN?ÓÒÔÖOOOÕO?Ø?OŒh?hiíìiîïiiiii??jk??llll??nn¨nñn?óòôöoooõo?ø?oœRRRSSŠS???TT?TÞÚÙÛÜUUUUUUU?U??W??Ý?YŸ???ZZŽ?rrr?ssšs??ßtt?tþúùûüuuuuuuu?u??w??ý?yÿ???zzž?]*$/
        }
   
		var container = $('div.container');
		// validate the form when it is submitted
		$.validator.addMethod("CheckDates", function(i,element) {
			return IsValidDate(element);
		}, "Please enter a correct date");
        $.validator.addMethod("regexp", function(value, element, param) { 
            return this.optional(element) || !param.test(value); 
        });         
        $.validator.addMethod('lettersonly', function(value, element) {
            return this.optional(element) || patterns.letters.test(value);
        });
        $.validator.addMethod('ukpostcode', function(value) {
            return patterns.ukpostcode.test(value);
        });
        $.validator.addMethod('addressline', function(value, element) {
            return (value == '') || patterns.addressline.test(value);
        });
        $.validator.addMethod('addressline1', function(value, element) {
            return (value == '') || patterns.addressline1.test(value);
        });
        $.validator.addMethod('firstname', function(value, element) {
            return (value == '') || patterns.firstname.test(value);
        });
        $.validator.addMethod('surname', function(value, element) {
            return (value == '') || patterns.surname.test(value);
        });
        $.validator.addMethod('telephone', function(value, element) {
            if ($(element).val() != '') {
                return patterns.telephone.test(value);
            } else {
                return true;
            }
        });
        $.validator.addMethod("valueNotEquals", function(value, element, arg){
            return arg != value;
        }, "Please select a value");        
        $.validator.addMethod('postcode', function(value) {
            return patterns.postcode.test(value);
        });
		$.validator.addClassRules({
			small: { required:true, CheckDates:true}
		});
		var validator = $("#ajaxafgflickr").bind("invalid-form.validate", function() {
            //$('#ajaxafgflickr button[type=submit]').removeAttr('disabled');			
		}).validate({
			debug: false,
			errorElement: "em",
			errorContainer: $(".form-item em"),			
			errorPlacement: function(error, element) {
				element.parents(".form-item").find("em").html(error);
			},
			focusCleanup: false,		
			rules: {
				afgfirstname: {
                    required: true,
                    minlength: 2,
                    firstname: true
                },
				afglastname: {
                    required: true,
                    minlength: 2,
                    surname: true
                },                
                afgimagetitle:{
                	maxlength: 80
                },
                afgimagestory:{
                	maxlength: 200
                },
				afgyouremail: {
					required:true,
					email: true,
				},
                afgphonenumber:{
                    telephone: true
                },
				afgconsent: "required",
				afgtsandcs: "required",				
			},
			messages: {
				afgfirstname: {
                  required: "Please enter your first name.",
                  firstname: "Please enter a valid first name.",
                  minlength: "Please enter a valid first name."
                },
				afglastname: {
                  required: "Please enter your last name.",
                  firstname: "Please enter a valid last name.",
                  minlength: "Please enter a valid last name."
                },
				afgyouremail: {
                  required: "Please enter your email address.",
                  email: "Please enter a valid email address."                
                },
				afgphonenumber: {
                  telephone: "Please enter a valid phone number."                
                },                
				afgtsandcs: "Please agree to allow this image to be used on the website",                
				afgtsandcs: "Please agree to understand and accept the terms and conditions"
			},
            submitHandler: function(f){
                //$('#ajaxafgflickr button[type=submit]').attr('disabled', 'disabled');
                $(f).submit();
            }
		});
		$("#ajaxafgflickr").removeAttr("novalidate");
	}
    $('#ajaxbhfflickr .error').hide();  

    var uploadingtext='Uploading in progress...'; // text for uploading
    /// load about
    
    $('.cropimage').hide();
    $("#yourfile").on('change',function(){
        $("#yourfile").css("color",'#000000');
		if($("#yourfile").val()!=''){
			var _validFileExtensions = [".jpg", ".jpeg", ".gif"];
			var blnValid = false;
            for (var j = 0; j < _validFileExtensions.length; j++) {
                var sCurExtension = _validFileExtensions[j];
                if ($("#yourfile").val().substr($("#yourfile").val().length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                    blnValid = true;
                    break;
                }
            }
            if (!blnValid){
                $('.noimg-loaded').show();
                $('.noimg-loaded p.error').html('Please upload only gif or jpeg files');            
            }
            else{			
	            $('#uploadFile').val($("#yourfile").val());
				$('#notice').text(uploadingtext).fadeIn();
				$("#upload_big").submit();
			}
		}
		else {
			$('.notice').hide();
		}
	}); 
	$( ".filter-sport").on('change', function() {
        var durl = window.location.href;
        var ordurl = durl;
        durl = updateURLParameter(durl, 'sport', $('.filter-sport option:selected').val());   
        if (ordurl != durl){
            window.location.href = durl;
        }
    }); 
    $( ".filter-category" ).on('change', function() {
        var durl = window.location.href;
        var ordurl = durl;
        durl = updateURLParameter(durl, 'category', $('.filter-category option:selected').val());   
        if (ordurl != durl){
            window.location.href = durl;
        }
    });    
    if ($('.img-loaded.show').length > 0){
        // load to preview image
        img_id = 'big';
        
        $('.img-loaded').show();
     
        var image = $('.cropimage'),
            cropwidth = image.attr('cropwidth'),
            cropheight = image.attr('cropheight'),
            results = $('#ajaxafgflickr'),
            x       = $('.x', results),
            y       = $('.y', results),
            w       = $('.w', results),
            h       = $('.h', results);
        image.cropbox( { width: cropwidth, height: cropheight, showControls: 'auto' } )
            .on('cropbox', function( event, results, img ) {
              $('#ajaxafgflickr .x').val( results.cropX );
              $('#ajaxafgflickr .y').val( results.cropY );              
              $('#ajaxafgflickr .w').val( results.cropW );
              $('#ajaxafgflickr .h').val( results.cropH );
            });
    }
    $("form.uploaderForm").submit(function() {            
        // get the sended form
        var fname = $(this).attr('name');
        var img_id='';        
        // check if there is a thumbnail selection
        if(fname == 'upload_thumb'){
            if($('#x').val() =="" || $('#y').val() =="" || $('#w').val() <="0" || $('#h').val() <="0"){
                $('#notice2').text(alertText).fadeIn();
                return false;
            }
        }
        $('#upload_target').unbind().load( function(){
            // get content from hidden iframe
            var img = $('#upload_target').contents().find('body').html();
           	//alert(img);
            // proof content if there is an error
            if(img.indexOf("uperror") != -1 || img.indexOf("uploads/big") == -1){
                $('.noimg-loaded').show();
                $('.noimg-loaded p.error').html('something has gone wrong');
                $('#upload_thumb').hide();// hide the generate button
                $('.notice').hide();
                $('#notice').html(img).fadeIn();//show error message
            }
            else {
                // save the image source
                $('.img_src').val(img);
                // load to preview image
                img_id = 'big';
                
                $('.img-loaded').show();
				//alert(scriptPath()+img);
                // set selection image
                $('.cropimage').attr("src",scriptPath()+img).fadeIn();

                $('#ajaxafgflickr .x').val(0);
                $('#ajaxafgflickr .y').val(0);
                $('#ajaxafgflickr .w').val(530 * 3);
                $('#ajaxafgflickr .h').val(530 * 3);
                

                var image = $('.cropimage'),
                    cropwidth = image.attr('cropwidth'),
                    cropheight = image.attr('cropheight'),
                    results = $('#ajaxafgflickr'),
                    x       = $('.x', results),
                    y       = $('.y', results),
                    w       = $('.w', results),
                    h       = $('.h', results);
                image.cropbox( { width: cropwidth, height: cropheight, showControls: 'auto' } )
                    .on('cropbox', function( event, results, img ) {
                      $('#ajaxafgflickr .x').val( results.cropX );
                      $('#ajaxafgflickr .y').val( results.cropY );              
                      $('#ajaxafgflickr .w').val( results.cropW );
                      $('#ajaxafgflickr .h').val( results.cropH );
                    });
				//alert('should be shown');
                $('.notice').fadeOut();
            } 
        });
    });
    /*
    function uploadFiles(event){
        event.stopPropagation();
        event.preventDefault();
        $('#ajaxbhfflickr .error').hide();  
        $('#ajaxbhfflickr .error').empty();  
        var error = 0;
        var name = $("input#yourname").val();           
        if (name == "") {  
            $("#name_error").html( 'Please enter your name' ).show();  
            $("input#yourname").focus(); 
            error = 1;
        }  
        var email = $("input#youremail").val();  
        if (email == "") {  
            $("#email_error").html( 'Please enter your email address' ).show();  
            $("input#youremail").focus();  
            error = 1;
        }
        var file = $("input#yourfile").val();  
        if (file == "") {  
            $("#file_error").html( 'Please choose a picture to upload' ).show();  
            $("input#yourfile").focus();  
            error = 1;
        }           
        if (!$("#tsandcs").is(':checked')) {  
            $("#tsandcs_error").html( 'You must accept the terms and conditions' ).show();  
            $("input#tsandcs").focus();  
            error = 1;
        }    
        if (error != 0) return false;  
        $.ajax({
            url: $('#ajaxbhfflickr').attr('action'),
            type: 'POST',
            data: {
                action: 'ajaxbhf_flickr_send',
                yourfile: $("input#yourfile").val(),
                yourname: $("input#yourname").val(),
                youremail: $("input#youremail").val(),
                tsandcs: $("#some_id").attr("checked") ? 1 : 0,
            },
            cache: false,
            success:function(data, textStatus, XMLHttpRequest){
                var id = '#ajaxbhfflickr';
                //$(id).replaceWith(data);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert(errorThrown);
            }
        });        
    }
    $('#ajaxbhfflickr').on('submit', uploadFiles);
    */
});
})(jQuery);
