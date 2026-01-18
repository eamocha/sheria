jQuery(document).ready(function (){
    jQuery("[id^='custom-field-']").each(function (){
        switch (jQuery(this).attr('field-type')) {
            case 'date':
                if (!jQuery(this).hasClass('sf-value-time')) {
                    makeFieldsDatePicker({fields: [jQuery(this).attr('id')]});
                }
                break;
            case 'date_time':
                if (!jQuery(this).hasClass('sf-value-time')) {
                    makeFieldsDatePicker({fields: [jQuery(this).attr('id')]});
                }
                break;
            case 'list':
                jQuery('#' + jQuery(this).attr('id')).chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
                break;
            case 'lookup':
                switch (jQuery(this).attr('field-type-data')) {
                    case 'companies':
                        companyAutocompleteMultiOption(jQuery('#' + jQuery(this).attr('id')), resultHandlerAfterCompanyAutocomplete);
                        break;
                    case 'contacts':
                        contactAutocompleteMultiOption(jQuery('#' + jQuery(this).attr('id')), '3', resultHandlerAfterContactsLegalCasesAutocomplete);
                        break;
                    case 'users':
                        userLookup(jQuery(this).attr('id'));
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
    });
});
