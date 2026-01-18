$reportName = '';
jQuery(function() {
	loadReportsEventsForFilters();
	$reportName = jQuery('#reportName', '#caseReportsSearchFilters').val();
	jQuery('a', '#pagination').each(function(index, element) {
		jQuery(element).click(function(e) {
			e.preventDefault();
			var containerForm = jQuery('#submitBtn2', '#caseReportsQuickSearchFilters').attr('clicked') == '1' ? '#caseReportsQuickSearchFilters' : '#caseReportsSearchFilters';
			jQuery('#skip', containerForm).val((element.innerHTML - 1) * jQuery('#take', '#caseReportsSearchFilters').val());
			jQuery(containerForm).attr('action', element.getAttribute('href')).submit();
		});
	});
	jQuery('[rel="tooltip"]','.case-report').tooltip({});
	collapseExpandRows('first');
	collapseExpandRows('second');

	jQuery('#outsourceTypeOperator', '#caseReportsSearchFilters').change(function () {
        jQuery('#outsourceToValue', '#caseReportsSearchFilters').val('');

        if (jQuery(this).val() == 'Company') {
            jQuery('#outsourceToField', '#caseReportsSearchFilters').val('companyOutsourceTo');
            jQuery('#outsourceToFunction', '#caseReportsSearchFilters').val(companyOutsourceToFieldFunction);
        } else if (jQuery(this).val() == 'Person') {
            jQuery('#outsourceToField', '#caseReportsSearchFilters').val('contactOutsourceTo');
            jQuery('#outsourceToFunction', '#caseReportsSearchFilters').val(contactOutsourceToFieldFunction);
        }
    });

    jQuery('#outsourceToValue', '#caseReportsSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#outsourceTypeOperator', '#caseReportsSearchFilters').val();
            if (lookupType !== '') {
                lookupType = lookupType === 'Company' ? 'companies' : 'contacts';
                jQuery.ajax({
                    url: getBaseURL() + lookupType + '/autocomplete',
                    dataType: "json",
                    data: request,
                    error: defaultAjaxJSONErrorsHandler,
                    success: function (data) {
                        if (data.length < 1) {
                            response([{
                                    label: _lang.no_results_matched_add.sprintf([request.term]),
                                    value: '',
                                    record: {
                                        id: -1,
                                        term: request.term
                                    }
                                }]);
                        } else {
                            response(jQuery.map(data, function (item) {
                                if (lookupType == 'contacts') {
                                    return {
                                        label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        record: item
                                    }
                                } else if (lookupType == 'companies') {
                                    return {
                                        label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                                        value: item.name,
                                        record: item
                                    }
                                }
                            }));
                        }
                    }
                });
            }

        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });
});
function collapseExpandRows(position){
	jQuery("i." + position + "-grouping").click(function() {
		$this = jQuery(this);
		$targetParent = $this.parent().parent();
		$target = $targetParent.find("table." + position + "-grouping");
		if ($target.is(':visible')) {
			$target.hide();
			$this.removeClass('fa-solid fa-angle-down');
			$this.addClass('fa-solid fa-angle-right');
		} else {
			$target.show();
			$this.removeClass('fa-solid fa-angle-right');
			$this.addClass('fa-solid fa-angle-down');
		}
	});
}
function collapseExpandAllRows(expand){
	expand = expand || false;
	jQuery("i.first-grouping, i.second-grouping").each(function(){
		$this = jQuery(this);
		$targetParent = $this.parent().parent();
		$target = $targetParent.find("table.first-grouping, table.second-grouping");
		if(expand){
			$target.show();
			$this.removeClass('fa-solid fa-angle-right');
			$this.addClass('fa-solid fa-angle-down');
		}else{
			$target.hide();
			$this.removeClass('fa-solid fa-angle-down');
			$this.addClass('fa-solid fa-angle-right');
		}	
	});
}
function advancedSearchFilters() {
	if (!jQuery('#filtersFormWrapper').is(':visible')) {
		jQuery('#filtersFormWrapper').slideDown();
	} else {
		scrollToId('#filtersFormWrapper');
	}
}
function hideAdvancedSearch() {
	jQuery('#filtersFormWrapper').slideUp();
}
function changePerPage(selectedNum) {
	var containerForm = jQuery('#submitBtn2', '#caseReportsQuickSearchFilters').attr('clicked') == '1' ? jQuery('#caseReportsQuickSearchFilters') : jQuery('#caseReportsSearchFilters');
	jQuery('#take', containerForm).val(selectedNum.options[selectedNum.selectedIndex].value);
	jQuery('#skip', containerForm).val('0');
	containerForm.attr('action', getBaseURL() + 'reports/' + $reportName).submit();
}
function loadReportsEventsForFilters() {
	makeFieldsDatePicker({fields: [jQuery('#dueDateValue', '#caseReportsSearchFilters'), jQuery('#dueDateEndValue', '#caseReportsSearchFilters'), 'arrivalDateValue','caseArrivalDateValue', 'arrivalDateEndValue', 'caseArrivalDateEndValue', 'sentenceDateValue', 'sentenceDateEndValue']});
	userLookup(jQuery('#assigneeValue', '#caseReportsSearchFilters'));
	contactAutocompleteMultiOption(jQuery('#contactContributorValue', '#caseReportsSearchFilters'), '3', functionReturnTrue);
	companyAutocompleteMultiOption(jQuery('#companyValue', '#caseReportsSearchFilters'),functionReturnTrue);
	contactAutocompleteMultiOption(jQuery('#contactValue', '#caseReportsSearchFilters'), '3', functionReturnTrue);
	contactAutocompleteMultiOption('contactOutsourceToValue', '3', functionReturnTrue);
}
function functionReturnTrue() {
	return true;
}