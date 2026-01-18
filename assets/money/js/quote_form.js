var quoteForm = jQuery("#quoteForm");
var timeLogsResponse, expensesResponse = {}, relatedCasesResponse, checkedExpenses, checkedTimeLogs, checkedCases = [], checkedCasesIds = [], expensesIds = [] , checkedCasesIdsToSave= [];
var quote_details, quote_details_row, activateDiscount, activateTax, clientId, clientAccountId, haveCheckedCases = false, removeCases = false, displayTimeLogItemByUserCode, invoiceLanguageLabels;

function relateMatters(voucherHeaderId) {
    voucherHeaderId = voucherHeaderId || false;
    clientId = jQuery('#client-model-id', quoteForm).val();
    clientAccountId = jQuery('#client_id', quoteForm).val();
    if (clientId || voucherHeaderId) {
        var data;
        if (voucherHeaderId && checkedCases.length < 1 && !removeCases) { //view cases for edit quote
            data = {voucher_header_id: voucherHeaderId, client_id: clientId, return: 'chosen_cases'};
        } else
        if (typeof checkedCases !== 'undefined' && checkedCases.length > 0) { //view chosen cases
            data = {chosen_cases: checkedCases, client_id: clientId, return: 'chosen_cases'};
        } else { //return all case related to this client
            data = {client_id: clientId, return: 'cases'}
        }
        jQuery.ajax({
            url: getBaseURL('money') + 'vouchers/relate_matters_to_quote',
            dataType: "json",
            type: 'GET',
            data: data,
            beforeSend: function () {
                jQuery("#loader-global").show();
            },
            success: function (response) {
                if (typeof response.related_cases !== 'undefined' && response.related_cases.length < 1) {
                    pinesMessage({ty: 'warning', m: _lang.feedback_messages.noRelatedCases});
                    return false;
                } else {
                    relatedCasesResponse = response.related_cases;
                    if (response.html) {
                        if (jQuery('#relate-matters-container').length <= 0) {
                            jQuery("<div id='relate-matters-container'></div>").appendTo("body");
                            var relateMattersContainer = jQuery('#relate-matters-container');
                            relateMattersContainer.html(response.html);
                            commonModalDialogEvents(relateMattersContainer);
                            if (voucherHeaderId && typeof relatedCasesResponse === 'undefined') { // display matters selected in edit form(if not draft)
                                jQuery('#submit-actions', relateMattersContainer).on('click', function (e) {
                                    jQuery(".modal", relateMattersContainer).modal("hide");
                                });
                                initializeModalSize(relateMattersContainer);
                            } else {
                                haveCheckedCases = jQuery('[data-field="record-checkbox"]:checked', relateMattersContainer).length > 0;
                                initializeModalSize(relateMattersContainer, 0.8);
                                initializeSelectAllCheckbox(relateMattersContainer);
                                jQuery('#submit-actions', relateMattersContainer).on('click', function (e) {
                                    actionsEvents(relateMattersContainer);
                                });
                            }
                        }
                    }
                }
            }, complete: function () {
                jQuery("#loader-global").hide();
            }
            , error: defaultAjaxJSONErrorsHandler
        });
    }
    else {
        pinesMessage({ty: 'warning', m: _lang.feedback_messages.clientNotChosen});
    }
}

