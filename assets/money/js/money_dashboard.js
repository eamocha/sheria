
var charts = [];
let currentLanguageIndex;
let currentLanguage;
let otherLanguages;
jQuery(document).ready(function () {
    currentLanguageIndex = languages.findIndex(lang => lang.fullName === _lang.languageSettings.langName);
    currentLanguage = languages[currentLanguageIndex];
    otherLanguages = languages.slice();
    otherLanguages.splice(currentLanguageIndex, 1);
    loadDashboardWidgetsIds();
    jQuery('#money-dashboard-widgets').sortable({
        items : '.money-dashboard-widgets',
        update: function( event, ui ) {
            var widgetsOrder = [];
            jQuery("[id^=chart-container-]").each(function (index, obj){
                widgetsOrder.push({'id': jQuery(this).attr('id').replace("chart-container-", ""), 'order': index + 1});
            });
            setWidgetsNewOrder (widgetsOrder) 
        }
    });
});
var allUpdatedWidgetDatas =[];
function setWidgetsNewOrder (widgetsOrder){
    jQuery.ajax({
        url: getBaseURL() + 'modules/money/money_dashboards/set_widgets_new_order',
        data: {
            'widgets_new_order': widgetsOrder
        },
        dataType: 'JSON',
        type: 'POST',
        success: function (response) {
            if (response.result) {
                pinesMessageV2({ty: 'success', m:  _lang.feedback_messages.updatesSavedSuccessfully});
            } else {
                pinesMessageV2({ty: 'error', m:  _lang.feedback_messages.updatesFailed});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function loadDashboardWidgetsIds() {
    jQuery.ajax({
        async: false,
        dataType: 'JSON',
        url: getBaseURL() + 'modules/money/money_dashboards',
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if(response.status){
                allWidgetsIds = response.ids;
                loadDashboardData(allWidgetsIds)
            }   
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    }); 
}
function loadDashboardData(ids){
    for (var widgetId of ids) {
        var widgetDiv = '<div class="card charts widget-container-'+ widgetId + ' mr-2 ml-2 scroll-y"><div class="card-body p-0">'+ 
        '<div class="d-none widget-loader-div loader-' + widgetId + '" ><span id="widget-loader-' + widgetId + '" class="widget-loader"></span></div>'+
        '</div></div>';
        jQuery('#chart-container-'+ widgetId).append(widgetDiv);
        loadWidgetData(widgetId)
    }
}
function loadWidgetData(widgetId, refresh = false){
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'modules/money/money_dashboards/load_widget_data',
        type: 'GET',
        data: {id: widgetId},
        beforeSend: function () {
            if (!refresh) {
                jQuery('.loader-' + widgetId, '#money-dashboard-widgets').removeClass('d-none');
                jQuery('#widget-loader-' + widgetId, '#money-dashboard-widgets').addClass('widget-loading');
            } else {
                jQuery('.loader-submit', '.widget-container-'+ widgetId).addClass('loading');
            }
        },
        success: function (response) {
            if (response.result) {
                if (response.chart) {
                    if (!refresh) {
                        loadWidgetTitle(response.chart);
                        jQuery('.widget-container-'+response.chart.settings.id).height(jQuery('.widget-container-'+response.chart.settings.id).height() + 30);
                    } else {
                        jQuery('.loader-submit', '.charts').addClass('d-none');
                    }
                    loadWidget(response.chart, refresh)
                } else {
                    jQuery('#widget-loader-' + widgetId, '#money-dashboard-widgets').removeClass('widget-loading');
                    jQuery('.loader-' + widgetId, '#money-dashboard-widgets').addClass('d-none');
                    jQuery('.widget-container-'+ widgetId ).height(jQuery('.money-dashboard-widgets').height() + 30);
                    jQuery('.card-body', '.widget-container-'+ widgetId).append("<p class='text-danger text-center'>" + _lang.feedback_messages.failedToLoadWidget + "</p>")
                    jQuery('.widget-container-'+ widgetId).addClass('d-flex');
                    jQuery('.card-body', '.widget-container-'+ widgetId).addClass('align-items-center d-flex justify-content-center');
                }
            } else {
                jQuery('#widget-loader-' + widgetId, '#money-dashboard-widgets').removeClass('widget-loading');
                jQuery('.loader-' + widgetId, '#money-dashboard-widgets').addClass('d-none');
                jQuery('.widget-container-'+ widgetId ).height(jQuery('.money-dashboard-widgets').height() + 30);
                jQuery('.card-body', '.widget-container-'+ widgetId).append("<p class='text-danger text-center'>" + response.msg + "</p>")
                jQuery('.widget-container-'+ widgetId).addClass('d-flex');
                jQuery('.card-body', '.widget-container-'+ widgetId).addClass('align-items-center d-flex justify-content-center');
            }   
        },
        complete: function () {
            if (!refresh) {
                jQuery('#widget-loader-' + widgetId, '#money-dashboard-widgets').removeClass('widget-loading');
                jQuery('.loader-' + widgetId, '#money-dashboard-widgets').addClass('d-none');
            } else {
                jQuery('.loader-submit', '.widget-container-'+ widgetId).removeClass('loading');            
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });  
}
function loadWidgetTitle(chartData, responsiveClass, isNewWidget) {
    const currentLanguageTitle = chartData.settings.title?.find(language => language.language_id === currentLanguage.id)?.title ?? _lang.noTitle;
    let otherLanguagesInputs = "";
    for (let key in otherLanguages) {
        const otherLanguageTitle = chartData.settings.title?.find(language => language.language_id === otherLanguages[key].id)?.title;
        otherLanguagesInputs += `<input class="form-control form-group mt-10" value="${otherLanguageTitle ? otherLanguageTitle : ''}" placeholder="${otherLanguages[key].display_name}" name="title_${otherLanguages[key].id}" id="money-dashboard-widget-title-${chartData.settings.id}" type="text" />`;
    }
    columnsNb = jQuery('#columns-nb-money-dashboard').val();
    filterType = chartData.settings.more_settings ? chartData.settings.more_settings.filter_type : '';

    filter = filterType ? '<a id="plus-filter-'+ chartData.settings.id +'" onclick="jQuery(\'#money-dashboard-widget-date-' + chartData.settings.id + '\').data(\'lastSelected\').attr(\'selected\', true);" role="button" class="collapsed pull-right mr-1 ml-1" data-toggle="collapse" data-target="#collapse-'+  chartData.settings.id + '" aria-expanded="false" aria-controls="collapse-'+  chartData.settings.id + '">'+
    '<span class="fa-stack fa-lg"><i class="fa fa-filter fa-stack-1x font-18"></i></span></a>' : ''
    
    refreshButton = '<a onclick="loadWidgetData(' + chartData.settings.id + ', true);jQuery(\'#loader-' + chartData.settings.id + '\').removeClass(\'hide\');" href="javascript:;" class="pull-right mr-1 ml-1">' +
    '<span class="fa-stack fa-lg"><i class="fa fa-refresh fa-stack-1x font-18"></i></span></a> ' +
    '<span class="loader-submit hide mt-1 mb-1" id="loader-'+ chartData.settings.id +'"></span>';
    
    deleteButton = filterType ? '<a onclick="removeMoneyDashboardChart('+ chartData.settings.id +',this)" href="javascript:;" class="widget-delete-icon pull-right mr-1 ml-1"><span class="fa-stack fa-lg"><i class="fa fa-trash fa-stack-1x pull-left-arabic font-18"></i></span></a>':
    '<a onclick="removeMoneyDashboardChart('+ chartData.settings.id +',this)" href="javascript:;" class="widget-delete-icon pull-right mr-1 ml-1"><span class="fa-stack fa-lg"><i class="fa fa-trash fa-stack-1x pull-left-arabic font-18"></i></span></a>';

    editTitleButton = `<a role="button" class="mr-1 ml-1 pull-right" data-toggle="collapse" href="#collapse-title-${chartData.settings.id}" aria-expanded="false"><span class="fa-stack fa-lg"><i class="fa fa-pen fa-stack-1x pull-left-arabic"></i></span></a>`;

    title = customizedTitle(chartData.settings, columnsNb, filterType, currentLanguageTitle)
    var widgetTitleDiv = '<div class="row widget-header">'+ 
    title +
    deleteButton +
    editTitleButton +
    filter +
    refreshButton +
    '</div></div>'+ 
    '<div class="card card-default no-margin-bottom">'+ 
    `<div class="collapse p-2" id="collapse-title-${chartData.settings.id}">
    <form id="form-widget-title-${chartData.settings.id}">
    <input class="form-control form-group mt-10 mb-0" value="${currentLanguageTitle}" name="title_${currentLanguage.id}" id="money-dashboard-widget-title-${chartData.settings.id}" type="text" />
    <a class="btn btn-link mt-0 pt-0 mb-2 collapsed btn-collapse-languages text-left" data-toggle="collapse" href="#collapse-languages-${chartData.settings.id}" role="button" aria-expanded="false"><i id="collapseLanguagesIcon" class="fa fa-lg fa-angle-down mr-1 ml-1"></i>${_lang.money.otherLanguages}</a>
    <div class="collapse" id="collapse-languages-${chartData.settings.id}">
    ${otherLanguagesInputs}
    </div>
    <button type="button" class="m-2 btn btn-primary pull-right" onclick="saveWidgetTitle(${chartData.settings.id})">${_lang.save}</button>
    </form>
    </div>` +
    '<div id="collapse-'+  chartData.settings.id + '" class="card-collapse collapse" aria-labelledby="headingOne" data-parent="#filter-buttons">'+ 
    '<div class="card-body" id="filter-'+  chartData.settings.id + '" >'+ '</div>'+ 
    '</div></div>'+ 
    '<div class="row ml-20 collapse" id="filter-'+  chartData.settings.id + '" >'+ '</div>'+ 
    '<div id="chart-cont-'+ chartData.settings.id +'" class="money_dashboard_chart_container money_dashboard_chart"><div id="chart-'+ chartData.settings.id +'"></div></div>'+ 
    '<div id="chart-drilldown-'+ chartData.settings.id +'" class="money_dashboard_chart_container money_dashboard_drilldown"></div>';
    jQuery('.card-body', '#chart-container-'+ chartData.settings.id).append(widgetTitleDiv);
    if (isNewWidget) {
        jQuery('<div class="col-md-' + 12/columnsNb +' '+ responsiveClass + ' money-dashboard-widgets margin-bottom-10 prl-5" id="chart-container-'+ chartData.settings.id +'">'+ 
        '<div class="card charts widget-container-'+ chartData.settings.id + ' mr-2 ml-2"><div class="card-body p-0">' +
        widgetTitleDiv +
        '</div>'+
        '</div></div>').insertBefore('#widget-add');
    }
}
function customizedTitle(settings, columnsNb, filterType, currentLanguageTitle) {
    let titleIcon = null;
    if (settings.type === "table") {
        titleIcon = `<i class="fa fa-table fa-stack-1x"></i>`;
    } else if (settings.type === "barChart") {
        titleIcon = `<i class="fa fa-bar-chart fa-stack-1x"></i>`;
    } else if (settings.type === "pieChart") {
        titleIcon = `<i class="fa fa-pie-chart fa-stack-1x"></i>`;
    } else {
        titleIcon = `<i class="fa fa-bars fa-stack-1x"></i>`;
    }
    if (filterType) {
        return titleHTML = columnsNb == 3 ? '<div class="chart-title p-0 col-md-7 col-xs-6"><span class="fa-stack fa-lg">' + titleIcon + '</span><span class="truncate">'+ currentLanguageTitle +'</span></div><div id="filter-buttons" class="col-md-5 col-xs-6 p-0 pull-right flex-row-reverse">' : '<div class="chart-title p-0 col-md-9 col-sm-9 col-xs-6"><span class="fa-stack fa-lg">' + titleIcon + '</span><span class="truncate">'+ currentLanguageTitle +'</span></div><div id="filter-buttons" class="col-md-3 col-sm-3 col-xs-6 p-0 pull-right flex-row-reverse">'
    } else {
        return titleHTML = columnsNb == 3 ? '<div class="chart-title p-0 col-md-9 col-xs-7"><span class="fa-stack fa-lg">' + titleIcon + '</span><span class="truncate">'+ currentLanguageTitle +'</span></div><div id="filter-buttons" class="col-md-3 col-xs-5 p-0 pull-right">' : '<div class="chart-title p-0 col-md-10 col-xs-7"><span class="fa-stack fa-lg">' + titleIcon + '</span><span class="truncate">'+ currentLanguageTitle +'</span></div><div id="filter-buttons" class="col-md-2 col-xs-5 p-0 pull-right">'
    }
}
function loadWidget(chartData, updated = false){
    var filterTypes = chartData.settings.more_settings ? chartData.settings.more_settings.filter_type : '';
    var names = [];
    var ids = [];
    if(filterTypes){
        if(!Array.isArray(filterTypes)){
            if (typeof window[filterTypes +'FilterLoad'] == 'function') { 
                window[filterTypes +'FilterLoad'](chartData, "", updated);
            }
        } else{
            for (var filterType of filterTypes) {
                if (typeof window[filterType +'FilterLoad'] == 'function') { 
                    window[filterType +'FilterLoad'](chartData, "", updated);
                }
            }
        }
    }
    if(!chartData.predefined_columns && chartData.settings.type !== 'table'){
        if(chartData.columns.length !== 0){
            chartData.columns.forEach(element => names.push(Object.values(element)[0]));
            chartData.columns.forEach(element => ids.push(Object.keys(element)[0]));
            chartData.names = names
            chartData.columns = names
            chartData['ids'] = ids
        }
    }
    charts[chartData.settings.id] = window[chartData.settings.type](chartData);
    if(updated) {
        if (allUpdatedWidgetDatas.length === 0){
            allUpdatedWidgetDatas.push(chartData)
        } else{
            changed = false
            for (var [key,updatedWidgetData] of allUpdatedWidgetDatas.entries()) {
                if(updatedWidgetData.settings.id === chartData.settings.id){
                    allUpdatedWidgetDatas[key] = chartData
                    changed = true
                }
            }
            if(!changed){
                allUpdatedWidgetDatas.push(chartData)
            }
        }
    }
    if(chartData.settings.type !== 'table'){
        charts[chartData.settings.id].addEventListener('dataPointSelection', function(e, chart, opts) {
            for (var updatedWidgetData of allUpdatedWidgetDatas) {
                if (chartData.settings.id == updatedWidgetData.settings.id) chartData = updatedWidgetData
            }
            if (opts.dataPointIndex >= 0) {
                var drilldown = jQuery('#chart-drilldown-'+chartData.settings.id)
                drilldown.addClass('active')
                if (drilldown.hasClass("active")) {
                    drilldownRelatedToColumn(opts.dataPointIndex, chartData, opts.w.config.labels)                           
                } 
            }
        });
    }
}

function dateFilterLoad(widgetSettings, filterId = "") {
    var dateObj = new Date();
    var year = dateObj.getUTCFullYear();
    var from_date = year + "-01-01"
    var to_date = year + "-12-31"
    var specific_date = dateObj.getFullYear()+'-'+(dateObj.getMonth()+1)+'-'+dateObj.getDate()
    var date = ''
    var operator = ''
    var container = 'filter-' + filterId + widgetSettings.settings.id
    var dateFilterMarginTop = !filterId === '' ? 'mt-10' : ''
    var filterType = ''
    if(widgetSettings.settings.more_settings){
        var moreSettings = widgetSettings.settings.more_settings
        from_date = moreSettings.from_date ? moreSettings.from_date : year + "-01-01"
        to_date = moreSettings.to_date ? moreSettings.to_date : year + "-12-31"
        specific_date = moreSettings.specific_date ? moreSettings.specific_date : dateObj.getFullYear()+'-'+(dateObj.getMonth()+1)+'-'+dateObj.getDate()
        date = moreSettings.date ? moreSettings.date : ''
        operator = moreSettings.operator ? moreSettings.operator : ''
        filterType = moreSettings.filter_type ? moreSettings.filter_type : ''
    }
    jQuery('#' + container).html(
        '<div class="row">'+
        '<div class="date-' + widgetSettings.settings.id + ' col-md-6 no-padding">'+
        '<div class="date-label-input-' + widgetSettings.settings.id + ' col-md-12 ">'+
        '<label class="control-label no-padding col-md-11 col-xs-10 ' + dateFilterMarginTop + '">' + _lang.date + '</label>'+
		'<select class="form-control" name="date" value="" id="money-dashboard-widget-date-' + widgetSettings.settings.id + '" onchange="hideAndShowDateInputs(this, '+widgetSettings.settings.id + ',\'' + filterId + '\');">' +
        '<option value="custom" class="option-' + widgetSettings.settings.id + '">'+_lang.customDate+'</option>'+
		'<option value="ty" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.thisYear+'</option>'+
        '<option value="ly" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.lastYear+'</option>'+
        '<option value="ts" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.thisSemester+'</option>'+
        '<option value="ls" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.lastSemester+'</option>'+
        '<option value="tq" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.thisQuarter+'</option>'+
        '<option value="lq" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.lastQuarter+'</option>'+
        '<option value="lm" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.lastMonth+'</option>'+
        '<option value="tm" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.thisMonth+'</option>'+
        '<option value="nm" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.nextMonth+'</option>'+
        '<option value="lw" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.lastWeek+'</option>'+
        '<option value="tw" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.thisWeek+'</option>'+
        '<option value="nw" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.nextWeek+'</option>'+
        '<option value="yesterday" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.yesterday+'</option>'+
        '<option value="today" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.today+'</option>'+
        '<option value="tomorrow" class="option-' + widgetSettings.settings.id + '">'+_lang.dateFilter.tomorrow+'</option>'+
		'</select>'+
        '</div></div>'+
        '<div class="date-inputs-' + widgetSettings.settings.id + ' col-md-6 no-padding">'+
        '<div class="date-label-input-' + widgetSettings.settings.id + ' operator-label-' + widgetSettings.settings.id + ' col-md-12">'+
        '<label class="control-label col-md-11 col-xs-10 ' + dateFilterMarginTop + ' no-padding">' + _lang.operator + '</label>'+
		'<select class="form-control" name="operator" value="" id="money-dashboard-widget-date-operator-' + widgetSettings.settings.id + '" onchange="changeDateInputs(jQuery(this).val(), '+widgetSettings.settings.id + ');">' +
        '<option value="between" class="operator-' + widgetSettings.settings.id + '">'+_lang.between+'</option>'+
        '<option value="equal" class="operator-' + widgetSettings.settings.id + '">=</option>'+
        '<option value="greator_than" class="operator-' + widgetSettings.settings.id + '">></option>'+
        '<option value="greator_than_or_equal" class="operator-' + widgetSettings.settings.id + '">>=</option>'+
        '<option value="less_than_or_equal" class="operator-' + widgetSettings.settings.id + '"><=</option>'+
        '<option value="less_than" class="operator-' + widgetSettings.settings.id + '"><</option>'+
		'</select>'+
        '</div></div></div>'+
        '<div class="row">'+
        '<div class="col-md-6 no-padding hide">'+
        '<div class="date-inputs-' + widgetSettings.settings.id + ' col-md-12 " id="specific-date-' + widgetSettings.settings.id + '">'+
        '<label class="control-label col-md-12 no-padding padding-10">Equal</label>'+
        '<div class="input-group mb-3" id="money-dashboard-from-' + widgetSettings.settings.id + '">'+
        '<input type="date" class="date form-control" value=' + specific_date + ' name="specific_date" id="specific-date-input-' + widgetSettings.settings.id + '" placeholder="YYYY-MM-DD" onchange="jQuery(\'#specific-date-input-' + widgetSettings.settings.id + '\').val(jQuery(this).val())">'+
        '</div></div></div>'+
        '<div class="col-md-6 no-padding">'+
        '<div class="date-inputs-' + widgetSettings.settings.id + ' col-md-12 " id="from-date-' + widgetSettings.settings.id + '">'+
        '<label class="control-label col-md-12  no-padding padding-10">'+ _lang.from +'</label>'+
        '<div class="input-group mb-3" id="money-dashboard-from-' + widgetSettings.settings.id + '">'+
        '<input type="date" class="date form-control" value=' + from_date + ' name="from_date" id="from-date-input-' + widgetSettings.settings.id + '" placeholder="YYYY-MM-DD" onchange="jQuery(\'#from-date-input-' + widgetSettings.settings.id + '\').val(jQuery(this).val())">'+
        '</div></div></div>'+
        '<div class="col-md-6 no-padding">'+
        '<div class="date-inputs-' + widgetSettings.settings.id + ' col-md-12 " id="to-date-' + widgetSettings.settings.id + '">'+
        '<label class="control-label col-md-12 no-padding mt-10 money-dashboard-to-' + widgetSettings.settings.id +'">'+_lang.to+'</label>'+
        '<div class="input-group mb-3" id="money-dashboard-to-' + widgetSettings.settings.id +'">'+
        '<input type="date" class="date form-control" value=' + to_date + ' name="to_date" id="to-date-input-' + widgetSettings.settings.id + '" placeholder="YYYY-MM-DD" onchange="jQuery(\'#to-date-input-' + widgetSettings.settings.id + '\').val(jQuery(this).val())">'+
        '</div></div></div></div>'
    );
    if (!filterId && !Array.isArray(filterType)){
        filterButtons(container, widgetSettings.settings.id, '\''+ filterType +'\'')
    }
    var container = jQuery('#filter-' + filterId + widgetSettings.settings.id);
    jQuery(".dashboard-date-filter", container).bootstrapDP({
        weekStart: 1,
        todayHighlight: true,
        format: "yyyy-mm-dd",
        autoclose: true,
        showOnFocus: false,
        language: _lang.languageSettings['langName'],
        defaultViewDate: jQuery('#from-date-input-'+ widgetSettings.settings.id).val(),
        endDate: Infinity,
        viewMode: 'days',
        minViewMode: 'days'
    });
    if(date){
        jQuery(".option-" + widgetSettings.settings.id + "[value='" + date + "']").attr("selected","selected");
        if(date !== 'custom'){
            jQuery('.date-inputs-' + widgetSettings.settings.id).hide();
            jQuery('.date-' + widgetSettings.settings.id).removeClass('col-md-6');
            jQuery('.date-' + widgetSettings.settings.id).addClass('col-md-12');
            jQuery('.date-label-input-' + widgetSettings.settings.id).removeClass('col-md-11 margin-left10 mr-10-ar');
            jQuery('.date-label-input-' + widgetSettings.settings.id).addClass('col-md-12');
        } else {
            jQuery('.date-inputs-' + widgetSettings.settings.id).show();
        }
        if(operator){
            jQuery(".operator-" + widgetSettings.settings.id + "[value='" + operator + "']").attr("selected","selected");
            changeDateInputs(operator, widgetSettings.settings.id)
        }
    }
    var setLastSelected = function(element) {
        jQuery(element).data('lastSelected', jQuery(element).find("option:selected"));
    };
             
    jQuery("#money-dashboard-widget-date-" + widgetSettings.settings.id).each(function () {
        setLastSelected(this);
    });
}
function filterButtons(container, widgetId, filterType){
    jQuery('#' + container).append(
        '<div class="d-flex justify-content-end mt-2">' +
        '<input type="button" value=' + _lang.save + ' id="filter-save-button-' + widgetId + '" class="btn btn-primary m-1 w-100" onclick="saveNewWidgetFilter({filter_type: ' + filterType + ', date: jQuery(\'#money-dashboard-widget-date-' + widgetId + '\').val(), operator: jQuery(\'#money-dashboard-widget-date-operator-' + widgetId + '\').val(),specific_date: jQuery(\'#specific-date-input-' + widgetId + '\').val(), from_date: jQuery(\'#from-date-input-' + widgetId + '\').val(), to_date: jQuery(\'#to-date-input-' + widgetId + '\').val(), expense_category: jQuery(\'#money-dashboard-widget-expense-category-' + widgetId + '\').val()},' + widgetId + ');"/>' +
        '<input type="button" value="' + _lang.preview + '" class="btn btn-secondary m-1 w-100" id="filter-preview-button-' + widgetId + '" onclick="updateWidgetData({filter_type: ' + filterType + ', date: jQuery(\'#money-dashboard-widget-date-' + widgetId + '\').val(), operator: jQuery(\'#money-dashboard-widget-date-operator-' + widgetId + '\').val(), specific_date: jQuery(\'#specific-date-input-' + widgetId + '\').val(), from_date: jQuery(\'#from-date-input-' + widgetId + '\').val(), to_date: jQuery(\'#to-date-input-' + widgetId + '\').val(), expense_category: jQuery(\'#money-dashboard-widget-expense-category-' + widgetId + '\').val()},' + widgetId +');"/>'+
        '<input type="button" value="' + _lang.cancel + '" class="btn btn-light m-1 w-100" id="filter-cancel-button-' + widgetId + '" onclick="cancelWidgetNewFilter('+ widgetId+');" data-toggle="collapse" data-target="#collapse-'+ widgetId + '" aria-expanded="true" aria-controls="collapse" data-target="#collapse-'+ widgetId + '"/>'+
        '</div>'
    );
}
function expenseCategoryFilterLoad(widgetSettings, filterId = "") {
    var container = 'filter-' + filterId + widgetSettings.settings.id
    jQuery('#' + container).append(
        '<div class="expense-category-label-input-' + widgetSettings.settings.id + ' col-md-12 col-xs-12 ">'+
        '<label class="control-label col-md-11 col-xs-10 mt-10 no-padding">Expense Category</label>'+
        '<select class="form-control sf-value multi-select" name="expense_category" value="" id="money-dashboard-widget-expense-category-' + widgetSettings.settings.id + '" multiple="multiple" onChange="changeHeightOfFormBox(' + widgetSettings.settings.id + ', \'' + filterId + '\');" style="height: 0px;">' +
        jQuery('#all-expense-categories').html() +
        '</select>'+
        '</div>'
    );
    var filterType = '';
    if(widgetSettings.settings.more_settings){
        date = widgetSettings.settings.more_settings.date ? widgetSettings.settings.more_settings.date : ''
        var ids = [];
        if(widgetSettings.names.length !== 0) widgetSettings.names.forEach(element => ids.push(Object.keys(element)[0]));
        for (var id of ids) {
            jQuery('#money-dashboard-widget-expense-category-' + widgetSettings.settings.id + ' option[value="' + id + '"]').attr("selected","selected");
        }
        if('filter_type' in widgetSettings.settings.more_settings ){
            filterType = '[\'' + widgetSettings.settings.more_settings.filter_type.join('\', \'') + '\']'
        }
    }
    !filterId && filterButtons(container, widgetSettings.settings.id, filterType, widgetSettings.settings.organization_id)
    jQuery('.multi-select', '#' + container).chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    
}
function changeHeightOfFormBox(widgetId, filterId){
    if (filterId) jQuery('.widget-form-'+widgetId).height(380 + jQuery('#money_dashboard_widget_expense_category_' + widgetId + '_chosen').height())
}
function hideAndShowDateInputs(that, widgetId) {
    var widgetFormHeight = jQuery('.widget-form-'+widgetId).height()
    jQuery('option:selected', '#money-dashboard-widget-date-' + widgetId).attr('selected',true).siblings().removeAttr('selected');
    jQuery('.widget-container-'+widgetId).height(jQuery('#money-dashboard-widgets #filter-' + widgetId).height()+ 400);
    var date = jQuery(that).val();
    jQuery(".option-" + widgetId + "[value='" + date + "']").attr("selected","selected");
    if(date === 'custom'){
        jQuery('.widget-container-'+widgetId).height(jQuery('#money-dashboard-widgets #filter-' + widgetId).height()+ 466 );
        jQuery('.date-inputs-' + widgetId).show();
        jQuery('.date-' + widgetId).removeClass('col-md-12');
        jQuery('.date-' + widgetId).addClass('col-md-6');
        jQuery('.date-label-input-' + widgetId).removeClass('col-md-12');
        jQuery('.date-label-input-' + widgetId).addClass('col-md-11 mr-10-ar margin-left10');
        if(widgetFormHeight ===677 || widgetFormHeight ===380 || widgetFormHeight ===496|| widgetFormHeight ===275) jQuery('.widget-form-'+widgetId).height(jQuery('.widget-form-'+widgetId).height()+70)
    } else {
        jQuery('.widget-container-'+widgetId).height(jQuery('#money-dashboard-widgets #filter-' + widgetId).height()+ 334);
        jQuery('.date-inputs-' + widgetId).hide();
        jQuery('.date-' + widgetId).removeClass('col-md-6');
        jQuery('.date-' + widgetId).addClass('col-md-12');
        jQuery('.date-label-input-' + widgetId).removeClass('col-md-11 mr-10-ar margin-left10');
        jQuery('.date-label-input-' + widgetId).addClass('col-md-12');
        if(widgetFormHeight ===747 || widgetFormHeight ===450 || widgetFormHeight ===566 || widgetFormHeight ===345) jQuery('.widget-form-'+widgetId).height(jQuery('.widget-form-'+widgetId).height()-70)
    }
}
function changeDateInputs(that, widgetId) {
    jQuery(".operator-" + widgetId + "[value='" + that + "']").attr("selected","selected");
    var fromDate = jQuery('#from-date-'+widgetId).parent();
    var toDate = jQuery('#to-date-'+widgetId).parent();
    var specificDate = jQuery('#specific-date-' + widgetId).parent();
    switch(that) {
        case 'greator_than':
        case 'greator_than_or_equal':
            fromDate.removeClass('d-none');
            toDate.addClass('d-none');
            specificDate.addClass('d-none');
          break;
        case 'less_than':
        case 'less_than_or_equal':
            fromDate.addClass('d-none');
            toDate.removeClass('d-none');
            specificDate.addClass('d-none');
            jQuery('#money-dashboard-to-' + widgetId).removeClass('ml-15');
            jQuery('.money-dashboard-to-' + widgetId).removeClass('ml-15');
          break;
        case 'equal':
            fromDate.addClass('d-none');
            toDate.addClass('d-none');
            specificDate.removeClass('d-none');
          break;
        case 'between':
            fromDate.removeClass('d-none');
            toDate.removeClass('d-none');
            specificDate.addClass('d-none');
            break;
    }
}

function updateWidgetData(filter, widgetId){
    jQuery('.widget-container-' + widgetId).height(jQuery('#money-dashboard-widgets #filter-' + widgetId).height()+ 407);
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'modules/money/money_dashboards/load_widget_data',
        type: 'GET',
        data: {id: widgetId, filter: filter},
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if(response.result){
                loadWidget(response.chart, true)
            }   
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    }); 
}
function saveNewWidgetFilter(filter, widgetId){
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'modules/money/money_dashboards/widget_update',
        type: 'POST',
        data: {id: widgetId, filter: filter},
        success: function (response) {
            if (response.result) {
                pinesMessageV2({ty: 'success', m:  _lang.feedback_messages.updatesSavedSuccessfully});
                loadWidgetData(widgetId, true)
            } else {
                pinesMessageV2({ty: 'error', m:  _lang.feedback_messages.updatesFailed});
            }   
        },
        error: defaultAjaxJSONErrorsHandler
    }); 
}
function cancelWidgetNewFilter(widgetId){
    jQuery('#plus-filter-'+ widgetId).addClass('collapsed');
    jQuery("#money-dashboard-widget-date-" + widgetId).data('lastSelected').attr("selected", true);
}

function drilldownRelatedToColumn(index, widget, labels, columnName){
    moreSettings = widget.settings.more_settings ? widget.settings.more_settings :'' ;
    if(widget.settings.type !== 'table') var column = labels.length !== 0 && widget.settings.widget_type === 'expenses_per_category' ? labels[index] : widget.names[index]
    if(widget.settings.type === 'table'){
        columnId = index
        column = columnName
    }else if (!widget.predefined_columns){
        columnId = widget.ids[index]
    } else {
        columnId = false
    }
    if(moreSettings){
        if(moreSettings.filter_type && Array.isArray(moreSettings.filter_type)){
            for (var type of moreSettings.filter_type) {
                filters = type ? window[type +'FilterData'](widget.settings.id, moreSettings.filter_type): '';          
            }
        } else{
            filters = moreSettings.filter_type ? window[moreSettings.filter_type +'FilterData'](widget.settings.id, moreSettings.filter_type): '';
        }
    }else{
        filters = '';
    }
    if(column in widget) {
        columnLang = widget[column].lang ? _lang[widget[column].lang][column] : _lang[column]
        drilldownColumns = widget[column].drilldown_columns
        drilldownModel = widget[column].model 
    } else {
        columnLang = column
        drilldownColumns = widget.drilldown_columns
        drilldownModel = widget.model
    }
    var tableHeaders = ""
    for (var drilldownColumn of drilldownColumns) {
        tableHeaders += '<th>' + drilldownColumn.name +'</th>' 
    }
    drilldownColumnId = index + '-drilldown-table-' + widget.settings.id;
    jQuery('#chart-drilldown-'+ widget.settings.id).html(
        '<div class="padding-left-20 pt-10 mb-10 pr-15-ar">'+
        '<button type="button" class="close padding-right-15" onclick="jQuery(\'#chart-drilldown-'+ widget.settings.id +'\').removeClass(\'active\');">×</button>'+
        '<h4 class="modal-title capitalize">' + columnLang + '</h4>'+
        '</div>'+
        '<table id="' + drilldownColumnId + '" class="table table-bordered table-hover table-striped drilldown-table w-100">'+
        '<thead>'+
        '<tr>' + 
        tableHeaders + 
        '</tr>'+
        '</thead>' + 
        '<tbody>' + 
        '</tbody>'+
        '</table>'
    );
    jQuery('#chart-drilldown-'+widget.settings.id);
    jQuery("#"+drilldownColumnId).css({"border": "1px solid #E0E0E0"})
    if(widget.settings.widget_type === 'transactions') column = widget[column].name;
    loadDrilldownTableData(column, widget.settings, filters, drilldownColumns, drilldownModel, index, columnId);
}
function dateFilterData(widgetId, filterType){
    var date = jQuery('#money-dashboard-widget-date-' + widgetId).val();
    var from_date = jQuery('#from-date-input-' + widgetId).val();
    var to_date = jQuery('#to-date-input-' + widgetId).val();
    var specific_date = jQuery('#specific-date-input-' + widgetId).val();
    var operator = jQuery('#money-dashboard-widget-date-operator-' + widgetId).val();
    filters = {filter_type: filterType, operator: operator, date: date, specific_date: specific_date, from_date: from_date, to_date: to_date}
    return filters
}
function expenseCategoryFilterData(widgetId, filterType){
    var date = jQuery('#money-dashboard-widget-date-' + widgetId).val();
    var from_date = jQuery('#from-date-input-' + widgetId).val();
    var to_date = jQuery('#to-date-input-' + widgetId).val();
    var specific_date = jQuery('#specific-date-input-' + widgetId).val();
    var operator = jQuery('#money-dashboard-widget-date-operator-' + widgetId).val();
    var expenseCategories = jQuery('#money-dashboard-widget-expense-category-' + widgetId).val();
    filters = {filter_type: filterType, operator: operator, date: date, specific_date: specific_date, from_date: from_date, to_date: to_date, expense_category: expenseCategories}
    return filters
}
function loadDrilldownTableData(column, widget, filters, drilldownColumns, model, index, columnId){
    var columns = []
    for (var drilldownColumn of drilldownColumns) {
        if('url' in drilldownColumn) {
            var url = drilldownColumn.url;
            column_name = {mData: drilldownColumn.drilldown_column ,
                fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                    var link = 'transaction_type' in oData ? url[oData.transaction_type] : url 
                    jQuery(nTd).html("<a href='"+getBaseURL('money') + link + oData.id + "' target='_blank'>"+sData+"</a>");
                }
            }
        }else {
            column_name = {mData: drilldownColumn.drilldown_column}  
        }
        columns.push(column_name);
    }
    drilldownColumnId = index + '-drilldown-table-' + widget.id;
    var table = jQuery('#'+ drilldownColumnId).DataTable({
        "searching": false,
        "pageLength": 5,
        "bLengthChange": false,
        "processing": true,
        "serverSide": true,
        "ordering": true,
        "order": [],
        "ajax": {
            "url": getBaseURL() + 'modules/money/money_dashboards/get_drilldown_lists',
            "type": "POST",
            "data":{"filters": filters, "column": column, "id": columnId, "model": model, "widget_type": widget.widget_type},
        },
        "columnDefs": [{ 
            "targets": [0],
            "orderable": true
        }],
        aoColumns: columns,
    });
    jQuery( table.table().container() ).removeClass( 'form-inline' );
    jQuery.fn.dataTable.ext.errMode = function ( settings, helpPage, message ) { 
        if(settings.jqXHR.responseText == 'access_denied') {
            pinesMessageV2({ty: 'warning', m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page});
        } else {
            pinesMessageV2({ty: 'warning', m: _lang.drilldownError});
        }   
    };
    jQuery(".dataTables_wrapper", "#chart-drilldown-"+widget.id).css({"padding-top": "10px"})
    jQuery(".row:nth-child(3)", "#chart-drilldown-"+widget.id).css({"margin-right": "0", "margin-left": "0"})
    jQuery(".row:nth-child(2)", "#chart-drilldown-"+widget.id).css({"margin-right": "0", "margin-left": "0"})
}
function addNewWidget(columnsNb, responsiveClass){
    var lastWidgetId = jQuery('#last-widget-id')
    var newWidgetIdVal = parseInt(lastWidgetId.val()) ? parseInt(lastWidgetId.val()) : 0;
    lastWidgetId.val(newWidgetIdVal + 1);
    addSingleWidget(parseInt(newWidgetIdVal + 1), columnsNb, responsiveClass);
    jQuery('.widget_add', '#money-dashboard-widgets').hide();
}
function addSingleWidget(widgetId, columnsNb, responsiveClass) {
    let otherLanguagesInputs = "";
    for (let key in otherLanguages) {
        otherLanguagesInputs += `<input class="form-control form-group mt-10" placeholder="${otherLanguages[key].display_name}" name="title_${otherLanguages[key].fullName}" id="money-dashboard-widget-title-${widgetId}" type="text" />`;
    }
	jQuery(`<div class="col-lg-${12/columnsNb} col-md-${responsiveClass} form-group padding-left-20" id="col-${widgetId}"> 
		<div class="grey-box-container-money-dashboard widget-form-${widgetId}" id="chart">
		<a onclick="jQuery(this).parent().parent().remove();jQuery(\'.widget_add\', \'#money-dashboard-widgets\').show();" href="javascript:;" class="pull-right mt-10"><i class="icon-alignment fa fa-trash light_red-color pull-left-arabic font-18"></i></a>
        <form id=form-widgets-${widgetId}>
		<label class="required mt-10">${_lang.money.title}</label>
        <input class="form-control form-group mt-10 mb-0" placeholder="${currentLanguage.display_name}" name="title_${currentLanguage.fullName}" id="money-dashboard-widget-title-${widgetId}" type="text" />
        <a class="btn btn-link mt-0 pt-0 mb-2 collapsed btn-collapse-languages" data-toggle="collapse" href="#collapseLanguages" role="button" aria-expanded="false"><i id="collapseLanguagesIcon" class="fa fa-lg fa-angle-down mr-1 ml-1"></i>${_lang.money.otherLanguages}</a>
        <div class="collapse" id="collapseLanguages">
        ${otherLanguagesInputs}
        </div>
        <div id="required-title-${widgetId}"></div>
		<label class="required">${_lang.widget}</label>
		<select class="form-control" name="money_dashboard_widgets_type_id" data-validation-engine="validate[required]" id="money-dashboard-widgets-type-id-${widgetId}" onChange="loadSelectedWidgetFilters('${widgetId}')">
		${jQuery('#all-widget-types').html()}
		</select>
        <div class="margin-top-15" id="filter-inputs-${widgetId}"></div>
        <div class="d-flex justify-content-end"><button type="button" id="widget-save-button" class="btn btn-primary btn-add-widget" onClick="saveWidget(${widgetId}, '${responsiveClass}')">${_lang.save}</button></div>
        </form></div></div>`
	).insertBefore('#widget-add'); 
    loadSelectedWidgetFilters(widgetId); 
}

