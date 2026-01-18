jQuery(document).ready(function () {
    var contactCompanyType = jQuery("select#contact-company-type").val();
    jQuery('#contact-company-type').change(function () {
        jQuery("#contact-company-lookup").val('');
        contactCompanyType = jQuery("select#contact-company-type").val();
    });
    jQuery("#contact-company-lookup").autocomplete({
        minLength: 3,
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + contactCompanyType + '/autocomplete',
                dataType: "json",
                data: request,
                success: function (data) {
                    if (data.length < 1) {
                        response([{label: _lang.no_results_matched, value: '', record: {id: -1, term: request.term}}]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            if (contactCompanyType == 'contacts') {
                                return {
                                    label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                    value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                    record: item
                                }
                            } else if (contactCompanyType == 'companies') {
                                return {
                                    label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                                    value: item.name,
                                    record: item
                                }
                            }
                        }));
                    }
                },
                error: defaultAjaxJSONErrorsHandler
            });
        },
        select: function (event, ui) {
			if (ui.item.record.id && ui.item.record.id > 0) {
				window.location = getBaseURL() + 'reports/conflict_of_interset/' + contactCompanyType + '/' + ui.item.record.id;
			}
        }
    });
});
