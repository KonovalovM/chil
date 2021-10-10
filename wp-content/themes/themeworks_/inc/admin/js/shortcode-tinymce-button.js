(function() {

    tinymce.PluginManager.add('themeworks_shortcodes', function( editor ) {

        var shortcodeValues = [];
        jQuery.each(shortcodes_button, function(i) {
            shortcodeValues.push({text: shortcodes_button[i], value: shortcodes_button[i]});
        });

        editor.addButton('themeworks_shortcodes', {
            type: 'listbox',
            text: 'Shortcodes',
            values: shortcodeValues,
            onselect: function(e) {
                var value = this.value();
                if ( 'tw_icon' == value || 'tw_divider' == value || 'tw_contact_form' == value ) {
                    var new_content = '[' + value + ']';
                } else {
                    var new_content = '[' + value + '] Add text here [/' + value + ']';
                }
                editor.insertContent( new_content );
            },
            
        });
    });
})();