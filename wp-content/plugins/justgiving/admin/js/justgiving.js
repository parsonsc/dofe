var data = {
    "jgADDclose": parent.tinyMCE.util.I18n.translate('Insert and Close'),
    "jgADD": parent.tinyMCE.util.I18n.translate('Insert'),
    "pages": []
};
function justgivinginit() {
    $(function() {
        $('#justgiving-type').change(function() {
            //console.log('#jg'+$('#justgiving-type option:selected').val());
            $('#jg'+$('#justgiving-type option:selected').val()).show();   
            $('#jgtype').hide();            
            $('.choice.submit').show();            
        });
        
    });
}
;
function I_Close() {
    parent.tinyMCE.activeEditor.windowManager.close();
}
function I_Insert() {
    //$('#jgtype').show();
    var shortcode = '[jg-' + $('#justgiving-type option:selected').val();
    $('#jg'+$('#justgiving-type option:selected').val()+' :input').each(function(){
        var valv = $(this).val();
        var showm = false;
        //console.log($(this).attr('type') + ' ' + Number.isInteger(parseInt(valv)) );
        if ($(this).attr('type') == 'checkbox'){
            valv = 'false';
            if ($(this).checked) valv = 'true';
            showm = true;
        }        
        else if ($(this).attr('type') == 'text'){
            if (valv.length > 0) showm = true;
            valv = "'"+valv+"'";
        }
        else if ($(this).attr('type') == 'number' && Number.isInteger(parseInt(valv)) && valv > 0){      
            showm = true;
        }
        else if ($(this).attr('type') == 'number' && !Number.isInteger(parseInt(valv))){        
            showm = false;
        }        
        else if ($(this).is("select")){
            if (valv.length > 0) showm = true;
            valv = "'"+valv+"'";
        }        
        else showm = true;
        if (showm) shortcode += ' ' + $(this).attr('name')+'='+valv; // This is the jquery object of the input, do what you will
    });
    shortcode += ']';
    parent.tinyMCE.activeEditor.setContent(parent.tinyMCE.activeEditor.getContent() + shortcode);
}

$.get('forma.html', function(template) {
    $.post('../../../../../wp-admin/admin-ajax.php', 'action=jg_listpages',function(pages){
        data.pages = $.parseJSON( pages );
/*        
        data.pages2 = $.parseJSON( pages );
        data.pages3 = $.parseJSON( pages );
        data.pages4 = $.parseJSON( pages );
        data.pages5 = $.parseJSON( pages );
        data.pages6 = $.parseJSON( pages );
        data.pages7 = $.parseJSON( pages );
        data.pages8 = $.parseJSON( pages );
        data.pages9 = $.parseJSON( pages );
        data.pages10 = $.parseJSON( pages );
        data.pages11 = $.parseJSON( pages );
        data.pages12 = $.parseJSON( pages );
        data.pages13 = $.parseJSON( pages );
        data.pages14 = $.parseJSON( pages );
        data.pages15 = $.parseJSON( pages );
        data.pages16 = $.parseJSON( pages );
        data.pages17 = $.parseJSON( pages );
        data.pages18 = $.parseJSON( pages );
        data.pages19 = $.parseJSON( pages );
        data.pages20 = $.parseJSON( pages );
        data.pages21 = $.parseJSON( pages );
        data.pages22 = $.parseJSON( pages );
        data.pages23 = $.parseJSON( pages );
*/        
        filled = Mustache.render(template, data);
        $('#template-container').append(filled);
        $('#template-container .choice').hide();
        justgivinginit();
    });
});