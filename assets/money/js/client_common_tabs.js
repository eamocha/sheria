//load client trust account balance
function clientTrustBalance(client,container){
    jQuery.ajax({
        url: getBaseURL('money') + 'clients/load_trust_data/',
        dataType: 'JSON',
        data: {client_id: client},
        type: 'GET',
        success: function (response) {
            if (response.amount) {
                if( !jQuery('.amount', jQuery('#trust-container', container)).length){
                jQuery('.details', jQuery('#trust-container', container)).append('<span class="amount"></span>');
            }
                jQuery('.amount', jQuery('#trust-container', container)).html(response.amount);
            }
        }, complete: function () {
            jQuery('.loader-submit').remove();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}