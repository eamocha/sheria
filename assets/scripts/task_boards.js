let taskBoards = (function() {
    'use strict';

    function getBoards(boardIdSelected, filterIdSelected, quickFilter, changeBoard, isQuickFilter){
        boardIdSelected = boardIdSelected || boardId;
        filterIdSelected = filterIdSelected || filterId;
        quickFilter = quickFilter || "";
        changeBoard = changeBoard || false;
        isQuickFilter = isQuickFilter || false;
        let postFilter
        let getBoardsURl = getBaseURL().concat('dashboard/tasks/');
        if(isQuickFilter){
            jQuery("#grid-filters-list").val("").selectpicker('refresh');
            filterIdSelected = "";
        }
        let data = quickFilter ? {'action': 'load_columns', 'quickFilter': quickFilter, 'postFilter': postFilter, 'filter_id': filterIdSelected, 'task_board_id': boardIdSelected}: {'action': 'load_columns','filter_id': filterIdSelected, 'task_board_id': boardIdSelected};
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
                    let container = jQuery("#kanban-task-container");
                    container.empty();
                    container.append(response.html);
                    if(response.board_options_columns){
                        let columnsElements = [];
                        jQuery.each(response.board_options_columns, function (index, tasks){
                            if(tasks && Array.isArray(tasks)){
                                jQuery.each(tasks, function (boardColumnIndex, boardColumn){
                                    columnsElements.push(document.getElementById("task-board-status-container-"+ parseInt(boardColumn['id']) + "-" + boardColumnIndex));
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
                                let parentColumn = targetElement.closest('.task-board-container-highlight');
                                let permissionResult = onBeforeDragIn(sourceElement.data('status-id'),elementSelector.attr('data-workflow'),targetElement.data('status-id'),elementSelector.attr('data-workflow'),elementSelector.attr("data-id"),parentColumn);
                                return (sourceElement.data('status-id') != targetElement.data('status-id') && permissionResult);
                            }}).on('drop', function (element, target, source, sibling) {
                            let targetElement = jQuery(target);
                            let sourceElement = jQuery(source);
                            let elementSelector = jQuery(element);
                            let parentColumn = targetElement.closest('.task-board-container-highlight');
                            let result = onAfterDrop(sourceElement.data('status-id'),targetElement.data('status-id'),elementSelector.attr("data-id"),parentColumn);
                        }).on('out', function (el, container) {
                            let parentColumn = jQuery(el).closest('.task-board-container-highlight');
                            parentColumn.removeClass('column-transition-highlight');
                        }).on('over', function (el, container) {
                            let parentColumn = jQuery(el).closest('.task-board-container-highlight');
                            parentColumn.removeClass('column-transition-highlight');
                        });
                        var scroll = autoScroll([
                            window,
                            document.querySelector('#kanban-task-container')
                        ],{
                            margin: 20,
                            autoScroll: function(){
                                return this.down && kanbanBoard.dragging;
                            }
                        });
                        if(response.saved_grid_details) setSavedFilterValues(response.saved_grid_details, filterIdSelected, isQuickFilter);
                        toggelSavedButtons(isQuickFilter);
                    }
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function toggelSavedButtons(isQuickFilter){
        isQuickFilter = isQuickFilter || false;
        let stateButtons = false;
        jQuery('input.filter-value-inputs, select.filter-value-inputs').each(function (index, value){
            if(jQuery(value).val() != "" && jQuery(value).val() != null){
                stateButtons = true;
                return false;
            }
        });
        if(stateButtons || jQuery("#grid-filters-list").val()){
            jQuery("#saved_filter_buttons_wrapper").removeClass('d-none');
        } else{
            jQuery("#saved_filter_buttons_wrapper").addClass('d-none');
        }
    }

    function setSavedFilterValues(savedGridDetails, filterIdSelected, isQuickFilter){
        isQuickFilter = isQuickFilter || false;
        if(!isQuickFilter){
            resetFilters(true);
        }
        if(filterIdSelected && filterIdSelected != 0){
            jQuery("#saved_filter_buttons_wrapper").removeClass('d-none');
            jQuery.each(json_decode(savedGridDetails)['filters'], function (index, value){
               switch (value['filters'][0]['field']){
                   case 'us.user_id':
                       jQuery("#assigned-to-id").val(value['filters'][0]['value']);
                       jQuery('#assigned-to-lookup', jQuery("#fixed-board-filters")).typeahead('val',value['filters'][0]['field_name']);
                       break;
                   case 'ta.due_date':
                       jQuery("#due-date-id").val(value['filters'][0]['value']);
                       break;
                   case 'ta.legal_case_id':
                       jQuery("#legal_case_id").val(value['filters'][0]['value']);
                       jQuery('#caseLookup', jQuery("#fixed-board-filters")).typeahead('val',value['filters'][0]['field_name']);
                       break;
                    case 'ta.contract_id':
                        jQuery("#contract_id").val(value['filters'][0]['value']);
                        jQuery('#contractLookup', jQuery("#fixed-board-filters")).typeahead('val',value['filters'][0]['field_name']);
                        break;
                   case 'provider_groups_users.provider_group_id':
                       jQuery("#provider-groups-list").val(value['filters'][0]['value'][0]).selectpicker('refresh');
                       break;
                   case 'ta.createdOn':
                       jQuery("#created-on").val(value['filters'][0]['value']);
                       break;
               }
            });
        }else {
            jQuery("#saved_filter_buttons_wrapper").addClass('d-none');
        }
    }

    function getFilterEvents(container){
        jQuery('#grid-filters-list',container).selectpicker().change(function () {
            resetFilters(true);
            let selectedBoard = jQuery(this).val();
            filterId = this.value;
            (this.value) ? jQuery("#type-filter",container).val("").selectpicker('refresh').attr('disabled','disabled').siblings(".btn").addClass("input-disabled") :
                jQuery("#type-filter",container).removeAttr("disabled").siblings(".btn").removeClass("input-disabled");
            let deleteFilterContainer = jQuery(".delete-filter-li");
            let deleteFilterLiHref = jQuery("#delete-filter-li-a");
            if(selectedBoard){
                deleteFilterContainer.removeClass('d-none');
            }else{
                deleteFilterContainer.addClass('d-none');
                resetFilters();
            }
            quickFilters();
        });
        if(filterId){
            let filterList = jQuery("#grid-filters-list", container);
            filterList.val(filterId).selectpicker('refresh');
        }
        jQuery('#task-board-selected',container).selectpicker({
            no_results_text: _lang.no_results_matched,
            placeholder_text: _lang.choosePlanningBoard,
            width: '100%'
        }).change(function () {
            boardId = this.value;
            getFilters();
        });
        jQuery('#tasks-board-filters .select-picker').selectpicker();
        setDatePicker('#created-on-date-container', jQuery('#filters-form-wrapper'));
        setDatePicker('#due-date-container', jQuery('#filters-form-wrapper'));
        jQuery('#due-date-id', jQuery('#filters-form-wrapper')).change(function (e) { quickFilters(true); });
        jQuery('#created-on', jQuery('#filters-form-wrapper')).change(
            function (e) { quickFilters(true);}
        );
        jQuery("#provider-groups-list",container).selectpicker().change(
            function (e) {
                quickFilters(true);
                jQuery("#quick-search-criteria-provider_group").html(jQuery(this).val());

            });
        jQuery("#users-list",container).selectpicker().change(function (e) { quickFilters(true); });
        lookUpUsers(jQuery('#assigned-to-lookup', container), jQuery('#assigned-to-id', container), 'assigned_to', jQuery('.assignee-container', container), container, false, {
            'callback': taskBoards.userSelect,
            'onClearLookup': taskBoards.userClear
        });
        lookUpCases(jQuery('#caseLookup', container), jQuery('#legal_case_id', container), 'legal_case_id', container, false, {
            'callback': taskBoards.userSelect,
            'onClearLookup': taskBoards.userClear
        });
        lookUpContracts({
            'lookupField': jQuery('#contractLookup', container),
            'hiddenId': jQuery('#contract_id', container),
        }, container, {
                'callback': taskBoards.userSelect,
                'onClearLookup': taskBoards.userClear
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

    function quickFilters(isQuickFilter) {
        isQuickFilter = isQuickFilter || false;
        let container = jQuery('#fixed-board-filters');
        let quickFilterForm = jQuery("#tasks-board-filters", container);
        let filterList = jQuery("#grid-filters-list", container);
        let savedFilterValue = filterList.val();
        disableEmpty(quickFilterForm);
        disablePostFilter();
        let taskBoardFilters = form2js('tasks-board-filters', '.', true);
        enableAll(quickFilterForm);
        getBoards(boardId, savedFilterValue, taskBoardFilters, false, isQuickFilter);
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
        let url = filterPostId ? getBaseURL().concat('dashboard/board_task_post_filters/').concat(boardId).concat("/"+ filterPostId):
            getBaseURL().concat('dashboard/board_task_post_filters/').concat(boardId);
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
        let url = getBaseURL().concat('dashboard/board_save_task_post_filter/')
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
        let boardFilterUrl = getBaseURL().concat('dashboard/board_task_post_filters/');
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
                            ['onclick="taskBoards.addBoardPostFilter('+ boardId +',' + dataItem.id + ')"', _lang.viewEdit],
                            ['onclick="taskBoards.deleteBoardPostFilter('+ dataItem.id + ')"', _lang.delete],
                        ]);
                    }
                },
                {field: "name", title: _lang.name, width: '120px', template: '#= (name!=null ? name : "") #'},
                {field: "field", title: _lang.field, width: '120px', template: '#= taskBoards.getBoardField(field) #'},
                {field: "operator", title: _lang.operator, width: '120px', template: '#= taskBoards.getOperator(operator) #'},
                {field: "value", title: _lang.value, width: '120px', template: '#= taskBoards.getBoardValue(value,field) #'},
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
        let url = getBaseURL().concat('dashboard/board_save_task_post_filter/')
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
                    url: getBaseURL().concat('dashboard/board_task_delete_post_filter/').concat("/" + filterPostId),
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
     * @param taskId  int "the matter id"
     * @param parentColumn object "workflow column"
     * @return {boolean}
     */
    function onAfterDrop(statusFrom, statusTo, taskId, parentColumn) {
        statusFrom = statusFrom.toString();
        statusTo = statusTo.toString();
        taskId = taskId.toString();
        jQuery("#task-id").val(taskId);
        jQuery("#new-status").val(statusTo);
        jQuery("#old-status").val(statusFrom);
        let container = jQuery('#fixed-board-filters');
        let quickFilterForm = jQuery("#tasks-board-filters", container);
        disableEmpty(quickFilterForm);
        disablePostFilter();
        let taskFilters = form2js('tasks-board-filters', '.', true);
        enableAll(quickFilterForm);
        let filterList = jQuery("#grid-filters-list", container);
        let savedFilterValue = filterList.val();
        savedFilterValue = savedFilterValue || filterId;
        let postData = {};
        postData.quickFilter = taskFilters;
        postData.newStatus = taskFilters.newStatus;
        postData.oldStatus = taskFilters.oldStatus;
        postData.taskId = taskFilters.taskId;
        postData.action = 'return_screen';
        postData.savedFilterValue = savedFilterValue;
        jQuery.ajax({
            url: getBaseURL() + 'dashboard/tasks_result/' + boardId + '/' + filterId,
            type: 'POST',
            dataType: 'JSON',
            data: postData,
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.result) {
                    if (typeof response.screen_html !== 'undefined') {
                        screenTransitionFormEvents(taskId, response.transition_id, response.screen_html, 'tasks', function () { taskBoards.quickFilters(); }, true, getBaseURL() + 'tasks/transition_screen_fields/');
                    } else {
                        for (var index  in response.data) {
                            jQuery("#total_number_tasks_" + index).text('');
                            jQuery("#total_number_tasks_" + index).text(response.data[index]);
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
     * @param taskId  int "the task id"
     * @param parentColumn object "workflow column"
     * @return {boolean}
     */
    function onBeforeDragIn(statusFrom, workflowFrom, statusTo, workflowTo, taskId, parentColumn) {
        // if we move an item from one list to another
        if (statusFrom != statusTo) {
            // check status transitions if is allowed to target column status
            let $return = false;
            // load workflow id statusTo
            let taskWorkflow = workflowFrom;
            parentColumn.removeClass('column-transition-highlight');
            if(workflowTo){
                if (jQuery.inArray(taskWorkflow, workflowTo) !== -1) {
                    if (typeof possibleTransitions[taskWorkflow] === 'object') {
                        var array;
                        jQuery.each(possibleTransitions[taskWorkflow], function (index, val) {
                            if (statusFrom == index) {
                                if (typeof val === 'object') {
                                    array = jQuery.map(val, function (value, index) {
                                        return [parseInt(value)];
                                    });
                                } else {
                                    array = val;
                                }
                                if (jQuery.inArray(statusTo, array) !== -1) {
                                    parentColumn.addClass('column-transition-highlight');
                                    $return = true;
                                } else {
                                    $return = false;
                                }
                            }
                        });
                    }

                } else {
                    $return = false;
                }
            }
            return $return;
        }

        return true;
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

    function getFilters(boardIdSelected, filterIdSelected){
        boardIdSelected = boardIdSelected || boardId;
        filterIdSelected = filterIdSelected || filterId;
        let getBoardsURl = getBaseURL().concat('dashboard/tasks/');
        let data = {'action': 'filter', 'filter_id': filterIdSelected, 'task_board_id': boardIdSelected};
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
                    taskBoards.getFilterEvents(container);
                    quickFilters();
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function archiveTasks(taskBoardId) {
        jQuery.ajax({
            url: getBaseURL().concat('dashboard/archiving_task/').concat(taskBoardId),
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.status) {
                    var archivingTaskContainer = "archiving-task-container";
                    jQuery('<div id="archiving-task-container"></div>').appendTo("body");
                    var container = jQuery("#" + archivingTaskContainer);
                    container.html(response.html);
                    initializeModalSize(container, 0.3, 'auto');
                    commonModalDialogEvents(container);
                    jQuery("#archiving-type",container).selectpicker().change(function (e) {
                        let archiveTaskStatus = jQuery(this).val() === 'archive' ? archiveTaskStatusView : [];
                        jQuery("#archive-task_status").val(archiveTaskStatus).selectpicker('refresh');
                        jQuery('.tooltip-title', container).tooltipster('destroy');
                        jQuery('.tooltip-title', container).tooltipster({
                            content: jQuery(this).val() === 'archive' ? tooltipArchiveBoardTask : tooltipHideBoradTask,
                            timer: 22800,
                            animation: 'grow',
                            delay: 200,
                            theme: 'tooltipster-default',
                            touchDevices: false,
                            trigger: 'hover',
                            maxWidth: 350,
                            interactive: true
                        });
                    });
                    jQuery("#archive-task_status",container).selectpicker();
                    jQuery('#filter-closed-on-operator', container).selectpicker();
                    jQuery('.tooltip-title', container).tooltipster({
                        timer: 22800,
                        animation: 'grow',
                        delay: 200,
                        theme: 'tooltipster-default',
                        touchDevices: false,
                        trigger: 'hover',
                        maxWidth: 350,
                        interactive: true
                    });
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
        var filtersForm = jQuery('#archiving-task-container');
        disableEmptyFilter(filtersForm);
        var searchFilters = form2js('archiving-task-container-form', '.', true);
        enableEmptyFilter(filtersForm);
        return searchFilters;
    }

    function archiveTasksSubmit(){
        let archivingTaskContainer = jQuery("#archiving-tasks-container");
        let formData = getArchiveFilters();
        jQuery.ajax({
            url: getBaseURL().concat('dashboard/archiving_task/'),
            dataType: 'JSON',
            type: 'POST',
            data: formData,
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                jQuery('.inline-error', archivingTaskContainer).addClass('d-none');
                if (response.status) {
                    if(response.affected_Rows > 0){
                        confirmationDialog(response.archive_task_status_message, {
                            resultHandler: function () {
                                jQuery.ajax({
                                    url: getBaseURL() + 'dashboard/archiving_task',
                                    dataType: 'JSON',
                                    type: 'POST',
                                    data: {archiveAction : true, formData: formData},
                                    success: function (response) {
                                        if(response.message){
                                            pinesMessageV2({ty: response.result ? 'success' : 'error', m: response.message});
                                        }
                                        let archivingTaskContainer = jQuery("#archiving-task-container");
                                        jQuery('.modal', archivingTaskContainer).modal('hide');

                                        quickFilters();
                                    },
                                    error: defaultAjaxJSONErrorsHandler
                                });
                            },
                            isKeyJs: true
                        })
                    } else{
                        pinesMessageV2({ty: 'information', m: _lang.noTasksToArchive});
                    }
                }else {
                    displayValidationErrors(response.validationErrors, archivingTaskContainer);
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function quickFilterTimeOut(isQuickFilter, time){
        isQuickFilter = isQuickFilter || false;
        time = time || 400;
        setTimeout(function (){
            quickFilters(isQuickFilter);
        }, time);
    }

    function clientTypeChange(val){
        if(val) quickFilterTimeOut(true);
    }

    let userClear = function _userClear(){
        quickFilterTimeOut(true);
    }

    let userSelect = function _userSelect(){
        quickFilterTimeOut(true);
    }

    let casesClear = function _userClear(){
        quickFilterTimeOut(true);
    }

    let casesSelect = function _userSelect(){
        quickFilterTimeOut(true);
    }

    function saveSearchFilters() {
        var saveFilterContainer = jQuery('.save-filter-container');
        var tasksBoardFilters = jQuery("#tasks-board-filters");
        jQuery('#filter-name', saveFilterContainer).val('');
        jQuery('.validation-error-container', saveFilterContainer).addClass('d-none');
        saveFilterContainer.removeClass('d-none');
        jQuery(document).keyup(function (e) {
            if (e.keyCode == 27) {
                jQuery('.modal', saveFilterContainer).modal('hide');
            }
        });
        jQuery('.modal', saveFilterContainer).modal({
            keyboard: false,
            show: true,
            backdrop: 'static'
        });
        jQuery("#save-filter-submit").click(function () {
            jQuery.ajax({
                beforeSend: function () {
                    jQuery('.loader-submit', saveFilterContainer).addClass('loading');
                    jQuery('#save-filter-submit', saveFilterContainer).attr('disabled', 'disabled');
                },
                data: {'savedForm': jQuery('#filter-name', saveFilterContainer).val(), 'taskBoardId': boardId,
                    'dueDate': jQuery('#due-date-id', tasksBoardFilters).val(), 'createdOn': jQuery('#created-on', tasksBoardFilters).val(),
                    'casesListValues': jQuery('#legal_case_id', tasksBoardFilters).val(), 'providerGroupsList': jQuery('#provider-groups-list', tasksBoardFilters).val(),
                    'contractListValues': jQuery('#contract_id', tasksBoardFilters).val(),
                    'taskId': jQuery('#task-id', tasksBoardFilters).val(), 'newStatus': jQuery('#new-status', tasksBoardFilters).val(),
                    'usersList': jQuery('#assigned-to-id', tasksBoardFilters).val()
                },
                dataType: 'JSON',
                type: 'POST',
                url: getBaseURL() + 'dashboard/task_board_save_search_filters',
                success: function (response) {
                    jQuery('.inline-error', saveFilterContainer).addClass('d-none');
                    if (response.result) {
                        jQuery('.modal', saveFilterContainer).modal('hide');
                        pinesMessageV2({ty: 'success', m: _lang.filterSavedSuccessfully});
                        var savedFiltersList = jQuery('#grid-filters-list');
                        if (jQuery('#savedFilters option').length <= 1) { // no saved reports
                            jQuery('div.showHide').removeClass('d-none');
                        }
                        savedFiltersList.append("<option value=" + response.id + ">" + response.keyName + "</option>");
                        jQuery(".delete-single-task-filter").attr('onclick','taskBoards.deleteSavedFilter('+ response.id +',true)');
                        jQuery('.delete-single-task-filter').removeClass('d-none');
                        savedFiltersList.val(response.id).selectpicker('refresh');
                    } else {
                        displayValidationErrors(response.validationErrors, saveFilterContainer);
                    }
                }, complete: function () {
                    jQuery('.loader-submit', saveFilterContainer).removeClass('loading');
                    jQuery('#save-filter-submit', saveFilterContainer).removeAttr('disabled');

                },
                error: defaultAjaxJSONErrorsHandler
            });
        });
        jQuery("input", saveFilterContainer).keypress(function (e) {
            if (e.which == 13) {
                e.preventDefault();
                jQuery("#save-filter-submit").click();
            }
        });
        jQuery('.modal-body').on("scroll", function() {
            jQuery('.bootstrap-select.open').removeClass('open');
        });
        jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
            jQuery('#filter-name', saveFilterContainer).focus();

        });
        ctrlS(function () {
            jQuery("#save-filter-submit").click();
        });
        resizeMiniModal(saveFilterContainer)
    }

    function deleteSavedFilter(filterId, single){
        single = single || false;
        let getBoardsURl = getBaseURL().concat('dashboard/task_board_delete_saved_reports/');
        let data = {'filter_id': filterId, 'single': single, 'board_id': boardId};
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
                    pinesMessageV2({ty: 'success', m: response.message});
                    if(single){
                        var itemSelectorOption = jQuery('#grid-filters-list option:selected');
                        itemSelectorOption.remove();
                    } else{
                        jQuery('#grid-filters-list').find('option').remove().end().append('<option value="">'+ savedFiltersNone +'</option>');
                        jQuery("#delete-filter-li").addClass('d-none');
                    }
                    jQuery("#grid-filters-list").val("");
                    jQuery("#grid-filters-list").selectpicker('refresh');
                    jQuery('.delete-single-task-filter').addClass('d-none');
                    quickFilters();
                } else{
                    pinesMessageV2({ty: 'error', m: response.message});
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function resetFilters(reloadColmuns){
        reloadColmuns = reloadColmuns || false;
        jQuery('input.filter-value-inputs-change').each(function (index, value){
            if(jQuery(value).val() != "" && jQuery(value).val() != null){
                jQuery(value).val("");
            }
        });
        jQuery('select.filter-value-inputs-change').each(function (index, value){
            if(jQuery(value).val() != "" && jQuery(value).val() != null){
                jQuery(value).val("").selectpicker('refresh');
            }
        });
        jQuery('#caseLookup', jQuery("#fixed-board-filters")).typeahead('val','');
        jQuery('#assigned-to-lookup', jQuery("#fixed-board-filters")).typeahead('val','');
        jQuery('#created-on').val("").bootstrapDP("update");
        jQuery('#due-date-id').val("").bootstrapDP("update");
        if(!reloadColmuns) quickFilters(true);
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
        archiveTasks: archiveTasks,
        clientTypeChange: clientTypeChange,
        quickFilterTimeOut: quickFilterTimeOut,
        userClear: userClear,
        userSelect: userSelect,
        archiveTasksSubmit: archiveTasksSubmit,
        casesSelect: casesSelect,
        casesClear: casesClear,
        saveSearchFilters: saveSearchFilters,
        deleteSavedFilter: deleteSavedFilter,
        resetFilters: resetFilters
    };
}());

jQuery(document).ready(function () {
    let container = jQuery("#fixed-board-filters");
    helpers.flexWrapped(jQuery('.post-filter-value-container'), taskBoards.onWrappedFilter);
});

var timeOutFunctionId;
function workAfterResizeIsDone() {
    helpers.flexWrapped(jQuery('.post-filter-value-container'), taskBoards.onWrappedFilter);
}
window.addEventListener("resize", function() {
    clearTimeout(timeOutFunctionId);
    timeOutFunctionId = setTimeout(workAfterResizeIsDone, 1000);
});
