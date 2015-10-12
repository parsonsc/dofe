jQuery(document).ready(function(){
    jQuery("a[rel^='example4']").afgcolorbox({
        maxWidth: "70%",
        maxHeight: "70%",
        current: "{current} of {total}",
        title: '',
        onComplete:function(){
            var imsrc = jQuery(document).find('.cboxPhoto').attr('src');
            var img = imsrc.substring( imsrc.indexOf('src=') );
            var hhref = '';
            if (window.location.href.indexOf("?") != -1){
                hhref = window.location.href +'&' + 'afg_pic_id='+ encodeURIComponent(img.split("&")[0]);
            }
            else{
                hhref = window.location.href +'?' + 'afg_pic_id='+ encodeURIComponent(img.split("&")[0]);
            }
            jQuery('#cboxTitle').empty().append('<div class="title">'+ jQuery(this).find('img').attr('alt') +'</div><div id="extra-info"><ul class="gallery-social"><li class="twitter"><a href="http://www.twitter.com/share?url='+ hhref +'&text=%23RAMPUP"><span>Twitter</span></a></li><li class="facebook"><a href="http://www.facebook.com/sharer/sharer.php?u='+ hhref +'"><span>Facebook</span></a></li></ul></div>'); 
        },
    });
});
