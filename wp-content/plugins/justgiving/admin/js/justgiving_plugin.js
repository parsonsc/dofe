tinymce.PluginManager.add('justgiving', function(editor) {
    function insertjg(){
        var title = "Insert JustGiving shortcode";
        win = editor.windowManager.open({
            title: title,
            file: '../wp-content/plugins/justgiving/admin/js/justgiving.html',
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
    editor.addButton('justgiving', {
        title : 'JustGiving Shortcodes',
        icon: true,
        image : '../wp-content/plugins/justgiving/admin/img/jg.gif',
        tooltip: 'Insert JustGiving Shortcode',
        onclick: insertjg
    });    
});