




$(document).ready(function() {

	/// load about
    $('#notice, .content').hide();
    
	$("#show_details").click(function () {
		$("#about_details").slideToggle("slow");
	});
	
	$("#file").on('change',function(){
		if($("#file").val()!=''){
			$('#notice').text(uploadingtext).fadeIn();
			$("#upload_big").submit();
		}
		else {
			$('.notice').hide();
		}
	});  
    $("form.uploaderForm").submit(function() {            
        // get the sended form
        var fname = $(this).attr('name');
        var img_id='';        
        // check if there is a thumbnail selection
        if(fname == 'upload_thumb'){
            if($('#x1').val() =="" || $('#y1').val() =="" || $('#width').val() <="0" || $('#height').val() <="0"){
                $('#notice2').text(alertText).fadeIn();
                return false;
            }
        }
        $('#upload_target').unbind().load( function(){
            // get content from hidden iframe
            var img = $('#upload_target').contents().find('body').html();
            // proof content if there is an error
            if(img.indexOf("uperror") != -1){
                $('#upload_thumb').hide();// hide the generate button
                $('.notice').hide();
                $('#notice').html(img).fadeIn();//show error message
            }
            else {
                // save the image source
                $('.img_src').attr('value',img);
                $('#big_uploader').fadeOut();
                $('.content').fadeIn();
                // load to preview image
                img_id = 'big';

                // set selection image
                $('.cropimage').attr("src",img);

                $('#upload_thumb').show();

                $('.x1').val(x1*3);
                $('.y1').val(y1*3);
                $('.width').val(530*3);
                $('.height').val(530*3);
                $('.cropimage').each( function () {
                    var image = $(this),
                        cropwidth = image.attr('cropwidth'),
                        cropheight = image.attr('cropheight'),
                        results = $('#upload_thumb'),
                        x       = $('.x1', results),
                        y       = $('.y1', results),
                        w       = $('.width', results),
                        h       = $('.height', results);
                    image.cropbox( {width: cropwidth, height: cropheight, showControls: 'auto' } )
                        .on('cropbox', function( event, results, img ) {
                          $('#upload_thumb .x1').val( results.cropX );
                          $('#upload_thumb .y1').val( results.cropY );              
                          $('#upload_thumb .width').val( results.cropW );
                          $('#upload_thumb .height').val( results.cropH );
                        });
                });
                $('.notice').fadeOut();
            } 
        });
    });    
});    