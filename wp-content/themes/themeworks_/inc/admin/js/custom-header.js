jQuery(document).ready(function($){
    var color = $('input#textcolorpicker[type=\'text\']').val();
    $('.themeworks-text-fx-preview').css('color', color);
    $('.color-picker').wpColorPicker({
        change: function(event, ui) {
            $('.themeworks-text-fx-preview').css( 'color', ui.color.toString());
        }
    });

    $('.themeworks-set-image').click( function() {

        // WordPress 3.5 Media Pop Up
        _this = $( this );

        var file_frame;

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use Selected Image',
            },
            multiple: false
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            var selection = file_frame.state().get( 'selection' );
            selection.map( function( attachment ) {
                attachment = attachment.toJSON();
                _this.parent().find( '.themeworks-header-image' ).val( attachment.id );
                _this.parent().find( '.themeworks-header-image-url' ).attr( 'src', attachment.url );
                _this.parent().find( '.themeworks-remove-image' ).show();
                _this.parent().find( '.themeworks-add-image' ).show();
                _this.parent().find( '.themeworks-set-image' ).hide();
            });
        });

        // open the modal
        file_frame.open();
        return false;
    });

    // remove image
    $('.themeworks-remove-image').click(function(e) {
        e.preventDefault();
        $( this ).parent().find( '.themeworks-header-image' ).val('');
        $( this ).parent().find( '.themeworks-header-image-url' ).attr( 'src', '' );
        $( this ).parent().find( '.themeworks-remove-image' ).hide();
        $( this ).parent().find( '.themeworks-add-image' ).hide();
        $( this ).parent().find( '.themeworks-set-image' ).show();
    });

    if( $( '.themeworks-header-image-url' ).attr( 'src' )){
        $( '.themeworks-remove-image, .themeworks-add-image' ).show();
        $( '.themeworks-set-image' ).hide();
    } else {
        $( '.themeworks-remove-image, .themeworks-add-image' ).hide();
        $( '.themeworks-set-image' ).show();
    }

});