jQuery(document).ready(function(){

});

/**
 * get the contact related companies,
 * if the contact has more than one company
 * let the user select one company from them
 * 
 * @param {number} contact_id
 */
function getContactCompanies(contact_id){
    jQuery.ajax({
        url: getBaseURL() + 'advisors/get_contact_related_companies/' + contact_id,
        dataType: "json",
        success: function (response) {
            if (response.status) {
                // as the firstLine is empty line, so the number of companies is always n+1
                if (response.totalRows > 2) {
                    advisorCompaniesModal(response.data);
                } else {
                    for (let [key, value] of Object.entries(response.data)) {
                        if(key!=""){
                            jQuery('#lookup-companies').val(value);
                            jQuery('#advisor-company-id').val(key);
                        }
                    }
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler,
    });
}

/**
 * open  advisor companies modal
 * 
 * @param {array} companies 
 */
function advisorCompaniesModal(companies){
    jQuery.ajax({
        url: getBaseURL() + 'advisors/get_advisor_companies_modal/',
        method: 'post',
        data: {companies: companies},
        dataType: "json",
        success: function (response) {
            if (response.status) {
                if (response.html) {
                    if (jQuery('#advisor-companies-modal').length <= 0) {
                        jQuery('<div id="advisor-companies-modal"></div>').appendTo("body");
                        var advisorCompaniesModalContainer = jQuery('#advisor-companies-modal');
                        advisorCompaniesModalContainer.html(response.html);
                        advisorCompaniesModalContainer.find('.modal').modal('show');
                        advisorCompaniesModalEvents(advisorCompaniesModalContainer);
                    }
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler,
    });
}

function advisorCompaniesModalEvents(modalContainer){
    var modal = modalContainer.find('.modal');
    var form = modal.find('#advisor-companies-form');
    var formSubmitBtn = modal.find('#form-submit');

    modal.on('hidden.bs.modal', function () {
        destroyModal(modalContainer);
    });

    formSubmitBtn.click(function(){
        form.submit();
    });

    form.submit(function(e){
        e.preventDefault();

        var selected_company_name = form.find('select[name="advisor_company_id"] > option:selected').text();
        var selected_company_id = form.find('select[name="advisor_company_id"]').val();

        jQuery('#lookup-companies').val(selected_company_name);
        jQuery('#advisor-company-id').val(selected_company_id);
        modal.modal('hide');
    });
}