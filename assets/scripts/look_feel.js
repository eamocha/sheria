var  allowedFileUploads;
jQuery(document).ready(function() {
    /**
     * init_all function 
     */
    init_all();
});

/**
 * init_all function 
 */
function init_all(){
    jQuery('#look_feel').validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false
    });
    
    var text_favicon = jQuery('#favicon-text').text();
    var new_text_favicon =   text_favicon.replace("250", "50");
    jQuery('#favicon-text').text(new_text_favicon.replace("350", "50"));
    jQuery('.activetheme-type').each(function (index) { 
        jQuery(this).selectpicker();
    });
    jQuery('#right_side_theme').on('click', 'div.bhoechie-tab-menu>div.list-group>a', function(e){
        e.preventDefault();
        jQuery(this).siblings('a.active').removeClass("active");
        jQuery(this).addClass("active");
        var index = jQuery(this).index();
        jQuery("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
        jQuery("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
    });     
    // on change theme retrive new html and append 
    jQuery("#activetheme-type").change(function(){
        jQuery.ajax({
            url: getBaseURL() + 'look_feel/load_theme',
            type: 'POST',
            dataType: 'JSON',
            data: {
                app_theme: jQuery(this).val()
            },
            beforeSend: function () {
                jQuery("#loader-global").show();
            },
            success: function (response) {
                jQuery("#loader-global").hide();
                jQuery("#right_side_theme").html(response.right_side_theme);
                jQuery("#left-side-theme").html(response.left_side_theme);
                jQuery('.tooltip-help').each(function(){
                    jQuery(this).tooltipster();
                });
            },
            error: defaultAjaxJSONErrorsHandler
        });
    });
     
    // open the browse pop-up when the user clicks on upload button
    jQuery('#look_feel').on('click', '.upload-file-btn', function(){
        jQuery(jQuery(this).data('target')).click();
    });
    
    jQuery('#look_feel_add_new').validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false
    });
    jQuery("#theme_must_save").val(0);
    if(jQuery('.tooltip-title').hasClass("tooltipstered")){
        jQuery('.tooltip-title').tooltipster('destroy');
    }
    if(!jQuery('.tooltip-help-title').hasClass("tooltipstered")){
        jQuery('.tooltip-help-title').tooltipster();
    }
    if(!jQuery('.tooltip-help-favicon').hasClass("tooltipstered")){
        jQuery('.tooltip-help-favicon').tooltipster();
    }
    if(!jQuery('.tooltip-help-customer-portal-login-logo').hasClass("tooltipstered")){
        jQuery('.tooltip-help-customer-portal-login-logo').tooltipster();
    }
    if(!jQuery('.tooltip-help-customer-portal-logo').hasClass("tooltipstered")){
        jQuery('.tooltip-help-customer-portal-logo').tooltipster();
    }

    $cropperModal = jQuery('#image_crop_modal');
    var image = document.getElementById('cropped_image');
    var cropper, imagePreviewer, maxWidth, maxHeight, inputName, inputId;

    jQuery(".cropper-image-upload").change(function(event){
        inputName = event.target.name;
        inputId = event.target.id;
        var imagePreviewerId = event.target.getAttribute('data-thumbnail-target');
        imagePreviewer = document.getElementById(imagePreviewerId);
        maxWidth = event.target.getAttribute('data-max-width');
        maxHeight = event.target.getAttribute('data-max-height');
        var files = event.target.files;

		var done = function(url){
			image.src = url;
			$cropperModal.modal('show');
		};

        if(files && files.length > 0){
			reader = new FileReader();
			reader.onload = function(event){
				done(reader.result);
			};
			reader.readAsDataURL(files[0]);
		}
    });

    $cropperModal.on('shown.bs.modal', function(){
		cropper = new Cropper(image, {
			viewMode: 1,
            minCropBoxHeight: 30,
            zoomable: false,
			preview:'.cropper-preview'
		});
	}).on('hidden.bs.modal', function(){
		cropper.destroy();
   		cropper = null;
	});
    jQuery('.modal-body').on("scroll", function() {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('#image_crop_save').click(function(){
		canvas = cropper.getCroppedCanvas({
			width: maxWidth,
			height: maxHeight
		});
        var imgSrc = canvas.toDataURL();
        imagePreviewer.src = imgSrc;
        var element = document.createElement("input");
        var elementName = inputName + "_hidden";
        if (document.getElementsByName(elementName).length === 0) {
            element.setAttribute("type", "hidden");
            element.setAttribute("name", elementName);
            element.setAttribute("value", imgSrc);
            document.getElementById(inputId).parentElement.appendChild(element);
        }
        else {
            document.getElementsByName(elementName)[0].setAttribute("value", imgSrc);
        }
	});
    jQuery('.cancel-crop-btn').click(function(){
	    document.getElementById(inputId).value = '';
	});
}
/**
 * open new theme model
 */
function open_new_theme(){ 
    jQuery(".inline-error").html("");       
    // check if add new theme 
    var theme_must_save = jQuery("#theme_must_save");
    if(theme_must_save.val() != 1){
        quickAdministrationDialog('look_feel', jQuery('#new_theme_model', '#save_newtheme'));
    }
}

/**
 * open_color
 * @param {*} el selectore
 * @param {*} id div
 */
function open_color(el,id){
    jQuery(el).closest("li").find("#toggle_div_color").toggle("fast");
    jQuery(el).closest("li").find("#cp1").colorpicker({
        customClass: 'colorpicker-2x',
        format: 'hex',
        sliders: {
            saturation: {
                maxLeft: 200,
                maxTop: 200
            },
            hue: {
                maxTop: 200
            },
            alpha: {
                maxTop: 200
            }
        }
    });
    jQuery(el).closest("li").find("#cp1").colorpicker().on('changeColor', function(e) {
        jQuery(el).closest("li").find('.color-preview').css('background',e.color.toString('rgba'));
        if(e.color.toString('rgba') != jQuery(el).closest("li").find('.value_text').text() ){
            run_tooltipster();
        } 
        jQuery(el).closest("li").find('.value_text').text(e.color.toString('rgba'));
        jQuery("#app_theme_version").val(Math.floor(Math.random()*90000) + 1000);
        jQuery("#theme_must_save").val(1);
    });
}


/**
 * run_tooltipster function
 */
function run_tooltipster() {
    if(!jQuery('.tooltip-title').hasClass("tooltipstered")){
        jQuery('.tooltip-title').tooltipster();
    }
}

function readURL(input,id,max_width,max_height) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        allowedFileUploads = allowedFileUploads ? (allowedFileUploads.constructor === Array ? allowedFileUploads : allowedFileUploads.split('|')) : false;
        reader.onload = function (e) {
            var image = new Image();
            image.src = e.target.result;
            image.onload = function () {
                var MAX_WIDTH = max_width ;
                var MAX_HEIGHT = max_height ;
                var width = this.width;
                var height = this.height;
                if (width > height) {
                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                } else {
                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }
                }
                jQuery('#'+id).attr('src', this.src);
                jQuery('#l'+id).attr('width', width);
                jQuery('#'+id).attr('height', height);
            };
        };
        reader.readAsDataURL(input.files[0]);
    }
    // end old upload copy from old file
}
/**
 * 
 */
