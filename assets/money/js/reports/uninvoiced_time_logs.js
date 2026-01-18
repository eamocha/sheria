jQuery(function () {
    jQuery(document).ready(function () {
        var form = jQuery('#uninvoiced_time_logs');
        if (jQuery('#logDateOpertator').val() != 'cast_between') {
            jQuery('#calendar-icon').addClass('d-none')
            disableEmpty(form);
        }
        submitForm(form);
        jQuery('#logDateValue').change(function () {
            jQuery('#logDateEndValue').bootstrapDP("setStartDate", jQuery(this).bootstrapDP('getDate'));
        });

        jQuery('#logDateEndValue').change(function () {
            jQuery(this).bootstrapDP("setStartDate", jQuery('#logDateValue').bootstrapDP('getDate'));
        });

        jQuery('#logDateOpertator').change(function () {
            if (jQuery('#logDateOpertator').val() == 'cast_between') {
                jQuery('#calendar-icon').removeClass('d-none');
                var e = disableEmpty(form);
                jQuery('div.form-group input.form-control:last-of-type, div.form-group select.form-control:last-of-type', e).each(function (e, t) {
                    if (String(t.value).trim() === "" && !jQuery(t).hasClass('hasDatepicker')) {
                        jQuery('input, select', jQuery(t).parent().parent()).removeAttr("disabled");
                    }
                });
                jQuery('div.form-group input.form-control.hasDatepicker:last-of-type', e).each(function (e, t) {
                    if (String(t.value).trim() === "") {
                        jQuery('input, select', jQuery(t).parent()).removeAttr("disabled");
                    }
                });
            } else {
                jQuery('#calendar-icon').addClass('d-none')
                disableEmpty(e);
            }
        });

        jQuery('#logDateValue', jQuery('#logDateValueContainer')).bootstrapDP(datePickerOptions);

        jQuery('.input-group-addon', jQuery('#logDateValueContainer')).click(function () {
            jQuery(document).ready(function () {
                setDatePicker('#logDateValueContainer');
                jQuery("#logDateValueContainer").bootstrapDP('show');
            });
        });

        jQuery('#logDateEndValue', jQuery('#logDateEndValueContainer')).bootstrapDP(jQuery.extend({}, datePickerOptions, { startDate: jQuery('#logDateValue').bootstrapDP('getDate') }));

        jQuery('.input-group-addon', jQuery('#logDateEndValueContainer')).click(function () {
            jQuery(document).ready(function () {
                setDatePicker("#logDateEndValueContainer");
                jQuery("#logDateEndValueContainer").bootstrapDP('show');
            });
        });

        if (_lang.languageSettings['langDirection'] === 'rtl') {
            gridScrollRTL();
        }

        jQuery('div.k-grid-content').css("height", "300px");

        jQuery('#export-module-btn').click(function () {
            exportReportToExcel();
        });

        jQuery('.uninvoiced-time-logs').on('click', '#pagination a', function (e) {
            e.preventDefault();
            var url = jQuery(this).attr('href');
            var form = jQuery('#uninvoiced_time_logs');

            submitForm(form, url);
        });
    });
});

function changePerPage(selectedNum) {
    var form = jQuery('#uninvoiced_time_logs');
    jQuery('#take', form).val(selectedNum.options[selectedNum.selectedIndex].value);
    jQuery('#skip', form).val('0');
    submitForm(form);
}

function exportReportToExcelHandler(element, groupByMatter) {
    element = element || false;
    if (parseInt(jQuery('#total_rows').val()) < 10000) {
        exportReportToExcel(groupByMatter);
    } else {
        applyExportingModuleMethod(element);
    }
}

function exportReportToExcel(groupByMatter) {
    var form = jQuery('#uninvoiced_time_logs');
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', form).val(pageNumber);
    form.attr('action', getBaseURL('money') + 'reports/uninvoiced_time_logs_to_excel/' + groupByMatter).submit();
}

