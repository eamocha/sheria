jQuery(document).ready(function () {

    jQuery('input', '.list-left').attr('disabled', 'disabled');
    jQuery('.lookup', '.list-left').removeAttr('disabled');
    jQuery("ul", '#transition-screen-fields').sortable({
        tolerance: 'pointer',
        revert: 'invalid',
        placeholder: 'span2 well placeholder tile',
        forceHelperSize: true
    });
    showHideHeaderLabel();
    jQuery('#transition-screen-fields').on('click', '.list-group .field-selector', function () {
        var $checkBox = jQuery(this);
        if (!$checkBox.hasClass('selected')) {
            $checkBox.addClass('selected');
            $checkBox.parent().addClass('selected');
            $checkBox.children('i').removeClass('fa-square').addClass('fa-square-check');
        } else {
            $checkBox.removeClass('selected');
            $checkBox.parent().removeClass('selected');
            $checkBox.children('i').removeClass('fa-square-check').addClass('fa-square');
        }
    });
    jQuery('.list-arrows button').click(function () {
        var $button = jQuery(this), actives = '';
        if ($button.hasClass('move-left')) {
            actives = jQuery('.list-right ul li.selected');
            actives.each(selectAccompanyingField);
            actives = jQuery('.list-right ul li.selected'); // Select another time to move the accompanying field
            actives.show();
            jQuery('.btn-switch', actives).addClass('d-none');
            actives.clone().appendTo('.list-left ul');
            jQuery('input', '.list-left').attr('disabled', 'disabled');
            jQuery('.lookup', '.list-left').removeAttr('disabled');
            actives.remove();
            showHideHeaderLabel();
        } else if ($button.hasClass('move-right')) {
            actives = jQuery('.list-left ul li.selected');
            actives.each(selectAccompanyingField);
            actives = jQuery('.list-left ul li.selected');
            actives.show();
            jQuery('.btn-switch', actives).removeClass('d-none');
            actives.clone().appendTo('.list-right ul');
            jQuery('input', '.list-right').removeAttr('disabled');
            actives.remove();
            showHideHeaderLabel();
        }
    });
    jQuery('.dual-list .selector').click(function () {
        var $checkBox = jQuery(this);
        if (!$checkBox.hasClass('selected')) {
            $checkBox.addClass('selected').closest('.well').find('ul li:not(.selected)').addClass('selected');
            $checkBox.closest('.well').find('ul li a:not(.selected)').addClass('selected').children('i').removeClass('fa-square').addClass('fa-square-check');
            $checkBox.children('i').removeClass('fa-square').addClass('fa-square-check');
        } else {
            $checkBox.removeClass('selected').closest('.well').find('ul li.selected').removeClass('selected');
            $checkBox.closest('.well').find('ul li a.selected').removeClass('selected').children('i').removeClass('fa-square-check').addClass('fa-square');
            $checkBox.children('i').removeClass('fa-square-check').addClass('fa-square');
        }
    });
    jQuery('.search_dual_list','#transition-screen-fields').keyup(function (e) {
        var code = e.keyCode || e.which;
        if (code == '9')
            return;
        if (code == '27')
            jQuery(this).val(null);
        var $rows = jQuery(this).closest('.dual-list').find('.list-group li');
        var val = jQuery.trim(jQuery(this).val()).replace(/ +/g, ' ').toLowerCase();
        $rows.show().filter(function () {
            var text = jQuery(this).text().replace(/\s+/g, ' ').toLowerCase();
            return !~text.indexOf(val);
        }).hide();
    });

    const opponentLi = jQuery(jQuery('input[name="screen_fields[opponent]"]').parent());
    const opponentPositionLi = jQuery(jQuery('input[name="screen_fields[opponent_position]"]').parent());
    const clientLi = jQuery(jQuery('input[name="screen_fields[client]"]').parent());
    const clientPositionLi = jQuery(jQuery('input[name="screen_fields[legal_case_client_position_id]"]').parent());
    const fieldsToHaveTooltip = {'opponent': opponentLi, 'opponentPosition': opponentPositionLi, 'client': clientLi, 'clientPosition': clientPositionLi};
    addToolTipForFields(fieldsToHaveTooltip);
});
function setOptionValue(element) {
    var checked = jQuery(element).children().is(':checked');
    if (checked) {
        jQuery(element).addClass('active');
        jQuery(element).attr('title', _lang.mark_optional);
    }
    else {
        jQuery(element).removeClass('active')
        jQuery(element).attr('title', _lang.mark_required);
    }
    jQuery('input[type="hidden"]', jQuery(element).parent()).val(checked ? 1 : '');
}
function showHideHeaderLabel() {
    if (jQuery('.ui-sortable li', '.list-right').length > 0) {
        jQuery('.header-div', '#transition-screen-fields').removeClass('d-none');
    } else {
        jQuery('.header-div', '#transition-screen-fields').addClass('d-none');
    }
}

function selectAccompanyingField() {
    const SelectedFieldNameFirstChild = jQuery(jQuery(this).children()[0]);
    const selectedFieldName = SelectedFieldNameFirstChild.attr('name');
    let accompanyingFieldName = '';
    switch (selectedFieldName) {
        case 'screen_fields[opponent]':
            accompanyingFieldName = 'screen_fields[opponent_position]';
            break;
        case 'screen_fields[opponent_position]':
            accompanyingFieldName = 'screen_fields[opponent]';
            break;
        case 'screen_fields[client]':
            accompanyingFieldName = 'screen_fields[legal_case_client_position_id]';
            break;
        case 'screen_fields[legal_case_client_position_id]':
            accompanyingFieldName = 'screen_fields[client]';
            break;
        default:
            break;
    }

    const parent = jQuery(jQuery(`input[name='${accompanyingFieldName}']`).parent());
    parent.addClass('selected');
    const checkboxWrapper = jQuery(parent.children()[1]);
    checkboxWrapper.addClass('selected');
    const checkbox =  jQuery(checkboxWrapper.children()[0]);
    checkbox.removeClass('fa-square');
    checkbox.addClass('fa-square-check');
}

function addToolTipForFields(fields) {
    for (let field in fields) {
        fields[field].tooltipster({
            content: _lang[field + 'Tooltip'],
            contentAsHTML: true,
            timer: 300000,
            animation: 'grow',
            delay: 200,
            theme: 'tooltipster-default',
            touchDevices: false,
            trigger: 'hover',
            maxWidth: 350,
            interactive: true,
            repositionOnScroll: true,
            position: 'left'
    
        });
    }
}