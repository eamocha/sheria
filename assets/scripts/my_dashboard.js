var requestedByMeTable,  assignedToMeTable, upcomingHearingsTable;
jQuery(document).ready( function(){
    jQuery('#main-container').attr('style', 'background-color: #F5F5F5 !important');
    jQuery('body').attr('style', 'background-color: #F5F5F5 !important');
    jQuery('.select-picker').selectpicker({dropupAuto: false});
    jQuery('#my-dashboard').sortable({
        update: function( event, ui ) {
            var fieldsOrderData = [];
            jQuery("[id^=div-]").each(function (index, obj){
                fieldsOrderData.push({'id': jQuery(this).attr('id').replace("div-", ""), 'field_order': index});
            });
            jQuery.ajax({
                url: getBaseURL() + 'dashboard/set_widgets_order',
                data: {
                    'widgets_order_data': fieldsOrderData
                },
                dataType: 'JSON',
                type: 'POST',
                success: function (response) {
                    if (response.result) {
                        pinesMessage({ty: 'success', m:  _lang.feedback_messages.updatesSavedSuccessfully});
                    } else {
                        pinesMessage({ty: 'error', m:  _lang.feedback_messages.updatesFailed});
                    }
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
    });
    requestedByMeTable = jQuery('#tasks-requested-by-me-table').DataTable({
        "bPaginate": true,
        "bLengthChange": false,
        "bFilter": true,
        "bInfo": true,
        "bAutoWidth": false,
        "searching": false,
        "pageLength": jQuery('.header-columns th', '#tasks-requested-by-me-table').length, //columns are not fixed(depends on the license package)
        "aaSorting": []
    });
    assignedToMeTable = jQuery('#tasks-assigned-to-me-table').DataTable({
        "bPaginate": true,
        "bLengthChange": false,
        "bFilter": true,
        "bInfo": true,
        "bAutoWidth": false,
        "searching": false,
        "pageLength": jQuery('.header-columns th', '#tasks-assigned-to-me-table').length, //columns are not fixed(depends on the license package)
        "aaSorting": []
    });
    upcomingHearingsTable = jQuery('#upcoming-hearings-table').DataTable({
        "bPaginate": true,
        "bLengthChange": false,
        "bFilter": true,
        "bInfo": true,
        "bAutoWidth": false,
        "searching": false,
        "pageLength": 6,
        "aaSorting": []
    });
    jQuery('.money-table').DataTable({
        "bPaginate": true,
        "bLengthChange": false,
        "bFilter": true,
        "bInfo": true,
        "bAutoWidth": false,
        "searching": false,
        "pageLength": 6,
        "aaSorting": []
    });
    loadDashboardData();
    pieCharts();
  });
  
  function loadDashboardData(widgets){
    widgets = widgets || 'all';
    var hearingsDate = jQuery('#hearings-date', '#my-dashboard').val();
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'dashboard/index',
        data: { 'widgets': widgets,
                'hearings_date' : hearingsDate
        },
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if(widgets == 'all' || widgets == 'tasks'){
                jQuery('#dashboard-task-list li').remove();
                jQuery(".tasks-empty-icon").remove();
                requestedByMeTable.clear();
                jQuery.each(response.tasks_reported_by_me, function(key, value){
                    data = ['<a href="' + getBaseURL() + 'tasks/view/' + value.id + '" target="_blank">' + value.taskId + '</a>', value.title];
                    if(licensePackage !== 'contract'){ //remove id the licensePackage is not core
                        data.push(value.legal_case_id != null ? '<a class="tooltip-title" title="' + value.case_subject + '" href="' + getBaseURL() + (value.caseCategory == 'IP' ? 'intellectual_properties' : 'cases') + '/edit/' + value.legal_case_id + '" target="_blank">' + value.caseId + '</a>' : '<span class="gray">' + _lang.noData + '</span>');
                    }
                    data.push(value.taskType,
                        value.taskStatus,
                        value.description,
                        value.due_date);
                    requestedByMeTable.row.add(data);
                });
                requestedByMeTable.draw();
                assignedToMeTable.clear();
                jQuery.each(response.tasks_assigned_to_me, function(key, value){
                    data = ['<a href="' + getBaseURL() + 'tasks/view/' + value.id + '" target="_blank">' + value.taskId + '</a>', value.title];
                    if(licensePackage !== 'contract'){ //remove id the licensePackage is not core
                        data.push(value.legal_case_id != null ? '<a class="tooltip-title" title="' + value.case_subject + '" href="' + getBaseURL() + (value.caseCategory == 'IP' ? 'intellectual_properties' : 'cases') + '/edit/' + value.legal_case_id + '" target="_blank">' + value.caseId + '</a>' : '<span class="gray">' + _lang.noData + '</span>');
                    }
                    data.push(value.taskType,
                        value.taskStatus,
                        value.description,
                        value.due_date);
                    assignedToMeTable.row.add(data);
                });
                assignedToMeTable.draw();
                if(response.todays_tasks.length > 0){
                    jQuery.each(response.todays_tasks, function (key, value) {
                        const priorityIcon = getPriorityIcon(value.priority);
                        if(key < 5){
                            jQuery("#dashboard-task-list").append("<li><div class='row'><div class='col-10 widget-item-title tasks-title'><i class='fa-2x m-2 " + priorityIcon + "'></i><a href='" + getBaseURL() + "tasks/view/" + value.id + "' target='_blank'> " + value.task_id + ": </a><span class='ml-1 mr-1'>" + value.title + "</span></div><span title='" + value.task_status + "' class='col-2 badge badge-primary tooltip-title widget-status " + (value.status_category == 'in progress' ? 'in-progress' : value.status_category) + "'>" + (value.task_status.length > 18 ? value.task_status.substring(0, 15) + "..." : value.task_status) + "</span></div><div class='row'><span class='badge badge-light widget-data widget-task-type ml-0'>" + value.task_type + "</span></div></li>");
                            jQuery(".tasks-empty-icon").remove();
                        }
                    });
                }else{
                    jQuery("#dashboard-task-list").append("<li><h5 class='m-4'>" + _lang.thereAreNoTasks + "</h5></li>");
                    jQuery('<i class="tasks-empty-icon fa-solid fa-envelope-open fa-4x text-center"></i>').insertAfter(jQuery("#dashboard-task-list").parent());
                }
                jQuery('#dashboard-task-count').text(response.todays_tasks.length);
            }
            if(widgets == 'all' || widgets == 'meetings'){
                jQuery('#dashboard-meeting-list li').remove();
                jQuery(".meetings-empty-icon").remove();
                if(response.todays_meetings.length > 0){
                    jQuery.each(response.todays_meetings, function(key, value){
                        if(key < 5){
                            const startTime = value.startTime.substring(0, 5) + " " + value.startTime.substring(5, value.startTime.length);
                            jQuery("#dashboard-meeting-list").append('<li class="d-flex justify-content-between"><a id="meeting-' + value.id + '" href="javascript:;">' + value.title + '</a><br><div class="list-widget-time"><span class="badge badge-primary">' + startTime + '</span></div></li>');
                            jQuery(".meetings-empty-icon").remove();
                            jQuery('#meeting-' + value.id).on('click', function () {
                                meetingForm({ text: value.title, ev_id: value.id, start_date: value.start_date, end_date: value.end_date});
                            });
                        }
                    });
                }else{
                    jQuery("#dashboard-meeting-list").append("<li><h5 class='m-4'>" + _lang.thereAreNoMeetings + "</h5></li>");
                    jQuery('<i class="meetings-empty-icon fa-solid fa-envelope-open fa-4x text-center"></i>').insertAfter(jQuery("#dashboard-meeting-list").parent());
                }
                jQuery('#dashboard-meeting-count').text(response.todays_meetings.length);
            }
            if(widgets == 'all' || widgets == 'reminders'){
                jQuery("#dashboard-reminder-list li").remove();
                jQuery(".reminders-empty-icon").remove();
                if(response.todays_reminders.length > 0){
                    jQuery.each(response.todays_reminders, function(key, value){
                        if(key < 5){
                            jQuery("#dashboard-reminder-list").append('<li id="reminder_' + value.id + '" class="d-flex"><div class="col-md-10"><a href="javascript:;" onclick="reminderForm(\'' + value.id + '\', \'edit\')">' + value.summary + '</a><a class="btn btn-link" title="' + _lang.dismiss + '" href="javascript:;" onclick="dismissReminder(\'' + value.id + '\')"><i class="fa fa-times-circle fa-lg"></i></a></div><div class="list-widget-time"><span class="badge badge-primary">' + value.remind_time + '</span></div></li>');
                            jQuery(".reminders-empty-icon").remove();
                        }
                    });
                }else{
                    jQuery("#dashboard-reminder-list").append("<li><h5 class='m-4'>" + _lang.thereAreNoReminders + "</h5></li>");
                    jQuery('<i class="reminders-empty-icon fa-solid fa-envelope-open fa-4x text-center"></i>').insertAfter(jQuery("#dashboard-reminder-list").parent());
                }
                jQuery('#dashboard-reminder-count').text(response.todays_reminders.length);
            }
            if(licensePackage == 'core' || licensePackage == 'core_contract') {
                if (widgets == 'all' || widgets == 'hearings') {
                    upcomingHearingsTable.clear();
                    jQuery.each(response.upcoming_hearings, function (key, value) {
                        upcomingHearingsTable.row.add([
                            value.hearing_day,
                            '<a class="row-title" href="javascript:;" onClick="fillHearingSummary(' + value.id + ');" title="' + _lang.fillHearingSummary + '"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:;" onclick="legalCaseHearingForm(\'' + value.id + '\', \'\', \'' + value.legal_case_id + '\');">' + value.startDate + ' ' + value.startTime + '</a>',
                            '<span class="' + (value.hearing_type == null ? "gray" : "") + '">' + (value.hearing_type != null ? value.hearing_type : _lang.noData) + '</span>',
                            '<span class="' + (value.court == null ? "gray" : "") + '">' + (value.court != null ? value.court : _lang.noData) + '</span>',
                            '<span class="' + (value.courtRegion == null ? "gray" : "") + '">' + (value.courtRegion != null ? value.courtRegion : _lang.noData) + '</span>',
                            '<a class="tooltip-title" title="' + value.case_subject + '" href="' + getBaseURL() + 'cases/edit/' + value.legal_case_id + '" target="_blank"><bdi>' + value.case_id + ': ' + value.case_subject + '</bdi></a>',
                            '<span class="' + (value.stage == null ? "gray" : "") + '">' + (value.stage != null ? value.stage : _lang.noData) + '</span>',
                            '<span class="' + (value.opponentLawyers == null ? "gray" : "") + '">' + (value.opponentLawyers != null ? value.opponentLawyers : _lang.noData) + '</span>',
                            '<span class="' + (value.filed_on == null ? "gray" : "") + '">' + (value.filed_on != null ? value.filed_on : _lang.noData) + '</span>',
                            '<span class="' + (value.reference == null ? "gray" : "") + '">' + (value.reference != null ? value.reference : _lang.noData) + '</span>',
                            '<span class="row-title ' + (value.comments == null ? "gray" : "") + '" title="' + value.comments + '">' + (value.comments != null ? (value.comments).substring(0, 25) + (value.comments.length > 25 ? '...' : '') : _lang.noData) + '</span>'
                        ]);
                    });
                    upcomingHearingsTable.draw();
                    jQuery('#export-hearings-dashboard-href', '#my-dashboard').attr('href', getBaseURL() + 'export/dashboard_list_hearings/' + hearingsDate);
                }
                if (widgets == 'all' || widgets == 'matters') {
                    jQuery('#dashboard-corporate-matter-list li').remove();
                    jQuery('#dashboard-litigation-case-list li').remove();
                    if (response.recent_corporate_matters.length > 0) {
                        jQuery.each(response.recent_corporate_matters, function (key, value) {
                            if (key < 3) {
                                jQuery("#dashboard-corporate-matter-list").append("<li class=\'widget-tooltip\' title=\'<div class=" + 'widget-details-tooltip' + "><div><b>" + _lang.matterCaseStage + ": </b>" + (value.stage_name != null ? value.stage_name : _lang.none) + "<br><b>" + _lang.internalReference + ": </b>" + (value.internalReference != null ? value.internalReference : _lang.none) + "<br><b>" + _lang.clientName + ": </b>" + (value.client_name != null ? value.client_name : _lang.none) + "</div><div><b>" + _lang.assignee + ": </b>" + (value.Assignee != null ? value.Assignee : _lang.none) + "<br><b>" + _lang.status + ": </b>" + value.status + "</div></div>\'><span class='widget-item-description cases-description'><a href='" + getBaseURL() + "cases/edit/" + value.id + "' target='_blank'>" + value.case_id + "</a>: " + value.subject + " </span><span class='badge badge-primary mr-2 ml-2 " + (value.status_category == 'in progress' ? 'in-progress' : value.status_category) + "'>" + (value.status.length > 18 ? value.status.substring(0, 16) + "..." : value.status) + "</span><br><span class='widget-data'>" + value.practice_area + "</span></li>");
                            }
                        });
                    }
                    if (response.recent_litigation_cases.length > 0) {
                        jQuery.each(response.recent_litigation_cases, function (key, value) {
                            if (key < 3) {
                                jQuery("#dashboard-litigation-case-list").append("<li class=\'widget-tooltip\' title=\'<div class=" + 'widget-details-tooltip' + "><div><b>" + _lang.matterCaseStage + ": </b>" + (value.stage_name != null ? value.stage_name : _lang.none) + "<br><b>" + _lang.internalReference + ": </b>" + (value.internalReference != null ? value.internalReference : _lang.none) + "<br><b>" + _lang.clientName + ": </b>" + (value.client_name != null ? value.client_name : _lang.none) + "</div><div><b>" + _lang.assignee + ": </b>" + (value.Assignee != null ? value.Assignee : _lang.none) + "<br><b>" + _lang.status + ": </b>" + value.status + "<br><b>" + _lang.clientPosition_Case + ": </b>" + (value.client_position != null ? value.client_position : _lang.none) + "</div></div>\'><span class='widget-item-description cases-description'><a href='" + getBaseURL() + "cases/edit/" + value.id + "' target='_blank'>" + value.case_id + "</a>: " + value.subject + " </span><span class='badge badge-primary mr-2 ml-2  " + (value.status_category == 'in progress' ? 'in-progress' : value.status_category) + "'>" + (value.status.length > 18 ? value.status.substring(0, 15) + "..." : value.status) + "</span><br><span class='widget-data'>" + value.practice_area + "</span></li>");
                            }
                        });
                    }
                }
                if (widgets == 'all' || widgets == 'containers') {
                    jQuery("#dashboard-matter-container-list li").remove();
                    if (response.recent_matter_containers.length > 0) {
                        jQuery.each(response.recent_matter_containers, function (key, value) {
                            if (key < 3) {
                                jQuery("#dashboard-matter-container-list").append("<li class=\'widget-tooltip\' title=\'<div class=" + 'widget-details-tooltip' + "><div><b>" + _lang.internalReference + ": </b>" + (value.internalReference != null ? value.internalReference : _lang.none) + "<br><b>" + _lang.clientName + ": </b>" + (value.client_name != null ? value.client_name : _lang.none) + "<br><b>" + _lang.status + ": </b>" + (value.container_status != null ? value.container_status : _lang.none) + "</div><div><b>" + _lang.assignee + ": </b>" + (value.assignee != null ? value.assignee : _lang.none) + "<br><b>" + _lang.clientPosition_Case + ": </b>" + (value.client_position != null ? value.client_position : _lang.none) + "</div></div>\'><span class='widget-item-description cases-description'><a href='" + getBaseURL() + "case_containers/edit/" + value.id + "' target='_blank'>" + value.container_id + "</a>: " + value.subject + " </span><span class='badge badge-primary mr-2 ml-2  " + (value.status_category == 'in progress' ? 'in-progress' : value.status_category) + "'>" + ((value.container_status != null && value.container_status.length > 18) ? value.container_status.substring(0, 15) + "..." : value.container_status) + "</span><br><span class='widget-data'>" + value.container_type_name + "</span></li>");
                            }
                        });
                    }
                }
                if (widgets == 'all' || widgets == 'IP') {
                    jQuery("#dashboard-intellectual-property-list li").remove();
                    if (response.recent_intellectual_properties.length > 0) {
                        jQuery.each(response.recent_intellectual_properties, function (key, value) {
                            if (key < 3) {
                                jQuery("#dashboard-intellectual-property-list").append("<li class=\'widget-tooltip\' title=\'<div class=" + 'widget-details-tooltip' + "><div><b>" + _lang.ip_class + ": </b>" + value.ipClass + "<br><b>" + _lang.status + ": </b>" + value.ipStatus + "<br><b>" + _lang.clientName + ": </b>" + (value.client != null ? value.client : _lang.none) + "</div><div><b>" + _lang.ip_name + ": </b>" + value.ipName + "<br><b>" + _lang.ip_subcategory + ": </b>" + (value.ipSubcategory != null ? value.ipSubcategory : _lang.none) + "<br><b>" + _lang.assignee + ": </b>" + (value.legalCaseAssignee != null ? value.legalCaseAssignee : _lang.none) + "</div></div>\'><span class='widget-item-description cases-description'><a href='" + getBaseURL() + "intellectual_properties/edit/" + value.id + "' target='_blank'>" + value.id + "</a>: " + value.subject + " </span><span class='badge badge-primary mr-2 ml-2  " + (value.ipStatusCategory == 'in progress' ? 'in-progress' : value.ipStatusCategory) + "'>" + (value.ipStatus.length > 18 ? value.ipStatus.substring(0, 15) + "..." : value.ipStatus) + "</span><br><span class='widget-data'>" + value.intellectualPropertyRight + "</span></li>");
                            }
                        });
                    }
                }
            }
            if(widgets == 'all' || widgets == 'time_logs'){
                var options = {
                    chart: {
                        height: 454,
                        type: 'bar',
                    },
                    title: {
                        text: _lang.billableAndNonBillableHours,
                        align: "center",
                        style: {
                            fontSize:  '14px',
                            fontWeight:  'bold',
                            color:  '#333354'
                          },
                    },
                    series: [],
                    colors: ["#008ffb", "#808080"],
                    legend: {
                        show: true,
                        position: 'top',
                    },
                    xaxis: {
                        categories: [_lang.currentWeek, _lang.currentMonth, _lang.currentYear],
                        labels: {
                          show: true,
                          style: {
                              color: 'blue',
                              fontSize: '12px'
                          },
                        }
                    }
                }
                var chart2 = new ApexCharts(document.querySelector("#time-logs-bar-chart"), options);
                chart2.render();
                chart2.updateSeries([{
                  name: _lang.billableNbOfHours,
                  data: [response.time_logs.week_billable > 0 ? response.time_logs.week_billable : 0, response.time_logs.month_billable > 0 ? response.time_logs.month_billable : 0, response.time_logs.year_billable > 0 ? response.time_logs.year_billable : 0]
                  }, {
                  name: _lang.nonBillableNbOfHours,
                  data: [response.time_logs.week_internal > 0 ? response.time_logs.week_internal : 0, response.time_logs.month_internal > 0 ? response.time_logs.month_internal : 0, response.time_logs.year_internal > 0 ? response.time_logs.year_internal : 0]
                }]);
    
                var options = {
                    chart: {
                        height: 400,
                        type: 'donut',
                    },
                    colors:['#008ffb', '#808080'],
                    legend: {
                        show: true,
                        position: 'top',
                        offsetY: 5,
                        itemMargin: {
                            horizontal: 5,
                            vertical: 6
                        },
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val, opts) {
                            return opts.w.config.series[opts.seriesIndex]
                        },
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                    },
                                    value: {
                                      show: true,
                                      fontSize: '24px',
                                    },
                                    total: {
                                      show: true,
                                      showAlways: true,
                                      label: _lang.totalHours,
                                      fontSize: '22px',
                                    }
                                }
                            },      
                        }
                    },
                    series: [],
                    labels: [_lang.billableNbOfHours, _lang.nonBillableNbOfHours],
                    title: {
                        text: _lang.billableVsNonBillableHoursLoggedToday,
                        align: "center",
                        style: {
                            fontSize:  '14px',
                            fontWeight:  'bold',
                            color:  '#333354'
                          },
                    },
                };
                var chart3 = new ApexCharts(
                  document.querySelector("#time-logs-pie-chart"),
                  options
                );
                chart3.render();
                chart3.updateSeries([response.time_logs.day_billable > 0 ? +response.time_logs.day_billable : 0, response.time_logs.day_internal > 0 ? +response.time_logs.day_internal : 0]);
            }
        }, 
        complete: function () {
            jQuery('#loader-global').hide();
            showToolTip();
            jQuery('.widget-tooltip').tooltipster({
                contentAsHTML: true,
                minWidth: 408,
            });
        },
        error: defaultAjaxJSONErrorsHandler
    });
    
  }
  function pieCharts(widget){
    widget = widget || 'all';
    var corporateMatters = {type: jQuery('#matters-type').val(), year: jQuery('#matters-year').val(), month: jQuery('#matters-month').val()};
    var litigationCases = {type: jQuery('#litigation-type').val(), year: jQuery('#litigation-year').val(), month: jQuery('#litigation-month').val()};
    var matterContainers = {type: jQuery('#containers-type').val(), year: jQuery('#containers-year').val(), month: jQuery('#containers-month').val()};
    var tasksAssignedToMe = {type: jQuery('#assigned-to-me-task-type').val(), year: jQuery('#assigned-to-me-tasks-year').val(), month: jQuery('#assigned-to-me-tasks-month').val()};
    var tasksReportedByMe = {type: jQuery('#reported-by-me-task-type').val(), year: jQuery('#reported-by-me-tasks-year').val(), month: jQuery('#reported-by-me-tasks-month').val()};
    var filters;
    if(widget != 'all'){
        switch(widget){
            case 'litigation-case-per-status':
                filters = {filters: {litigation_cases: litigationCases}};
            break;
            case 'corporate-matter-per-status':
                filters = {filters: {corporate_matters: corporateMatters}};
            break;
            case 'container-per-status':
                filters = {filters: {matter_containers: matterContainers}};
            break;
            case 'requested-by-me-per-status':
                filters = {filters: {tasks_reported_by_me: tasksReportedByMe}};
            break;
            case 'assigned-to-me-per-status':
                filters = {filters: {tasks_assigned_to_me: tasksAssignedToMe}};
            break;
            default:
                filters = {filters: {corporate_matters: corporateMatters, litigation_cases: litigationCases, matter_containers: matterContainers, tasks_assigned_to_me: tasksAssignedToMe, tasks_reported_by_me: tasksReportedByMe}};
            break;
        }
    }else{
        filters = {filters: {corporate_matters: corporateMatters, litigation_cases: litigationCases, matter_containers: matterContainers, tasks_assigned_to_me: tasksAssignedToMe, tasks_reported_by_me: tasksReportedByMe}};
    }
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'dashboard/pie_charts_widgets',
        data: filters,
        type: 'GET',
        beforeSend: function () {
            jQuery('.loader-submit', '#'+widget).addClass('loading');
        },
        success: function (response) {
            jQuery.each(response.pie_charts, function(key, value){
                chartId = "#pie-chart-" + key;
                var options = {
                    chart: {
                        height: 350,
                        type: 'donut',
                    },
                    legend: {
                        show: true,
                        position: 'bottom',
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function (val, opts) {
                            return opts.w.config.series[opts.seriesIndex]
                        },
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                    },
                                    value: {
                                      show: true,
                                      fontSize: '24px',
                                    },
                                    total: {
                                      show: true,
                                      showAlways: true,
                                      label: _lang.total,
                                      fontSize: '22px'
                                    }
                                }
                            },      
                        }
                    },
                    series: [],
                    colors: ["#008ffb", "#00e396", "#ff9718", "#ff4560", "#775dd0", "#ff1411", "#00d7fe", "#22e01a", "#0001ff", "#f7fe00", "#ed65ff", "#ff6929", "#21ff00", "#b505af", "#ff80c6", "#648f6f", "#6b2727", "#e3e68c", "#a2c431", "#b0ffca", "#b5fff4", "#7580d1", "#eaccff", "#ff0df3", "#ffc2c7", "#943139", "#2b4452", "#c9c9c9"],
                    labels: value.statuses,
                    tooltip: {
                        custom: function ({ seriesIndex }) {
                            const maxItemsNumber = 20;
                            const allItemsCount = value.names[seriesIndex].split(',').length;
                            const items = value.names[seriesIndex].split(',').slice(0, maxItemsNumber);
                            let namesTemplate = '';
                            items.forEach(name => {
                                namesTemplate += `<li>${name}</li>`;
                            });
                            if (allItemsCount > items.length) {
                                namesTemplate += `<li>${_lang.andMore.sprintf([allItemsCount - maxItemsNumber])}</li>`;
                            }
                            return `<div class="p-2">
                            <h6>${_lang.status}: ${value.statuses[seriesIndex]}</h6>
                            <h6>${_lang.total}: ${value.values[seriesIndex]}</h6>
                            ${getTooltipListLabel(key)}
                            <ul class="font11">
                            ${namesTemplate}
                            </ul>
                            </div>`;
                          }
                      }
                };
                var chart = new ApexCharts(
                  document.querySelector(chartId),
                  options
                );
                chart.render();
                chart.updateSeries(value.values);
            });
        },
        complete: function () {
            jQuery('.loader-submit', '#'+widget).removeClass('loading');
        },
    });
}

function getTooltipListLabel(type) {
    switch (type) {
        case 'litigation':
            return `<h6>${_lang.litigationCases}: </h6>`
        case 'matter':
            return `<h6>${_lang.cases}: </h6>`
        case 'matter_container':
            return `<h6>${_lang.caseContainers }: </h6>`
        case 'tasks_assigned_to_me':
        case 'tasks_reported_by_me':
            return `<h6>${_lang.tasks}: </h6>`
        default:
            return `<h6>${_lang.cases}: </h6>`
    }
}
  
function getPriorityIcon(value) {
    let priority = null;
    switch (value) {
        case "low":
            priority = "fa fa-angles-down text-success";
            break;
        case "medium":
            priority = "fa fa-angles-up text-primary";
            break;
        case "high":
            priority = "fa fa-bolt text-warning";
            break;
        case "critical":
            priority = "fa fa-solid fa-circle-exclamation text-danger";
            break;
        default:
            priority = "fa fa-angles-down text-success"
            break;
    }
    return priority;
}