function show_guide(type,content,textbutton){
    var target,content,textbutton,position = 'bottom';
    switch (type) {
        case "menu_background_color":
            target = "#top-links-navbar-collapse-2 .nav > li:nth-child(2) > a";
            break;
        case "menu_background_hover_color":
             target ="#top-links-navbar-collapse-2 .nav > li:nth-child(2) > a";
             jQuery(target).first().addClass("hover");
            break;
        case "menu_dropdown_background_color":
             target ="#dashboard-menu-demo-open > .dropdown-menu";
             setTimeout(function(){ 
                jQuery("#dashboard-menu-demo-open > .dropdown-menu").addClass("show");
               }, 100);
             position = {
                    top: '3em',
                    left: '14em'
                };
            break;
        case "menu_dropdown_text_color":
            target ="#dashboard-menu-demo-open > .dropdown-menu";
            setTimeout(function(){ 
             jQuery("#dashboard-menu-demo-open > .dropdown-menu").addClass("show");
            }, 100);
            position = {
                top: '-1em',
                left: '14em'
            };
         break;
        case "menu_universal_search_background_color":
            target = "#universal_search";
            break;
         case "buttons_background_color":
             target = ".btn-info";
            break;
        case "buttons_text_color":
             target = ".btn-info";
            break;
        case "buttons_hover_background_color":
             target = ".btn-info";
             jQuery(target).first().addClass("hover");
        case "buttons_background_hover_color":
             target = ".btn-info";
             jQuery(target).first().addClass("hover");
            break;
        case "buttons_hover_text_color":
             target = ".btn-info";
             jQuery(target).first().addClass("hover");
            break;
        case "tabs_header_background_color":
            target = ".bhoechie-tab-menu div.list-group>a:first-child";
            break;
        case "tabs_header_active_background_color":
            target = ".bhoechie-tab-menu div.list-group > a.active";
             break;  
        case "tabs_header_active_text_color":
            target = ".bhoechie-tab-menu div.list-group > a.active";
            break;
        case "tabs_header_active_text_hover_color":
             target = ".bhoechie-tab-menu div.list-group > a.active";
             jQuery(target).first().addClass("hover");
            break;     
        case "footer_background_color":
            target = "#footer";
            position = 'top';
           break;
        case "footer_text_color":
            target = "#footer p a";
            position = 'top';
          break;
        case "footer_background_body":
          target ="#wrap";
         break;
        case "look_feel_logo_browser":
         target =".app-logo";
         break;
        case "look_feel_footer_logo":
            target =".footer1";
            position = 'center-bottom';
         break;
        case "look_feel_app_login_second_logo_browser":
            target =".top1";
        break;
         
        default:
            target = ".top1";
    }
    var anno1 = new Anno({
        target : target,
        content: content,
        position:position,
        buttons: [
            {
                text: textbutton,
                click: function(anno, evt){
                    anno.hide();
                    evt.preventDefault();
                }
            }
        ],
        onHide: function(anno, $target, $annoElem, returnFromOnShow) {
            jQuery(target).removeClass("hover");
            jQuery(target).removeClass("show");

               // target footer fix css
            if(type == 'footer_text_color'){
                jQuery(target).attr("style",' ');
            }
        },
        onShow: function (anno, $target, $annoElem) {
            // target footer fix css
            if(type == 'footer_text_color'){
                jQuery(target).attr('style','top:12px;background:transparent;');
                jQuery(".anno-placeholder").hide();
            }
            if(type == 'menu_dropdown_background_color'){
                jQuery(target).attr("style", ' ');
            }
            if (type == 'look_feel_logo_browser'){
                jQuery(target).attr('style','background-color:transparent;');
            }
        }
    });
    anno1.show();
}

