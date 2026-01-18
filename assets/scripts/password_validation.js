var passwordValidation = (function() {
    'use strict';
    /**
     * @param element
     */

    /**
     * @param string
     */
    function hasLowerCase(string) {
        return (/[a-z]/.test(string));
    }
    /**
     * @param string
     */
    function hasUpperCase(string) {
        return /[A-Z]/.test(string)
    }

    function hasNumberCase(string) {
        return /[1-9]/.test(string)
    }

    function checkStrongPassword(element, actionButton) {
        element.on("blur", function (event) {
            var str = jQuery(this).val();
            var filter = /(?=^.{8,}$)((?=.*\d)(?=.*[A-Z])(?=.*[a-z]))^.*/;
            if (!filter.test(str)) {
                jQuery(this).addClass('warning');
                jQuery(this).next().removeClass('d-none');
                var message = '';
                message += getMessage((str.length < 8), _lang.eightCharacters ,'');
                message += getMessage((hasLowerCase(str)), _lang.lowercase ,_lang.one);
                message += getMessage((hasUpperCase(str)), _lang.uppercase ,_lang.one);
                message += getMessage((hasNumberCase(str)),_lang.digit ,_lang.one);
                jQuery(this).next().find('li').html(message);
                actionButton.attr("disabled","disabled");
            } else {
                actionButton.removeAttr("disabled");
                jQuery(this).removeClass('warning');
                jQuery(this).next().addClass('d-none');
            }
        });
    }

    function getMessage(state, langMessage, digitMessage) {
        return state ? "<span class=\"fa-solid fa-trash-can red error_color\"></span><span class='error_color'> "+ ((_lang.languageSettings['langDirection'] === 'rtl') ? (langMessage + " " + digitMessage) : (digitMessage + " " + langMessage)) + "</span></br>" :
            '<span class=\"fa-solid fa-circle-check limegreen\"></span><span class=\'limegreen\'> ' + ((_lang.languageSettings['langDirection'] === 'rtl') ? (langMessage + " " + digitMessage) : (digitMessage + " " + langMessage)) + '</span></br>';
    }

    function showPassword() {
        let element = document.getElementById("newPassword");
        let showPasswordSelectorImg = jQuery("#show-password-visibility");
        if(element.type === "password"){
            element.type = "text";
            showPasswordSelectorImg.attr("src","assets/images/visible.svg");
        } else{
            element.type = "password";
            showPasswordSelectorImg.attr("src","assets/images/notvisible.svg");
        }
    }

    return {
        checkStrongPassword: checkStrongPassword,
        showPassword: showPassword
    };
}());