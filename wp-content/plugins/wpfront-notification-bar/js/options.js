jQuery(document).ready(function () {
    var $ = jQuery;

    $('#wpfront-notification-bar-options .pages-selection input[type="checkbox"]').change(function () {
        var $this = $(this);
        var $input = $this.parent().parent().parent().prev();
        var $text = $input.val();

        if ($this.prop('checked')) {
            $text += ',' + $this.val();
        } else {
            $text = (',' + $text + ',').replace(',' + $this.val() + ',', ',');
        }

        $text = $text.replace(/(^[,\s]+)|([,\s]+$)/g, '');
        $input.val($text);
    });

    $('#wpfront-notification-bar-options .roles-selection input[type="checkbox"]').change(function () {
        var values = [];
        var div = $(this).parent().parent().parent();
        div.find('input:checked').each(function (i, e) {
            values.push($(e).val());
        });
        div.children(":first").val(JSON.stringify(values));
    });

    $('#wpfront-notification-bar-options input.date').datepicker({
        'dateFormat': 'yy-mm-dd'
    });

    $('#wpfront-notification-bar-options input.time').timepicker({
        'timeFormat': 'h:i a'
    });
    
    $('#chk_button_action_url_noopener').change(function() {
        $('#txt_button_action_url_noopener').val($(this).prop('checked') ? 1 : 0);
    });
    
    function setColorPicker(div) {
        if(div.ColorPicker) {
            div.ColorPicker({
                color: div.attr('color'),
                onShow: function (colpkr) {
                    $(colpkr).fadeIn(500);
                    return false;
                }, onHide: function (colpkr) {
                    $(colpkr).fadeOut(500);
                    return false;
                },
                onChange: function (hsb, hex, rgb) {
                    div.css('backgroundColor', '#' + hex);
                    div.next().val('#' + hex);
                }
            }).css('backgroundColor', div.attr('color'));
        }
    }

    $('#wpfront-notification-bar-options').find(".color-selector").each(function (i, e) {
        setColorPicker($(e));
    });
    
});