let caseBoards = (function() {
    'use strict';

    function getBoards(boardIdSelected, filterIdSelected, quickFilter, changeBoard, isBoardFilter){
        boardIdSelected = boardIdSelected || boardId;
        filterIdSelected = filterIdSelected || filterId;
        isBoardFilter = isBoardFilter || isBoardFilterId;
        quickFilter = quickFilter || "";
        changeBoard = changeBoard || false;
        let postFilter
        let getBoardsURl = getBaseURL().concat('dashboard/cases/');
        let data = quickFilter ? {'action': 'load_columns', 'quickFilter': quickFilter, 'postFilter': postFilter, 'isBoardFilter': isBoardFilter, 'filter_id': filterIdSelected, 'planning_board_id': boardIdSelected}: {'action': 'load_columns','filter_id': filterIdSelected, 'planning_board_id': boardIdSelected};
        jQuery.ajax({
            url: getBoardsURl,
            data: data,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if(response.status){
                    let container = jQuery("#kanban-container");
                    container.empty();
                    container.append(response.html);
                    if(response.board_options_columns){
                        let columnsElements = [];
                        jQuery.each(response.board_options_columns, function (index, cases){
                            if(cases && Array.isArray(cases)){
                                jQuery.each(cases, function (boardColumnIndex, boardColumn){
                                    columnsElements.push(document.getElementById("case-board-status-container-"+ parseInt(boardColumn['id']) + "-" +boardColumnIndex));
                                });
                            }
                        });
                        if(changeBoard){
                            changeBoardFilter(response.post_filters);
                        }
                        jQuery('.tooltip-title', container).tooltipster();
                        var kanbanBoard = dragula({containers : columnsElements, accepts: function (element, target, source, sibling){
                            let targetElement = jQuery(target);
                            let sourceElement = jQuery(source);
                            let elementSelector = jQuery(element);
                            let parentColumn = targetElement.closest('.case-board-container-highlight');
                            let permissionResult = onBeforeDragIn(sourceElement.data('status-id'),elementSelector.attr('data-workflow'),targetElement.data('status-id'),elementSelector.attr('data-workflow'),elementSelector.attr("data-id"),parentColumn);
                            return (sourceElement.data('status-id') != targetElement.data('status-id') && permissionResult);
                        }}).on('drop', function (element, target, source, sibling) {
                            let targetElement = jQuery(target);
                            let sourceElement = jQuery(source);
                            let elementSelector = jQuery(element);
                            let parentColumn = targetElement.closest('.case-board-container-highlight');
                            let result = onAfterDrop(sourceElement.data('status-id'),targetElement.data('status-id'),elementSelector.attr("data-id"),parentColumn);
                        }).on('out', function (el, container) {
                            let parentColumn = jQuery(el).closest('.case-board-container-highlight');
                            parentColumn.removeClass('column-transition-highlight');
                        }).on('over', function (el, container) {
                            let parentColumn = jQuery(el).closest('.case-board-container-highlight');
                            parentColumn.removeClass('column-transition-highlight');
                        });
                        var scroll = autoScroll([
                            window,
                            document.querySelector('#kanban-container')
                        ],{
                            margin: 20,
                            autoScroll: function(){
                                return this.down && kanbanBoard.dragging;
                            }
                        });
                    }
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function getFilterEvents(container){
        jQuery('#grid-filters-list',container).selectpicker().change(function () {
            let selectedBoard = jQuery(this).find(':selected');
            filterId = this.value;
            isBoardFilterId = (selectedBoard.data('is-board') && selectedBoard.data('is-board').charAt(0) === 'B');
            (this.value) ? jQuery("#type-filter",container).val("").selectpicker('refresh').attr('disabled','disabled').siblings(".btn").addClass("input-disabled") :
            jQuery("#type-filter",container).removeAttr("disabled").siblings(".btn").removeClass("input-disabled");
            if(this.value){
                jQuery("#type-filter",container).val("").siblings(".btn").addClass("input-disabled");
                jQuery("#type-filter",container).attr('disabled','disabled');
                jQuery("#type-filter",container).selectpicker('refresh');
            } else {
                jQuery("#type-filter",container).siblings(".btn").removeClass("input-disabled");
                jQuery("#type-filter",container).removeAttr("disabled");
                jQuery("#type-filter",container).selectpicker('refresh');
            }
            let deleteFilterContainer = jQuery(".delete-filter-li");
            let deleteFilterLiHref = jQuery("#delete-filter-li-a");
            if(isBoardFilterId){
                deleteFilterContainer.removeClass('d-none');
                deleteFilterLiHref.attr('href','dashboard/case_board_delete_saved_reports/' + boardId + '/single/' + selectedBoard.data('is-board').substring(2));
            }else{
                deleteFilterContainer.addClass('d-none');
            }
            quickFilters();
        });
        if(filterId){
            let filterList = jQuery("#grid-filters-list", container);
            jQuery("#grid-filters-list option[data-is-board='" + (isBoardFilterId ? ('B-' + filterId) : ('M-' + filterId)) +"']").attr('selected', 'selected');
            filterList.selectpicker('refresh');
        }
        jQuery('#planning-board-selected',container).selectpicker({
            no_results_text: _lang.no_results_matched,
            placeholder_text: _lang.choosePlanningBoard,
            width: '100%'
        }).change(function () {
            boardId = this.value;
            getFilters();
        });
        var caseBoardDueDate = jQuery("#case-board-due-date");
        var caseBoardArrivalDate = jQuery("#case-board-arrival-date");
        jQuery('#planning-board-filters .select-picker').selectpicker();
        setDatePicker('#case-board-due-date', jQuery('#filters-form-wrapper'));
        setDatePicker('#case-board-arrival-date', jQuery('#filters-form-wrapper'));
        jQuery('#due-date-id', jQuery('#filters-form-wrapper')).change(function (e) { quickFilters(jQuery(this)); });
        jQuery('#case-arrival-date', jQuery('#filters-form-wrapper')).change(
                function (e) { quickFilters(jQuery(this));}
            );
        jQuery("#case-type-id",container).selectpicker().change(function (e) { quickFilters(jQuery(this)); });
        jQuery("#clients-list",container).selectpicker().change(function (e) { quickFilters(jQuery(this)); });
        jQuery("#provider-groups-list",container).selectpicker().change(
            function (e) {
                quickFilters(jQuery(this));
                jQuery("#quick-search-criteria-provider_group").html(jQuery(this).val());

            });
        jQuery("#users-list",container).selectpicker().change(function (e) { quickFilters(jQuery(this)); });
        jQuery("#show-list",container).selectpicker().change(function (e) { changeShowListOperator(jQuery(this));quickFilters(jQuery(this)); });
        jQuery("#type-filter",container).selectpicker().change(function (e) { quickFilters(jQuery(this)); });
        jQuery("#priority",container).selectpicker().change(function (e) { quickFilters(jQuery(this)); });
        jQuery('#client-type', container).selectpicker();
        clientInitialization(jQuery('#filters-form-wrapper'), {'onselect': caseBoards.onselectClientFilter, 'onClearLookup': caseBoards.onClearCaseBoardClientLookup, 'clientTypeChange': caseBoards.clientTypeChange});
        lookUpUsers(jQuery('#assigned-to-lookup', container), jQuery('#assigned-to-id', container), 'assigned_to', jQuery('.assignee-container', container), container, false, {
            'callback': caseBoards.userSelect,
            'onClearLookup': caseBoards.userClear
        });
    }

    function changeShowListOperator(element)
    {
        let filterOperatorShowList = jQuery("#quick-search-filter-operator-show-list");
        if(element.val() == 1){
            filterOperatorShowList.val("not_empty")
        }else if(element.val() == 2){
            filterOperatorShowList.val("empty");
        } else{
            filterOperatorShowList.val("")
        }
    }

    function quickFilters(element) {
        let container = jQuery('#fixed-board-filters');
        let quickFilterForm = jQuery("#planning-board-filters", container);
        let filterList = jQuery("#grid-filters-list", container);
        let savedFilterValue = filterList.val();
        let isBoard = filterList.find(':selected').data('is-board');
        let isBoardFilter = (isBoard && isBoard.charAt(0) === 'B');
        disableEmpty(quickFilterForm);
        disablePostFilter();
        let planningBoardFilters = form2js('planning-board-filters', '.', true);
        enableAll(quickFilterForm);
        getBoards(boardId, savedFilterValue, planningBoardFilters, false, isBoardFilter);
    }

    function toggleFilter(){
        let container = jQuery("#fixed-board-filters");
        let filtersFormWrapper = jQuery('#filters-form-wrapper',container);
        if(filtersFormWrapper.is(':visible')){
            filtersFormWrapper.slideUp();
        } else{
            filtersFormWrapper.slideDown();
        }
    }

    function showMore() {
        let container = jQuery("#fixed-board-filters");
        let moreFiltersDiv = jQuery("#more-filters", container);
        if (moreFiltersDiv.hasClass("hide")) {
            moreFiltersDiv.removeClass("hide");
            jQuery("#show-more").html(_lang.showLess);
        } else {
            moreFiltersDiv.addClass("d-none");
            jQuery("#show-more").html(_lang.showMore);
        }
    }

    function addBoardPostFilter(boardId, filterPostId){
        filterPostId = filterPostId || "";
        let url = filterPostId ? getBaseURL().concat('dashboard/board_post_filters/').concat(boardId).concat("/"+ filterPostId):
            getBaseURL().concat('dashboard/board_post_filters/').concat(boardId);
        let data = {};
        jQuery.ajax({
            url: url,
            data: data,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.status) {
                    let postFilterBoard = "add_post_filter_board";
                    jQuery('<div id="add_post_filter_board"></div>').appendTo("body");
                    var container = jQuery("#" + postFilterBoard);
                    container.html(response.html);
                    initializeModalSize(container, 0.35, 'auto');
                    commonModalDialogEvents(container);
                    jQuery("#filter-board-field",container).selectpicker();
                    selectBoardFilterInput(container);
                    jQuery("#filter-board-operator",container).selectpicker();
                    jQuery("#filter-board-field-options",container).selectpicker();
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function selectBoardFilterInput(container){
        let filterBoardOperatorContainer = jQuery("#filter-board-operator-container",container);
        let filterBoardFieldContainer = jQuery("#filter-board-field-container",container);
        let filterBoardOperator = jQuery("#filter-board-operator",container);
        let filterBoardField = jQuery("#filter-board-field",container);
        let filterBoardFieldDetails = jQuery("#filter-board-field-details",container);
        jQuery("#filter-board-field",container).on("changed.bs.select", function(event) {
            let selectedFilter = jQuery(event.currentTarget);
            let fieldOperator = selectedFilter.find(':selected').data('field_operator');
            let field = selectedFilter.find(':selected').data('field_name');
            if(selectedFilter.val()){
                filterBoardOperatorContainer.removeClass("d-none")
                filterBoardFieldContainer.removeClass("d-none")
            } else{
                filterBoardOperatorContainer.addClass("d-none")
                filterBoardFieldContainer.addClass("d-none")
            }
            let OperatorContent = '<option value=""></option>';
            jQuery.each(operators[fieldOperator], function (index, value){
                OperatorContent += '<option value="'+index+'">'+value+'</option>';
            });
            filterBoardOperator.html(OperatorContent).selectpicker('refresh');
            filterBoardFieldDetails.html(fieldsDetails[field].html);
            loadFilterEvent(fieldsDetails[field].field_type,container,fieldsDetails[field].id);
        });
    }

    function loadFilterEvent(type,container,id){
        jQuery("#criteria-field-"+id, container).selectpicker();
    }

    function savePostFilter(){
        let boardFilterGrid = jQuery('#board-filter-list-grid');
        let container = jQuery("#add-post-filter-board-container");
        let url = getBaseURL().concat('dashboard/board_save_post_filter/')
        var formData = jQuery("form#add-post-filter-board-form", container).serializeArray();
        jQuery.ajax({
            url: url,
            data: formData,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                ajaxEvents.beforeActionEvents(container);
            },
            success: function (response) {
                jQuery(".inline-error").addClass('d-none');
                if(response.status){
                    jQuery(".modal").modal("hide");
                    boardFilterGrid.data("kendoGrid").dataSource.read();
                } else{
                    displayValidationErrors(response.validationErrors, container);
                }
            }, complete: function () {
                ajaxEvents.completeEventsAction(container);
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function boardFilterList() {
        let boardFilterGrid = jQuery('#board-filter-list-grid');
        let boardFilterUrl = getBaseURL().concat('dashboard/board_post_filters/');
        let boardFilterListDataSource = new kendo.data.DataSource({
            transport: {
                read: {
                    url: boardFilterUrl,
                    dataType: "JSON",
                    type: "POST",
                    complete: function () {
                        animateDropdownMenuInGrids('add-post-filter-board-container');
                        if (_lang.languageSettings['langDirection'] === 'rtl')
                            gridScrollRTL();
                    }
                },
                parameterMap: function (options, operation) {
                    if ("read" !== operation && options.models) {
                        return {
                            models: kendo.stringify(options.models)
                        };
                    }
                    options.boardId = boardId;
                    jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                    return options;
                }
            },
            schema: {
                model: {
                    id: "id",
                    fields: {
                        id: {type: "integer"},
                        name: {editable: false, type: "string"},
                    }
                },
            }, error: function (e) {
                defaultAjaxJSONErrorsHandler(e.xhr)
            },
            pageSize: 5,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true
        });
        let boardFilterListOptions = {
            autobind: true,
            dataSource: boardFilterListDataSource,
            columns: [
                {
                    field: "", title: "", width: '15px', template: function (dataItem) {
                        return helpers.getSettingGridTemplate([
                            ['onclick="caseBoards.addBoardPostFilter('+ boardId +',' + dataItem.id + ')"', _lang.viewEdit],
                            ['onclick="caseBoards.deleteBoardPostFilter('+ dataItem.id + ')"', _lang.delete],
                        ]);
                    }
                },
                {field: "name", title: _lang.name, width: '120px', template: '#= (name!=null ? name : "") #'},
                {field: "field", title: _lang.field, width: '120px', template: '#= caseBoards.getBoardField(field) #'},
                {field: "operator", title: _lang.operator, width: '120px', template: '#= caseBoards.getOperator(operator) #'},
                {field: "value", title: _lang.value, width: '120px', template: '#= caseBoards.getBoardValue(value,field) #'},
            ],
            editable: false,
            filterable: false, height: 500,
            pageable: {
                input: true,
                messages: _lang.kendo_grid_pageable_messages,
                numeric: false,
                pageSizes: [5, 10, 20, 50, 100],
                refresh: false
            },
            reorderable: true,
            resizable: true,
            scrollable: true,
            sortable: {mode: "multiple"},
            selectable: false
        };
        boardFilterGrid.kendoGrid(boardFilterListOptions);
        return false;
    }

    function activePostFilter(_element){
        let element = jQuery(_element);
        let activeStatus = 0;
        let postFilterValueId = element.find('.post-filter-value-id').val();
        if (element.hasClass("opacity-title-board")) activeStatus = 1;
        saveActiveFilter(postFilterValueId,activeStatus,element);
    }

    function saveActiveFilter(postFilterBoardId, activeStatus, element){
        let url = getBaseURL().concat('dashboard/board_save_post_filter/')
        let data = {'post-filter-board-id': postFilterBoardId, 'active': activeStatus, 'toggleFilter' : true};
        jQuery.ajax({
            url: url,
            data: data,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
            },
            success: function (response) {
                if(response.status){
                    updatePostFilterActiveState(element);
                } else{
                    pinesMessageV2({ty: 'error', m: response.message});
                }
            }, complete: function () {
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function updatePostFilterActiveState(element){
        if (element.hasClass("opacity-title-board")) {
            element.removeClass( "opacity-title-board" );
        }else{
            element.addClass( "opacity-title-board" );
        }
        quickFilters();
    }

    function disablePostFilter(){
        let postFilterValueContainer = jQuery(".post-filter-value-container");
        let clientBoardFilterContainer = jQuery(".client-board-filter-container");
        jQuery.each(postFilterValueContainer, function (){
            if(!jQuery(this).hasClass("opacity-title-board")) {
                jQuery(this).find("input").attr("disabled", true);
            }else{
                jQuery(this).find("input").attr("disabled", false);
            }
        });
        jQuery.each(clientBoardFilterContainer, function (){
            if(!jQuery("#contact-company-id").val()){
                jQuery(this).find("input").attr("disabled", true);
                jQuery(this).find("select").attr("disabled", true);
            }
        });
    }

    function getOperator(operator){
        let operatorName = operator;
        jQuery.each(operators,function (index, value){
            if(operator in value) {
                operatorName = value[operator];
                return false;
            }
        });
        return operatorName;
    }

    function getBoardValue(boardValue,field){
        let boardValueName = boardValue;
        jQuery.each(fieldsDetails,function (index, value){
            if(value['db_value'] == field){
                boardValueName = value['field_options'][boardValue];
                return false;
            }
        });
        return boardValueName;
    }

    function deleteBoardPostFilter(filterPostId){
        let boardFilterGrid = jQuery('#board-filter-list-grid');
        confirmationDialog('confirm_delete_record', {
            resultHandler: function () {
                jQuery.ajax({
                    url: getBaseURL().concat('dashboard/board_delete_post_filter/').concat("/" + filterPostId),
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function () {
                        jQuery('#loader-global').show();
                    },
                    success: function (response) {
                        if (response.status) {
                            boardFilterGrid.data("kendoGrid").dataSource.read();
                            pinesMessageV2({ty: 'success', m: _lang.feedback_messages.success});
                        } else {
                            pinesMessageV2({ty: 'error', m: _lang.feedback_messages.deleteRowFailed});
                        }
                    }, complete: function () {
                        jQuery('#loader-global').hide();
                    },
                    error: defaultAjaxJSONErrorsHandler
                });
            }, parm: filterPostId
        })
    }

    function getBoardField(field){
        let fieldName = field;
        jQuery.each(fieldsData,function (index, value){
            if(value['db_value'] == field){
                fieldName = value['name'];
                return false;
            }
        });
        return fieldName;
    }

    /**
     * to check if transaction possible or not
     * @param statusFrom int "the status from "
     * @param statusTo int "the status to"
     * @param matterId  int "the matter id"
     * @param parentColumn object "workflow column"
     * @return {boolean}
     */
    function onAfterDrop(statusFrom, statusTo, matterId, parentColumn) {
        statusFrom = statusFrom.toString();
        statusTo = statusTo.toString();
        matterId = matterId.toString();
        jQuery("#case-id").val(matterId);
        jQuery("#new-status").val(statusTo);
        jQuery("#old-status").val(statusFrom);
        let container = jQuery('#fixed-board-filters');
        let quickFilterForm = jQuery("#planning-board-filters", container);
        disableEmpty(quickFilterForm);
        disablePostFilter();
        let planningBoardFilters = form2js('planning-board-filters', '.', true);
        enableAll(quickFilterForm);
        let filterList = jQuery("#grid-filters-list", container);
        let savedFilterValue = filterList.val();
        savedFilterValue = savedFilterValue || filterId;
        let isBoard = filterList.find(':selected').data('is-board');
        let isBoardFilter = (isBoard && isBoard.charAt(0) === 'B');
        let postData = {};
        postData.quickFilter = planningBoardFilters;
        postData.newStatus = planningBoardFilters.newStatus;
        postData.oldStatus = planningBoardFilters.oldStatus;
        postData.caseId = planningBoardFilters.caseId;
        postData.action = 'return_screen';
        postData.isBoardFilter = isBoardFilter;
        postData.savedFilterValue = savedFilterValue;
        jQuery.ajax({
            url: getBaseURL() + 'dashboard/cases_result/' + boardId,
            type: 'POST',
            dataType: 'JSON',
            data: postData,
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.result) {
                    if (typeof response.screen_html !== 'undefined') {
                        screenTransitionFormEvents(matterId, response.transition_id, response.screen_html, 'cases', function () { caseBoards.quickFilters(); }, true, getBaseURL() + 'cases/transition_screen_fields/');
                    } else {
                        for (var index  in response.data) {
                            jQuery("#total_number_cases_" + index).text('');
                            jQuery("#total_number_cases_" + index).text(response.data[index]);
                        }
                        parentColumn.removeClass('column-transition-highlight');
                    }
                } else {
                    pinesMessageV2({ty: 'warning', m: response.display_message});
                }
            },
            complete: function (event) {
                jQuery('#loader-global').hide();
                if (!event.responseJSON.result) quickFilters();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    /**
     * to check if transaction possible or not
     * @param statusFrom int "the status from "
     * @param workflowFrom int "workflow status from"
     * @param statusTo int "the status to"
     * @param workflowTo int "workflow status to"
     * @param matterId  int "the matter id"
     * @param parentColumn object "workflow column"
     * @return {boolean}
     */
    function onBeforeDragIn(statusFrom, workflowFrom, statusTo, workflowTo, matterId, parentColumn) {
        statusFrom = statusFrom.toString();
        workflowFrom = workflowFrom.toString();
        statusTo = statusTo.toString();
        workflowTo = workflowTo.toString();
        matterId = matterId.toString();
        // check status transitions if is allowed to target column status
        parentColumn.removeClass('column-transition-highlight');
        if (
            // allow transitional statuses to transition to another statuse
            (possibleTransitions[statusFrom] && ((possibleTransitions[statusFrom]['allowed_transitions'] && (jQuery.inArray(statusTo, possibleTransitions[statusFrom]['allowed_transitions'].replace(/\s+/g, '').split(",")) !== -1))))
            // allow global status to transition to all statuses - To-Do: it is a bug due to the limitation in possibleTransitions array so currently the global can be moved to all workflows
            || (possibleTransitions[statusFrom] && possibleTransitions[statusFrom]['isGlobal'] == 1 && (jQuery.inArray(workflowTo, possibleTransitions[statusFrom]['workflow_id'].replace(/\s+/g, '').split(",")) !== -1))
            // allow all transtional statuses to transition to global statuse: same bug that reported in the second condition
            || (possibleTransitions[statusTo] && possibleTransitions[statusTo]['isGlobal'] == 1 && (jQuery.inArray(workflowFrom, possibleTransitions[statusTo]['workflow_id'].replace(/\s+/g, '').split(",")) !== -1))
        ) {
            // let kanbanColumn = jQuery('div[view_id="' + dragContext.to.config.id + '"]');
            if(statusFrom != statusTo){
                parentColumn.addClass('column-transition-highlight');
            }
            return true;
        } else{
            return false;
        }
    }

    let onWrappedFilter = function (isWrapped){
        let filterContainer = jQuery('.post-filter-container');
        let filterShowMore = jQuery('#show-more-post-filter', filterContainer);
        if(isWrapped){
            filterShowMore.removeClass('d-none');
            filterContainer.addClass('more-post-filter-trim');
        } else{
            filterShowMore.addClass('d-none');
            filterContainer.removeClass('more-post-filter-trim');
        }
    }

    function showMoreWrap(element){
        let filterContainer = jQuery('.post-filter-container');
        let moreFilterStatus = jQuery(element).data("show");
        if(moreFilterStatus){
            jQuery(element).data("show", false);
            filterContainer.removeClass("flex-wrap");
            filterContainer.addClass("more-post-filter-trim");
            jQuery(element).find(".post-filter-value-container").text(_lang.showMore);
        } else{
            jQuery(element).data("show", true);
            filterContainer.addClass("flex-wrap");
            filterContainer.removeClass("more-post-filter-trim");
            jQuery(element).find(".post-filter-value-container").text(_lang.showLess);
        }
    }

    function changeBoardFilter(){
        jQuery("#filters-dorm-wrapper-container").html();
    }

    function getFilters(boardIdSelected, filterIdSelected, isBoardFilter){
        boardIdSelected = boardIdSelected || boardId;
        filterIdSelected = filterIdSelected || filterId;
        isBoardFilter = isBoardFilter || isBoardFilterId;
        let getBoardsURl = getBaseURL().concat('dashboard/cases/');
        let data = {'action': 'filter', 'filter_id': filterIdSelected, 'planning_board_id': boardIdSelected, 'isBoardFilter': isBoardFilter};
        jQuery.ajax({
            url: getBoardsURl,
            data: data,
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if(response.status){
                    let container = jQuery("#fixed-board-filters");
                    container.empty();
                    container.append(response.html);
                    caseBoards.getFilterEvents(container);
                    quickFilters();
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function archiveCases(planningBoardId) {
        jQuery.ajax({
            url: getBaseURL().concat('dashboard/archiving/').concat(planningBoardId),
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.status) {
                    var archivingMatterContainer = "archiving-matter-container";
                    jQuery('<div id="archiving-matter-container"></div>').appendTo("body");
                    var container = jQuery("#" + archivingMatterContainer);
                    container.html(response.html);
                    initializeModalSize(container, 0.3, 'auto');
                    commonModalDialogEvents(container);
                    jQuery("#archiving-type",container).selectpicker().change(function (e) {
                        let archiveCaseStatus = jQuery(this).val() === 'archive' ? archiveCaseStatusView : [];
                        jQuery("#archive-case_status").val(archiveCaseStatus).selectpicker('refresh');
                        jQuery('.tooltip-title', container).tooltipster('content', jQuery(this).val() === 'archive' ? tooltipArchiveBoard : tooltipHideBorad);
                    });
                    jQuery("#archive-case_status",container).selectpicker();
                    jQuery('.tooltip-title', container).tooltipster();
                    jQuery('#filter-closed-on-operator', container).selectpicker();
                    setDatePicker('#filter-closed-on-value-container', container);
                    setDatePicker('#filter-closed-on-value-container-end', container);
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function getArchiveFilters() {
        var filtersForm = jQuery('#archiving-matter-container');
        disableEmptyFilter(filtersForm);
        var searchFilters = form2js('archiving-matter-container-form', '.', true);
        enableEmptyFilter(filtersForm);
        return searchFilters;
    }

    function archiveCasesSubmit(){
        let archivingMatterContainer = jQuery("#archiving-matter-container");
        let formData = getArchiveFilters();
        jQuery.ajax({
            url: getBaseURL().concat('dashboard/archiving/'),
            dataType: 'JSON',
            type: 'POST',
            data: formData,
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                jQuery('.inline-error', archivingMatterContainer).addClass('d-none');
                if (response.status) {
                    if(response.affected_Rows > 0){
                        confirmationDialog(response.archive_case_status_messgae, {
                            resultHandler: function () {
                                jQuery.ajax({
                                    url: getBaseURL() + 'dashboard/archiving',
                                    dataType: 'JSON',
                                    type: 'POST',
                                    data: {archiveAction : true, formData: formData},
                                    success: function (response) {
                                        if(response.message){
                                            pinesMessageV2({ty: response.result ? 'success' : 'error', m: response.message});
                                        }
                                        let archivingMatterContainer = jQuery("#archiving-matter-container");
                                        jQuery('.modal', archivingMatterContainer).modal('hide');

                                        quickFilters();
                                    },
                                    error: defaultAjaxJSONErrorsHandler
                                });
                            },
                            isKeyJs: true
                        })
                    } else{
                        pinesMessageV2({ty: 'information', m: _lang.noMattersToArchive});
                    }
                }else {
                    displayValidationErrors(response.validationErrors, archivingMatterContainer);
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    let onselectClientFilter = function _onselectClientFilter(){
        quickFilterTimeOut();
    }

    let onClearCaseBoardClientLookup = function _onClearCaseBoardClientLookup(){
        quickFilterTimeOut();
    }

    function quickFilterTimeOut(time){
        time = time || 400;
        setTimeout(function (){
            quickFilters();
        }, time);
    }

    function clientTypeChange(val){
        if(val) quickFilterTimeOut();
    }

    let userClear = function _userClear(){
        quickFilterTimeOut();
    }

    let userSelect = function _userSelect(){
        quickFilterTimeOut();
    }

    return {
        getBoards: getBoards,
        quickFilters: quickFilters,
        getFilterEvents: getFilterEvents,
        showMore: showMore,
        toggleFilter: toggleFilter,
        addBoardPostFilter: addBoardPostFilter,
        boardFilterList: boardFilterList,
        savePostFilter: savePostFilter,
        activePostFilter: activePostFilter,
        getOperator: getOperator,
        deleteBoardPostFilter: deleteBoardPostFilter,
        getBoardField: getBoardField,
        getBoardValue: getBoardValue,
        onAfterDrop: onAfterDrop,
        onBeforeDragIn: onBeforeDragIn,
        onWrappedFilter: onWrappedFilter,
        showMoreWrap: showMoreWrap,
        archiveCases: archiveCases,
        onselectClientFilter: onselectClientFilter,
        onClearCaseBoardClientLookup: onClearCaseBoardClientLookup,
        clientTypeChange: clientTypeChange,
        quickFilterTimeOut: quickFilterTimeOut,
        userClear: userClear,
        userSelect: userSelect,
        archiveCasesSubmit: archiveCasesSubmit
    };
}());

jQuery(document).ready(function () {
    let container = jQuery("#fixed-board-filters");
    helpers.flexWrapped(jQuery('.post-filter-value-container'), caseBoards.onWrappedFilter);
});

var timeOutFunctionId;
function workAfterResizeIsDone() {
    helpers.flexWrapped(jQuery('.post-filter-value-container'), caseBoards.onWrappedFilter);
}
window.addEventListener("resize", function() {
    clearTimeout(timeOutFunctionId);
    timeOutFunctionId = setTimeout(workAfterResizeIsDone, 1000);
});
function removePlanningBoard(boardId, rowObj) {
    if (confirm(_lang.confirmationDeletePlanningBoard)) {
        jQuery.ajax({
            url: getBaseURL() + 'dashboard/case_boards',
            type: 'POST',
            dataType: 'JSON',
            data: {
                id: boardId
            },
            success: function (response) {
                if (response.status == 500) {
                    pinesMessage({ty: 'success', m: _lang.deleteRecordSuccessfull});
                    jQuery(rowObj).remove();
                } else {
                    pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
                    message = _lang.deleteRecordFailed;
                    type = 'error';
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function caseAssigneeForm(id) {
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        data: {
            action: "assigneeForm",
            id: id
        },
        url: getBaseURL() + 'cases/edit/' + id,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery(".case-assignee-dialog-container-hidden").length <= 0) {
                    jQuery('<div id="caseAssigneeDialog" class="d-none case-assignee-dialog-container-hidden"></div>').appendTo("body");
                    var caseAssigneeDialog_hidden = jQuery('.case-assignee-dialog-container-hidden');
                    caseAssigneeDialog_hidden.html(response.html).removeClass('d-none');
                    jQuery('.modal', caseAssigneeDialog_hidden).modal({
                        keyboard: false,
                        backdrop: 'static',
                        show: true
                    });

                    jQuery(document).keyup(function (e) {
                        if (e.keyCode == 27) {
                            jQuery('.modal', caseAssigneeDialog_hidden).modal('hide');
                        }
                    });
                    jQuery(caseAssigneeDialog_hidden).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            jQuery("#caseAssigneeDialog #save-case-assignee-btn").trigger("click");
                        }
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(caseAssigneeDialog_hidden);
                    });
                    ctrlS(function () {
                        jQuery("#save-case-assignee-btn").click();
                    });
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function caseAssigneeFormSubmit() {
    var id = jQuery("#caseAssigneeDialog #case-id").val();
    jQuery('#assigneeContainer #action').val('assigneeFormSubmit');
    var formData = jQuery(".case-assignee-dialog-container-hidden form#assigneeCaseForm").serialize();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit').addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'cases/edit/' + id,
        success: function (response) {
            if (response.result) {
                jQuery('.modal', '.case-assignee-dialog-container-hidden').modal('hide');
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                location.reload();
            }
            jQuery('.inline-error', '.case-assignee-dialog-container-hidden').html('');
            for (i in response.validationErrors) {
                jQuery("div", '.case-assignee-dialog-container-hidden').find("[data-field=" + i + "]").removeClass('d-none').html(response.validationErrors[i]).addClass('validation-error');
            }

        }, complete: function () {
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('.loader-submit').removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}
