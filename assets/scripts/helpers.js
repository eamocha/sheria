
/**
 * This module containes all function which is globle and help us to reduce the repetitive code
 * @type {{capitalizeFirstLetter: *, getSettingGridTemplate: *, numberWithCommas: *}}
 */
var helpers = (function() {
    'use strict';

    /**
     * make the first letter of string capital
     * @param string
     * @returns {string}
     */
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    /**
     * Create the gear template of kendo grid
     * @param options [] first option it is the event and the second for title "[list of events or classes, title]"
     * @returns {string}
     */
    function getSettingGridTemplate(options, wheelIcon){
        wheelIcon = wheelIcon || false;
        var listOptions = '';
        if(Array.isArray(options) && options.length){
            jQuery.each(options, function(index, value){
                listOptions += '<a class="dropdown-item" style="width:auto;" href="javascript:;" '+ value[0] +'>' + value[1] + '</a>';
            });
        }
        if(!wheelIcon){
            return '<div class="dropdown">' +
                '<a class="dropdown-toggle-new btn btn-default btn-xs dms-action-wheel" data-toggle="dropdown" href="##">' +
                '<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-three-dots-vertical" fill="currentColor" xmlns="http://www.w3.org/2000/svg">\n' +
                '<path fill-rule="evenodd" d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>\n' +
                '</svg>' +
                '</a>' +
                '<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">' + listOptions + '</ul>' +
                '</div>';
        } else{
            return '<div class="dropdown">' +
                '<a class="dropdown-toggle-new btn btn-default btn-xs dms-action-wheel" data-toggle="dropdown" href="##"><i class="fa-solid fa-gear"></i> <span class="caret no-margin"></span></a>'+
                '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' + listOptions + '</div>' +
                '</div>';
        }
    }

    /**
     * make the number with camma sperator 1000 == 1,000
     * @param number
     * @returns {string}
     */
    function numberWithCommas(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    /**
     * Get name of from id of array {text: value}
     * @param arr
     * @param text
     * @param value
     * @param targetValue
     * @returns {string}
     */
    function getObjectFromArr(arr, text, value, targetValue) {
        targetValue = typeof targetValue.value === "undefined" ? targetValue : targetValue.value;
        var result = '';
        jQuery.each(arr, function(index){
            var item = arr[index];
            if (item[value] == targetValue) {
                result = item[text];
                return true;
            }
        });
        return result;
    }

    /**
     * Toggel all fields inside a container
     * @param container string the container of fields
     * @param state boolean if enable or disable
     */
    function readonlyFieldsEnable(container, state){
        state = state || 'false';
        container = ('string' == typeof container) ? jQuery('#' + container) : container;
        jQuery('.form-control, .btn, select, .select-picker, .input-group-addon, .dropdown-toggle', container).each(function (e, t) {
            jQuery(this).prop('readonly', state === 'false');
        });
        jQuery('.btn, input[type=checkbox], .duplicate-button', container).each(function (e, t) {
            jQuery(this).attr("disabled", "disabled");
        });
    }

    /**
     * Bind name of file uploaded to a span
     * @param element string the container of fields
     * @param fileId span id to bind the input file name to
     */
    function bindFileNameToUploadFile(element, fileId) {
        var fileName = '';
        fileName = jQuery(element).val();
        jQuery('#'+ fileId).html(fileName);
    }

    /**
     * Events fire on page loads
     */
    function onPageLoadEvents()
    {
        jQuery('.notification-send-email').on('click', function () {
            var checkBox = jQuery(this).find("input:checkbox");
            checkBox.prop("checked") ? checkBox.val(1 ): checkBox.val(0 );
        });
        var activeTitle = jQuery('.active-title');
        var parentMenu = activeTitle.parent().parent().siblings();
        jQuery.each(parentMenu, function (index, item){
            if(jQuery(item).hasClass('nav-link')){
                jQuery(item).addClass("opacity-title");
            }
        });
        jQuery('.search').attr('autocomplete','off');
        jQuery('.lookup').attr('autocomplete','off');
        changeActiveMenuItemOnActive();
        userGuideStartUp();
        headerTowlevelsMenu('user-multiple-menu');
        fixFooterHeaderBgColumn();
    }

    function fixFooterHeaderBgColumn()
    {
        jQuery('.footer-bg').attr('style', 'background-color:' + jQuery('.footer').css('background-color') + '!important');
        jQuery('.nav-money-bg').attr('style', 'background-color:' + jQuery('#subNavMenu').css('background-color') + '!important');
    }

    /**
     * To make Main menu active on the page is opened
     */
    function changeActiveMenuItemOnActive(){
        var activeTitle = jQuery('.active-title');
        var parentMenu = activeTitle.parent().parent().siblings();
        jQuery.each(parentMenu, function (index, item){
            if(jQuery(item).hasClass('nav-link')){
                jQuery(item).addClass("opacity-title");
            }
        });
    }

    /**
     * To make Main menu active on the page is opened
     */

    /**
     * To get when a flex box is wrapped or not
     * @param element string flex box container
     * @param callback function a callback fucntion
     * @param callbackBefore function a callback fucntion
     */
    function flexWrapped(element, callback, callbackBefore) {
        callback = callback || false;
        callbackBefore = callbackBefore || false;
        if(isFunction(callbackBefore)) callbackBefore(wrapped);
        var offset_top_prev;
        var wrapped =  false;
        element.each(function() {
            var offset_top = jQuery(this).offset().top;
            if (offset_top > offset_top_prev) {
                wrapped = true;
            }
            offset_top_prev = offset_top;
        });
        if(isFunction(callback)) callback(wrapped);
    }

    function truncate( str, n, useWordBoundary , isRtl){
        if (str.length <= n) { return str; }
        const subString = str.substr(0, n - 1); // the original check
        return !isRtl ? ((useWordBoundary
            ? subString.substr(0, subString.lastIndexOf(" "))
            : subString) + "&hellip;") : ( "&hellip;" + (useWordBoundary
            ? subString.substr(0, subString.lastIndexOf(" "))
            : subString));
    }

    function convertDate(date) {
        if (date instanceof Date === false) {
            date = new Date(date);
        }
        var mnth = ("0" + (date.getMonth() + 1)).slice(-2),
            day = ("0" + date.getDate()).slice(-2),
            hours = ("0" + date.getHours()).slice(-2),
            minutes = ("0" + date.getMinutes()).slice(-2);
        var dateAndTime = {'date': [date.getFullYear(), mnth, day].join("-"), 'time': [hours, minutes].join(":")};
        return dateAndTime;
    }

    function decodeHtml(html) {
        var txt = document.createElement("textarea");
        txt.innerHTML = html;
        return txt.value;
    }

    function userGuideStartUp(){
        if(userGuide == "" && isloggedIn == "logged" && jQuery("#display-whats-new").val() === ""){
            if((typeof openAvatarForm !== 'undefined' && !openAvatarForm) ||  (typeof openAvatarForm === 'undefined')){
                userGuideObject.userGuideSetup();
            }
        }
    }

    function headerTowlevelsMenu(element){
        var dropElement = jQuery(".dropdown-menu a." + element);
        dropElement.on("click", function(e) {
            jQuery(this) .next("ul").toggle();
            e.stopPropagation();
            e.preventDefault();
        });
    }

    function emailValid(email){
        return String(email).toLowerCase().match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
    }

    return {
        capitalizeFirstLetter: capitalizeFirstLetter,
        getSettingGridTemplate: getSettingGridTemplate,
        numberWithCommas: numberWithCommas,
        getObjectFromArr: getObjectFromArr,
        readonlyFieldsEnable: readonlyFieldsEnable,
        bindFileNameToUploadFile: bindFileNameToUploadFile,
        onPageLoadEvents: onPageLoadEvents,
        flexWrapped: flexWrapped,
        truncate: truncate,
        convertDate: convertDate,
        decodeHtml: decodeHtml,
        userGuideStartUp: userGuideStartUp,
        emailValid: emailValid
    };
}());