function loadSelectedWidgetFilters(widgetId){
    var selected_widget_type_id = jQuery('#money-dashboard-widgets-type-id-'+ widgetId).val(); 
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'modules/money/money_dashboards/load_filter_type',
        data: {id: selected_widget_type_id ? selected_widget_type_id : 1},
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result){
                if(response.type){
                    filterTypes = response.type
                    widget_settings = {settings: {id: widgetId.toString()}}
                    if(!Array.isArray(filterTypes)){
                        if (typeof window[filterTypes +'FilterLoad'] == 'function') { 
                            window[filterTypes +'FilterLoad'](widget_settings, 'inputs-');
                        }
                    } else {
                        for (var filterType of filterTypes) {
                            if (typeof window[filterType +'FilterLoad'] == 'function') { 
                                window[filterType +'FilterLoad'](widget_settings, 'inputs-');
                            }
                        }
                    }
                } else {
                    jQuery('#filter-inputs-' + widgetId).empty();
                }
            }else{
                pinesMessageV2({ty: 'warning', m: response.msg});
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function saveWidget(index, responsiveClass) {
    var form = jQuery("#form-widgets-" + index)[0];
    var formData = new FormData(form);
    const currentLanguage = _lang.languageSettings['langName'];
    if (!formData.get(`title_${currentLanguage}`)) {
        showValidationErrors(index);
        return;
    }
    if (formData.has("expense_category")) {
        formData.set("expense_category", JSON.stringify(jQuery('.multi-select', "#form-widgets-" + index).chosen().val()));
    }
    formData.append("money_dashboard_id", 1);
    jQuery.ajax({
        processData: false,
        contentType: false,
        url: getBaseURL() + 'modules/money/money_dashboards/widget_save',
        data: formData,
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                jQuery('.widget_add', '#money-dashboard-widgets').show();
                if(response.chart){
                    loadWidgetTitle(response.chart, responsiveClass, true) 
                    jQuery( "#col-" + index ).addClass( "hide" );
                    loadWidget(response.chart)
                    jQuery('.widget-container-'+response.chart.settings.id).height(jQuery('.widget-container-'+response.chart.settings.id).height()+330);
                    jQuery('#last-widget-id').val(response.chart.settings.id)
                    pinesMessageV2({ty: 'success', m:  _lang.feedback_messages.newWidgetAddedSuccessfully});
                } else {
                    pinesMessageV2({ty: 'error', m:  _lang.feedback_messages.newWidgetAddingFailed});
                }
            } else if ('required_error' in response) {
                    showValidationErrors(index);
            } else {
                pinesMessageV2({ty: 'error', m:  _lang.feedback_messages.newWidgetAddingFailed});
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function removeMoneyDashboardChart(widgetId) {
    if (confirm(_lang.confirmationDeleteMoneyDashboardChart)) {
        jQuery.ajax({
            url: getBaseURL() + 'modules/money/money_dashboards/widget_delete',
            type: 'POST',
            dataType: 'JSON',
            data: {
                id: widgetId
            },
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if(response.result){
                    pinesMessageV2({ty: 'information', m:  _lang.feedback_messages.widgetDeletedSuccessfully});
                    jQuery('#chart-container-' + widgetId).remove();
                } else {
                    pinesMessageV2({ty: 'error', m:  _lang.feedback_messages.deleteWidgetFailed});
                }
            },
            complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function showValidationErrors(index) {
    jQuery('#required-title-' + index).html('<p class="required-message">' + _lang.requiredField + '</p>');
    jQuery('#money-dashboard-widget-title-'+index).removeClass('form-group');
    jQuery('#money-dashboard-widget-title-'+index).addClass('required-field');
}

function saveWidgetTitle(widgetId) {
    var form = jQuery('#form-widget-title-' + widgetId)[0];
    var formData = new FormData(form);
    jQuery.ajax({
        processData: false,
        contentType: false,
        url: getBaseURL() + 'modules/money/money_dashboards/save_widget_title/' + widgetId,
        data: formData,
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status) {
                jQuery('.chart-title', '.widget-container-' + widgetId).children().eq(1).html(Object.fromEntries(formData)['title_' + currentLanguage.id]);
                jQuery('#collapse-title-' + widgetId).collapse('hide');
                pinesMessageV2({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
            } else {
                pinesMessageV2({ty: 'error', m:  _lang.feedback_messages.error });
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
