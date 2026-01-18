jQuery(document).ready(function () {
     jQuery("#list-0").sortable({
          connectWith: "#list-1, #list-2"
     }).disableSelection();

     jQuery("#list-1").sortable({
          connectWith: "#list-0, #list-2"
     }).disableSelection();

     jQuery("#list-2").sortable({
          connectWith: "#list-0, #list-1"
     }).disableSelection();

     jQuery('#save-dashboard-order-btn').click(function () {
          hiddenWidgets = [];
          leftWidgets = [];
          rightWidgets = [];
          jQuery("#list-0 [id^=widget-]").each(function (index, obj) {
               hiddenWidgets.push(jQuery(this).attr('id').replace("widget-", ""));
          });
          jQuery("#list-1 [id^=widget-]").each(function (index, obj) {
               leftWidgets.push(jQuery(this).attr('id').replace("widget-", ""));
          });
          jQuery("#list-2 [id^=widget-]").each(function (index, obj) {
               rightWidgets.push(jQuery(this).attr('id').replace("widget-", ""));
          });
          jQuery.ajax({
               url: getBaseURL() + 'dashboard/litigation_dashboard_config/' + jQuery('#dashboard-number', '#litigation-dashboard-config').val(),
               data: {
                    'hidden_widgets': hiddenWidgets,
                    'left_widgets': leftWidgets,
                    'right_widgets': rightWidgets
               },
               dataType: 'JSON',
               type: 'POST',
               success: function (response) {
                    if (response.result) {
                         pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                    } else {
                         pinesMessage({ ty: 'error', m: _lang.feedback_messages.updatesFailed });
                    }
               },
               error: defaultAjaxJSONErrorsHandler
          });
     });
});

