/**
 * @module Case amount
 * @public
 */
let capAmountModel = (function () {
    'use strict';
    let expensesAmount = false;
    let logsAmount = false;
    let expensesCapRatioPercentage = false;
    let timeLogsCapRatioPercentage = false;
    let remainingCapAmount = false;

    function registerCapAmountElement() {
        let capAmountForm = jQuery("form#cap-amount-form", jQuery("#cap-amount"));
        capAmountForm.submit(function(e){ e.preventDefault();});
        jQuery('#enable-cap-amount', '#cap-amount-form').selectpicker();
        jQuery('#cap-amount-disallow', '#cap-amount-form').selectpicker();
    }

    function hideCappingWidget(status) {
        let capAmountContainer = jQuery('#cap-amount-container',jQuery('#client-account-status'));
        let widgetColumns = jQuery('.widget-columns',jQuery('#client-account-status'));
        if(status){
            capAmountContainer.removeClass('d-none');
            widgetColumns.each(function (index,value) {
                jQuery(this).removeClass('col-sm-3 col-md-3');
                jQuery(this).addClass('col-sm-5ths col-md-5ths ');
            });
        } else {
            capAmountContainer.addClass('d-none');
            widgetColumns.each(function (index,value) {
                jQuery(this).removeClass('col-sm-5ths col-md-5ths');
                jQuery(this).addClass('col-sm-3 col-md-3');
            });
        }
    }

    function hoverUpdateCapWidget() {
        let container = jQuery('#capping-container');
        if(jQuery('#time-logs-cap-ratio-percentage', container).length > 0){
            _hoverUpdateCapWidget();
        } else {
            setTimeout(function () {
                hoverUpdateCapWidget();
            }, 50);
        }
    }

    function _hoverUpdateCapWidget() {
        let container = jQuery('#capping-container');
        if(expensesAmount != false){
            jQuery('#expenses-amount', container).html(expensesAmount);
            jQuery('#logs-amount', container).html(logsAmount);
            jQuery('#expenses-cap-ratio-percentage', container).html(expensesCapRatioPercentage);
            jQuery('#time-logs-cap-ratio-percentage', container).html(timeLogsCapRatioPercentage);
            jQuery('#remaining-cap-amount', container).html(remainingCapAmount);
            jQuery('#capping-container').tooltipster('content', jQuery('.popover-content', jQuery('#capping-container')).html());
        }
    }

    function updateCappingWidget() {
        if (jQuery('.capping-amount', jQuery('#capping-container')).length > 0) {
            jQuery.ajax({
                dataType: 'JSON',
                url: getBaseURL() + 'cases/' + 'load_client_widgets/'.concat(caseId),
                beforeSend: function () {
                    jQuery('#loader-global').show();
                },
                success:function (response) {
                    if(response.result){
                        let container = jQuery('#capping-container');
                        jQuery('.capping-amount', container).html(response.capping.capping_amount);
                        expensesAmount = response.capping.cap_expenses_amount;
                        logsAmount = response.capping.cap_time_logs_amount;
                        expensesCapRatioPercentage = response.capping.expenses_cap_ratio_percentage;
                        timeLogsCapRatioPercentage = response.capping.time_logs_cap_ratio_percentage;
                        remainingCapAmount = response.capping.remaining_cap_amount;
                    }
                },
                complete: function (response) {
                    jQuery('#loader-global').hide();
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
    }

    /**
     * @function save total cap amount
     * @public
     */
    function capAmountSave(element) {
        var capAmountForm = jQuery("form#cap-amount-form", jQuery("#cap-amount"));
        let capAmountEdit = getBaseURL() + 'cases/' + 'edit';
        let formData = capAmountForm.serialize();
        jQuery.ajax({
            data: formData,
            dataType: 'JSON',
            type: 'POST',
            url: capAmountEdit,
            success: function (response) {
                if(response.warning){
                    pinesMessageV2({ty: 'warning', m: response.warning});
                    jQuery('.inline-error', jQuery("#cap-amount")).addClass('d-none');
                }
                if (response.result) {
                    jQuery('#enable-cap-amount', jQuery("#cap-amount")).val() == 1 ? hideCappingWidget(true) : hideCappingWidget(false);
                    updateCappingWidget();
                    jQuery('.inline-error', jQuery("#cap-amount")).addClass('d-none');
                    pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                } else {
                    if(response.message){
                        pinesMessageV2({ty: 'error', m: response.message});
                    }
                    displayValidationErrors(response.validationErrors, jQuery("#cap-amount"));
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    return {
        capAmountSave: capAmountSave,
        registerCapAmountElement: registerCapAmountElement,
        hoverUpdateCapWidget: hoverUpdateCapWidget
    }
})();

/**
 * @module Case rate
 * @public
 */
let caseRateModel = (function () {
    'use strict';

    /**
     * @function On success add a new case rate per user per case
     * @param response
     * @access private
     */
    let _onSuccessAddCaseRateFunction = function onSuccessAddCaseRateFunction(response){
        let caseRateGrid = jQuery('#case_rate_grid');
        let that = jQuery('#add_case_rate_dialog');
        if (response.result) {
            jQuery(that).dialog("close");
            if (caseRateGrid.is(':visible')) {
                caseRateGrid.data('kendoGrid').dataSource.read();
            } else if (caseRateGrid.is(':visible')) {
                caseRateGrid.data('kendoGrid').dataSource.read();
            }
        } else {
            for (i in response.validationErrors) {
                pinesMessageV2({ty: 'error', m: response.validationErrors[i]});
            }
        }
    };

    /**
     * @function Add case rate function
     * @access public
     */
    let _addCaseRateFunction = function _addCaseRate() {
        let caseRateForm = jQuery("#case_rate_form");
        let addCaseRateUrl = getBaseURL().concat(userSettingsController).concat('/add_case_rate/').concat(caseId);
        let dataIsValid = caseRateForm.validationEngine('validate');
        if (dataIsValid) {
            let formData = addCaseRateUrl.serialize();
            jQuery.ajax({
                data: formData,
                dataType: 'JSON',
                type: 'POST',
                url: addCaseRateUrl,
                success: _onSuccessAddCaseRateFunction,
                error: defaultAjaxJSONErrorsHandler
            });
        }
    };

    /**
     * @function On success Get case rate html content popup
     * @access public
     */
    let _onSuccessGetCaseRatePopup = function _onSuccessGetCaseRatePopup(response) {
        if (response.html) {
            let isCaseRateDialogExit = document.getElementById("case_rate_dialog");
            if(isCaseRateDialogExit){
                isCaseRateDialogExit.remove();
            }
            jQuery('<div id="case_rate_dialog"></div>').appendTo("body");
            let caseRateDialog = jQuery('#case_rate_dialog');
            caseRateDialog.html(response.html);
            jQuery(".select-picker",caseRateDialog).selectpicker();
            commonModalDialogEvents(caseRateDialog,caseRateModel.addCaseRate);
        } else {
            pinesMessageV2({ty: 'error', m: _lang.feedback_messages.error});
        }
    };

    /**
     * @function get case rate popup html
     * @param caseId Case id
     * @param caseRatesId case rate Id
     * @public
     */
    function getCaseRatePopup(caseId = '',caseRatesId = '') {
        if (licenseHasExpired) {
            alertLicenseExpirationMsg();
            return false;
        }
        let caseRatePopupAction = getBaseURL().concat(userSettingsController).concat('/get_add_case_rate_view/').concat(caseId);
        jQuery.ajax({
            dataType: 'JSON',
            url: caseRatePopupAction,
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success:_onSuccessGetCaseRatePopup,
            complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    /**
     * @function Reset case rate Form
     * @access private
     */
    function _resetCaseRateForm() {
        let caseRateDialog = jQuery("#add_case_rate_dialog");
        let caseRateForm = jQuery("form#case_rate_form", caseRateDialog);
        caseRateForm.validationEngine("hide");
        if (!caseRateDialog.empty()) {
            caseRateForm[0].reset();
        }
        jQuery(".invalid", caseRateForm).each(function (e, t) {
            jQuery(t).removeClass("invalid")
        });
    }

    /**
     * @function Get case rate popup buttons
     * @return {*[]}
     * @access private
     */
    function _getCaseRateDialogButtons() {
        return [
            {text: _lang.save,
                "class": "btn btn-info",
                click: _addCaseRateFunction
            },
            {
                text: _lang.cancel,
                "class": "btn btn-default btn-link",
                click: function () {
                    _resetCaseRateForm();
                    jQuery(this).dialog("close");
                }
            }
        ];
    }

    /**
     * @function On success get case rate
     * @param response
     * @access private
     */
    let onSuccessAddCaseRateFunction = function _onSuccessAddCaseRateFunction(response) {
        let caseRateDialog = jQuery('#case_rate_dialog');
        jQuery('.inline-error', caseRateDialog).addClass('d-none');
        if(response.result){
            let caseRateGrid = jQuery('#case_rate_grid');
            let msg = _lang.feedback_messages.addedCaseRateSuccessfully;
            pinesMessageV2({ty: 'success', m: msg});
            jQuery('.modal', '#case_rate_dialog').modal('hide');
            caseRateGrid.data("kendoGrid").dataSource.read();
        }else{
            displayValidationErrors(response.validationErrors, caseRateDialog);
        }
    };

    /**
     * @function Add a case rate
     * @access public
     */
    function addCaseRate() {
        let caseAddCaseRateUrl = getBaseURL().concat(userSettingsController).concat('/add_case_rate/');
        let addCaseRateForm = jQuery('#add_case_rate_form');
        let caseAddRateData = addCaseRateForm.serialize();
        jQuery.ajax({
            url: caseAddCaseRateUrl,
            type: 'POST',
            dataType: 'JSON',
            data: caseAddRateData,
            beforeSend: function () {
            },
            success: onSuccessAddCaseRateFunction,
            error: defaultAjaxJSONErrorsHandler
        });
    }

    /**
     * @function Delete case rate row conformation
     * @param caseRateId
     * @access public
     */
    function deleteCaseRate(caseRateId) {
        confirmationDialog('confim_delete_action', {resultHandler: _deleteCaseRate, parm: caseRateId});
    }

    /**
     * @function Delete case rate row
     * @param caseRateId case rate id get from html
     * @access private
     */
    function _deleteCaseRate(caseRateId) {
        let deleteCaseRateUrl = getBaseURL().concat(userSettingsController).concat('/delete_case_rate');
        let caseRateGrid = jQuery('#case_rate_grid');
        jQuery.ajax({
            url: deleteCaseRateUrl,
            type: 'POST',
            dataType: 'JSON',
            data: {'case_rate_id': caseRateId},
            success: function (response) {
                let error = 'error';
                let message = '';
                switch (response.status) {
                    case true:	// removed successful
                        error = 'information';
                        message = _lang.deleteRecordSuccessfull;
                        break;
                    case false:	// could not remove record
                        message = _lang.recordNotDeleted;
                        break;
                    default:
                        break;
                }
                pinesMessageV2({ty: error, m: message});
                caseRateGrid.data("kendoGrid").dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    /**
     * @function Get columns of case rate Grid
     * @var caseSettings.getCaseRatePopup
     * @var caseSettings.deleteCaseRate
     * @return {*[]}
     * @access private
     */
    function _getColumnsOfCaseRateGrid() {
        return [
            {title: "", field: '',
                template: '<div class="wraper-actions"><div class="list-of-actions">' +
                        '<div class="dropdown">' + gridActionIconHTML + '<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<li><a class="dropdown-item" href="javascript:;" onclick="caseSettings.deleteCaseRate(\'#= id #\')">' + _lang.deleteRow + '</a></li>' +
                        '</ul></div>' +
                    '</div></div>',
                sortable: false, width: '30px'},
            {field: "rate_per_hour", title: _lang.ratePerHour, width: '180px'},
            {field: "entityName", template: '#= entityName# (' + '#=currencyCode#)', title: _lang.relatedEntity, width: '180px'},
        ];
    }

    /**
     * @function Get Add users HTML Template
     * @var caseSettings.getCaseRatePopup
     * @return {string}
     * @access private
     */
    function _getAddCaseRateTemplate() {
        return '<div class="pull-right margin-bottom-5 margin-top-5 margin-right-10">'
            + '<div class="btn-group pull-right">'
            + '<button type="button" class="btn btn-info dropdown-toggle mx-2" data-toggle="dropdown">'
            + _lang.actions + ' <span class="caret"></span>'
            + '<span class="sr-only">Toggle Dropdown</span>'
            + '</button>'
            + '<ul class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
            + '<li class ="action-add-btn"><button class="dropdown-item btn-action" href="javascript:;" onclick="caseSettings.getCaseRatePopup(\'' + caseId + '\');" >' + _lang.addCaseRate + '</button></li>'
            + '</ul>'
            + '</div>'
            + '</div>';
    }

    /**
     * @function Get toolbar of case rate grid
     * @return {*[]}
     * @access private
     */
    function _getToolbarOfCaseRateGrid() {
        return [{
            name: "case-rate-grid-toolbar",
            template: _getAddCaseRateTemplate()
        },
            {name: "gridToolbarOpen", template: '<div class="pull-right margin-bottom-5 margin-top-5">'},
            {name: "save", text: _lang.save},
            {name: "cancel", text: _lang.cancel},
            {name: "gridToolbarClose", template: '</div>'}
        ];
    }

    /**
     * @function Get schema of case rate rate for data source of kindo grid
     * @return object {}
     * @access private
     */
    function _getSchemaOfCaseRateSrc() {
        return {
            type: "json",
            data: "data",
            total: "totalRows",
            model: {
                id: "id",
                fields: {
                    id: {editable: false, type: "integer"},
                    rate_per_hour: {type: "integer", validation: {required: true}},
                    entityName: {editable: false, type: "string"},
                    currencyCode: {editable: false, type: "string"},
                    actions: {editable: false}
                }
            },
            parse: function(response) {
                var rows = [];
                if(response.data){
                    var data = response.data;
                    rows = response;
                    rows.data = [];
                    for (var i = 0; i < data.length; i++) {
                        var row = data[i];
                        row['entityName'] = escapeHtml(row['entityName']);
                        row['currencyCode'] = escapeHtml(row['currencyCode']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }
    }

    /**
     * @function Get Kindo Data source instance
     * @return {*|G|G|Function|G}
     * @access private
     */
    function _getCaseRateDataSrc() {
        let _caseRateReadActionUrl = getBaseURL() + userSettingsController + "/get_case_rate";
        let _caseRateEditActionUrl = getBaseURL() + userSettingsController + "/inline_grid_edit_case_rate/";
        return new kendo.data.DataSource({
            transport: {
                read: {
                    url: _caseRateReadActionUrl,
                    dataType: "JSON",
                    type: "POST",
                    complete: function () {
                        animateDropdownMenuInGrids('case_rate_grid');
                        if (_lang.languageSettings['langDirection'] === 'rtl')
                            gridScrollRTL();
                    }
                },
                update: {
                    url: _caseRateEditActionUrl,
                    dataType: "jsonp",
                    type: "POST",
                    complete: function (XHRObj) {
                        let msg = _lang.feedback_messages.editCaseRateSuccessfully;
                        pinesMessageV2({ty: 'success', m: msg});
                        jQuery('#case_rate_grid').data('kendoGrid').dataSource.read();
                    }
                },
                parameterMap: function (options, operation) {
                    if ("read" !== operation && options.models) {
                        return {
                            models: kendo.stringify(options.models)
                        };
                    }
                    options.caseId = caseId;
                    jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                    return options;
                }
            },
            schema: _getSchemaOfCaseRateSrc(),
            error: function (e) {
                defaultAjaxJSONErrorsHandler(e.xhr)
            },
            pageSize: 20,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true
        });
    }

    /**
     * @function Get grid case rate
     * @return {null} or {object}
     * @private
     */
    function _getCaseRateGridOptions() {
        let caseRateGridOptions = null;
        try {
            caseRateGridOptions = {
                autobind: true,
                dataSource: _getCaseRateDataSrc(),
                columns: _getColumnsOfCaseRateGrid(),
                editable: true,
                filterable: false,
                height: 500,
                pageable: {
                    input: true,
                    messages: _lang.kendo_grid_pageable_messages,
                    numeric: false,
                    refresh: true,
                    pageSizes: [10, 20, 50, 100]
                },
                reorderable: true,
                resizable: true,
                scrollable: true,
                selectable: "single",
                sortable: {
                    mode: "multiple"
                },
                columnMenu: {
                    messages: _lang.kendo_grid_sortable_messages
                },
                toolbar: _getToolbarOfCaseRateGrid()
            };
        }catch (e) {

        }
        return caseRateGridOptions;
    }

    /**
     * @function Get all Case rate from database and load in Kindo Grid
     * @var kendoGrid
     * @access public
     */
    function getCaseRate() {
        let caseRateGrid = jQuery('#case_rate_grid');
        if (caseRateGrid.data('kendoGrid') === undefined) {
            caseRateGrid.kendoGrid(_getCaseRateGridOptions());
            customGridToolbarCSSButtons();
            return false;
        }
        caseRateGrid.data('kendoGrid').dataSource.read();
        return false;
    }

    return {
        getCaseRate: getCaseRate,
        deleteCaseRate: deleteCaseRate,
        addCaseRate: addCaseRate,
        getCaseRatePopup: getCaseRatePopup,
    }
})();


/**
 * @module User Rate Per Hour Per Case Model
 * @public
 */
let userRatePerHourPerCaseModel = (function () {
    'use strict';

    /**
     * @function Get schema of users rate for data source of kindo grid
     * @return object {}
     * @access private
     */
    function _getSchemaOfUserRateSrc() {
        return {
            type: "json",
            data: "data",
            total: "totalRows",
            model: {
                id: "id",
                fields: {
                    id: {editable: false, type: "integer"},
                    ratePerHour: {type: "integer", validation: {required: true}},
                    entityName: {editable: false, type: "string"},
                    currencyCode: {editable: false, type: "string"},
                    seniorityLevel: {editable: false, type: "string"},
                    firstName: {editable: false, type: "string"},
                    lastName: {editable: false, type: "string"},
                    actions: {editable: false}
                }
            },
            parse: function(response) {
                var rows = [];
                if(response.data){
                    var data = response.data;
                    rows = response;
                    rows.data = [];
                    for (var i = 0; i < data.length; i++) {
                        var row = data[i];
                        row['entityName'] = escapeHtml(row['entityName']);
                        row['currencyCode'] = escapeHtml(row['currencyCode']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }
    }

    /**
     * @function Get Kindo Data source instance
     * @return {*|G|G|Function|G}
     * @access private
     */
    function _getUserRatesDataSrc() {
        let _usersRateReadActionUrl = getBaseURL() + userSettingsController + "/user_rates_per_hour";
        let _usersRateEditActionUrl = getBaseURL() + userSettingsController + "/user_rate_edit/".concat(caseId);
        return new kendo.data.DataSource({
            transport: {
                read: {
                    url: _usersRateReadActionUrl,
                    dataType: "JSON",
                    type: "POST",
                    complete: function () {
                        if (_lang.languageSettings['langDirection'] === 'rtl')
                            gridScrollRTL();
                    }
                },
                update: {
                    url: _usersRateEditActionUrl,
                    dataType: "jsonp",
                    type: "POST",
                    complete: function (XHRObj) {
                        jQuery('#users_rates_grid').data('kendoGrid').dataSource.read();
                    }
                },
                parameterMap: function (options, operation) {
                    if ("read" !== operation && options.models) {
                        return {
                            models: kendo.stringify(options.models)
                        };
                    }
                    options.requestType = 'readData';
                    options.caseId = caseId;
                    jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                    return options;
                }
            },
            schema: _getSchemaOfUserRateSrc(),
            error: function (e) {
                defaultAjaxJSONErrorsHandler(e.xhr)
            },
            pageSize: 20,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true
        });
    }

    /**
     * @function Delete Rate of user per hour per case row
     * @param rateId rate id from html
     * @access public
     */
    function deleteRatesRow(rateId) {
        let deleteRateRowUrl = getBaseURL().concat(userSettingsController).concat('/delete_user_rate');
        if (confirm(_lang.confirmationDeleteSelectedRecord)) {
            let usersRatesGrid = jQuery('#users_rates_grid');
            jQuery.ajax({
                url: deleteRateRowUrl,
                type: 'POST',
                dataType: 'JSON',
                data: {rateId: rateId},
                success: function (response) {
                    var ty = 'error';
                    var m = '';
                    switch (response.status) {
                        case 202:	// removed successfuly
                            ty = 'information';
                            m = _lang.deleteRecordSuccessfull;
                            break;
                        case 101:	// could not remove record
                            m = _lang.recordNotDeleted;
                            break;
                        default:
                            break;
                    }
                    pinesMessageV2({ty: ty, m: m});
                    usersRatesGrid.data("kendoGrid").dataSource.read();
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
    }

    /**
     * @function Get Delete Action Template
     * @var caseSettings.deleteUserRate function from case settings route
     * @return {string}
     * @access private
     */
    function _getDeleteActionTemplateOfUsersGrid() {
        return '<div class="wraper-actions"><div class="list-of-actions">' +
                    '<a class="" href="javascript:;" onclick="caseSettings.deleteUserRate(\'#= id #\')"  title="' + _lang.deleteRow + '">' +
                    '<i class="fa fa-fw fa-trash light_red-color"></i></a>' +
                '</div></div>';
    }

    /**
     * @function Get columns of users Grid
     * @return {*[]}
     * @access private
     */
    function _getColumnsOfUsersGrid() {
        return [
            {field: "", template: _getDeleteActionTemplateOfUsersGrid(), sortable: false, title: "", width: '40px'},
            {field: "ratePerHour", title: _lang.ratePerHour, width: '156px'},
            {field: "entityName", template: '#= entityName# (' + '#=currencyCode#)', title: _lang.relatedEntity, width: '156px'},
            {field: "firstName", title: _lang.firstName, width: '120px'},
            {field: "lastName", title: _lang.lastName, width: '120px'},
            {field: "seniorityLevel", title: _lang.SeniorityLevel, width: '192px'},
        ];
    }

    /**
     * @function Reset User Rates Form
     * @access private
     */
    function _resetUserRatesForm() {
        let userRateDialog = jQuery("#userRateDialog");
        let userRateForm = jQuery("form#userRateForm", userRateDialog);
        userRateForm.validationEngine("hide");
        if (!userRateDialog.empty()) {
            userRateForm[0].reset();
        }
        jQuery(".invalid", userRateForm).each(function (e, t) {
            jQuery(t).removeClass("invalid")
        });
        jQuery('#user_id', userRateForm).val('');
    }

    /**
     * @function Fetch user rate
     * @param record
     * @access public
     */
    function fetchUserRate(record) {
        var organizationId = jQuery('#organizations', '#userRateForm').val();
        let fetUserRateUrl = getBaseURL().concat(userSettingsController).concat('/user_rates_per_hour/');
        var userId = record.id;
        if (undefined !== userId) {
            jQuery.ajax({
                url: fetUserRateUrl,
                type: 'POST',
                dataType: 'JSON',
                data: {requestType: 'getRatePerCurrUser', userId: userId, caseId: caseId, organizationId: organizationId},
                success: function (response) {
                    var userRate = response.userRate;
                    jQuery('#ratePerHour', '#userRateForm').val(userRate);
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
    }

    /**
     * @function Get Add users HTML Template
     * @return {string}
     * @var caseSettings.addUserRate
     * @access private
     */
    function _getAddUsersTemplate() {

        return '<div class="pull-right margin-bottom-5 margin-top-5">'
            + '<div class="btn-group pull-right">'
            + '<button type="button" class="btn btn-info dropdown-toggle mx-2" data-toggle="dropdown">'
            + _lang.actions + ' <span class="caret"></span>'
            + '<span class="sr-only">Toggle Dropdown</span>'
            + '</button>'
            + '<ul class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
            + '<li class ="action-add-btn"><button class="dropdown-item btn-action" href="javascript:;" onclick="caseSettings.addUserRate(\'' + caseId + '\');" >' + _lang.addUserRate + '</button></li>'
            + '</ul>'
            + '</div>'
            + '</div>';
    }

    /**
     * @function Get toolbar of users Grid
     * @return {*[]}
     * @access private
     */
    function _getToolbarOfUsersGrid() {
        return [{
            name: "users-grid-toolbar",
            template: _getAddUsersTemplate()
        },
            {name: "gridToolbarOpen", template: '<div class="pull-right margin-bottom-5 margin-top-5">'},
            {name: "save", text: _lang.save},
            {name: "cancel", text: _lang.cancel},
            {name: "gridToolbarClose", template: '</div>'}
        ];
    }

    /**
     * @function Get grid rate options
     * @return {null} or {object}
     * @access private
     */
    function _getUsersRatesGridOptions() {
        let userRatesGridOptions = null;
        try {
            userRatesGridOptions = {
                autobind: true,
                dataSource: _getUserRatesDataSrc(),
                columns: _getColumnsOfUsersGrid(),
                editable: true,
                filterable: false,
                height: 480,
                pageable: {
                    input: true,
                    messages: _lang.kendo_grid_pageable_messages,
                    numeric: false,
                    refresh: true,
                    pageSizes: [10, 20, 50, 100]
                },
                reorderable: true,
                resizable: true,
                scrollable: true,
                selectable: "single",
                sortable: {
                    mode: "multiple"
                },
                columnMenu: {
                    messages: _lang.kendo_grid_sortable_messages
                },
                toolbar: _getToolbarOfUsersGrid()
            };
        }catch (e) {

        }
        return userRatesGridOptions;
    }

    /**
     * @function On success add a new user rate per user per case
     * @param response
     * @access private
     */
    let onSuccessAddUserRatePerHourPerCaseFunction = function _onSuccessAddUserRatePerHourPerCase(response){
        let userRateGrid = jQuery('#users_rates_grid');
        let that = jQuery('#userRateDialog');
        if (response.result) {
            jQuery(that).dialog("close");
            if (userRateGrid.is(':visible')) {
                userRateGrid.data('kendoGrid').dataSource.read();
            } else if (jQuery('#timeTrackingGrid').is(':visible')) {
                jQuery('#timeTrackingGrid').data('kendoGrid').dataSource.read();
            }
        } else {
            for (i in response.validationErrors) {
                pinesMessageV2({ty: 'error', m: response.validationErrors[i]});
            }
        }
    };

    /**
     * @function Add user rate per per case
     * @access public
     */
    let addUserRatePerCasePerHourFunction = function addUserRatePerCasePerHour() {
        let userRateForm = jQuery("#userRateForm");
        let addUserRateUrl = getBaseURL().concat(userSettingsController).concat('/user_rate_add/').concat(caseId);
        let dataIsValid = userRateForm.validationEngine('validate');
        if (dataIsValid) {
            let formData = userRateForm.serialize();
            jQuery.ajax({
                data: formData,
                dataType: 'JSON',
                type: 'POST',
                url: addUserRateUrl,
                success: onSuccessAddUserRatePerHourPerCaseFunction,
                error: defaultAjaxJSONErrorsHandler
            });
        }
    };

    /**
     * @function Get user rate popup buttons
     * @return {*[]}
     * @access private
     */
    function _getUserRateDialogButtons() {
        return [
            {text: _lang.save,
                "class": "btn btn-info",
                click: addUserRatePerCasePerHourFunction
            },
            {
                text: _lang.cancel,
                "class": "btn btn-default btn-link",
                click: function () {
                    _resetUserRatesForm();
                    jQuery(this).dialog("close");
                }
            }
        ];
    }

    /**
     * @function On success get user rate per user per case
     * @param response
     * @access private
     */
    let onSuccessGetUserRateFunction = function _onSuccessGetUserRate(response) {
        let userRateDialog = jQuery("#userRateDialog");
        if (userRateDialog.length === 0) {
            userRateDialog = jQuery('<div id="userRateDialog"></div>').addClass("loading").appendTo("body")
        }
        userRateDialog.dialog({
            autoOpen: true,
            buttons: _getUserRateDialogButtons(),
            close: _resetUserRatesForm(),
            open: function () {
                jQuery(this).removeClass('d-none');
                jQuery(window).bind('resize', (function () {
                    resizeNewDialogWindow(jQuery(this), '50%', '300');
                }));
                resizeNewDialogWindow(jQuery(this), '50%', '300');

            },
            draggable: true, modal: false, resizable: true, responsive: true, title: _lang.addUserRate
        });

        userRateDialog.html(response.html);
        if (userRateDialog.hasClass('ui-dialog-content')) {
            userLookup("user_id", "userId", fetchUserRate, 'active');
        }
    };

    /**
     * @function Add a specific user rate per hour per case
     * @param caseId
     * @access public
     */
    function addUserRate(caseId) {
        let userAddUserRateUrl = getBaseURL().concat(userSettingsController).concat('/user_rates_per_hour/');
        jQuery.ajax({
            url: userAddUserRateUrl,
            type: 'POST',
            dataType: 'JSON',
            data: {caseId: caseId, requestType: 'getRateForm'},
            beforeSend: function () {
            },
            success: onSuccessGetUserRateFunction,
            error: defaultAjaxJSONErrorsHandler
        });
    }

    /**
     * @function Get all users Rate from database and bind them with kindo grid
     * @var kendoGrid
     * @return {boolean}
     * @access public
     */
    function getUserRates() {
        let usersRatesGrid = jQuery('#users_rates_grid');
        if (usersRatesGrid.data('kendoGrid') === undefined) {
            usersRatesGrid.kendoGrid(_getUsersRatesGridOptions());
            customGridToolbarCSSButtons();
            return false;
        }
        usersRatesGrid.data('kendoGrid').dataSource.read();
        return false;
    }

    return {
        getUserRates: getUserRates,
        addUserRate: addUserRate,
        deleteRatesRow: deleteRatesRow
    };
})();

/**
 * @module Case settings module
 * @type {{getUserRates: getUserRates}}
 * @access public
 */
let caseSettings = (function() {
    'use strict';

    /**
     * @function Get user rates per hour per case
     * @access public
     *
     */
    function getUserRates() {
        userRatePerHourPerCaseModel.getUserRates();
    }

    /**
     * @function Delete Rate of user per hour per case row
     * @access public
     */

    function deleteUserRate(rateId) {
        userRatePerHourPerCaseModel.deleteRatesRow(rateId)
    }

    /**
     * @function Add a specific user rate per hour per case
     * @access public
     */
    function addUserRate() {
        userRatePerHourPerCaseModel.addUserRate(caseId)
    }

    /**
     * @function Add a case rate
     * @access public
     */
    function addCaseRate() {
        caseRateModel.addCaseRate()
    }

    /**
     * @function get HTML content of popup of case rate
     * @param caseId case id
     * @access public
     */
    function getCaseRatePopup(caseId) {
        caseRateModel.getCaseRatePopup(caseId)
    }

    /**
     * @function Get All Case rate in Kindo grid
     * @access public
     */
    function getCaseRate() {
        caseRateModel.getCaseRate();
    }

    /**
     * @function Delete case rate row
     * @param caseRateId case rate id
     * @access public
     */
    function deleteCaseRate(caseRateId) {
        caseRateModel.deleteCaseRate(caseRateId)
    }

    /**
     * @function Save cap amount for matter
     * @access public
     */
    function capAmountSave() {
        capAmountModel.capAmountSave();
    }

    /**
     * @function Register Cap Amount Element
     * @access public
     */
    function registerCapAmountElement() {
        capAmountModel.registerCapAmountElement();
    }

    function hoverUpdateCapWidget() {
        capAmountModel.hoverUpdateCapWidget();
    }

    return {
        getUserRates: getUserRates,
        addUserRate: addUserRate,
        deleteUserRate: deleteUserRate,
        getCaseRate: getCaseRate,
        deleteCaseRate: deleteCaseRate,
        addCaseRate: addCaseRate,
        getCaseRatePopup: getCaseRatePopup,
        capAmountSave: capAmountSave,
        registerCapAmountElement: registerCapAmountElement,
        hoverUpdateCapWidget: hoverUpdateCapWidget
    };
}());

/**
 * On ready Get users rates
 */
jQuery(document).ready(function () {
    /**
     * @var caseSettings.getCaseRate()
     * @var caseSettings.registerCapAmountElement()
     * @var caseSettings.getCaseRate()
     */
    caseSettings.getUserRates();
    caseSettings.getCaseRate();
    caseSettings.registerCapAmountElement();

    loadPartnersSharesSection();
    jQuery(document).ready(function(){
        actionAddBtn = jQuery('.action-add-btn');
        if('undefined' !== typeof(disableMatter) && disableMatter){
            disableFields(actionAddBtn);
        }
    });
});
var $nbRows = 0;

function savePartnersShares(id) {
    var partnerPerncetagesForm = jQuery("form#partners-percentage-form", jQuery("#partners-percentage"));
    let url = getBaseURL() + 'cases/settings/' + caseId;
    jQuery.ajax({
        data: partnerPerncetagesForm.serializeArray(),
        dataType: 'JSON',
        type: 'POST',
        url: url,
        beforeSend: function() {
            jQuery("#loader-global").show();
        },
        success: function(response) {
            jQuery("#loader-global").hide();
            if (response['status']) {
                pinesMessageV2({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
            } else {
                pinesMessageV2({ ty: 'error', m: _lang.feedback_messages.updatesFailed });
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function loadPartnersSharesSection() {
    let url = getBaseURL() + 'cases/settings/' + caseId;
    jQuery.ajax({
        data: { 'action': 'load_partners' },
        dataType: 'JSON',
        type: 'POST',
        url: url,
        beforeSend: function() {
            jQuery("#loader-global").show();
        },
        success: function(response) {
            jQuery("#loader-global").hide();
            if (response['partners_shares'].length > 0) {
                jQuery.each(response['partners_shares'], function(k, v) {
                    newPartnerShareRow(v['id'], v['name']+" - "+v['currencyCode'], v['percentage'])
                });
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function newPartnerShareRow(partnerId, partnerName, partnerPercentage) {
    partnerId = partnerId || 0;
    partnerName = partnerName || '';
    partnerPercentage = partnerPercentage || '';
    labelPercentage = labelPartner = '';
    rowId = "share-row-nb-" + $nbRows
    container = jQuery("#partners-percentage-container", "#partners-percentage");
    container.append(jQuery('<div class="form-row d-flex" style="align-content: center" id="' + rowId + '"></div>'));
    var alignStyle = '';
    var deleteStyle = 'margin-top: 29px !important;';
    if ($nbRows == 0) {
        labelPartner = _lang.partner;
        labelPercentage = _lang.percentage;
    } else{
        alignStyle = 'margin-top: -7px;';
        deleteStyle = 'margin-top: 5px !important;';
    }
    jQuery('<div class="form-group col-md-5" style="height: 31px;"><label>' + labelPartner + '</label><input class="form-control" value="' + partnerName + '" placeholder="' + _lang.partner + '" id="partner-name-' + $nbRows + '" type="text" /><input class="form-control" id="partner-id-' + $nbRows + '" value="' + partnerId + '" type="hidden" name="partners[]" /> </div>').appendTo('#' + rowId)
    jQuery('<div class="form-group col-md-3" style="height: 31px;'+ alignStyle +'"><label>' + labelPercentage + '</label><input class="form-control" type="text" placeholder="' + _lang.percentage + '" value="' + partnerPercentage + '"  name="percentages[]" /> </div>').appendTo('#' + rowId)
    jQuery('<div class="form-group col-md-1" style="'+deleteStyle+'"><a href="javascript:;" onclick="removeRow(' + ($nbRows) + ')" class="btn btn-sm btn-danger"> <i class="fa fa-fw fa-trash"></i> </a> </div>').appendTo('#' + rowId)
    lookUpPartners({
        'lookupField': jQuery('#partner-name-' + $nbRows, container),
        'hiddenId': jQuery('#partner-id-' + $nbRows, container),
        'errorDiv': 'partner-lookup-wrapper'
    }, container);
    $nbRows++;
}

function removeRow(nb) {
    container = jQuery("#partners-percentage-container", "#partners-percentage");
    jQuery("#share-row-nb-" + nb, container).remove();
}