function loadCustomFieldsEvents(container){
    jQuery("[id^='custom-field-']").each(function () {
        switch (jQuery(this).attr('field-type')) {
            case 'date':
                setDatePicker('#date-'+jQuery(this).attr('id'), container);
                // makeFieldsDatePicker({fields: [jQuery(this).attr('id')]});
                break;
            case 'date_time':
                if (jQuery(this).hasClass('date')) {
                    jQuery(this).wrap('<div class="col-md-8 no-padding"></div>');
                    makeFieldsDatePicker({fields: [jQuery(this).attr('id')]});
                } else {
                    jQuery(this).wrap('<div class="col-md-4 no-padding-right"></div>');
                    makeFieldsTimePicker({fields: [jQuery(this).attr('id')]});
                }
                break;
            case 'list':
                jQuery('#' + jQuery(this).attr('id')).chosen({
                    no_results_text: _lang.no_results_matched,
                    placeholder_text: _lang.choose,
                    width: '100%'
                });
                break;
            case 'lookup':
                var displaySegments = jQuery(this).attr('display-segments').split(',');
                var displayFormatSingleSegment = jQuery(this).attr('display-format-single-segment');
                var displayFormatDoubleSegment = jQuery(this).attr('display-format-double-segment');
                var displayFormatTripleSegment = jQuery(this).attr('display-format-triple-segment');
                var fieldTypeData = jQuery(this).attr('field-type-data');
                jQuery('#' + jQuery(this).attr('id')).selectize({
                    plugins: ['remove_button'],
                    placeholder: _lang.startTyping,
                    valueField: 'id',
                    labelField: displaySegments[0],
                    searchField: [displaySegments[0], displaySegments[1]],
                    create: false,
                    render: {
                        option: function (item, escape) {
                            var displayOption = ''
                            if (typeof displayFormatTripleSegment !== 'undefined') {
                                if (item[displaySegments[2]] !== null && typeof item[displaySegments[2]] !== 'undefined') {
                                    displayOption = displayFormatTripleSegment.sprintf([escape(item[displaySegments[0]]), escape(item[displaySegments[1]]), escape(item[displaySegments[2]])]);
                                } else if (item[displaySegments[1]] !== null && typeof item[displaySegments[1]] !== 'undefined') {
                                    displayOption = escape(item[displaySegments[0]]) + ' ' + escape(item[displaySegments[1]]);
                                } else {
                                    displayOption = escape(item[displaySegments[0]]);
                                }
                            } else
                            if (typeof displayFormatDoubleSegment !== 'undefined') {
                                if (item[displaySegments[1]] !== null && typeof item[displaySegments[1]] !== 'undefined') {
                                    displayOption = displayFormatDoubleSegment.sprintf([escape(item[displaySegments[0]]), escape(item[displaySegments[1]])]);
                                } else {
                                    displayOption = escape(item[displaySegments[0]]);
                                }
                            } else {
                                displayOption = displayFormatSingleSegment.sprintf([escape(item[displaySegments[0]])]);
                            }
                            return '<div><span>' + displayOption + '</span></div>';
                        }, item: function (item, escape) {
                            var displayOption = ''
                            if (typeof displayFormatTripleSegment !== 'undefined') {
                                if (item[displaySegments[2]] !== null && typeof item[displaySegments[2]] !== 'undefined') {
                                    displayOption = displayFormatTripleSegment.sprintf([escape(item[displaySegments[0]]), escape(item[displaySegments[1]]), escape(item[displaySegments[2]])]);
                                } else if (item[displaySegments[1]] !== null && typeof item[displaySegments[1]] !== 'undefined') {
                                    displayOption = escape(item[displaySegments[0]]) + ' ' + escape(item[displaySegments[1]]);
                                } else {
                                    displayOption = escape(item[displaySegments[0]]);
                                }
                            } else
                            if (typeof displayFormatDoubleSegment !== 'undefined') {
                                if (item[displaySegments[1]] !== null && typeof item[displaySegments[1]] !== 'undefined' && fieldTypeData !== 'companies') {//to not show the short name of the company
                                    displayOption = displayFormatDoubleSegment.sprintf([escape(item[displaySegments[0]]), escape(item[displaySegments[1]])]);
                                } else {
                                    displayOption = escape(item[displaySegments[0]]);
                                }
                            } else {
                                displayOption = displayFormatSingleSegment.sprintf([escape(item[displaySegments[0]])]);
                            }
                            return '<div>' + displayOption + '</div>';
                        }
                    },
                    load: function (query, callback) {
                        if (query.length < 3)
                            return callback();
                        jQuery.ajax({
                            url: getBaseURL() + jQuery('#' + this.$input[0].id).attr('field-type-data') + "/autocomplete"+(fieldTypeData=='users'?'/active':''),
                            type: 'GET',
                            data: {
                                term: encodeURIComponent(query),
                            },
                            dataType: 'json',
                            error: function () {
                                callback();
                            },
                            success: function (res) {
                                callback(res);
                            }
                        });
                    }
                });
                break;
            default:
                break;
        }
    });
}
