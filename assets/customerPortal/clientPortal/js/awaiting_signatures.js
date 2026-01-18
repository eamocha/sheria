jQuery(document).ready(function () {
    jQuery('#contracts-table').DataTable({
        /* Disable initial sort */
        "aaSorting": []
    });
    setActiveTab('contracts');
    jQuery('#filter-dropdown', '#cp-awaiting-signatures-list').change(function () {
        switch (jQuery('#filter-dropdown', '#cp-awaiting-signatures-list').val()) {
            case 'my_signatures':
                window.location.href = getBaseURL('customer-portal') + 'contracts/awaiting_signatures/my_signatures/';

                break;
            default:
                window.location.href = getBaseURL('customer-portal') + 'contracts/awaiting_signatures';
                break;
        }
    });
});