function look_feel_add_new() {
    var form = jQuery("#look_feel_add_new").serialize();
    jQuery.ajax({
        url: jQuery("#look_feel_add_new").attr('action'),
        dataType: 'JSON',
        type: 'POST',
        data: form,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if(response.validationErrors){
                jQuery(".inline-error").html(response.validationErrors);
            }else{
                var redirect_to = jQuery(".theme_name").val();
                jQuery('#new_theme_model').modal('toggle'); 
                pinesMessage({ty: 'success', m: response.done});
                setTimeout(function(){ 
                    window.location.href = getBaseURL() + 'look_feel/index?active='+redirect_to;
                }, 1000);
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

/**
 * confirmationDialog
 * @param {*} key 
 * @param {*} resultHandlerArray 
 */
function confirmationDialog(key, resultHandlerArray) {
    var confirmationCategory = resultHandlerArray.confirmationCategory ? resultHandlerArray.confirmationCategory : 'default'; // this flag will be used to color the button "yes", the default is blue
    jQuery.ajax({
        url: getBaseURL() + 'home/confirm_request/',
        dataType: 'JSON',
        type: 'POST',
        data: {
            key_message: key,
            confirmation_category: confirmationCategory // default => blue, danger => red (btn-danger), warning => orange (btn-warning), success => green (btn-warning), ...
        },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery(".confirmation-dialog-container").length <= 0) {
                    jQuery('<div class="d-none confirmation-dialog-container"></div>').appendTo("body");
                    var confirmationContainer = jQuery('.confirmation-dialog-container');
                    confirmationContainer.html(response.html).removeClass('d-none');
                    jQuery('.modal', confirmationContainer).addClass("confirmation-dialog-modal");
                    jQuery('.modal', confirmationContainer).modal({
                        keyboard: false,
                        show: true,
                        backdrop: 'static'
                    });
                    resizeMiniModal(confirmationContainer);
                    jQuery(window).bind('resize', (function () {
                        resizeMiniModal(confirmationContainer);
                    }));
                    jQuery("#cancel-confirmation-dialog", confirmationContainer).click(function () {
                        if (resultHandlerArray.onCloseHandler) {
                            resultHandlerArray.onCloseHandler(resultHandlerArray.onCloseParm ? resultHandlerArray.onCloseParm : false);
                        }
                    });
                    jQuery("#confirmation-dialog-submit", confirmationContainer).click(function () {
                        modalDismiss(confirmationContainer, resultHandlerArray);
                        if (!resultHandlerArray.resultHandler) {
                            jQuery('.modal', confirmationContainer).modal('hide');
                            resultHandlerArray.modelOpen.modal('hide');
                            return;
                        }
                        resultHandlerArray.resultHandler(resultHandlerArray.parm ? resultHandlerArray.parm : false);
                        jQuery('.modal', confirmationContainer).modal('hide');
                    });
                    jQuery("#confirmation-dialog-submit").keypress(function(e) {
                        e.preventDefault();
                        if (e.which == 13) {
                            modalDismiss(confirmationContainer, resultHandlerArray);
                        }
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(confirmationContainer);
                    });
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
/**
 * look_feel_restore_default
 * @param {*} param 
 */
function look_feel_restore_default(param) {
    window.location.replace(param.url);
}
/**
 * remove_image
 * @param {*} param 
 */
function remove_image(param) {
    window.location.replace(param.url);
}

function modalDismiss(confirmationContainer, resultHandlerArray)
{
    if (!resultHandlerArray.resultHandler) {
        jQuery('.modal', confirmationContainer).modal('hide');
        resultHandlerArray.modelOpen.modal('hide');
        return;
    }
    resultHandlerArray.resultHandler(resultHandlerArray.parm ? resultHandlerArray.parm : false, typeof resultHandlerArray.module !== 'undefined' ? resultHandlerArray.module : false);
    jQuery('.modal', confirmationContainer).modal('hide');
}
