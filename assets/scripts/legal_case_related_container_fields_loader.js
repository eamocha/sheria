function loadContainerFields(){
    jQuery.ajax({
        url: getBaseURL() + "case_containers/load_container_fields/",
        type: 'GET',
        dataType: "json",
        data: {container_id: jQuery('#id', '#caseContainerContainer').val()},
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            if (response.html) {
                jQuery('#container-display-fields', '#caseContainerContainer').html(response.html);
                jQuery('#caseContainerContainer').removeClass("d-none");
            }
        }
    });
}

jQuery(document).ready(function(){
    loadContainerFields();
});