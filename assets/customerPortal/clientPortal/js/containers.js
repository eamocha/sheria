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
        jQuery('#containers-table').DataTable({
            /* Disable initial sort */
            "aaSorting": []
        });
    setActiveTab('containers');
});