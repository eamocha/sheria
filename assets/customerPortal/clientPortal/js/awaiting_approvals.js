jQuery(document).ready(function () {
    jQuery('#contracts-table').DataTable({
        /* Disable initial sort */
        "aaSorting": []
    });
    setActiveTab('contracts');
    jQuery('#filter-dropdown', '#cp-awaiting-approvals-list').change(function () {
        switch (jQuery('#filter-dropdown', '#cp-awaiting-approvals-list').val()) {
            case 'my_approvals':
                window.location.href = getBaseURL('customer-portal') + 'contracts/awaiting_approvals/my_approvals/';

                break;
            default:
                window.location.href = getBaseURL('customer-portal') + 'contracts/awaiting_approvals';
                break;
        }
    });
});