function actionsEvents(relateMattersContainer) {
    var actions = jQuery('#submit-actions', relateMattersContainer).attr('action');
    switch (actions) {
        case 'submit-cases':
            var count_checked = jQuery('[data-field="record-checkbox"]:checked', relateMattersContainer).length > 0;
            if (!count_checked) {
                if (!haveCheckedCases) {
                    pinesMessage({ty: 'warning', m: _lang.feedback_messages.caseNotChosen});
                    return false;
                } else {
                    removeCases = true;
                    jQuery(".modal", relateMattersContainer).modal("hide");
                    // delete all old expenses
                    emptyData('expenses');
                    // delete all old timelogs
                    emptyData('timelogs');
                    addDefaultItem();
                    checkedCases = [];
                    jQuery('#related-cases', quoteForm).val('');
                    jQuery('#related-cases-data', quoteForm).html('<p class="padding-5 no-margin">' + _lang.noRelatedMatters + '</p>');
                    return true;
                }
            }
            selectedCases = [];
            selectedCases = jQuery('form#relate-matters-form', relateMattersContainer).serializeArray();
            var formData = jQuery('form#relate-matters-form', relateMattersContainer).serializeArray();
            formData.push({name: 'client_id', value: clientId}, {name: 'client_account_id', value: clientAccountId}, {name: 'return', value: 'expenses_and_time_logs'});
            jQuery.ajax({
                url: getBaseURL('money') + 'vouchers/relate_matters_to_quote',
                dataType: "json",
                type: 'GET',
                data: formData,
                beforeSend: function () {
                    jQuery("#loader-global").show();
                },
                success: function (response) {
                    expensesResponse = {};
                    timeLogsResponse = {};
                    if (jQuery.isEmptyObject(response.expenses.data) && jQuery.isEmptyObject(response.time_logs.data)) {
                        jQuery('#relate-matters-form', relateMattersContainer).html(_lang.noExpensesTimeLogsRelatedCase);
                        jQuery('#submit-actions', relateMattersContainer).html(_lang.ok);
                        jQuery('#submit-actions', relateMattersContainer).attr('action', 'no-actions');
                        addDefaultItem();
                        return false;
                    }
                    if (!jQuery.isEmptyObject(response.expenses.data)) {
                        if (jQuery.isEmptyObject(response.time_logs.data)) {
                            jQuery('#submit-actions', relateMattersContainer).html(_lang.add);
                        }
                        jQuery('#submit-actions', relateMattersContainer).attr('action', 'next-action');
                        jQuery('#relate-matters-form', relateMattersContainer).html(response.expenses.html);
                        jQuery('#title', relateMattersContainer).html(response.expenses.title);
                        expensesResponse = response.expenses;
                    } else {
                        jQuery('#submit-actions', relateMattersContainer).attr('action', 'submit-form');
                        jQuery('#submit-actions', relateMattersContainer).html(_lang.add);
                        jQuery('#relate-matters-form', relateMattersContainer).html(response.time_logs.html);
                        jQuery('#title', relateMattersContainer).html(response.time_logs.title);
                    }
                    timeLogsResponse = response.time_logs;
                    initializeSelectAllCheckbox(relateMattersContainer);
                }, complete: function () {
                    jQuery("#loader-global").hide();
                }, error: defaultAjaxJSONErrorsHandler
            });
            break;
        case 'next-action':
            expensesIds = [];
            checkedExpenses = [];
            var trParent;
            jQuery('[name="caseExpensesIds"]:checked', relateMattersContainer).each(function () {
                trParent = jQuery(this).parent().parent();
                if (jQuery('td.billingStatus', trParent).attr('expbillingstatus') === 'not-set') {
                    expensesIds.push(jQuery(this).val());
                }
                if (jQuery('td.billingStatus', trParent).attr('expbillingstatus') === 'to-invoice') {
                    checkedExpenses.push(jQuery(this).val());
                }
            });
            if (!jQuery.isEmptyObject(timeLogsResponse.data)) {
                jQuery('#relate-matters-form', relateMattersContainer).html(timeLogsResponse.html);
                initializeSelectAllCheckbox(relateMattersContainer);
                jQuery('#title', relateMattersContainer).html(timeLogsResponse.title);
                jQuery('#submit-actions', relateMattersContainer).attr('action', 'submit-form');
            } else {
                if (!jQuery.isEmptyObject(expensesIds)) {
                    converExpenseBillingStatus();
                }
                injectCaseItems();
                saveMatterSelected();
            }
            break;
        case 'submit-form':
            if (!jQuery.isEmptyObject(timeLogsResponse.data)) {
                checkedTimeLogs = [];
                jQuery('[name="timeLogsIds"]:checked', relateMattersContainer).each(function () {
                    checkedTimeLogs.push(jQuery(this).val());
                });
            }
            if (!jQuery.isEmptyObject(expensesIds)) {
                converExpenseBillingStatus();
            }
            displayTimeLogItemByUserCode = jQuery('input[name=display_time_logs_item_by_user_code]:checked', '#relate-matters-container').val();
            jQuery('#display-time-logs-item-by-user-code', quoteForm).val(displayTimeLogItemByUserCode);
            displayTimeLogItemByUserCode = displayTimeLogItemByUserCode == '0' ? false : true;
            let timeLogsFilterContainer = jQuery('#time-logs-date-filter-tax-discount-container');
            let taxValue=  jQuery("#tax-filter",timeLogsFilterContainer).val();
            let discountValue = jQuery("#discount-filter",timeLogsFilterContainer).val();
            injectCaseItems(taxValue, discountValue);
            saveMatterSelected();
            break;
        case 'no-actions':
            // delete all old expenses
            emptyData('expenses');
            // delete all old timelogs
            emptyData('timelogs');
            addDefaultItem();
            calTotal();
            saveMatterSelected();
            jQuery(".modal", relateMattersContainer).modal("hide");
            break
    }
}
function saveMatterSelected(){
    checkedCasesIds = [];
    jQuery.each(selectedCases, function (index, val) {
        checkedCasesIds.push(val['value']);
    });
    if (typeof relatedCasesResponse !== 'undefined') {
        checkedCases = [];
        relatedCases = [];
        var casesHtml = '';
        var caseCount = 0;
        jQuery.each(relatedCasesResponse, function (i, value) {
            if (jQuery.inArray(value['id'], checkedCasesIds) !== -1) {
                caseCount++;
                checkedCases.push(value);
                relatedCases.push(value['id']);
                casesHeader = '<tr><th>'+_lang.case+'</th><th>'+_lang.caseSubject+'</th><th>'+_lang.assignee+'</th><th>'+_lang.caseType+'</th></tr>';
                casesHtml += '<tr><td><a title="' + value.subject + '" href="' + (value.case_category == "IP" ? "intellectual_properties" : "cases") + '/edit/' + value.id + '" class="btn btn-default btn-link no-padding padding-5 tooltip-title">' + (value.case_category == "IP" ? value.id : value.case_id) + '</a></td>'
                            +'<td>'+value.subject+'</td>'
                            +'<td>'+(value.assignee == null ? "" : value.firstName +' '+ value.lastName)+'</td>'
                            +'<td>'+value.practice_area+'</td></tr>';
                casesHtmlTable = casesHeader + casesHtml;            }
        });
    }
    jQuery('#related-cases', quoteForm).val(relatedCases);
    jQuery('#related-cases-data', quoteForm).html(casesHtmlTable);
}
function initializeSelectAllCheckbox(container) {
    jQuery('#select-all', container).click(function () {
        jQuery('[data-field="record-checkbox"]', container).each(function () {
            this.checked = jQuery('#select-all', container).is(':checked') ? true : false;
        });
    });
}
function injectCaseItems(timeLogTaxFilter,timeLogDiscountFilter) {
    timeLogTaxFilter = timeLogTaxFilter || '';
    timeLogDiscountFilter = timeLogDiscountFilter || '';
      // delete all old expenses
      emptyData('expenses');
      // delete all old timelogs
      emptyData('timelogs');
     // empty items if no choose
     if(jQuery("[base_price]").length == 0){
         jQuery("#quote-details-items").html("");
     }
    jQuery("#loader-global").show();
    $items = '';
    $i =  jQuery("[base_price]").length + 1 ;
    var colspan = 5;
    if (activateDiscount)
        colspan++;
    if (activateTax)
        colspan++;
    var expenses = expensesResponse.data;
    var timeLogs = timeLogsResponse.data;
    if (!jQuery.isEmptyObject(expenses)) {
        if (!jQuery.isEmptyObject(checkedExpenses)) {
            $items += '<tr class="head-expenses"><td><strong>' + invoiceLanguageLabels['expenses'] + '</strong></td></tr>';
            $items += jQuery('#expenses-thead').html();
        }
        for (i in expenses) {
            if (!jQuery.isEmptyObject(checkedExpenses) && jQuery.inArray(expenses[i]['expenseID'], checkedExpenses) !== -1) {
                var unitPrice = (expenses[i]['amount'] == '.00') ? '0.00' : expenses[i]['amount'];
                $items += '<tr expense_id="' + parseInt(expenses[i]['expenseID']) + '"  id="' + $i + '" base_price="' + expenses[i]['amount'] + '" currency_id="' + expenses[i]['currency_id'] + '">';
                $items += '<td><input type="text" readonly = "readonly" value="' + expenses[i]['dated'] + '" class="form-control"/><input type="hidden" name="item_date[]" autocomplete="Off"  value="' + expenses[i]['dated'] + '"/></td>';
                $items += '<td style="width:20%">';
                $items += '<select style="display:none;" id="itemIds_' + $i + '" name="itemIds[]"><option value=""></option></select>';
                $items += '<input type="hidden" id="expense_' + $i + '" name="expenseIds[]" value="' + expenses[i]['expenseID'] + '"/>';
                $items += '<input type="hidden" id="timeLogs_' + $i + '" name="timeLogsIds[]" value=""/>';
                $items += '<input type="hidden" id="record_' + $i + '" name="record[' + $i + ']" value=""/>';
                $items += '<input type="hidden" id="account_' + $i + '" name="accountIds[]" value="' + expenses[i]['expense_account'] + '"/>';
                $items += '<input type="text" readonly="" class="form-control form-control-medium" name="item[]" id="item_' + $i + '" value="' + expenses[i]['expensesCategoryName'] + '">';
                $items += '</td>';
                $items += '<td><select id = "subItemIds_' + $i + '" readonly = "readonly" class="form-control" name = "subItemIds[]"><option value="">' + _lang.chooseSubItem + ' - ' + _lang.noData + '</option></select></td>';
                $description = (typeof expenses[i]['description'] !== 'string' || expenses[i]['description'] === '') ? expenses[i]['dated'] : expenses[i]['dated'] + ' - ' + expenses[i]['description'];
                $items += '<td><textarea name="description[]" id="description_' + $i + '" class="no-margin form-control" required="required" data-validation-engine="validate[required]" >' + $description + '</textarea></td>';
                $items += '<td><input type="hidden" name="quantities[]" id="quantities_' + $i + '" autocomplete="Off"  value=""/><input type="text" name="quantity[]" id="quantity_' + $i + '" onblur="calAmount(' + $i + ');"  class="no-margin form-control" autocomplete="Off" required="required" value="1" data-validation-engine="validate[required,funcCall[validateDecimal],funcCall[checkQuatityMaxDigitsNum]]" onkeypress="handle(event);"/></td>';
                $items += '<td>';
                $items += '<div class="input-group">';
                $items += '<span class="input-group-addon p-1 currencyCode" id="currencyCode">' + (jQuery.isEmptyObject(expenses[i]['clientAccountCurrency']) === true ? jQuery("#clientCurrency").html() : expenses[i]['clientAccountCurrency']) + '</span>';
                $items += '<input type="text" name="unitPrice[]" id="unitPrice_' + $i + '" onblur="calAmount(' + $i + ');" autocomplete="Off" class="no-margin form-control" required="required" value="' + round(unitPrice, 2) + '" data-validation-engine="validate[required,funcCall[validateDecimal],funcCall[checkPriceMaxDigitsNum]]" onkeypress="handle(event);"/>';
                $items += '</div></td>';
                if (activateDiscount)
                    $items += '<td>' + jQuery('#discountsDiv', quote_details_row).html() + '</td>';
                if (activateTax)
                    $items += '<td>' + jQuery('#taxesDiv', quote_details_row).html() + '</td>';
                var itemAmount = (expenses[i]['amount'] == '.00') ? '0.00' : expenses[i]['amount'];
                $items += '<td><span class="amount_' + $i + '">' + itemAmount + '</span><input type="hidden" id="amount_' + $i + '" value=""/><a class="pull-right" href="javascript:;" onclick="deleteRow(jQuery(this), event);calTotal();"><i class="fa-solid fa-trash"></i></a></td>';
                $items += '</tr>';
                jQuery("#quote-details-expenses").append($items);
                jQuery('#taxesSelect', "#quote-details-expenses").attr('id', 'taxIds_' + $i).attr('onchange', 'setTaxData(this,' + $i + ');');
                jQuery('#percentageInput', "#quote-details-expenses").attr('id', 'percentage_' + $i);
                jQuery('#discountsSelect', "#quote-details-expenses").attr('id', 'discountIds_' + $i).attr('onchange', 'setDiscountData(this,' + $i + ');');
                jQuery('#discountInputHiden', "#quote-details-expenses").attr('id', 'discountInput' + $i).attr('onblur', 'setDiscountDataOthers(this,' + $i + ');').attr('onkeypress', 'otherDiscountKeypressHandle(event,this,' + $i + ')');
                jQuery('#iconHiden', "#quote-details-expenses").attr('id', 'icon' + $i).attr('onclick', 'deleteDiscountInput(jQuery(this), event,' + $i + ');');
                jQuery('#discountpercentageInput', "#quote-details-expenses").attr('id', 'discountPercentage_' + $i);
                $items = '';
                calAmount($i);
                $i++;
            }
        }
        if (jQuery.isEmptyObject(checkedExpenses) === false) {
            jQuery("#quote-details-expenses").append('<tr><td colspan="' + colspan + '"></td><td colspan="1"><strong>' + _lang.expensesSubTotal + ' :</strong></td><td  id="totalExpenses"><strong></strong></td></tr>');
        }
        recalculation_all_unitPrices();
        calTotal();
    }
    if (!jQuery.isEmptyObject(timeLogs)) {
        if (!jQuery.isEmptyObject(checkedTimeLogs)) {
            $items += '<tr class="head-time-logs"><td><strong>' + invoiceLanguageLabels['time_logs'] + '</strong></td></tr>';
            $items += jQuery('#time-logs-thead').html();
        }
        if (jQuery('#groupByLegPrac:checked').length) {
            jQuery('#groupTimeLogsByUserInExport', quoteForm).val('1');
            jQuery("#loader-global").show();
            $userActivityLogs = timeLogs;
            $timeLogsCheckedIds = checkedTimeLogs;
            $timeLogsArr = [];
            // group time logs
            jQuery.each($userActivityLogs, function (index, value) {
                if (jQuery.isArray($timeLogsCheckedIds) && jQuery.inArray(value['id'], $timeLogsCheckedIds) != -1) {
                    $timeLogComment = value['comments'];
                    $userActivityLogs[index]['comments'] = (value['timeTypeName'] ? value['timeTypeName']+' - ' : '') + value['logDate'] + ' - ' + parseFloat(value['effectiveEffort']) + ' '  + (value['comments'] ? (' - ' + value['comments']) : '');
                    if ((value['worker'] + value['legal_case_id']) in $timeLogsArr) {
                        if ($timeLogsArr[value['worker'] + value['legal_case_id']]['ratePerHour'] == value['ratePerHour']) {
                            $timeLogsArr[value['worker'] + value['legal_case_id']].effectiveEffort.push(value['effectiveEffort']);
                            $timeLogsArr[value['worker'] + value['legal_case_id']].description_per_log.push($timeLogComment);
                            $timeLogsArr[value['worker'] + value['legal_case_id']].date_per_log.push(value['logDate']);
                            $timeLogsArr[value['worker'] + value['legal_case_id']]['comments'] += "\n" + $userActivityLogs[index]['comments'];
                            $timeLogsArr[value['worker'] + value['legal_case_id']].id.push(value['id']);
                            delete $userActivityLogs[index];
                        }
                    } else {
                        value['id'] = [value['id']];
                        value['effectiveEffort'] = [value['effectiveEffort']];
                        value['description_per_log'] = [$timeLogComment];
                        value['date_per_log'] = [value['logDate']];
                        $timeLogsArr[value['worker'] + value['legal_case_id']] = value;
                        delete $userActivityLogs[index];
                    }
                } else {
                    delete $userActivityLogs[index];
                }
            });
            $userActivityLogs = [];
            if (Object.keys($timeLogsArr).length != 0) {
                for (var key in $timeLogsArr) {
                    if ($timeLogsArr.hasOwnProperty(key)) {
                        var sorted = $timeLogsArr[key]["date_per_log"].slice() // copy the array for keeping original array with order
                                .sort(function (a, b) {// sort by parsing them to date
                                    return new Date(a) - new Date(b);
                                });
                        $timeLogsArr[key]["date_per_log"] = json_encode($timeLogsArr[key]["date_per_log"]);
                        $timeLogsArr[key]["logDate"] = sorted.pop();//show the latest date for the grouped time logs
                        $userActivityLogs.push($timeLogsArr[key]);
                    }
                }
            }
            // render html and events
            timeLogs = $userActivityLogs;
            for (i in timeLogs) {
                var ratePerHour = (timeLogs[i]['ratePerHour'] == '.00') ? '0.00' : timeLogs[i]['ratePerHour'];
                if (ratePerHour == 0) {
                    ratePerHourEmpty = true;
                }
                $items += '<tr  timelogs_id="' + timeLogs[i]['id'] + '" id="' + $i + '" base_price="' + timeLogs[i]['ratePerHour'] + '" currency_id="' + $organizationCurrencyID + '">';
                $items += '<td> <input type="text" readonly = "readonly"  value="' + timeLogs[i]['logDate'] + '" class="form-control"/><input type="hidden" name="item_date[]" autocomplete="Off"  value="' + timeLogs[i]['logDate'] + '"/></td>';
                $items += '<td style="width:20%">';
                $items += '<select style="display:none;" id="itemIds_' + $i + '" name="itemIds[]"><option value=""></option></select>';
                $items += '<input type="hidden" id="expense_' + $i + '" name="expenseIds[]" value=""/>';
                var ids = [];
                var effectiveEffort = [];
                var totalEffectiveEffort = 0.00;
                timeLogs[i]['id'].forEach(function (entry, index) {
                    ids.push(parseInt(entry));
                    effectiveEffort.push(parseFloat(timeLogs[i]['effectiveEffort'][index]));
                    totalEffectiveEffort = parseFloat(totalEffectiveEffort) + parseFloat(timeLogs[i]['effectiveEffort'][index]);
                    totalEffectiveEffort = totalEffectiveEffort.toFixed(2);
                });
                var descLog = '';
                for (x in timeLogs[i]['description_per_log']){
                    descLog = descLog + ';;;' + timeLogs[i]['description_per_log'][x];
                }
                if(descLog){
                    descLog = descLog.substring(3);
                }
                $items += '<input type="hidden" id="timeLogs_' + $i + '" name="timeLogsIds[]" value="' + JSON.stringify(ids) + '"/>';
                $items += '<input type="hidden" name="description_per_log[]" value="' + (descLog) + '"/>';
                $items += '<input type="hidden" name="date_per_log[]" value="' + JSON.parse(timeLogs[i]['date_per_log']) + '"/>';
                $items += '<input type="hidden" name="user_id[]" value="' + timeLogs[i]['user_id'] + '"/>';
                $items += '<input type="hidden" id="record_' + $i + '" name="record[' + $i + ']" value=""/>';
                $items += '<input type="hidden" id="account_' + $i + '" name="accountIds[]" value="' + $salesAccount + '"/>';
                $items += '<input type="hidden" id="record_' + $i + '" name="record[' + $i + ']" value=""/>';
                $items += '<input type="text" readonly="readonly" class="form-control form-control-medium" name="item[]" id="item_' + $i + '" value="' + (displayTimeLogItemByUserCode ? timeLogs[i]['user_code'] : timeLogs[i]['worker']) + '">';
                $items += '</td>';
                $items += '<td><select id = "subItemIds_' + $i + '" readonly = "readonly" class="form-control" name = "subItemIds[]"><option value="">' + _lang.chooseSubItem + ' - ' + _lang.noData + '</option></select></td>';
                $comments = timeLogs[i]['comments'];
                $items += '<td><textarea readonly="readonly" name="description[]" id="description_' + $i + '" class="no-margin form-control" required="required" data-validation-engine="validate[required]" >' + $comments + '</textarea></td>';
                $items += '<td><input type="hidden" name="quantities[]" id="quantities_' + $i + '" autocomplete="Off"  value="' + JSON.stringify(effectiveEffort) + '"/><input readonly="readonly" type="text" name="quantity[]" id="quantity_' + $i + '" autocomplete="Off" onblur="calAmount(' + $i + ');" class="no-margin form-control" required="required" value="' + totalEffectiveEffort + '" data-validation-engine="validate[required,funcCall[validateDecimal],funcCall[checkQuatityMaxDigitsNum]]" onkeypress="handle(event);"/></td>';
                $items += '<td>';
                $items += '<div class="input-group">';
                $items += '<span class="input-group-addon p-1 currencyCode" id="currencyCode">' + jQuery("#clientCurrency").html() + '</span>';
                $items += '<input type="text" readonly="readonly" name="unitPrice[]" id="unitPrice_' + $i + '" onblur="calAmount(' + $i + ');" autocomplete="Off" class="no-margin form-control" required="required" value="' + round(ratePerHour, 2) + '" data-validation-engine="validate[required,funcCall[validateDecimal],funcCall[checkPriceMaxDigitsNum]]" onkeypress="handle(event);"/>';
                $items += '</div></td>';
                if (activateDiscount)
                    $items += '<td>' + jQuery('#discountsDiv', quote_details_row).html() + '</td>';
                if (activateTax)
                    $items += '<td>' + jQuery('#taxesDiv', quote_details_row).html() + '</td>';
                var activityLogsAmount = (timeLogs[i]['amount'] == '.00') ? '0.00' : timeLogs[i]['amount'];
                $items += '<td><span class="amount_' + $i + '">' + activityLogsAmount + '</span><input type="hidden" id="amount_' + $i + '" value=""/><a class="pull-right" href="javascript:;" onclick="deleteRow(jQuery(this), event);calTotal();"><i class="fa-solid fa-trash"></i></a></td>';
                $items += '</tr>';
                jQuery("#quote-details-timelogs").append($items);
                jQuery('#taxesSelect', "#quote-details-timelogs").attr('id', 'taxIds_' + $i).attr('onchange', 'setTaxData(this,' + $i + ');');
                jQuery('#taxIds_' + $i, "#quote-details-timelogs").val(timeLogTaxFilter);
                jQuery('#percentageInput', "#quote-details-timelogs").attr('id', 'percentage_' + $i);
                jQuery('#discountsSelect', "#quote-details-timelogs").attr('id', 'discountIds_' + $i).attr('onchange', 'setDiscountData(this,' + $i + ');');
                jQuery('#discountIds_' + $i, "#quote-details-timelogs").val(timeLogDiscountFilter);
                jQuery('#discountInputHiden', "#quote-details-timelogs").attr('id', 'discountInput' + $i).attr('onblur', 'setDiscountDataOthers(this,' + $i + ');').attr('onkeypress', 'otherDiscountKeypressHandle(event,this,' + $i + ')');
                jQuery('#iconHiden', "#quote-details-timelogs").attr('id', 'icon' + $i).attr('onclick', 'deleteDiscountInput(jQuery(this), event,' + $i + ');');
                jQuery('#discountpercentageInput', "#quote-details-timelogs").attr('id', 'discountPercentage_' + $i);
                $items = '';
                setTaxData(jQuery('#taxIds_' + $i, jQuery("#quote-details-timelogs")), $i);
                setDiscountData(jQuery('#discountIds_' + $i, jQuery("#quote-details-timelogs")), $i);
                calAmount($i);
                count = ++$i;
            }
            // final preparation
            jQuery("#loader-global").hide();
            if (!jQuery.isEmptyObject(checkedTimeLogs)) {
                if (ratePerHourEmpty == true) {
                    pinesMessage({ty: 'warning', m: _lang.defaultRateNotSet});
                }
                jQuery("#quote-details-timelogs").append('<tr><td colspan="' + colspan + '"></td><td colspan="1"><strong>' + _lang.timeLogsSubTotal + ' :</strong></td><td  id="totalLogs"><strong></strong></td></tr>');
            }
            recalculation_all_unitPrices();
            calTotal();
        } else {
            jQuery('#groupTimeLogsByUserInExport', quoteForm).val('0');
            for (i in timeLogs) {
                if (jQuery.isEmptyObject(checkedTimeLogs) === false && jQuery.inArray(timeLogs[i]['id'], checkedTimeLogs) !== -1) {
                    var ratePerHour = (timeLogs[i]['ratePerHour'] == '.00') ? '0.00' : timeLogs[i]['ratePerHour'];
                    $items += '<tr  timelogs_id="' + timeLogs[i]['id'] + '" id="' + $i + '" base_price="' + timeLogs[i]['ratePerHour'] + '" currency_id="' + $organizationCurrencyID + '">';
                    $items += '<td> <input type="text" readonly = "readonly" value="' + timeLogs[i]['logDate'] + '" class="form-control"/><input type="hidden" name="item_date[]" autocomplete="Off"  value="' + timeLogs[i]['logDate'] + '"/></td>';
                    $items += '<td style="width:20%">';
                    $items += '<select style="display:none;" id="itemIds_' + $i + '" name="itemIds[]"><option value=""></option></select>';
                    $items += '<input type="hidden" id="expense_' + $i + '" name="expenseIds[]" value=""/>';
                    var ids = [];
                    ids.push(parseInt(timeLogs[i]['id']));
                    var effectiveEffort = [];
                    effectiveEffort.push(parseFloat(timeLogs[i]['effectiveEffort']));
                    $items += '<input type="hidden" id="timeLogs_' + $i + '" name="timeLogsIds[]" value="' + JSON.stringify(ids) + '"/>';
                    $items += '<input type="hidden" name="description_per_log[]" value="' + timeLogs[i]['comments'] + '"/>';
                    $items += '<input type="hidden" name="date_per_log[]" value="' + timeLogs[i]['logDate'] + '"/>';
                    $items += '<input type="hidden" name="user_id[]" value="' + timeLogs[i]['user_id'] + '"/>';
                    $items += '<input type="hidden" id="record_' + $i + '" name="record[' + $i + ']" value=""/>';
                    $items += '<input type="hidden" id="account_' + $i + '" name="accountIds[]" value="' + $salesAccount + '"/>';
                    $items += '<input type="text" readonly="" class="form-control form-control-medium" name="item[]" id="item_' + $i + '" value="' + (displayTimeLogItemByUserCode ? timeLogs[i]['user_code'] : timeLogs[i]['worker']) + '">';
                    $items += '</td>';
                    $items += '<td><select id = "subItemIds_' + $i + '" readonly = "readonly" class="form-control" name = "subItemIds[]"><option value="">' + _lang.chooseSubItem + ' - ' + _lang.noData + '</option></select></td>';
                    $comments = ((timeLogs[i]['timeTypeName'] !== undefined && timeLogs[i]['timeTypeName'] !== null) ? (timeLogs[i]['timeTypeName']+' - ' ): '' ) + (((timeLogs[i]['comments'])?timeLogs[i]['comments']:timeLogs[i]['logDate'] + ' - ' + parseFloat(timeLogs[i]['effectiveEffort'])));
                    $items += '<td><textarea name="description[]" id="description_' + $i + '" class="no-margin form-control" required="required" data-validation-engine="validate[required]" >' + $comments + '</textarea></td>';
                    $items += '<td><input type="hidden" name="quantities[]" id="quantities_' + $i + '" autocomplete="Off"  value="' + JSON.stringify(effectiveEffort) + '"/><input type="text" name="quantity[]" id="quantity_' + $i + '" autocomplete="Off" onblur="calAmount(' + $i + ');"  class="no-margin form-control" required="required" value="' + timeLogs[i]['effectiveEffort'] + '" data-validation-engine="validate[required,funcCall[validateDecimal],funcCall[checkQuatityMaxDigitsNum]]" onkeypress="handle(event);"/></td>';
                    $items += '<td>';
                    $items += '<div class="input-group">';
                    $items += '<span class="input-group-addon p-1 currencyCode" id="currencyCode">' + jQuery("#clientCurrency").html() + '</span>';
                    $items += '<input type="text" name="unitPrice[]" id="unitPrice_' + $i + '" onblur="calAmount(' + $i + ');" autocomplete="Off" class="no-margin form-control" required="required" value="' + round(ratePerHour, 2) + '" data-validation-engine="validate[required,funcCall[validateDecimal],funcCall[checkPriceMaxDigitsNum]]" onkeypress="handle(event);"/>';
                    $items += '</div></td>';
                    if (activateDiscount)
                        $items += '<td>' + jQuery('#discountsDiv', quote_details_row).html() + '</td>';
                    if (activateTax)
                        $items += '<td>' + jQuery('#taxesDiv', quote_details_row).html() + '</td>';
                    var activityLogsAmount = (timeLogs[i]['amount'] == '.00') ? '0.00' : timeLogs[i]['amount'];
                    $items += '<td><span class="amount_' + $i + '">' + activityLogsAmount + '</span><input type="hidden" id="amount_' + $i + '" value=""/><a class="pull-right" href="javascript:;" onclick="deleteRow(jQuery(this), event);calTotal();"><i class="fa-solid fa-trash"></i></a></td>';
                    $items += '</tr>';
                    jQuery("#quote-details-timelogs").append($items);
                    jQuery('#taxesSelect', "#quote-details-timelogs").attr('id', 'taxIds_' + $i).attr('onchange', 'setTaxData(this,' + $i + ');');
                    jQuery('#taxIds_' + $i, "#quote-details-timelogs").val(timeLogTaxFilter);
                    jQuery('#percentageInput', "#quote-details-timelogs").attr('id', 'percentage_' + $i);
                    jQuery('#discountsSelect', "#quote-details-timelogs").attr('id', 'discountIds_' + $i).attr('onchange', 'setDiscountData(this,' + $i + ');');
                    jQuery('#discountIds_' + $i, "#quote-details-timelogs").val(timeLogDiscountFilter);
                    jQuery('#discountInputHiden', "#quote-details-timelogs").attr('id', 'discountInput' + $i).attr('onblur', 'setDiscountDataOthers(this,' + $i + ');').attr('onkeypress', 'otherDiscountKeypressHandle(event,this,' + $i + ')');
                    jQuery('#iconHiden', "#quote-details-timelogs").attr('id', 'icon' + $i).attr('onclick', 'deleteDiscountInput(jQuery(this), event,' + $i + ');');
                    jQuery('#discountpercentageInput', "#quote-details-timelogs").attr('id', 'discountPercentage_' + $i);
                    $items = '';
                    setTaxData(jQuery('#taxIds_' + $i, jQuery("#quote-details-timelogs")), $i);
                    setDiscountData(jQuery('#discountIds_' + $i, jQuery("#quote-details-timelogs")), $i);
                    calAmount($i);
                    $i++;
                }
            }
            if (jQuery.isEmptyObject(checkedTimeLogs) === false) {
                jQuery("#quote-details-timelogs").append('<tr><td colspan="' + colspan + '"></td><td colspan="1"><strong>' + _lang.timeLogsSubTotal + ' :</strong></td><td  id="totalLogs"><strong></strong></td></tr>');
            } 
        }
        recalculation_all_unitPrices();
        calTotal();
    }
    count = $i;
    jQuery("#loader-global").hide();
    jQuery(".modal", '#relate-matters-container').modal("hide");
    if(jQuery("[item_id][base_price]").length == 0){
        jQuery("#quote-details-items").html("");
        addItemRow(count);
        count = count + 1;
    }
}
function addDefaultItem() {
    $quote_details_data = quote_details.html();
    if(jQuery("[item_id]","#quote-details-items").length == 0){
        emptyData("items");
        addItemRow(1);
        jQuery('#sub_total').val(0);
        jQuery('.sub_total').html(number_format(0, 2, '.', ','));
        jQuery('#total_discount').val(0);
        jQuery('.total_discount').html(number_format(0, 2, '.', ','));
        jQuery('#total_tax').val(0);
        jQuery('.total_tax').html(number_format(0, 2, '.', ','));
        jQuery('#total').val(0);
        jQuery('.total').html(number_format(0, 2, '.', ','));
    }
}
function converExpenseBillingStatus() {
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/convert_expense_billingStatus_to_quote',
        dataType: "json",
        async: false,
        type: 'post',
        data: {
            'client_id': clientId,
            'client_account_id': clientAccountId,
            'expensesIds': expensesIds
        },
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                for (i in expensesIds) {
                    checkedExpenses.push(expensesIds[i]);
                }
                pinesMessage({ty: 'success', m: _lang.expenseStatusChangedSuccessfully.sprintf([_lang.ExpenseStatus['to-invoice']])});
            } else {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        }
    });
}
function toggleComments(quoteId, fetchFromServer, expenseCommentId) {
    fetchFromServer = fetchFromServer || false;
    if (jQuery('#comments-list').html() == '' || fetchFromServer) {
        jQuery.ajax({
            url: getBaseURL('money') + 'vouchers/get_quote_notes/',
            data: {
                'id': quoteId
            },
            type: 'post',
            dataType: 'json',
            success: function (response) {
                if (response.result) {
                    var commentsList = jQuery('#comments-list');
                    jQuery('#quote-comments-fieldset').removeClass('d-none');

                    commentsList.html(response.html);
                    if (expenseCommentId > 0) {
                        jQuery('span#commentText', jQuery('div.comments-list', commentsList)).slideUp();
                        jQuery('a > i', commentsList).removeClass('fa-solid fa-chevron-down');
                        jQuery('a > i', commentsList).addClass('fa-solid fa-chevron-right ');
                        jQuery('i', '#expense-comment_' + expenseCommentId + ' a:first').removeClass('fa-solid fa-chevron-right ');
                        jQuery('i', '#expense-comment_' + expenseCommentId + ' a:first').addClass('fa-solid fa-chevron-down');
                        jQuery('span#commentText', jQuery('#expense-comment_' + expenseCommentId)).slideDown();
                    }
                    if (response.nbOfNotesHistory === 0) {
                        jQuery('#quoteCommentsPaginationContainer').html('');
                    }
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        toggle_notes();
    }
}
function toggle_notes() {
    var notesContainer = jQuery('#quoteNote');
    var notesToggleIcon = jQuery('#quoteNotesToggleIcon');
    if (notesContainer.is(':visible')) {
        notesContainer.slideUp();
        notesToggleIcon.removeClass('fa-solid fa-chevron-down');
        notesToggleIcon.addClass('fa-solid fa-chevron-right ');
    } else {
        notesContainer.slideDown();
        notesToggleIcon.removeClass('fa-solid fa-chevron-right ');
        notesToggleIcon.addClass('fa-solid fa-chevron-down');
    }
}
function exportSubmit(id, container) {
    
    var template = jQuery('#export-template option:selected',container).val();

    format = jQuery('#export-type option:selected',container).val() == 'word' ?  '' : 'pdf';
    jQuery('#form-sub-total','#export-options').val(jQuery('#sub_total','#quoteForm').val());
    jQuery('#form-total-tax','#export-options').val(jQuery('#total_tax','#quoteForm').val());
    jQuery('#form-total','#export-options').val(jQuery('#total','#quoteForm').val());
    
    var urlAction = getBaseURL('money') + 'vouchers/quote_export_to_word/' + id + '/' + template + '/' + format;
    jQuery("#export-options").attr('action', urlAction);
    jQuery("#export-options").submit(); 
    jQuery(".modal", container).modal("hide");
}
/**
 * Empty Data function
 * @param {string} type 
 * @param {string} removeItems 
 * @param {string} container 
 */
function emptyData(type , removeItems , container){
    if(removeItems){
        jQuery(removeItems,container).remove();
    }else{
        jQuery("#quote-details-"+type).html("");
    }
}

/**
 * @function Get total amount of invoice
 */
function calTotal() {
    let sub_total = 0;
    let total_tax = 0;
    let total_discount = 0;
    let total = 0;
    let itemRow = jQuery('#quote-details-items tr, #quote-details-expenses tr , #quote-details-timelogs tr');
    itemRow.each(function () {
        let id = jQuery(this).attr('id');
        if (id !== undefined) {
            let discountPercentage = getDiscount(id,this);
            let taxPercentage = getTaxPercentage(id,this);
            let totalPrice = callSubTotal(id,this).toFixed(allowedDecimalFormat);
            sub_total += parseFloat(totalPrice);
            let subTotalDiscount = parseFloat(callTotalDiscount(totalPrice ,discountPercentage));
            total_discount += subTotalDiscount;
            total_tax += parseFloat(getTotalTax(totalPrice,subTotalDiscount,taxPercentage));
            total = parseFloat(sub_total) + parseFloat(total_tax) - parseFloat(total_discount);
        }
    });
    jQuery('#sub_total').val(accounting.toFixed(sub_total,allowedDecimalFormat));
    jQuery('.sub_total').html(number_format(sub_total,allowedDecimalFormat, '.', ','));
    jQuery('#total_discount').val(accounting.toFixed(total_discount,allowedDecimalFormat));
    jQuery('.total_discount').html(number_format(total_discount,allowedDecimalFormat, '.', ','));
    if(total_discount > 0){
        jQuery('.sub-total-after-discount-container').removeClass('d-none');
        jQuery('#sub-total-after-discount').val(sub_total-total_discount);
        jQuery('.sub-total-after-discount').html(number_format(sub_total-total_discount, allowedDecimalFormat, '.', ','));
    }else{
        jQuery('.sub-total-after-discount-container').addClass('d-none');
    }
    jQuery('#total_tax').val(accounting.toFixed(total_tax,allowedDecimalFormat));
    jQuery('.total_tax').html(number_format(total_tax,allowedDecimalFormat, '.', ','));
    jQuery('#total').val(accounting.toFixed(total,allowedDecimalFormat));
    jQuery('.total').html(number_format(total,allowedDecimalFormat, '.', ','));

    var totalExpenses = 0;
    var TotalTimeLogs = 0;
    var totalItems = 0;
    itemRow.each(function () {
        var id = jQuery(this).attr('id');
        if (id !== undefined) {
            var expVal = jQuery('#expense_' + id).val();
            var logVal = jQuery('#timeLogs_' + id).val();
            if (expVal !== undefined && expVal !== null && expVal !== '') {
                var amountExp = accounting.toFixed(jQuery('#amount_' + id).val(),allowedDecimalFormat);
                totalExpenses = parseFloat(totalExpenses) + parseFloat(amountExp);
            } else if (logVal !== undefined && logVal !== null && logVal !== '') {
                var amountExp = accounting.toFixed(jQuery('#amount_' + id).val(),allowedDecimalFormat);
                TotalTimeLogs = parseFloat(TotalTimeLogs) + parseFloat(amountExp);
            } else {
                var amountItem = accounting.toFixed(jQuery('#amount_' + id).val(),allowedDecimalFormat);
                if (amountItem !== undefined && amountItem !== '')
                    totalItems = parseFloat(totalItems) + parseFloat(amountItem);
            }
        }
    });
    jQuery('#totalExpenses').html(number_format(totalExpenses,allowedDecimalFormat, '.', ','));
    jQuery('#totalLogs').html(number_format(TotalTimeLogs, allowedDecimalFormat, '.', ','));
    jQuery('#totalItems').html(number_format(totalItems, allowedDecimalFormat, '.', ','));
}

/**
 * @function call discount of each invoice row
 * @param id row id
 * @param itemRow invoice row jquery instance
 * @return {any}
 */
function getDiscount(id,itemRow) {
    return activateDiscount ? accounting.toFixed(jQuery('#discountPercentage_' + id, itemRow).val(),allowedDiscountFormat) : 0;
}

/**
 * @function cal total tax for each invoice row
 * @param id
 * @param itemRow
 * @return {any}
 */
function getTaxPercentage(id,itemRow) {
    return activateTax ? accounting.toFixed(jQuery('#percentage_' + id, itemRow).val(),allowedDecimalFormat) : 0;
}

/**
 *
 * @param id
 * @param itemRow
 * @return {number}
 */
function callSubTotal(id,itemRow){
    let quantity = accounting.toFixed(jQuery('#quantity_' + id, itemRow).val(),allowedDecimalFormat);
    let unitPrice = accounting.toFixed(jQuery('#unitPrice_' + id, itemRow).val(),allowedDecimalFormat);
    return accounting.toFixed(parseFloat(quantity) * parseFloat(unitPrice),allowedDecimalFormat) * 1;
}

/**
 * @function get total discount of each invoice row
 * @param totalPrice
 * @param discountPercentage
 * @return {string}
 */
function callTotalDiscount(totalPrice,discountPercentage) {
    return accounting.toFixed((totalPrice * discountPercentage)/100, allowedDecimalFormat);
}

/**
 * @function get total tax of each invoice row
 * @param totalPrice
 * @param totalDiscount
 * @param taxPercentage
 * @return {string}
 */
function getTotalTax(totalPrice,totalDiscount,taxPercentage) {
    let valueAfterDiscount = totalPrice - totalDiscount;
    return accounting.toFixed((valueAfterDiscount * parseFloat(taxPercentage))/100, allowedDecimalFormat);
}

function validateTimeLogs(timeLogs, callback){
    jQuery.ajax({
        async: false,
        url: getBaseURL() + 'time_tracking/validate_time_logs',
        type: 'POST',
        dataType: 'JSON',
        data: {
            timeLogs: timeLogs
        }, success: function (response) {
            callback(response.invalidLogs);
        }
    });
}

jQuery(document).ready(function () {
    tinymce.init({
        selector: '#notes',
        menubar: false,
        statusbar: false,
        branding: false,
        height: 200,
        resize: false,
        plugins: ['link', 'paste'],
        paste_text_sticky: true,
        apply_source_formatting: true,
        toolbar: 'formatselect | bold italic underline | link | undo redo | alignleft aligncenter alignright alignjustify',
        block_formats: 'Paragraph=p;Huge=h4;Normal=h5;Small=h6;',
        paste_word_valid_elements: "b,strong,i,em,h1,h2,span,br,div,p",
        paste_webkit_styles: "color font-size",
        paste_remove_styles: false,
        paste_retain_style_properties: "all",
        forced_root_block: 'p',
        forced_root_block_attrs: {"style": "margin: 0;"},
        formats: {
            underline: {inline: 'u', exact: true},
            alignleft: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'left'}},
            aligncenter: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'center'}},
            alignright: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'right'}},
            alignjustify: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'justify'}}
        }, paste_preprocess: function (plugin, args) {
            tinymce.activeEditor.windowManager.alert('Some of the pasted content will not stay with the same format');
        },
        setup: function (editor) {
            editor.on('init', function (e) {
                jQuery('#notes_ifr').contents().find('body').prop("dir", "auto");
                e.pasteAsPlainText = true;
            });
        }
    });

    showToolTip();

    jQuery("#quoteForm").on('submit', function(){
        var invalidTimeLogs = [];
        validateTimeLogs(checkedTimeLogs, function(_invalidTimeLogs){
            invalidTimeLogs = _invalidTimeLogs;
        });
        invalidTimeLogs.forEach(function(timeLog){
            var timeLogsId = "[timelogs_id=" + timeLog + "]";
            deleteRow(jQuery('a', jQuery(timeLogsId, "#quote-details-timelogs")));
            calTotal();
        });
        if (invalidTimeLogs.length > 0){
            pinesMessage({ty: 'information', m: _lang.feedback_messages.deletedLogs});
        }
     });
});