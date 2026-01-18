function create_gadget(column, row) {
    for (i = 1; i <= row; i++)
        jQuery('#column' + column).append('<div class="portlet" data-column="' + column + '" data-row="' + i + '" id="' + column + '-' + i + '"></div><br/>');

}
function widget(column, row, name, title, settings,board) {
    url = getBaseURL() + board;
    jQuery.ajax({
        url: url,
        data: {
            'settings': settings,
            'title': title,
            'column': column,
            'row': row,
            'name': name,
            'widget': name
        },
        type: 'post',
        dataType: 'json',
        beforeSend: function () {
            jQuery('#' + name).html('<div id="loading" align="center"><img src="assets/images/loader_1.gif" width="23" height="16" /></div>');
        },
        success: function (response) {
            jQuery('#' + column + '-' + row).html(response.html);
            configuration();
            jsSettings(column, row, name, settings);
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function configuration() {
    jQuery(document).ready(function () {
        jQuery(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
                .find(".portlet-header")
                .addClass("ui-widget-header ui-corner-all")
                .end()
                .find(".portlet-content");
    });
}
function jsSettings(column, row, name, settings) {
    jQuery.getScript(getBaseURL() + 'assets/scripts/' + name + '.js')
            .done(function (script, textStatus) {
                window[name](column, row, settings);
            });
}
function save_dashboard() {
    old_param_column1 = new Array();
    new_param_column1 = new Array();
    if (jQuery('.portlet', '#column1').length > 0 && jQuery('.portlet', '#column2').length > 0) {
        jQuery("#saveButton").attr('disabled', 'disabled');
        jQuery('.portlet', '#column1').each(function (index, element) {
            collapse = 0;
            if (jQuery('.portlet-content', element).is(":hidden"))
                collapse = 1;
            old_param_column1[index] = {
                'column': jQuery(element).attr('data-column'),
                'row': jQuery(element).attr('data-row')
            };
            new_param_column1[index] = {
                'column': 1,
                'row': index + 1,
                'collapse': collapse
            };
        });
        old_param_column2 = new Array();
        new_param_column2 = new Array();
        jQuery('.portlet', '#column2').each(function (index, element) {
            collapse = 0;
            if (jQuery('.portlet-content', element).is(":hidden"))
                collapse = 1;
            old_param_column2[index] = {
                'column': jQuery(element).attr('data-column'),
                'row': jQuery(element).attr('data-row')
            };
            new_param_column2[index] = {
                'column': 2,
                'row': index + 1,
                'collapse': collapse
            };
        });
        jQuery.ajax({
            url: 'dashboard/index',
            type: 'post',
            data: {
                'widget': 'save_dashboard',
                'old_param_1': old_param_column1,
                'new_param_1': new_param_column1,
                'old_param_2': old_param_column2,
                'new_param_2': new_param_column2
            },
            dataType: 'json',
            success: function (response) {
                if (response.success)
                    window.location = getBaseURL() + 'dashboard';
                else {
                    pinesMessage({ty: 'warning', m: _lang.unable_to_save_your_changes});
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        pinesMessage({ty: 'warning', m: _lang.unable_to_save_your_changes});
    }
}
