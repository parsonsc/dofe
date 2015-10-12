tinymce.PluginManager.add('registerforevent', function(editor) {
    function insertjg(){
        var title = "Insert RFE shortcode";
        win = editor.windowManager.open({
            title: title,
            file: '../wp-content/plugins/registerforevent/admin/js/rfe.html',
            width: 800,
            height: 500,
            inline: 1,
            buttons: [{
                text: 'cancel',
                onclick: function() {
                        this.parent().parent().close();
                }
            }]
        });    
    }   
    editor.addButton('registerforevent', {
        title : 'RFE Shortcodes',
        icon: true,
        image : '../wp-content/plugins/registerforevent/admin/img/jg.gif',
        tooltip: 'Insert RFE Shortcode',
        onclick: insertjg
    });    
});