jQuery(document).ready(function($){
 
    // Instantiates the variable that holds the media library frame.
    var client_image_frame;
 
    // Runs when the image button is clicked.
    $('#studyimage_button').click(function(e){
 
        // Prevents the default action from occuring.
        e.preventDefault();
        var button = $(this);
        var id = button.attr('id').replace('_button', '');
        // If the frame already exists, re-open it.
        if ( client_image_frame ) {
            client_image_frame.open();
            return;
        }
 
        // Sets up the media library frame
        client_image_frame = wp.media.frames.file_frame = wp.media({
            title: 'Choose an image',
            button: { text: 'Choose an image' },
            library: { type: 'image' }
        });
 
        // Runs when an image is selected.
        client_image_frame.on('select', function(){
 
            // Grabs the attachment selection and creates a JSON representation of the model.
            var media_attachment = client_image_frame.state().get('selection').first().toJSON();
            $('#'+id).val(media_attachment.url);
        });
 
        // Opens the media library frame.
        client_image_frame.open();
    });
});