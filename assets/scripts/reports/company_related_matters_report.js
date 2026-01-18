jQuery(document).ready(function(){
    var container = jQuery('#company-form');
    var lookupDetails = {'lookupField': jQuery('#client-lookup'),hiddenId: jQuery('#company-id'),'errorDiv': 'companies'};
    lookUpCompanies(lookupDetails, container);
    jQuery(this).parents('#company-form').find('.empty').removeAttr('onClick');
    jQuery('#client-lookup').bind('typeahead:asynccancel typeahead:asyncreceive',function (obj, datum) {
        if (datum === undefined) {
            jQuery('.empty', lookupDetails['lookupContainer']).html(_lang.no_results_matched_for.sprintf([lookupDetails['lookupField'].val()])).removeClass('click').attr('onClick', '');
        }
    });
    jQuery('#client-lookup').bind('typeahead:select', function(ev, suggestion) {
        ev.preventDefault();
        var companyLabel = suggestion.name + (suggestion.shortName != null ? ' (' + suggestion.shortName + ')' : '');
        jQuery('input#company-id').val(suggestion.id);
        jQuery(this).typeahead('val', companyLabel);
        changeCompany();
    });
    jQuery('.data-table').DataTable({
        "bPaginate": true,
        "bLengthChange": false,
        "bFilter": true,
        "bInfo": true,
        "bAutoWidth": false,
        "searching": false,
        "pageLength": 10,
        "aaSorting": []
    });

});
function changeCompany(){
    if(jQuery('#company-id').val()!='')
window.location=getBaseURL()+'reports/company_related_matters/'+jQuery('#company-id').val();
        return false;
}
function toggleElements(elementsToggleIcon, elementContainer) {
    if (elementContainer.is(':visible')) {
        elementContainer.slideUp();
        elementsToggleIcon.removeClass('fa-solid fa-chevron-down');
        elementsToggleIcon.addClass('fa-solid fa-chevron-right');
        elementContainer.addClass('d-none');
        updateURL(elementContainer.attr("id"),true);
    }else {
        elementContainer.slideDown();
        elementsToggleIcon.removeClass('fa-solid fa-chevron-right');
        elementsToggleIcon.addClass('fa-solid fa-chevron-down');
        elementContainer.removeClass('d-none');
        updateURL(elementContainer.attr("id"));
    }
}
function expandAllCompanies() {
    jQuery('.stage-box', jQuery('#company-matters')).slideDown();
    jQuery('a.collapsing-arrow > i', jQuery('#company-matters')).removeClass('fa-solid fa-chevron-right').addClass('fa-solid fa-chevron-down');
}
function collapseAllCompanies() {
    jQuery('.stage-box', jQuery('#company-matters')).slideUp();
    jQuery('a.collapsing-arrow > i', jQuery('#company-matters')).removeClass('fa-solid fa-chevron-down').addClass('fa-solid fa-chevron-rightt');
}