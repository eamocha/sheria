var userActivityLogForm = jQuery("#userActivityLogForm");
var caseClientId = null, caseClient = null;
var timelogCaseClientMismatch = jQuery("#timelog-case-client-mismatch", userActivityLogForm).val();
disableAutocomplete(userActivityLogForm);
if (timelogCaseClientMismatch != "false") {
    pinesMessage({ty: 'warning', m: _lang.timelogCaseClientMismatch});
}
selectTimeStatus(jQuery("input[name='timeStatus']:checked", userActivityLogForm).val(), true);

jQuery(document).ready(function(){
    jQuery('.effective-effort-tooltip').tooltipster({
            contentAsHTML: true,
            timer: 22800,
            animation: 'grow',
            delay: 200,
            theme: 'tooltipster-default',
            touchDevices: false,
            trigger: 'hover',
            maxWidth: 350,
            interactive: true
    });
});

function validateNumbers(field, rules, i, options) {
    var val = field.val();
    var decimalPattern = /^[0-9]+(\.[0-9]{1,2})?$/;
    if (!decimalPattern.test(val)) {
        return _lang.decimalAllowed;
    }
}
function selectTimeStatus(timeStatus, onLoad) {
    var onLoad = onLoad || false;
    if (timeStatus == "internal") {
        jQuery("#client-div", userActivityLogForm).addClass("d-none");
    } else {
        if (jQuery("#legalCaseLookupId", userActivityLogForm).val() != "" || jQuery("input[name='logOptionsMenu']:checked", userActivityLogForm).val() == "case") {
            jQuery("#client-div", userActivityLogForm).removeClass("hide");
            if (!onLoad && jQuery("#client-id", userActivityLogForm).val() == "") {
                fetchCaseClient({"caseId": jQuery("#legalCaseLookupId", userActivityLogForm).val(), "clientLookupField": jQuery("#client-name", userActivityLogForm), "clientHiddenField": jQuery("#client-id", userActivityLogForm)});
            }
        }
    }
}
function checkClientName() {
    if (jQuery("#client-name", userActivityLogForm).val() == "") {
        jQuery("#client-id", userActivityLogForm).val("");
    }
}
function onCaseSelectTimeTrackingHandler(records) {
    if (records["timeTrackingBillable"] === "1") {
        jQuery("#billable").attr("checked", true);
    }
    else {
        jQuery("#internal").attr("checked", true);
    }
    caseClientId = records.client_id;
    caseClient = records.clientName;
    if (jQuery('input[name="timeStatus"]:checked', userActivityLogForm).val() == "billable") {
        jQuery("#client-div", userActivityLogForm).removeClass("hide");
    }
    if (caseClientId) {
        jQuery("#client-id", userActivityLogForm).val("");
        checkCaseClient();
    } else {
        jQuery("#client-name", userActivityLogForm).attr("readonly", false).val("");
        jQuery("#client-id", userActivityLogForm).val("");
    }
}
function checkCaseClient() {
    var caseId = jQuery("#legalCaseLookup", userActivityLogForm).val();
    var clientId = jQuery("#client-case-id", userActivityLogForm).val(); //id of the client for the case
    var caseClientName = jQuery("#client-case-name", userActivityLogForm).val();//name of the client for the case
    if (caseId) {
        if (caseClientId || clientId) {
            if (jQuery("#client-id", userActivityLogForm).val() == '') {
                jQuery("#client-name", userActivityLogForm).val(caseClient ? caseClient : caseClientName).attr("readonly", true);
                jQuery("#client-id", userActivityLogForm).val(caseClientId ? caseClientId : clientId);
            }
        }
    }
}
function onCaseLookupChange(lookupFiled) {
    if (lookupFiled.value == "") {
        jQuery("#legalCaseLookupId", lookupFiled.parentNode.parentNode.parentNode.parentNode).val("");
        jQuery("#legalCaseSubject", lookupFiled.parentNode).text("");
        jQuery("#client-name", userActivityLogForm).attr("readonly", false);
        if (jQuery("input[name='logOptionsMenu']:checked", userActivityLogForm).val() == "task") {
            jQuery("#client-div", userActivityLogForm).addClass("d-none");
        }
    }
}
