jQuery(document).ready(function( $ ){

    // Set default page view
    $('#comment_status').prop('checked', '');
    $('#ping_status').prop('checked', '');

    // Show page builder metabox
    var page_template = $('#page_template option:selected').val();
    if ( page_template == 'page-builder.php' ) {
        $('#tw_page_sections').show();
        $('#postdivrich').hide();
    }

    $('#page_template').change(function(){

        var page_template = $('#page_template option:selected').val();

        if ( page_template == 'page-builder.php' ) {
            $('#tw_page_sections').show();
            $('#postdivrich').hide();
        } else {
            $('#tw_page_sections').hide();
            $('#postdivrich').show();
        }
    });

    // Update section order
    function tw_update_sections_order() {
        var sections = [];
        $('.themeworks-page-builder-sortable .active-section').each( function() {
            sections.push( $(this).attr('id') );
        });
        $('.themeworks-page-sections').val(sections);
    }

    // Sortable sections
    $('.themeworks-page-builder-sortable').sortable({
        opacity: 0.6,
        revert: 200,
        cursor: 'move',
        handle: '.drag-handle',

        update : function () {
            tw_update_sections_order();
        }
    });

    // Add section
    $('.themeworks-builder-add-section').on( "click", function() {
        var section_id = $(this).attr('id');
        var section_name = $(this).text();
        $('.themeworks-page-builder-sortable .help-text').remove();

        var sections = [];
        $('.themeworks-page-builder-sortable .active-section').each( function() {
            sections.push( $(this).attr('id') );
        });

        // Check if section was already added
        if ( $.inArray( section_id, sections ) !== -1 ) {
            alert('This section design has already added. Please delete the existing section design from the right before adding it again.');

        } else {
            $('.themeworks-page-builder-sortable').append('<div class="active-section" id="'+section_id+'"><span class="tw-delete-section dashicons dashicons-no"></span><div class="drag-handle"><img src="'+themeworks.themedir+'/inc/admin/images/sections/'+section_id+'.png" /></div></div>');
            $('.themeworks-page-builder-sortable').sortable( "refresh" );
            tw_update_sections_order();
        }
    });

    // Delete section
    $('.themeworks-page-builder-sortable').on( "click", '.tw-delete-section', function() {
        $(this).parent().remove();
        $('.themeworks-page-builder-sortable').sortable( "refresh" );
        tw_update_sections_order();
    });

});