function submitForm(form, url) {
    url = url || false;
    var groupByMatter = jQuery('#group-by-matter').val();
    jQuery.ajax({
        url: url ? url : getBaseURL('money') + 'reports/uninvoiced_time_logs/1/' + groupByMatter + '/' + jQuery('#logDateOpertator').val() + '/' + jQuery('#logDateValue').val() + '/' + jQuery('#logDateEndValue').val(),
        method: 'post',
        dataType: 'json',
        data: form.serialize(),
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            if (response.status) {
                if (response.total_rows > 0) {
                    jQuery('#report-table').html(response.html);
                    bindFiltersData(response.filters_data);
                    setTotalAmount();
                    setTotalTax();
                    jQuery('#report-filters').removeClass('d-none');
                    jQuery('.no-data').addClass('d-none');
                    loadExportModalRanges(response.total_rows);
                } else {
                    clearFiltersData();
                    jQuery('.no-data').removeClass('d-none');
                    jQuery('#report-filters').addClass('d-none');
                    jQuery('#report-table').html('');
                }
            } else {
                pinesMessage({ ty: 'warning', m: _lang.feedback_messages.tooManyRecords });
            }
        }
    });
}

function groupBy(groupByMatter) {
    window.location = getBaseURL('money') + 'reports/uninvoiced_time_logs/1/' + groupByMatter + '/' + jQuery('#logDateOpertator').val() + '/' + jQuery('#logDateValue').val() + '/' + jQuery('#logDateEndValue').val();
}

function bindFiltersData(data) {
    data = data || null;

    setClientsList(data.clients, data.filters.filters);
    setMattersList(data.matters, data.filters.filters);
    setCaseTypesList(data.caseTypes, data.filters.filters);
    setAssineesList(data.assignees, data.filters.filters);
    setTaxList(data.taxes, data.chosen_tax_percentage);
    setCurrencyList(data.currencies, data.chosen_currency);
}

function clearFiltersData() {
    clearClientsList();
    clearMattersList();
    clearCaseTypesList();
    clearAssigneesList();
    clearTaxList();
    clearCurrencyList();
}

function setClientsList(data, filters) {
    data = data || null;
    filters = filters || null;

    var selected_client = getSelectedClient(filters);
    var html = '<option value="" selected>' + _lang.none + '</option>';

    for (let client of data) {
        html += '<option value="' + client.client_id + '" data-company-id="' + client.company_id + '" data-contact-id="' + client.company_id + '" ' + (client.client_id == selected_client ? 'selected' : '') + '>' + (client.company_name ? client.company_name : client.contact_name) + '</option>';
    }

    jQuery('#client_id').html(html).selectpicker('refresh').off('change').change(function () {
        var client_id = jQuery(this).val();
        setClientIdFilters(client_id);
        submitForm(jQuery('#uninvoiced_time_logs'));
    });

    var client_id = jQuery('#client_id').val();
    setClientIdFilters(client_id);
}

function clearClientsList() {
    var html = '<option value="" selected>' + _lang.none + '</option>';
    jQuery('#client_id').html(html);
    setClientIdFilters();
}

function setCaseTypesList(data, filters) {
    data = data || null;
    filters = filters || null;

    var selectedCaseType = getSelectedCaseType(filters);

    if (selectedCaseType > 0) {
        jQuery('#case_type_id').val(selectedCaseType);
    } else {
        html = '<option value="" selected>' + _lang.none + '</option>';

        for (let caseType of data) {
            html += '<option value="' + caseType.legal_case_type_id + '">' + caseType.legal_case_type + '</option>';
        }

        jQuery('#case_type_id').html(html);
    }

    jQuery('#case_type_id').selectpicker('refresh').off('change').change(function () {
        var caseTypeId = jQuery(this).val();
        setCaseTypeIdFilters(caseTypeId);
        submitForm(jQuery('#uninvoiced_time_logs'));
    });

    var caseTypeId = jQuery('#case_type_id').val();
    setCaseTypeIdFilters(caseTypeId);
}

function clearCaseTypesList() {
    var html = '<option value="" selected>' + _lang.none + '</option>';
    jQuery('#case_type_id').html(html);
    setCaseTypeIdFilters();
}

