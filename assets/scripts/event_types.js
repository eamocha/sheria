
jQuery(document).ready(function() {
    /**
     * init_all function 
     */
    init_all();   
    
});

function init_all(){
    jQuery("#eventTypeForm").submit(function(event){
        event.preventDefault();
        var form = jQuery(this).serialize();
        jQuery.ajax({
            url: jQuery(this).attr('action'),
            dataType: 'JSON',
            type: 'POST',
            data: form,
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if(response.validationErrors){
                    jQuery(".inline-error").html("");
                    jQuery.each(response.validationErrors , function( index, value ) {
                        jQuery('*[data-field="'+index+'"]').html(value); 
                    });
                }else{
                    pinesMessage({ty: 'success' , m: response.done});
                    setTimeout(function(){ 
                        window.location.replace(getBaseURL() + 'event_types');
                     }, 1000);
                }
               
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    });
}
