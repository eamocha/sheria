jQuery(document).ready(function () {
    jQuery('input[type="text"], textarea, select').addClass('form-control');
    jQuery('div[requiredField="yes"]').each(function () {
        var container = jQuery(this);
        jQuery('.cp-screen-field', container).attr('required', '');
    });
    jQuery('.cp-screen-date-field').each(function () {
        var options = jQuery.extend({changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", firstDay: 1});
        jQuery(this).datepicker(options);
    });
    setActiveTab('tickets');
    jQuery('#ticketsTable').DataTable({
        /* Disable initial sort */
        "aaSorting": []
    });
    jQuery('#filter-dropdown', '#cpTicketsList').change(function(){
        switch(jQuery('#filter-dropdown', '#cpTicketsList').val()){
            case 'created_by_others': window.location.href = getBaseURL('customer-portal') + 'tickets/index/created_by_others';
                break;
            case 'created_by_me': window.location.href = getBaseURL('customer-portal') + 'tickets/index/created_by_me';
                break;
            case 'corporate_only': window.location.href = getBaseURL('customer-portal') + 'tickets/index/corporate_only';
                break;
            case 'litigation_only': window.location.href = getBaseURL('customer-portal') + 'tickets/index/litigation_only';
                break;
            default: window.location.href = getBaseURL('customer-portal') + 'tickets';
        }
    });
});