function setAssineesList(data, filters) {
    data = data || null;
    filters = filters || null;

    var selectedAssignee = getSelectedAssignee(filters);

    if (selectedAssignee > 0) {
        jQuery('#assignee_id').val(selectedAssignee);
    } else {
        html = '<option value="" selected>' + _lang.none + '</option>';

        for (let assignee of data) {
            html += '<option value="' + assignee.legal_case_assignee_id + '">' + assignee.legal_case_assignee + '</option>';
        }

        jQuery('#assignee_id').html(html);
    }

    jQuery('#assignee_id').selectpicker('refresh').off('change').change(function () {
        var assigneeId = jQuery(this).val();
        setAssigneeIdFilters(assigneeId);
        submitForm(jQuery('#uninvoiced_time_logs'));
    });

    var assigneeId = jQuery('#assignee_id').val();
    setAssigneeIdFilters(assigneeId);
}

function clearAssigneesList() {
    var html = '<option value="" selected>' + _lang.none + '</option>';
    jQuery('#assignee_id').html(html);
    setAssigneeIdFilters();
}

function setMattersList(data, filters) {
    data = data || null;
    filters = filters || null;

    var selected_matter = getSelectedMatter(filters);

    if (selected_matter > 0) {
        jQuery('#matter_id').val(selected_matter);
    } else {
        html = '<option value="" selected>' + _lang.none + '</option>';

        for (let matter of data) {
            html += '<option value="' + matter.legal_case_id + '">M' + matter.legal_case_id + ': ' + matter.legal_case_subject + '</option>';
        }

        jQuery('#matter_id').html(html);
    }


    jQuery('#matter_id').selectpicker('refresh').off('change').change(function () {
        var matter_id = jQuery(this).val();
        setMatterIdFilters(matter_id);
        submitForm(jQuery('#uninvoiced_time_logs'));
    });

    var matter_id = jQuery('#matter_id').val();
    setMatterIdFilters(matter_id);
}

function clearMattersList() {
    var html = '<option value="" selected>' + _lang.none + '</option>';
    jQuery('#matter_id').html(html);
    setMatterIdFilters();
}

function setTaxList(data, chosenTaxPercentage) {
    data = data || null;
    chosenTaxPercentage = chosenTaxPercentage || false;

    var html = '';
    var firstItem = true;

    for (let tax of data) {
        if (chosenTaxPercentage) {
            html += '<option value="' + tax.percentage + '"' + (tax.percentage == chosenTaxPercentage ? ' selected' : '') + '>' + tax.name + ' (' + tax.percentage + '%)' + '</option>';
        } else {
            html += '<option value="' + tax.percentage + '"' + (firstItem ? ' selected' : '') + '>' + tax.name + ' (' + tax.percentage + '%)' + '</option>';
        }

        firstItem = false;
    }

    jQuery('#tax_id').html(html).selectpicker('refresh');
}

function clearTaxList() {
    jQuery('#tax_id').html('');
}

function setCurrencyList(data, chosenCurrency) {
    data = data || null;

    var html = '';

    for (let currency of data) {
        if (chosenCurrency) {
            html += '<option value="' + currency.name + '"' + (currency.name == chosenCurrency ? ' selected' : '') + ' data-exchange-rate="' + currency.exchangeRate + '">' + currency.name + '</option>';
        } else {
            html += '<option value="' + currency.name + '"' + (currency.organization_currencry == true ? ' selected' : '') + ' data-exchange-rate="' + currency.exchangeRate + '">' + currency.name + '</option>';
        }
    }

    jQuery('#currency_id').html(html).selectpicker('refresh');
    setCurrency();
}

function clearCurrencyList() {
    jQuery('#currency_id').html('');
}

function setCurrency() {
    var currency = jQuery('#currency_id').val();
    jQuery('#exchange_rate').val(getSelectedCurrencyexchangeRate());
    jQuery('.currency-unit', '.uninvoiced-time-logs').each(function () {
        jQuery(this).text(currency);
    });
    setTotalAmount();
    setTotalTax();
    jQuery('#chosen_currency', '#uninvoiced_time_logs').val(currency);
}

function getSelectedCurrencyexchangeRate() {
    return jQuery('option:selected', jQuery('#currency_id')).length > 0 ? jQuery('option:selected', jQuery('#currency_id')).attr('data-exchange-rate') : 1;
}

function setTotalAmount() {
    var val = calculateTotalAmount();
    jQuery('#time_log_total_amount').val(val);
    jQuery('#time_log_total_amount_display').text(accounting.formatMoney(accounting.toFixed(val, 2), ""));
}

