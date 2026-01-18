jQuery(document).on('click', '.integration-tab',function(){
    let integration = jQuery(this).data('integration-code');
    let isIframeLoaded = jQuery(this).data('iframe-loaded');

    if (!isIframeLoaded) {// check if the iframe hasn't been loaded yet
        loadApp4Legal360DocsIframe(integration);
        jQuery(this).data('iframe-loaded', true);// set the iframe loaded flag as true
    }
});

function loadApp4Legal360DocsIframe(integration) {
    let app4Legal360DocsUrl = `https://docs.app4legal.com/${integration}`;
    let lang                = document.documentElement.lang;
    let moduleName          = document.getElementById('module').value;
    let moduleRecordId      = document.getElementById('module-record-id').value;

    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + '/users/get_api_token_data',
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                let jsonObj = {
                    "x-api-key": response.apiKey,
                    "api-base-url": response.apiBaseUrl,
                    "lang": lang,
                    "module": moduleName,
                    "module-record-id": moduleRecordId
                };
                let iframeEl = document.getElementById(`app4legal-${integration}-iframe`);
                if (iframeEl != null) {
                    iframeEl.addEventListener('load', function() {
                        console.log(`${integration} iframe is loaded, sending message...`);
                        iframeEl.contentWindow.postMessage({payload: jsonObj}, "*");
                        jQuery('#loader-global').hide();
                    });
                }
                iframeEl.src = app4Legal360DocsUrl;
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}