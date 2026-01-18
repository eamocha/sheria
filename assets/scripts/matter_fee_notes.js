
function fetchClientAccountStatus(caseId) {

    jQuery.ajax({
        url: getBaseURL() + 'cases/get_matter_account_status/' + caseId,
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.success) {
                jQuery('#extCounsel-total_fees').html(formatMoney(response.data.total_fees));
                jQuery('#extCounsel-amount_settled').html(formatMoney(response.data.total_settled));
                jQuery('#extCounsel-balance_due').html(formatMoney(response.data.balance_due));
            } else {
                pinesMessageV2({ ty: 'error', m: response.message });
            }
        }, complete: function () {

        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
    try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

        const negativeSign = amount < 0 ? "-" : "";

        let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
    } catch (e) {
        console.log(e)
    }
}
//list of matter feeNotes from cases/list_matter_feenotes(id). load the retruned response.html to #feeNotes-container modal
function list_matter_feenotes(caseId) {
    jQuery.ajax({
        url: getBaseURL() + 'cases/list_matter_feenotes/' + caseId,
        type: "GET",
        dataType: "json",
        beforeSend: function() {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.success) {
                // Remove any existing modal to avoid duplicates
                jQuery('#feeNotesModal').remove();
                // Append the modal HTML to the body
                jQuery('body').append(response.html);
                // Show the modal
                jQuery('#feeNotesModal').modal('show');
            } else {
                pinesMessageV2({ ty: 'error', m: response.message });
            }
        },
        complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}