function calculateTotalAmount() {
    var total = 0;

    jQuery('.time_log_amount', jQuery('#report-table')).each(function () {
        jQuery(this).val(jQuery(this).data('initial-value') / getSelectedCurrencyexchangeRate()).siblings('span').text(accounting.formatMoney(accounting.toFixed(jQuery(this).val(), 2), ""));
        total += parseFloat(jQuery(this).val()) + parseFloat(jQuery(this).parent().siblings('td').find('.time_log_tax').val());
    });

    return total;
}

function setTotalTax() {
    var val = calculateTotalTax();
    jQuery('#time_log_total_tax').val(val);
    jQuery('#time_log_total_tax_display').text(accounting.formatMoney(accounting.toFixed(val, 2), ""));
    jQuery('#tax_percentage').val(jQuery('#tax_id').val());
    setTotalAmount();
}

function calculateTotalTax() {
    var total = 0;

    jQuery('.time_log_tax_percentage', jQuery('#report-table')).each(function () {
        var selected_tax_percentage = jQuery('#tax_id').val();

        jQuery(this).val(selected_tax_percentage);
        jQuery(this).siblings('span').text(selected_tax_percentage ? selected_tax_percentage + '%' : '');

        var amount = jQuery(this).parent().siblings('td').find('.time_log_amount').val();
        var tax_percentage = jQuery(this).val();
        var tax = parseFloat((tax_percentage / 100) * amount);

        jQuery(this).parent().siblings('td').find('.time_log_tax').val(tax);
        jQuery(this).parent().siblings('td').find('.time_log_tax_display').text(accounting.formatMoney(accounting.toFixed(tax, 2), ""));

        total += tax;
    });

    return total;
}

function setClientIdFilters(id) {
    id = id || null;

    jQuery('input[name="filter[filters][1][filters][0][field]"]').attr('disabled', id ? false : true);
    jQuery('input[name="filter[filters][1][filters][0][operator]"]').attr('disabled', id ? false : true);
    jQuery('input[name="filter[filters][1][filters][0][value]"]').val(id).attr('disabled', id ? false : true);
}

function setMatterIdFilters(id) {
    id = id || null;

    jQuery('input[name="filter[filters][2][filters][0][field]"]').attr('disabled', id ? false : true);
    jQuery('input[name="filter[filters][2][filters][0][operator]"]').attr('disabled', id ? false : true);
    jQuery('input[name="filter[filters][2][filters][0][value]"]').val(id).attr('disabled', id ? false : true);
}

function setCaseTypeIdFilters(id) {
    id = id || null;
 
    jQuery('input[name="filter[filters][3][filters][0][field]"]').attr('disabled', id ? false : true);
    jQuery('input[name="filter[filters][3][filters][0][operator]"]').attr('disabled', id ? false : true);
    jQuery('input[name="filter[filters][3][filters][0][value]"]').val(id).attr('disabled', id ? false : true);
}

function setAssigneeIdFilters(id) {
    id = id || null;

    jQuery('input[name="filter[filters][4][filters][0][field]"]').attr('disabled', id ? false : true);
    jQuery('input[name="filter[filters][4][filters][0][operator]"]').attr('disabled', id ? false : true);
    jQuery('input[name="filter[filters][4][filters][0][value]"]').val(id).attr('disabled', id ? false : true);
}

function getSelectedClient(filters) {
    filters = filters || null;

    if (filters != null && typeof filters[1] != 'undefined' && typeof filters[1]['filters'][0]['value'] != 'undefined') {
        return filters[1]['filters'][0]['value'];
    }

    return 0;
}

function getSelectedMatter(filters) {
    filters = filters || null;

    if (filters != null && typeof filters[2] != 'undefined' && typeof filters[2]['filters'][0]['value'] != 'undefined') {
        return filters[2]['filters'][0]['value'];
    }

    return 0;
}

function getSelectedCaseType(filters) {
    filters = filters || null;

    if (filters != null && typeof filters[3] != 'undefined' && typeof filters[3]['filters'][0]['value'] != 'undefined') {
        return filters[3]['filters'][0]['value'];
    }

    return 0;
}

function getSelectedAssignee(filters) {
    filters = filters || null;

    if (filters != null && typeof filters[4] != 'undefined' && typeof filters[4]['filters'][0]['value'] != 'undefined') {
        return filters[4]['filters'][0]['value'];
    }

    return 0;
}