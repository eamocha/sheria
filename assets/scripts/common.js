datePickerOptions = {
    weekStart: 1,
    todayHighlight: true,
    format: "yyyy-mm-dd",
    autoclose: true,
    showOnFocus: false,
    language: _lang.languageSettings['langName'],
    startDate: -Infinity,
    endDate: Infinity,
    viewMode: 'days',
    minViewMode: 'days'
};
var companyContactFormMatrix = { contactDialog: {}, companyDialog: {}, commonLookup: {} };
var lowerBound = 100, upperBound = 999;
var escapeHtmlMap = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
};

function addslashes(e) {
    return(e + "").replace(/[\\"']/g, "\\$&").replace(/\u0000/g, "\\0");
}
function parseDate(e, t) {
    if (e == "")
        return new Date("");
    t = t || "yyyy-mm-dd";
    var n = e.match(/(\d+)/g), r = 0, i = {};
    t.replace(/(yyyy|dd?|mm?)/g, function (e) {
        i[e] = r++;
    });
    return new Date(n[i["yyyy"]], n[i["mm"] == undefined ? i["m"] : i["mm"]] - 1, n[i["dd"] == undefined ? i["d"] : i["dd"]]);
}
function convertVErrorToPoshyTip(e) {
    e = e || ".help-block.error";
    jQuery(e).each(function (e, t) {
        var n = t.innerHTML;
        jQuery(t.parentNode).children(":first").poshytip({className: "tip-error", content: n, showOn: "none", alignTo: "target", alignX: "left", alignY: "center", offsetX: 5}).poshytip("show");
        jQuery(t).remove();
    });
}
function getBaseURL(e) {
    if ("string" == typeof e)
        e = "modules/" + e + "/";
    else
        e = "";
    if (jQuery("base:first").length == 1 ) {
        return jQuery("base:first").attr("href") + e;
    }
}
function scrollToId(e, t) {
    t = t || "html,body";
    jQuery(t).animate({scrollTop: jQuery(e).offset().top - jQuery("#header_div")[0].clientHeight}, {queue: false});
}
function ctrlS(e) {
    return true; //To avoid conflict between dialog and edit forms (if edit form is openned and then a dialog,when using ctrlS the edit form is submitting and the dialog closes losing the data that has been entered
    if (true === jQuery.browser.mozilla) { // Firefox + IE
        jQuery(window).keypress(function (t) { // not working on IE
            if (!(t.which == 115 && t.ctrlKey && !t.altKey) && !(t.which == 19))
                return true;
            e();
            t.preventDefault();
            return false;
        });
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE "); // If Internet Explorer 10 | 9 | 8 | ... => return version number
        var isIE11 = !!navigator.userAgent.match(/Trident\/7\./);
        if (msie > 0 || isIE11) {
            document.addEventListener("keydown", function (t) {
                if (t.keyCode == 83 && (navigator.platform.match("Mac") ? t.metaKey : t.ctrlKey) && !t.altKey) {
                    t.preventDefault();
                    e();
                }
            }, false);
        }
    } else {
        if (document.addEventListener) {
            document.addEventListener("keydown", function (t) {
                if (t.keyCode == 83 && (navigator.platform.match("Mac") ? t.metaKey : t.ctrlKey) && !t.altKey) {
                    t.preventDefault();
                    e();
                }
            }, false);
        } else if (document.attachEvent) {
            document.attachEvent("onkeydown", function (t) {
                if (t.keyCode == 83)
                    if (t.keyCode == 83 && t.ctrlKey && !t.altKey) {
                        e();
                        t.returnValue = false;
                    }
            }, false);
        }
    }
}
function ctrlAltS(e) {
    if (true === jQuery.browser.mozilla) {
        jQuery(window).keypress(function (t) {
            if (!(t.which == 115 && t.ctrlKey && t.altKey) && !(t.which == 19))
                return true;
            e();
            t.preventDefault();
            return false;
        });
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE "); // If Internet Explorer 10 | 9 | 8 | ... => return version number
        var isIE11 = !!navigator.userAgent.match(/Trident\/7\./);
        if (msie > 0 || isIE11) {
            document.addEventListener("keydown", function (t) {
                if (t.keyCode == 83 && (navigator.platform.match("Mac") ? t.metaKey : t.ctrlKey) && t.altKey) {
                    t.preventDefault();
                    e();
                }
            }, false);
        }
    } else {
        if (document.addEventListener) {
            document.addEventListener("keydown", function (t) {
                if (t.keyCode == 83 && (navigator.platform.match("Mac") ? t.metaKey : t.ctrlKey) && t.altKey) {
                    t.preventDefault();
                    e();
                }
            }, false);
        } else if (document.attachEvent) {
            document.attachEvent("onkeydown", function (t) {
                if (t.keyCode == 83)
                    if (t.keyCode == 83 && t.ctrlKey && t.altKey) {
                        e();
                        t.returnValue = false;
                    }
            }, false);
        }
    }
}
function ctrlC(e) {
    if (true === jQuery.browser.mozilla) {
        jQuery(window).keypress(function (t) {
            if (!(t.which == 99 && t.ctrlKey && !t.altKey) && !(t.which == 19))
                return true;
            e();
            t.preventDefault();
            return false;
        });
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE "); // If Internet Explorer 10 | 9 | 8 | ... => return version number
        var isIE11 = !!navigator.userAgent.match(/Trident\/7\./);
        if (msie > 0 || isIE11) {
            document.addEventListener("keydown", function (t) {
                if (t.keyCode == 67 && (navigator.platform.match("Mac") ? t.metaKey : t.ctrlKey && !t.altKey)) {
                    e();
                    t.preventDefault();
                }
            }, false);
        }
    } else {
        if (document.addEventListener) {
            document.addEventListener("keydown", function (t) {
                if (t.keyCode == 67 && (navigator.platform.match("Mac") ? t.metaKey : t.ctrlKey && !t.altKey)) {
                    e();
                    t.preventDefault();
                }
            }, false);
        } else if (document.attachEvent) {
            document.attachEvent("onkeydown", function (t) {
                if (t.keyCode == 67)
                    if (t.keyCode == 67 && t.ctrlKey && !t.altKey) {
                        if (t.altKey) {
                            e();
                            t.returnValue = false;
                        }
                    }
            }, false);
        }
    }
}
function ctrlAltC(e) {
    if (true === jQuery.browser.mozilla) {
        jQuery(window).keypress(function (t) {
            if (!(t.which == 99 && t.ctrlKey && t.altKey) && !(t.which == 19))
                return true;
            e();
            t.preventDefault();
            return false;
        });
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE "); // If Internet Explorer 10 | 9 | 8 | ... => return version number
        var isIE11 = !!navigator.userAgent.match(/Trident\/7\./);
        if (msie > 0 || isIE11) {
            document.addEventListener("keydown", function (t) {
                if (t.keyCode == 67 && (navigator.platform.match("Mac") ? t.metaKey : t.ctrlKey && t.altKey)) {
                    e();
                    t.preventDefault();
                }
            }, false);
        }
    } else {
        if (document.addEventListener) {
            document.addEventListener("keydown", function (t) {
                if (t.keyCode == 67 && (navigator.platform.match("Mac") ? t.metaKey : t.ctrlKey && t.altKey)) {
                    e();
                    t.preventDefault();
                }
            }, false);
        } else if (document.attachEvent) {
            document.attachEvent("onkeydown", function (t) {
                if (t.keyCode == 67)
                    if (t.keyCode == 67 && t.ctrlKey && t.altKey) {
                        if (t.altKey) {
                            e();
                            t.returnValue = false;
                        }
                    }
            }, false);
        }
    }
}
function pinesMessage(msgObj) {
    var feedbackMessageUniqueId = (new Date().getTime() * Math.floor(Math.random() * 1000000));
    var n = noty({
        layout: 'topRight',
        template:
                '<div id="feedback-message-container-' + feedbackMessageUniqueId + '" class="feedback-message-container">' +
                '<div id="feedback-message-header" class="feedback-message-header feedback-message-type-' + msgObj.ty + '">' +
                '<img id="feedback-message-icon" src="assets/images/icons/32/fb_' + msgObj.ty + '.png" class="feedback-message-icon"/>' +
                '<span class="feedback-message-title">' + _lang.feedback_messages[msgObj.ty].toUpperCase() + '</span>' +
                '<span id="feedback-message-close-button-' + feedbackMessageUniqueId + '" class="feedback-message-close-button">&#10005;</span>' +
                '</div>' +
                '<div id="feedback-message-body" class="feedback-message-body">' +
                '<span id="feedback-message-text" class="feedback-message-text">' + decodeURIComponent(msgObj.m) + '</span>' +
                '</div>' +
                '</div>',
        animation: {
            open: 'animated fadeInDown',
            close: 'animated fadeOutUp'
        },
        callback: {
            afterShow: function () {
                jQuery('#feedback-message-close-button-' + feedbackMessageUniqueId).click(function () {
                    n.close();
                });
                if (msgObj.d !== 0) { //when msgObj.d is not set or set but not = 0 then the pinesmessage will disappear after a while by itself while when msgObj.d = 0 it will disappear only if the user closes the window
                    jQuery('#feedback-message-container-' + feedbackMessageUniqueId).mouseenter(function () {
                        jQuery('#feedback-message-container-' + feedbackMessageUniqueId).attr('data-extend-timeout', 'true');
                    });
                    jQuery('#feedback-message-container-' + feedbackMessageUniqueId).mouseleave(function () {
                        jQuery('#feedback-message-container-' + feedbackMessageUniqueId).removeAttr('data-extend-timeout');
                        timeOut(1000);
                    });
                    timeOut(msgObj.d || 3000);
                    function timeOut(delay) {
                        setTimeout(function () {
                            if (jQuery('#feedback-message-container-' + feedbackMessageUniqueId).attr('data-extend-timeout') === undefined) {
                                n.close();
                            }
                        }, delay);
                    }
                }
            }
        },
        closeWith: ['button']
    });
}
function rawurlencode(e) {
    e = (e + "").toString();
    return encodeURIComponent(e).replace(/!/g, "%21").replace(/'/g, "%27").replace(/\(/g, "%28").replace(/\)/g, "%29").replace(/\*/g, "%2A");
}

function resizeNewDialogWindow(e, w, h) {
    if (w === undefined || w < 0) {
        var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        var t = Math.floor(w - 180), t = t > 1280 ? 1280 : t;
    } else {
        t = w;
    }
    if (h === undefined || h < 0) {
        var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - 60;
        var r = jQuery("#wrap")[0].clientHeight - jQuery(".footer")[0].clientHeight;
        if (r > h || r < 400)
            r = h;
        var headerHeight = 80;
        var i = Math.floor(r - headerHeight - 10);
    } else {
        i = h;
    }
    e.dialog("option", "height", i).dialog("option", "width", t).dialog("option", "position", {my: 'center top', at: 'center bottom', of: jQuery('#header_div')});
}
function quick_search(t) {
    if (t !== "") {
        var ua = getBaseURL() + "search/universal/1";
        var et = rawurlencode(t);
        window.location.href = ua + '?keyword=' + et;
    }
}
function defaultAjaxJSONErrorsHandler(e, n) {
    switch (e.responseText) {
        case"access_denied":
            ajaxAccessDenied();
            break;
        case"feature_access_denied":
            ajaxFeatureAccessDenied();
            break;
        case"login_needed":
            ajaxLoginForm();
            break;
        case"cp_login_needed":
            ajaxLoginForm('customer-portal');
            break;
        case"undefined_rate":
            window.location = getBaseURL('money') + "setup/rate_between_money_currencies"; 
            break;
        case "":
            break;
        default:
            if ('string' == typeof n)
                pinesMessage({ty: "warning", m: n});
            break;
    }
    if (jQuery("#loader-global").is(':visible'))
        jQuery("#loader-global").hide();
}
function defaultAjaxHTMLErrorsHandler(e) {
    switch (e) {
        case"access_denied":
            ajaxAccessDenied();
            break;
        case"feature_access_denied":
            ajaxFeatureAccessDenied();
            break;
        case"login_needed":
            ajaxLoginForm();
            break;
        case"cp_login_needed":
            ajaxLoginForm('customer-portal');
            break;
        default:
            pinesMessage({ty: "warning", m: e});
            break
    }
}
function ajaxAccessDenied() {
    pinesMessage({ty: "warning", m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page});
}
function ajaxFeatureAccessDenied() {
    var planFeatureWarningMsgsObj = JSON.parse(planFeatureWarningMsgs);
    pinesMessage({ty: "warning", m: planFeatureWarningMsgsObj['In-line-Word-Editor'] ? planFeatureWarningMsgsObj['In-line-Word-Editor'] : _lang.you_do_not_have_enough_previlages_to_access_the_requested_feature});
}
function ajaxLoginForm(module) {
    module = module || false;
    var loggedinUserId = jQuery("#loggedin-user-id").attr('value');
    if (loggedinUserId === "" || undefined === loggedinUserId) {
        window.location = (module ? getBaseURL(module) : getBaseURL())  + "users/login";
    } else {
        jQuery.ajax({
            url: (module ? getBaseURL(module) : getBaseURL()) + "users/login",
            type: "GET",
            dataType: "JSON",
            data: {"id":loggedinUserId},
            beforeSend: function () {
            },
            success: function (response) {
                if (jQuery(".login-onthefly-hidden").length <= 0) {
                    jQuery('<div class="d-none login-onthefly-hidden"></div>').appendTo("body");
                    if (response.html) {
                        var onthefly_hidden = jQuery('.login-onthefly-hidden');
                        onthefly_hidden.html(response.html).removeClass('d-none');
                        jQuery("#username", onthefly_hidden).val(response.username).attr("readonly", "readonly");
                        jQuery('.modal', onthefly_hidden).modal({
                            keyboard: false, backdrop: 'static', show: true});
                        jQuery('.modal').on('hidden.bs.modal', function () {
                            destroyModal(onthefly_hidden);
                        });
                        jQuery('.modal-body').on("scroll", function() {
                            jQuery('.bootstrap-select.open').removeClass('open');
                        });
                        jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
                            jQuery('#password', onthefly_hidden).focus();
                        });
                        jQuery(onthefly_hidden).find('input').keypress(function (e) {
                            // Enter pressed?
                            if (e.which == 13) {
                                jQuery("#login-button-on-fly", "#loginForm").trigger("click");
                            }
                        });
                        resizeLoginModal(onthefly_hidden);
                        jQuery(window).bind('resize', (function () {
                            resizeLoginModal(onthefly_hidden);
                        }));
                    }
                }
            }, error: defaultAjaxJSONErrorsHandler});
    }
}
function effectiveEffortCompact(e) {
    return e == null ? "" : String(e);
    $_systemDefaults.businessWeekEquals = parseFloat($_systemDefaults.businessWeekEquals);
    $_systemDefaults.businessDayEquals = parseFloat($_systemDefaults.businessDayEquals);
    var t = Math.floor(e / $_systemDefaults.businessDayEquals);
    var n = Math.floor(t / $_systemDefaults.businessWeekEquals), r = parseFloat(e % $_systemDefaults.businessDayEquals);
    t -= n * $_systemDefaults.businessWeekEquals;
    var i = "";
    if (n > 0)
        i += String(n) + "w ";
    if (t > 0)
        i += String(t) + "d ";
    if (r > 0)
        i += String(r) + "h";
    return i;
}
function json_decode(str_json) {
    var json = this.window.JSON;
    if (typeof json === "object" && typeof json.parse === "function") {
        try {
            return json.parse(str_json);
        } catch (err) {
            if (!(err instanceof SyntaxError)) {
                throw new Error("Unexpected error type in json_decode()")
            }
            this.php_js = this.php_js || {};
            this.php_js.last_error_json = 4;
            return null
        }
    }
    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
    var j;
    var text = str_json;
    cx.lastIndex = 0;
    if (cx.test(text)) {
        text = text.replace(cx, function (e) {
            return"\\u" + ("0000" + e.charCodeAt(0).toString(16)).slice(-4)
        })
    }
    if (/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:\s*\[)+/g, ""))) {
        j = eval("(" + text + ")");
        return j
    }
    this.php_js = this.php_js || {};
    this.php_js.last_error_json = 4;
    return null
}
function json_encode(e) {
    var t, n = this.window.JSON;
    try {
        if (typeof n === "object" && typeof n.stringify === "function") {
            t = n.stringify(e);
            if (t === undefined) {
                throw new SyntaxError("json_encode")
            }
            return t
        }
        var r = e;
        var i = function (e) {
            var t = /[\\\"\u0000-\u001f\u007f-\u009f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
            var n = {"\b": "\\b", "	": "\\t", "\n": "\\n", "\f": "\\f", "\r": "\\r", '"': '\\"', "\\": "\\\\"};
            t.lastIndex = 0;
            return t.test(e) ? '"' + e.replace(t, function (e) {
                var t = n[e];
                return typeof t === "string" ? t : "\\u" + ("0000" + e.charCodeAt(0).toString(16)).slice(-4)
            }) + '"' : '"' + e + '"'
        };
        var s = function (e, t) {
            var n = "";
            var r = "    ";
            var o = 0;
            var u = "";
            var a = "";
            var f = 0;
            var l = n;
            var c = [];
            var h = t[e];
            if (h && typeof h === "object" && typeof h.toJSON === "function") {
                h = h.toJSON(e)
            }
            switch (typeof h) {
                case"string":
                    return i(h);
                case"number":
                    return isFinite(h) ? String(h) : "null";
                case"boolean":
                case"null":
                    return String(h);
                case"object":
                    if (!h) {
                        return"null"
                    }
                    if (this.PHPJS_Resource && h instanceof this.PHPJS_Resource || window.PHPJS_Resource && h instanceof window.PHPJS_Resource) {
                        throw new SyntaxError("json_encode")
                    }
                    n += r;
                    c = [];
                    if (Object.prototype.toString.apply(h) === "[object Array]") {
                        f = h.length;
                        for (o = 0; o < f; o += 1) {
                            c[o] = s(o, h) || "null"
                        }
                        a = c.length === 0 ? "[]" : n ? "[\n" + n + c.join(",\n" + n) + "\n" + l + "]" : "[" + c.join(",") + "]";
                        n = l;
                        return a
                    }
                    for (u in h) {
                        if (Object.hasOwnProperty.call(h, u)) {
                            a = s(u, h);
                            if (a) {
                                c.push(i(u) + (n ? ": " : ":") + a)
                            }
                        }
                    }
                    a = c.length === 0 ? "{}" : n ? "{\n" + n + c.join(",\n" + n) + "\n" + l + "}" : "{" + c.join(",") + "}";
                    n = l;
                    return a;
                case"undefined":
                case"function":
                default:
                    throw new SyntaxError("json_encode")
            }
        };
        return s("", {"": r})
    } catch (o) {
        if (!(o instanceof SyntaxError)) {
            throw new Error("Unexpected error type in json_encode()")
        }
        this.php_js = this.php_js || {};
        this.php_js.last_error_json = 4;
        return null
    }
}
function makeDateTimePair() {
    jQuery(function () {
        function r() {
            var e = jQuery(this);
            var t = e.find("input.start.date");
            var n = e.find("input.end.date");
            var r = 0;
            if (t.length && n.length) {
                if (t.val() != "")
                    n.datepicker("option", "minDate", t.val());
                var i = parseDate(t.val()), s = parseDate(n.val());
                r = s.getTime() - i.getTime();
                e.data("dateDelta", r)
            }
            var o = e.find("input.start.time");
            var u = e.find("input.end.time");
            if (o.length && u.length) {
                var a = o.timepicker("getSecondsFromMidnight");
                var f = u.timepicker("getSecondsFromMidnight");
                e.data("timeDelta", f - a);
                if (r < 864e5)
                    u.timepicker("option", "minTime", a)
            }
        }
        function i() {
            jQuery('.ui-dialog-buttonset').focus();
            var e = jQuery(this);
            if (e.val() == "")
                return;
            var t = e.closest(".datepair");
            if (e.hasClass("date"))
                s(e, t);
            else if (e.hasClass("time"))
                o(e, t)
        }
        function s(e, t) {
            var r = t.find("input.start.date");
            var i = t.find("input.end.date");
            if (!r.length || !i.length)
                return;
            var s = parseDate(r.val()), o = parseDate(i.val()), u = t.data("dateDelta");
            if (!isNaN(u) && u !== null && e.hasClass("start")) {
                var a = new Date(s.getTime() + u);
                i.val(a.format(n));
                i.datepicker("option", "minDate", r.val());
                return
            } else {
                var f = o.getTime() - s.getTime();
                if (f < 0) {
                    f = 0;
                    if (e.hasClass("start")) {
                        i.val(r.val());
                        i.datepicker("option", "minDate", r.val())
                    } else if (e.hasClass("end")) {
                        r.val(i.val())
                    }
                }
                if (f < 864e5) {
                    var l = t.find("input.start.time").val();
                    if (l) {
                        t.find("input.end.time").timepicker("option", {minTime: l})
                    }
                } else {
                    t.find("input.end.time").timepicker("option", {minTime: null})
                }
                t.data("dateDelta", f)
            }
        }
        function o(e, t) {
            var r = t.find("input.start.time");
            var i = t.find("input.end.time");
            if (!r.length)
                return;
            var s = r.timepicker("getSecondsFromMidnight");
            var o = t.data("dateDelta");
            if (e.hasClass("start") && (!o || o < 864e5)) {
                i.timepicker("option", "minTime", s)
            }
            if (!i.length)
                return;
            var u = i.timepicker("getSecondsFromMidnight"), a = t.data("timeDelta"), f = 0, l;
            if (a && e.hasClass("start")) {
                var c = (s + a) % 86400;
                if (c < 0)
                    c += 86400;
                i.timepicker("setTime", c);
                l = c - s
            } else if (s !== null && u !== null)
                l = u - s;
            else
                return;
            t.data("timeDelta", l);
            if (l < 0 && (!a || a > 0)) {
                f = 864e5
            } else if (l > 0 && a < 0) {
                f = -864e5
            }
            var h = t.find(".start.date"), p = t.find(".end.date");
            if (h.val() && !p.val()) {
                p.val(h.val());
                p.datepicker("option", "minDate", h.val());
                o = 0;
                t.data("dateDelta", 0)
            }
            if (f != 0) {
                if (o || o === 0) {
                    var d = parseDate(p.val());
                    var c = new Date(d.getTime() + f);
                    p.val(c.format(n));
                    p.datepicker("option", "minDate", h.val());
                    t.data("dateDelta", o + f)
                }
            }
        }
        var e = "yyyy-mm-dd", t = "H:i", n = "Y-m-d";
        jQuery(".datepair input.date").each(function () {
            var e = jQuery(this);
            var opt = _lang.jQuery_datepicker_options;
            opt.changeMonth = true;
            opt.changeYear = true;
            opt.dateFormat = "yy-mm-dd";
            opt.firstDay = 1;
            opt.showAnim = "slide", opt.showWeek = true;
            e.datepicker(opt);
            e.attr('readonly', 'readonly');
            if (e.hasClass("start") || e.hasClass("end")) {
                e.on("changeDate change", i)
            }
        });
        jQuery(".datepair input.time").each(function () {
            var e = jQuery(this);
            e.timepicker({scrollDefaultNow: true, step: 30, timeFormat: t, showDuration: true});
            if (e.hasClass("start") || e.hasClass("end")) {
                e.on("changeTime change", i)
            }
        });
        jQuery(".datepair").each(r)
    })
}
function number_format(e, t, n, r) {
    e = (e + "").replace(/[^0-9+\-Ee.]/g, "");
    var i = !isFinite(+e) ? 0 : +e, s = !isFinite(+t) ? 0 : Math.abs(t), o = typeof r === "undefined" ? "," : r, u = typeof n === "undefined" ? "." : n, a = "", f = function (e, t) {
        var n = Math.pow(10, t);
        return"" + Math.round(e * n) / n
    };
    a = (s ? f(i, s) : "" + Math.round(i)).split(".");
    if (a[0].length > 3) {
        a[0] = a[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, o)
    }
    if ((a[1] || "").length < s) {
        a[1] = a[1] || "";
        a[1] += (new Array(s - a[1].length + 1)).join("0")
    }
    return a.join(u)
}
if (typeof String.prototype.trim !== "function")
    String.prototype.trim = function () {
        return this.replace(/^\s+|\s+$/g, "")
    };
if (typeof String.prototype.sprintf !== "function")
    String.prototype.sprintf = function (e, t) {
        var t = t || "%s";
        var n = this.split(t);
        var r = "";
        for (i = 0, j = n.length - 1; i < j; i++) {
            r += n[i] + e[i]
        }
        return r + n[i]
    };
Date.prototype.format = function (e) {
    var t = "";
    var n = Date.replaceChars;
    for (var r = 0; r < e.length; r++) {
        var i = e.charAt(r);
        if (n[i]) {
            t += n[i].call(this)
        } else {
            t += i
        }
    }
    return t
};
Date.replaceChars = {shortMonths: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], longMonths: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"], shortDays: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"], longDays: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"], d: function () {
        return(this.getDate() < 10 ? "0" : "") + this.getDate()
    }, D: function () {
        return Date.replaceChars.shortDays[this.getDay()]
    }, j: function () {
        return this.getDate()
    }, l: function () {
        return Date.replaceChars.longDays[this.getDay()]
    }, N: function () {
        return this.getDay() + 1
    }, S: function () {
        return this.getDate() % 10 == 1 && this.getDate() != 11 ? "st" : this.getDate() % 10 == 2 && this.getDate() != 12 ? "nd" : this.getDate() % 10 == 3 && this.getDate() != 13 ? "rd" : "th"
    }, w: function () {
        return this.getDay()
    }, z: function () {
        return"Not Yet Supported"
    }, W: function () {
        return"Not Yet Supported"
    }, F: function () {
        return Date.replaceChars.longMonths[this.getMonth()]
    }, m: function () {
        return(this.getMonth() < 9 ? "0" : "") + (this.getMonth() + 1)
    }, M: function () {
        return Date.replaceChars.shortMonths[this.getMonth()]
    }, n: function () {
        return this.getMonth() + 1
    }, t: function () {
        return"Not Yet Supported"
    }, L: function () {
        return this.getFullYear() % 4 == 0 && this.getFullYear() % 100 != 0 || this.getFullYear() % 400 == 0 ? "1" : "0"
    }, o: function () {
        return"Not Supported"
    }, Y: function () {
        return this.getFullYear()
    }, y: function () {
        return("" + this.getFullYear()).substr(2)
    }, a: function () {
        return this.getHours() < 12 ? "am" : "pm"
    }, A: function () {
        return this.getHours() < 12 ? "AM" : "PM"
    }, B: function () {
        return"Not Yet Supported"
    }, g: function () {
        return this.getHours() % 12 || 12
    }, G: function () {
        return this.getHours()
    }, h: function () {
        return((this.getHours() % 12 || 12) < 10 ? "0" : "") + (this.getHours() % 12 || 12)
    }, H: function () {
        return(this.getHours() < 10 ? "0" : "") + this.getHours()
    }, i: function () {
        return(this.getMinutes() < 10 ? "0" : "") + this.getMinutes()
    }, s: function () {
        return(this.getSeconds() < 10 ? "0" : "") + this.getSeconds()
    }, e: function () {
        return"Not Yet Supported"
    }, I: function () {
        return"Not Supported"
    }, O: function () {
        return(-this.getTimezoneOffset() < 0 ? "-" : "+") + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? "0" : "") + Math.abs(this.getTimezoneOffset() / 60) + "00"
    }, P: function () {
        return(-this.getTimezoneOffset() < 0 ? "-" : "+") + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? "0" : "") + Math.abs(this.getTimezoneOffset() / 60) + ":" + (Math.abs(this.getTimezoneOffset() % 60) < 10 ? "0" : "") + Math.abs(this.getTimezoneOffset() % 60)
    }, T: function () {
        var e = this.getMonth();
        this.setMonth(0);
        var t = this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/, "$1");
        this.setMonth(e);
        return t
    }, Z: function () {
        return-this.getTimezoneOffset() * 60
    }, c: function () {
        return this.format("Y-m-d") + "T" + this.format("H:i:sP")
    }, r: function () {
        return this.toString()
    }, U: function () {
        return this.getTime() / 1e3
    }}
function setlocale(category, locale) {
    // http://kevin.vanzonneveld.net
    // +   original by: Brett Zamir (http://brett-zamir.me)
    // +   derived from: Blues at http://hacks.bluesmoon.info/strftime/strftime.js
    // +   derived from: YUI Library: http://developer.yahoo.com/yui/docs/YAHOO.util.DateLocale.html
    // -    depends on: getenv
    // %          note 1: Is extensible, but currently only implements locales en,
    // %          note 1: en_US, en_GB, en_AU, fr, and fr_CA for LC_TIME only; C for LC_CTYPE;
    // %          note 1: C and en for LC_MONETARY/LC_NUMERIC; en for LC_COLLATE
    // %          note 2: Uses global: php_js to store locale info
    // %          note 3: Consider using http://demo.icu-project.org/icu-bin/locexp as basis for localization (as in i18n_loc_set_default())
    // *     example 1: setlocale('LC_ALL', 'en_US');
    // *     returns 1: 'en_US'
    var categ = '',
            cats = [],
            i = 0,
            d = this.window.document;

    // BEGIN STATIC
    var _copy = function _copy(orig) {
        if (orig instanceof RegExp) {
            return new RegExp(orig);
        } else if (orig instanceof Date) {
            return new Date(orig);
        }
        var newObj = {};
        for (var i in orig) {
            if (typeof orig[i] === 'object') {
                newObj[i] = _copy(orig[i]);
            } else {
                newObj[i] = orig[i];
            }
        }
        return newObj;
    };

    // Function usable by a ngettext implementation (apparently not an accessible part of setlocale(), but locale-specific)
    // See http://www.gnu.org/software/gettext/manual/gettext.html#Plural-forms though amended with others from
    // https://developer.mozilla.org/En/Localization_and_Plurals (new categories noted with "MDC" below, though
    // not sure of whether there is a convention for the relative order of these newer groups as far as ngettext)
    // The function name indicates the number of plural forms (nplural)
    // Need to look into http://cldr.unicode.org/ (maybe future JavaScript); Dojo has some functions (under new BSD),
    // including JSON conversions of LDML XML from CLDR: http://bugs.dojotoolkit.org/browser/dojo/trunk/cldr
    // and docs at http://api.dojotoolkit.org/jsdoc/HEAD/dojo.cldr
    var _nplurals1 = function (n) { // e.g., Japanese
        return 0;
    };
    var _nplurals2a = function (n) { // e.g., English
        return n !== 1 ? 1 : 0;
    };
    var _nplurals2b = function (n) { // e.g., French
        return n > 1 ? 1 : 0;
    };
    var _nplurals2c = function (n) { // e.g., Icelandic (MDC)
        return n % 10 === 1 && n % 100 !== 11 ? 0 : 1;
    };
    var _nplurals3a = function (n) { // e.g., Latvian (MDC has a different order from gettext)
        return n % 10 === 1 && n % 100 !== 11 ? 0 : n !== 0 ? 1 : 2;
    };
    var _nplurals3b = function (n) { // e.g., Scottish Gaelic
        return n === 1 ? 0 : n === 2 ? 1 : 2;
    };
    var _nplurals3c = function (n) { // e.g., Romanian
        return n === 1 ? 0 : (n === 0 || (n % 100 > 0 && n % 100 < 20)) ? 1 : 2;
    };
    var _nplurals3d = function (n) { // e.g., Lithuanian (MDC has a different order from gettext)
        return n % 10 === 1 && n % 100 !== 11 ? 0 : n % 10 >= 2 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2;
    };
    var _nplurals3e = function (n) { // e.g., Croatian
        return n % 10 === 1 && n % 100 !== 11 ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2;
    };
    var _nplurals3f = function (n) { // e.g., Slovak
        return n === 1 ? 0 : n >= 2 && n <= 4 ? 1 : 2;
    };
    var _nplurals3g = function (n) { // e.g., Polish
        return n === 1 ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2;
    };
    var _nplurals3h = function (n) { // e.g., Macedonian (MDC)
        return n % 10 === 1 ? 0 : n % 10 === 2 ? 1 : 2;
    };
    var _nplurals4a = function (n) { // e.g., Slovenian
        return n % 100 === 1 ? 0 : n % 100 === 2 ? 1 : n % 100 === 3 || n % 100 === 4 ? 2 : 3;
    };
    var _nplurals4b = function (n) { // e.g., Maltese (MDC)
        return n === 1 ? 0 : n === 0 || (n % 100 && n % 100 <= 10) ? 1 : n % 100 >= 11 && n % 100 <= 19 ? 2 : 3;
    };
    var _nplurals5 = function (n) { // e.g., Irish Gaeilge (MDC)
        return n === 1 ? 0 : n === 2 ? 1 : n >= 3 && n <= 6 ? 2 : n >= 7 && n <= 10 ? 3 : 4;
    };
    var _nplurals6 = function (n) { // e.g., Arabic (MDC) - Per MDC puts 0 as last group
        return n === 0 ? 5 : n === 1 ? 0 : n === 2 ? 1 : n % 100 >= 3 && n % 100 <= 10 ? 2 : n % 100 >= 11 && n % 100 <= 99 ? 3 : 4;
    };
    // END STATIC
    // BEGIN REDUNDANT
    this.php_js = this.php_js || {};

    var phpjs = this.php_js;

    // Reconcile Windows vs. *nix locale names?
    // Allow different priority orders of languages, esp. if implement gettext as in
    //     LANGUAGE env. var.? (e.g., show German if French is not available)
    if (!phpjs.locales) {
        // Can add to the locales
        phpjs.locales = {};

        phpjs.locales.en = {
            'LC_COLLATE': // For strcoll


                    function (str1, str2) { // Fix: This one taken from strcmp, but need for other locales; we don't use localeCompare since its locale is not settable
                        return (str1 == str2) ? 0 : ((str1 > str2) ? 1 : -1);
                    },
            'LC_CTYPE': {// Need to change any of these for English as opposed to C?
                an: /^[A-Za-z\d]+$/g,
                al: /^[A-Za-z]+$/g,
                ct: /^[\u0000-\u001F\u007F]+$/g,
                dg: /^[\d]+$/g,
                gr: /^[\u0021-\u007E]+$/g,
                lw: /^[a-z]+$/g,
                pr: /^[\u0020-\u007E]+$/g,
                pu: /^[\u0021-\u002F\u003A-\u0040\u005B-\u0060\u007B-\u007E]+$/g,
                sp: /^[\f\n\r\t\v ]+$/g,
                up: /^[A-Z]+$/g,
                xd: /^[A-Fa-f\d]+$/g,
                CODESET: 'UTF-8',
                // Used by sql_regcase
                lower: 'abcdefghijklmnopqrstuvwxyz',
                upper: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
            },
            'LC_TIME': {// Comments include nl_langinfo() constant equivalents and any changes from Blues' implementation
                a: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                // ABDAY_
                A: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                // DAY_
                b: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                // ABMON_
                B: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                // MON_
                c: '%a %d %b %Y %r %Z',
                // D_T_FMT // changed %T to %r per results
                p: ['AM', 'PM'],
                // AM_STR/PM_STR
                P: ['am', 'pm'],
                // Not available in nl_langinfo()
                r: '%I:%M:%S %p',
                // T_FMT_AMPM (Fixed for all locales)
                x: '%m/%d/%Y',
                // D_FMT // switched order of %m and %d; changed %y to %Y (C uses %y)
                X: '%r',
                // T_FMT // changed from %T to %r  (%T is default for C, not English US)
                // Following are from nl_langinfo() or http://www.cptec.inpe.br/sx4/sx4man2/g1ab02e/strftime.4.html
                alt_digits: '',
                // e.g., ordinal
                ERA: '',
                ERA_YEAR: '',
                ERA_D_T_FMT: '',
                ERA_D_FMT: '',
                ERA_T_FMT: ''
            },
            // Assuming distinction between numeric and monetary is thus:
            // See below for C locale
            'LC_MONETARY': {// Based on Windows "english" (English_United States.1252) locale
                int_curr_symbol: '',
                currency_symbol: '',
                mon_decimal_point: '.',
                mon_thousands_sep: ',',
                mon_grouping: [3],
                // use mon_thousands_sep; "" for no grouping; additional array members indicate successive group lengths after first group (e.g., if to be 1,23,456, could be [3, 2])
                positive_sign: '',
                negative_sign: '-',
                int_frac_digits: 2,
                // Fractional digits only for money defaults?
                frac_digits: 2,
                p_cs_precedes: 1,
                // positive currency symbol follows value = 0; precedes value = 1
                p_sep_by_space: 0,
                // 0: no space between curr. symbol and value; 1: space sep. them unless symb. and sign are adjacent then space sep. them from value; 2: space sep. sign and value unless symb. and sign are adjacent then space separates
                n_cs_precedes: 1,
                // see p_cs_precedes
                n_sep_by_space: 0,
                // see p_sep_by_space
                p_sign_posn: 3,
                // 0: parentheses surround quantity and curr. symbol; 1: sign precedes them; 2: sign follows them; 3: sign immed. precedes curr. symbol; 4: sign immed. succeeds curr. symbol
                n_sign_posn: 0 // see p_sign_posn
            },
            'LC_NUMERIC': {// Based on Windows "english" (English_United States.1252) locale
                decimal_point: '.',
                thousands_sep: ',',
                grouping: [3] // see mon_grouping, but for non-monetary values (use thousands_sep)
            },
            'LC_MESSAGES': {
                YESEXPR: '^[yY].*',
                NOEXPR: '^[nN].*',
                YESSTR: '',
                NOSTR: ''
            },
            nplurals: _nplurals2a
        };
        phpjs.locales.en_US = _copy(phpjs.locales.en);
        phpjs.locales.en_US.LC_TIME.c = '%a %d %b %Y %r %Z';
        phpjs.locales.en_US.LC_TIME.x = '%D';
        phpjs.locales.en_US.LC_TIME.X = '%r';
        // The following are based on *nix settings
        phpjs.locales.en_US.LC_MONETARY.int_curr_symbol = '';
        phpjs.locales.en_US.LC_MONETARY.p_sign_posn = 1;
        phpjs.locales.en_US.LC_MONETARY.n_sign_posn = 1;
        phpjs.locales.en_US.LC_MONETARY.mon_grouping = [3, 3];
        phpjs.locales.en_US.LC_NUMERIC.thousands_sep = '';
        phpjs.locales.en_US.LC_NUMERIC.grouping = [];

        phpjs.locales.en_GB = _copy(phpjs.locales.en);
        phpjs.locales.en_GB.LC_TIME.r = '%l:%M:%S %P %Z';

        phpjs.locales.en_AU = _copy(phpjs.locales.en_GB);
        phpjs.locales.C = _copy(phpjs.locales.en); // Assume C locale is like English (?) (We need C locale for LC_CTYPE)
        phpjs.locales.C.LC_CTYPE.CODESET = 'ANSI_X3.4-1968';
        phpjs.locales.C.LC_MONETARY = {
            int_curr_symbol: '',
            currency_symbol: '',
            mon_decimal_point: '',
            mon_thousands_sep: '',
            mon_grouping: [],
            p_cs_precedes: 127,
            p_sep_by_space: 127,
            n_cs_precedes: 127,
            n_sep_by_space: 127,
            p_sign_posn: 127,
            n_sign_posn: 127,
            positive_sign: '',
            negative_sign: '',
            int_frac_digits: 127,
            frac_digits: 127
        };
        phpjs.locales.C.LC_NUMERIC = {
            decimal_point: '.',
            thousands_sep: '',
            grouping: []
        };
        phpjs.locales.C.LC_TIME.c = '%a %b %e %H:%M:%S %Y'; // D_T_FMT
        phpjs.locales.C.LC_TIME.x = '%m/%d/%y'; // D_FMT
        phpjs.locales.C.LC_TIME.X = '%H:%M:%S'; // T_FMT
        phpjs.locales.C.LC_MESSAGES.YESEXPR = '^[yY]';
        phpjs.locales.C.LC_MESSAGES.NOEXPR = '^[nN]';

        phpjs.locales.fr = _copy(phpjs.locales.en);
        phpjs.locales.fr.nplurals = _nplurals2b;
        phpjs.locales.fr.LC_TIME.a = ['dim', 'lun', 'mar', 'mer', 'jeu', 'ven', 'sam'];
        phpjs.locales.fr.LC_TIME.A = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
        phpjs.locales.fr.LC_TIME.b = ['jan', 'f\u00E9v', 'mar', 'avr', 'mai', 'jun', 'jui', 'ao\u00FB', 'sep', 'oct', 'nov', 'd\u00E9c'];
        phpjs.locales.fr.LC_TIME.B = ['janvier', 'f\u00E9vrier', 'mars', 'avril', 'mai', 'juin', 'juillet', 'ao\u00FBt', 'septembre', 'octobre', 'novembre', 'd\u00E9cembre'];
        phpjs.locales.fr.LC_TIME.c = '%a %d %b %Y %T %Z';
        phpjs.locales.fr.LC_TIME.p = ['', ''];
        phpjs.locales.fr.LC_TIME.P = ['', ''];
        phpjs.locales.fr.LC_TIME.x = '%d.%m.%Y';
        phpjs.locales.fr.LC_TIME.X = '%T';

        phpjs.locales.fr_CA = _copy(phpjs.locales.fr);
        phpjs.locales.fr_CA.LC_TIME.x = '%Y-%m-%d';
    }
    if (!phpjs.locale) {
        phpjs.locale = 'en_US';
        var NS_XHTML = 'http://www.w3.org/1999/xhtml';
        var NS_XML = 'http://www.w3.org/XML/1998/namespace';
        if (d.getElementsByTagNameNS && d.getElementsByTagNameNS(NS_XHTML, 'html')[0]) {
            if (d.getElementsByTagNameNS(NS_XHTML, 'html')[0].getAttributeNS && d.getElementsByTagNameNS(NS_XHTML, 'html')[0].getAttributeNS(NS_XML, 'lang')) {
                phpjs.locale = d.getElementsByTagName(NS_XHTML, 'html')[0].getAttributeNS(NS_XML, 'lang');
            } else if (d.getElementsByTagNameNS(NS_XHTML, 'html')[0].lang) { // XHTML 1.0 only
                phpjs.locale = d.getElementsByTagNameNS(NS_XHTML, 'html')[0].lang;
            }
        } else if (d.getElementsByTagName('html')[0] && d.getElementsByTagName('html')[0].lang) {
            phpjs.locale = d.getElementsByTagName('html')[0].lang;
        }
    }
    phpjs.locale = phpjs.locale.replace('-', '_'); // PHP-style
    // Fix locale if declared locale hasn't been defined
    if (!(phpjs.locale in phpjs.locales)) {
        if (phpjs.locale.replace(/_[a-zA-Z]+$/, '') in phpjs.locales) {
            phpjs.locale = phpjs.locale.replace(/_[a-zA-Z]+$/, '');
        }
    }

    if (!phpjs.localeCategories) {
        phpjs.localeCategories = {
            'LC_COLLATE': phpjs.locale,
            // for string comparison, see strcoll()
            'LC_CTYPE': phpjs.locale,
            // for character classification and conversion, for example strtoupper()
            'LC_MONETARY': phpjs.locale,
            // for localeconv()
            'LC_NUMERIC': phpjs.locale,
            // for decimal separator (See also localeconv())
            'LC_TIME': phpjs.locale,
            // for date and time formatting with strftime()
            'LC_MESSAGES': phpjs.locale // for system responses (available if PHP was compiled with libintl)
        };
    }
    // END REDUNDANT
    if (locale === null || locale === '') {
        locale = this.getenv(category) || this.getenv('LANG');
    } else if (Object.prototype.toString.call(locale) === '[object Array]') {
        for (i = 0; i < locale.length; i++) {
            if (!(locale[i] in this.php_js.locales)) {
                if (i === locale.length - 1) {
                    return false; // none found
                }
                continue;
            }
            locale = locale[i];
            break;
        }
    }

    // Just get the locale
    if (locale === '0' || locale === 0) {
        if (category === 'LC_ALL') {
            for (categ in this.php_js.localeCategories) {
                cats.push(categ + '=' + this.php_js.localeCategories[categ]); // Add ".UTF-8" or allow ".@latint", etc. to the end?
            }
            return cats.join(';');
        }
        return this.php_js.localeCategories[category];
    }

    if (!(locale in this.php_js.locales)) {
        return false; // Locale not found
    }

    // Set and get locale
    if (category === 'LC_ALL') {
        for (categ in this.php_js.localeCategories) {
            this.php_js.localeCategories[categ] = locale;
        }
    } else {
        this.php_js.localeCategories[category] = locale;
    }
    return locale;
}

function money_format(format, number) {
    if (typeof number !== 'number') {
        return null;
    }
    var regex = /%((=.|[+^(!-])*?)(\d*?)(#(\d+))?(\.(\d+))?([in%])/g; // 1: flags, 3: width, 5: left, 7: right, 8: conversion

    this.setlocale('LC_ALL', 'en_US'); // Ensure the locale data we need is set up
    var monetary = this.php_js.locales[this.php_js.localeCategories['LC_MONETARY']]['LC_MONETARY'];

    var doReplace = function (n0, flags, n2, width, n4, left, n6, right, conversion) {
        var value = '',
                repl = '';
        if (conversion === '%') { // Percent does not seem to be allowed with intervening content
            return '%';
        }
        var fill = flags && (/=./).test(flags) ? flags.match(/=(.)/)[1] : ' '; // flag: =f (numeric fill)
        var showCurrSymbol = !flags || flags.indexOf('!') === -1; // flag: ! (suppress currency symbol)
        width = parseInt(width, 10) || 0; // field width: w (minimum field width)

        var neg = number < 0;
        number = number + ''; // Convert to string
        number = neg ? number.slice(1) : number; // We don't want negative symbol represented here yet

        var decpos = number.indexOf('.');
        var integer = decpos !== -1 ? number.slice(0, decpos) : number; // Get integer portion
        var fraction = decpos !== -1 ? number.slice(decpos + 1) : ''; // Get decimal portion

        var _str_splice = function (integerStr, idx, thous_sep) {
            var integerArr = integerStr.split('');
            integerArr.splice(idx, 0, thous_sep);
            return integerArr.join('');
        };

        var init_lgth = integer.length;
        left = parseInt(left, 10);
        var filler = init_lgth < left;
        if (filler) {
            var fillnum = left - init_lgth;
            integer = new Array(fillnum + 1).join(fill) + integer;
        }
        if (flags.indexOf('^') === -1) { // flag: ^ (disable grouping characters (of locale))
            // use grouping characters
            var thous_sep = monetary.mon_thousands_sep; // ','
            var mon_grouping = monetary.mon_grouping; // [3] (every 3 digits in U.S.A. locale)

            if (mon_grouping[0] < integer.length) {
                for (var i = 0, idx = integer.length; i < mon_grouping.length; i++) {
                    idx -= mon_grouping[i]; // e.g., 3
                    if (idx <= 0) {
                        break;
                    }
                    if (filler && idx < fillnum) {
                        thous_sep = fill;
                    }
                    integer = _str_splice(integer, idx, thous_sep);
                }
            }
            if (mon_grouping[i - 1] > 0) { // Repeating last grouping (may only be one) until highest portion of integer reached
                while (idx > mon_grouping[i - 1]) {
                    idx -= mon_grouping[i - 1];
                    if (filler && idx < fillnum) {
                        thous_sep = fill;
                    }
                    integer = _str_splice(integer, idx, thous_sep);
                }
            }
        }

        // left, right
        if (right === '0') { // No decimal or fractional digits
            value = integer;
        } else {
            var dec_pt = monetary.mon_decimal_point; // '.'
            if (right === '' || right === undefined) {
                right = conversion === 'i' ? monetary.int_frac_digits : monetary.frac_digits;
            }
            right = parseInt(right, 10);

            if (right === 0) { // Only remove fractional portion if explicitly set to zero digits
                fraction = '';
                dec_pt = '';
            } else if (right < fraction.length) {
                fraction = Math.round(parseFloat(fraction.slice(0, right) + '.' + fraction.substr(right, 1))) + '';
                if (right > fraction.length) {
                    fraction = new Array(right - fraction.length + 1).join('0') + fraction; // prepend with 0's
                }
            } else if (right > fraction.length) {
                fraction += new Array(right - fraction.length + 1).join('0'); // pad with 0's
            }
            value = integer + dec_pt + fraction;
        }

        var symbol = '';
        if (showCurrSymbol) {
            symbol = conversion === 'i' ? monetary.int_curr_symbol : monetary.currency_symbol; // 'i' vs. 'n' ('USD' vs. '$')
        }
        var sign_posn = neg ? monetary.n_sign_posn : monetary.p_sign_posn;

        // 0: no space between curr. symbol and value
        // 1: space sep. them unless symb. and sign are adjacent then space sep. them from value
        // 2: space sep. sign and value unless symb. and sign are adjacent then space separates
        var sep_by_space = neg ? monetary.n_sep_by_space : monetary.p_sep_by_space;

        // p_cs_precedes, n_cs_precedes // positive currency symbol follows value = 0; precedes value = 1
        var cs_precedes = neg ? monetary.n_cs_precedes : monetary.p_cs_precedes;

        // Assemble symbol/value/sign and possible space as appropriate
        if (flags.indexOf('(') !== -1) { // flag: parenth. for negative
            // Fix: unclear on whether and how sep_by_space, sign_posn, or cs_precedes have
            // an impact here (as they do below), but assuming for now behaves as sign_posn 0 as
            // far as localized sep_by_space and sign_posn behavior
            repl = (cs_precedes ? symbol + (sep_by_space === 1 ? ' ' : '') : '') + value + (!cs_precedes ? (sep_by_space === 1 ? ' ' : '') + symbol : '');
            if (neg) {
                repl = '(' + repl + ')';
            } else {
                repl = ' ' + repl + ' ';
            }
        } else { // '+' is default
            var pos_sign = monetary.positive_sign; // ''
            var neg_sign = monetary.negative_sign; // '-'
            var sign = neg ? (neg_sign) : (pos_sign);
            var otherSign = neg ? (pos_sign) : (neg_sign);
            var signPadding = '';
            if (sign_posn) { // has a sign
                signPadding = new Array(otherSign.length - sign.length + 1).join(' ');
            }

            var valueAndCS = '';
            switch (sign_posn) {
                // 0: parentheses surround value and curr. symbol;
                // 1: sign precedes them;
                // 2: sign follows them;
                // 3: sign immed. precedes curr. symbol; (but may be space between)
                // 4: sign immed. succeeds curr. symbol; (but may be space between)
                case 0:
                    valueAndCS = cs_precedes ? symbol + (sep_by_space === 1 ? ' ' : '') + value : value + (sep_by_space === 1 ? ' ' : '') + symbol;
                    repl = '(' + valueAndCS + ')';
                    break;
                case 1:
                    valueAndCS = cs_precedes ? symbol + (sep_by_space === 1 ? ' ' : '') + value : value + (sep_by_space === 1 ? ' ' : '') + symbol;
                    repl = signPadding + sign + (sep_by_space === 2 ? ' ' : '') + valueAndCS;
                    break;
                case 2:
                    valueAndCS = cs_precedes ? symbol + (sep_by_space === 1 ? ' ' : '') + value : value + (sep_by_space === 1 ? ' ' : '') + symbol;
                    repl = valueAndCS + (sep_by_space === 2 ? ' ' : '') + sign + signPadding;
                    break;
                case 3:
                    repl = cs_precedes ? signPadding + sign + (sep_by_space === 2 ? ' ' : '') + symbol + (sep_by_space === 1 ? ' ' : '') + value : value + (sep_by_space === 1 ? ' ' : '') + sign + signPadding + (sep_by_space === 2 ? ' ' : '') + symbol;
                    break;
                case 4:
                    repl = cs_precedes ? symbol + (sep_by_space === 2 ? ' ' : '') + signPadding + sign + (sep_by_space === 1 ? ' ' : '') + value : value + (sep_by_space === 1 ? ' ' : '') + symbol + (sep_by_space === 2 ? ' ' : '') + sign + signPadding;
                    break;
            }
        }

        var padding = width - repl.length;
        if (padding > 0) {
            padding = new Array(padding + 1).join(' ');
            // Fix: How does p_sep_by_space affect the count if there is a space? Included in count presumably?
            if (flags.indexOf('-') !== -1) { // left-justified (pad to right)
                repl += padding;
            } else { // right-justified (pad to left)
                repl = padding + repl;
            }
        }
        return repl;
    };

    return format.replace(regex, doReplace);
}
function round(value, precision, mode) {
    // http://kevin.vanzonneveld.net
    // +   original by: Philip Peterson
    // +    revised by: Onno Marsman
    // +      input by: Greenseed
    // +    revised by: T.Wild
    // +      input by: meo
    // +      input by: William
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Josep Sanz (http://www.ws3.es/)
    // +    revised by: Rafał Kukawski (http://blog.kukawski.pl/)
    // %        note 1: Great work. Ideas for improvement:
    // %        note 1:  - code more compliant with developer guidelines
    // %        note 1:  - for implementing PHP constant arguments look at
    // %        note 1:  the pathinfo() function, it offers the greatest
    // %        note 1:  flexibility & compatibility possible
    // *     example 1: round(1241757, -3);
    // *     returns 1: 1242000
    // *     example 2: round(3.6);
    // *     returns 2: 4
    // *     example 3: round(2.835, 2);
    // *     returns 3: 2.84
    // *     example 4: round(1.1749999999999, 2);
    // *     returns 4: 1.17
    // *     example 5: round(58551.799999999996, 2);
    // *     returns 5: 58551.8
    var m, f, isHalf, sgn; // helper variables
    precision |= 0; // making sure precision is integer
    m = Math.pow(10, precision);
    value *= m;
    sgn = (value > 0) | -(value < 0); // sign of the number
    isHalf = value % 1 === 0.5 * sgn;
    f = Math.floor(value);

    if (isHalf) {
        switch (mode) {
            case 'PHP_ROUND_HALF_DOWN':
                value = f + (sgn < 0); // rounds .5 toward zero
                break;
            case 'PHP_ROUND_HALF_EVEN':
                value = f + (f % 2 * sgn); // rouds .5 towards the next even integer
                break;
            case 'PHP_ROUND_HALF_ODD':
                value = f + !(f % 2); // rounds .5 towards the next odd integer
                break;
            default:
                value = f + (sgn > 0); // rounds .5 away from zero
        }
    }

    return (isHalf ? value : Math.round(value)) / m;
}
function str_pad(input, pad_length, pad_string, pad_type) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + namespaced by: Michael White (http://getsprink.com)
    // +      input by: Marco van Oort
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: str_pad('Kevin van Zonneveld', 30, '-=', 'STR_PAD_LEFT');
    // *     returns 1: '-=-=-=-=-=-Kevin van Zonneveld'
    // *     example 2: str_pad('Kevin van Zonneveld', 30, '-', 'STR_PAD_BOTH');
    // *     returns 2: '------Kevin van Zonneveld-----'
    var half = '',
            pad_to_go;

    var str_pad_repeater = function (s, len) {
        var collect = '',
                i;

        while (collect.length < len) {
            collect += s;
        }
        collect = collect.substr(0, len);

        return collect;
    };

    input += '';
    pad_string = pad_string !== undefined ? pad_string : ' ';

    if (pad_type !== 'STR_PAD_LEFT' && pad_type !== 'STR_PAD_RIGHT' && pad_type !== 'STR_PAD_BOTH') {
        pad_type = 'STR_PAD_RIGHT';
    }
    if ((pad_to_go = pad_length - input.length) > 0) {
        if (pad_type === 'STR_PAD_LEFT') {
            input = str_pad_repeater(pad_string, pad_to_go) + input;
        } else if (pad_type === 'STR_PAD_RIGHT') {
            input = input + str_pad_repeater(pad_string, pad_to_go);
        } else if (pad_type === 'STR_PAD_BOTH') {
            half = str_pad_repeater(pad_string, Math.ceil(pad_to_go / 2));
            input = half + input + half;
            input = input.substr(0, pad_length);
        }
    }

    return input;
}
/*
 * @param form: if form is the form id: It will prevent default on all form's fields, if form is a field id inside a form then it will prevent default only on this specific field
 */
function preventEnterSubmit(form) {
    form.keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
}
/*
 * resize Modal dialog on small screens
 * if the window width has a max width of 500 px ,then the model will resize to a smaller size and will have a class modal-xs
 * @param string container, jQuery selector for modal container
 */
function resizeLoginModal(container) {
    resizeMiniModal(container);
    setTimeout(function () {
        jQuery("#image", container).width(jQuery(".logo").width());
    }, 200);
}

// Convert Gregorian date to Hijri date
function gregorianToHijri(gregorianDate, dateTime) {
    dateTime = dateTime || false;
    if(gregorianDate && dateTime){
        dateParts = gregorianDate.split(" - ");
        m = moment(dateParts[0]);
        return m.locale('en').format('YYYY-MM-DD') !== 'Invalid date' ? (m.format('iYYYY-iMM-iDD') + ' - ' + dateParts[1]) : '';
    }else{
        m = moment(gregorianDate);
        return m.locale('en').format('YYYY-MM-DD') !== 'Invalid date' ? m.format('iYYYY-iMM-iDD') : '';
    }
}

// Convert Hijri date to Gregorian date
function hijriToGregorian(hijriDate, dateTime) {
    m = moment(hijriDate, 'iYYYY-iMM-iDD');
    return m.locale('en').format('YYYY-MM-DD');
}

function pinesMessageV2(msgObj) {
    var feedbackMessageUniqueId = (new Date().getTime() * Math.floor(Math.random() * 1000000));
    var messageContainer = 'custom-notification-wrapper-' + feedbackMessageUniqueId;
    var messageContainerElement = jQuery("#"+ messageContainer);
    var n = noty({
        layout: 'topRight',
        template:
            '<div id="feedback-message-container-' + feedbackMessageUniqueId + '" class="feedback-message-container feedback-message-v2">' +
                '<div id="feedback-message-header" class="d-flex feedback-message-header feedback-message-type-' + msgObj.ty + '">' +
                    '<img id="feedback-message-icon" src="assets/images/icons/fb_v2_' + msgObj.ty + '.png" class="feedback-message-icon d-block"/>' +
                    '<span id="feedback-message-close-button-' + feedbackMessageUniqueId + '" class="feedback-message-close-button d-block">&#10005;</span>' +
                '</div>' +
                '<div id="feedback-message-body" class="feedback-message-body">' +
                    '<span class="feedback-message-title-v2 center-block text-center d-block">' + _lang.feedback_messages[msgObj.ty] + '</span>' +
                    '<span id="feedback-message-text" class="feedback-message-text center-block text-center margin-top-5 d-block">' + decodeURIComponent(msgObj.m) + '</span>' +
                '</div>' +
            '</div>',
        animation: {
            open: 'animated fadeInDown',
            close: 'animated fadeOutUp'
        },
        callback: {
            afterShow: function () {
                jQuery('#feedback-message-close-button-' + feedbackMessageUniqueId).click(function () {
                    n.close();
                });
                if (msgObj.d !== 0) { //when msgObj.d is not set or set but not = 0 then the pinesmessage will disappear after a while by itself while when msgObj.d = 0 it will disappear only if the user closes the window
                    messageContainerElement.mouseenter(function () {
                        jQuery(this).attr('data-extend-timeout', 'true');
                    });
                    messageContainerElement.mouseleave(function () {
                        jQuery(this).removeAttr('data-extend-timeout');
                        timeOut(1000);
                    });
                    timeOut(msgObj.d || 3000);
                    function timeOut(delay) {
                        setTimeout(function () {
                            if (messageContainerElement.attr('data-extend-timeout') === undefined) {
                                n.close();
                            }
                        }, delay);
                    }
                }
            }
        },
        closeWith: ['button']
    });
}

/**
 * Test if time has empty or valid valid format
 *
 * @param value
 * @returns {boolean}
 */
function timeValidation(value) {
    if(!value) return false;
    value = value.toLowerCase();
    var valueToCheck = value.replace(/\s/g, '');
    const regex = /^([0-9]+h?|[0-9]+h[0-9]m|[0-9]+h[0-9]+m|^[0-9]m|[0-9]+:[0-5]*[0-9]|[0-9]+m?|^[+-]?((\d+(\.\d*)?)|(\.\d+))h?$)$/i;
    return ((regex.exec(valueToCheck)) !== null);
}

/**
 * Convert hours and minutes to decimal hours
 *
 * @param timeHoursMinutes
 * @return float|string
 */
function convertTimeToDecimal(timeHoursMinutes) {
    if(!timeHoursMinutes) return '';
    timeHoursMinutes = decodeURIComponent(timeHoursMinutes);
    timeHoursMinutes = timeHoursMinutes.toLowerCase();
    var timeToConvert = timeHoursMinutes.replace(/\s/g, '');
    var result = timeHoursMinutes;
    const regex = /^([0-9]+h?|[0-9]+h[0-9]m|[0-9]+h[0-9]+m|^[0-9]m|[0-9]+:[0-5]*[0-9]|[0-9]+m?|^[+-]?((\d+(\.\d*)?)|(\.\d+))h?$)$/i;
    if(((regex.exec(timeToConvert)) !== null)){
        result = 0;
        if(timeToConvert.includes(":")){
            var TimesArr = timeToConvert.split(':',2);
            var timesArrHValue = TimesArr[0];
            var timesArrMValue = TimesArr[1];
            result += parseFloat(timesArrHValue);
            result += round(parseFloat(timesArrMValue)/60, 2);
        } else{
            var timesArrH = timeToConvert.split('h',2);
            var timesArrM = timeToConvert.split('m',2);
            timesArrHValue = timesArrH[0].includes("m") ? '' : timesArrH[0];
            timesArrMValue = timesArrM[0].includes("h") ? (timesArrM[0].replace(timesArrHValue +'h', '')) : ((timeHoursMinutes.includes("m") || timeHoursMinutes.includes("h")) ? timesArrM[0] : '');
            if(timesArrHValue){
                result += parseFloat(timesArrHValue);
            }
            if (timesArrMValue){
                result += round(parseFloat(timesArrMValue)/60, 2);
            }
        }
        return parseFloat(result);
    }
    return false;
}

/**
 * Convert decimal numbers to time readable
 *
 * @param options
 * @returns {string}
 */
function convertDecimalToTime(options){
    var settings = jQuery.extend({
        time: 0,
        hourMinutes: 60
    }, options);

    function timeToHumanReadable(){
        var output = '';
        if(/\d*\.?\d/.test(settings.time)){
            output = hoursToTime(settings.time);
        }
        return output;
    }

    function hoursToTime(inputHours) {
        inputHours = parseFloat(inputHours);
        var remainingHours = inputHours % 1;
        var hours = parseInt(inputHours);
        var minutes = remainingHours * settings.hourMinutes;
        var integer_minutes = parseInt(minutes);
        var decimal_minutes = minutes - integer_minutes <= 0.5 ? 0 : 1;
        minutes = (integer_minutes + decimal_minutes);
        var sections = {
            'h': parseFloat(hours),
            'm': parseFloat(minutes)
        };
        return getTimeParts(sections);
    }

    function getTimeParts(sections){
        // Format and return
        timeParts = [];
        for(var key in sections){
            if(sections[key] > 0){
                timeParts.push(sections[key] + '' + key);
            }
        }
        return timeParts.join(" ");
    }
    return timeToHumanReadable();
}

function lookupCompanyContactType(lookupDetails, container, lookupType, module , systemPreferenceContact) {
    module = module || false;
    lookupDetails.callback = lookupDetails.callback || {};
    lookupDetails['callback']['onselect'] = lookupDetails['callback']['onselect'] || false;
    lookupDetails['callback']['onClearLookup'] = lookupDetails['callback']['onClearLookup'] || false;
    lookupDetails['callback']['onEraseLookup'] = lookupDetails['callback']['onEraseLookup'] || false;
    var id = '#' + container.attr('id');
    lookupType = lookupType ? lookupType : companyContactFormMatrix.commonLookup[id].lookupType;
    extra_data = '';
    is_advisor = false;
    if (lookupType == 'contact' && typeof lookupDetails['type'] !== 'undefined' && lookupDetails['type'] == 'outsource') {
        is_advisor = true;
        extra_data = lookupDetails['type'] == 'outsource' ? '&extra_data=advisors' : '';
    }
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        replace: function (url, uriEncodedQuery) {
            return url + "#" + uriEncodedQuery;
            // the part after the hash is not sent to the server
        },
        remote: {
            url: (module ? getBaseURL(module) : getBaseURL()) + (lookupType == 'company' ? 'companies' : 'contacts') + '/autocomplete?term=%QUERY' + extra_data,
            filter: function (data) {
                return data;
            },
            'cache': false,
            wildcard: '%QUERY',
        }
    });
// Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
// Instantiate the Typeahead UI
    jQuery(lookupDetails['lookupField']).unbind();
    var typeahead = jQuery(lookupDetails['lookupField']).typeahead({
            hint: false,
            highlight: true,
            minLength: 3
        },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                if (lookupType == 'contact') {
                    var contactName = (item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName) + (is_advisor ? item.is_advisor =="advisor" ? " ("+_lang.advisors+")" : "" : "");

                    return contactName;
                } else if (lookupType == 'company') {
                    return item.name
                } else {
                    return ''
                }
            },
            templates: {
                empty: [
                    '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                suggestion: function (data) {
                    if (lookupType == 'contact') {
                        foreignFullName = data.foreignFullName.trim();
                        var contactName = '<div>' + ((data.father ? data.firstName + ' ' + data.father + ' ' + data.lastName : data.firstName + ' ' + data.lastName) + (foreignFullName ? (' - ' + foreignFullName) : '')) + (is_advisor ? data.is_advisor =="advisor" ? " ("+_lang.advisors+")" : "" : "") + '</div>';

                        return contactName;
                    } else if (lookupType == 'company' && data.shortName == null) {
                        return '<div>' + data.name + (null == data.foreignName ? '' : ' - ' + data.foreignName) + '</div>'
                    } else {
                        return '<div>' + data.name + ' (' + data.shortName + ')' + (null == data.foreignName ? '' : ' - ' + data.foreignName) + '</div>'
                    }
                }
            }
        });
    typeahead.on('typeahead:selected', function (obj, datum) {
    }).on('typeahead:asyncrequest', function () {
        jQuery('.loader-submit', container).addClass('loading');
    }).on('typeahead:asynccancel typeahead:asyncreceive', function (obj, datum) {
        if (datum == undefined && typeof lookupDetails['resultHandler'] !== 'undefined') {
            if (lookupType == 'contact' && container.attr('id') != 'sla-management-form' && container.attr('id') != 'main-container') {
                if ( systemPreferenceContact == 'no') {
                    jQuery('.empty', container).html(_lang.no_results_matched);
                }
                else {
                    jQuery('.empty', container).html(_lang.no_results_matched_add.sprintf([lookupDetails['lookupField'].val()])).addClass('click').attr('onClick', 'triggerAddContact("' + lookupDetails['lookupField'].val() + '",' + lookupDetails['resultHandler'] + ',"' + container.attr('id') + '");');     }
            } else if (lookupType == 'company' && container.attr('id') != 'sla-management-form' && container.attr('id') != 'main-container') {
                if(systemPreferenceContact == 'no'){
                    jQuery('.empty', container).html(_lang.no_results_matched);
                }
                else { 
                jQuery('.empty', container).html(_lang.no_results_matched_add.sprintf([lookupDetails['lookupField'].val()])).addClass('click').attr('onClick', 'triggerAddCompany("' + lookupDetails['lookupField'].val() + '",' + lookupDetails['resultHandler'] + ',"' + container.attr('id') + '");');
                }
            }
        }
        jQuery('.loader-submit', container).removeClass('loading');
    }).on('typeahead:select', function (ev, datum) {
        if (lookupDetails['lookupField'].attr('id') === 'client-lookup') {
            openManageMoneyAccounts(lookupType, datum.id, lookupType == 'contact' ? datum.contact_category_id : datum.company_category_id, 'client');
        }
        if (lookupDetails.callback['onselect'] && isFunction(lookupDetails.callback['onselect'])) {
            lookupDetails.callback.onselect(ev, datum, container);
        }
    });
    lookupCommonFunctions(lookupDetails['lookupField'], lookupDetails['hiddenInput'], lookupDetails['errorDiv'], container, lookupDetails['callback']['onEraseLookup'], lookupDetails['callback']['onClearLookup']);
}
//add extra options to the datepicker
function setDatePicker(idOrClass, container, options) {
    options = options || datePickerOptions;
    if (_lang.languageSettings['langName'] === 'arabic') {
            datePickerOptions['container'] = idOrClass;
             options['container'] = idOrClass;
    }
    else {
        datePickerOptions['container'] = idOrClass;
        options['container'] = idOrClass;
    }
    jQuery(idOrClass, container).bootstrapDP(options);
}
/*
 *Return the full options of the date picker after extending the standard options with the extraOptions send to the function
 *@parm array extraOptions(array of extra options needed for the datepicker)
 */
function datePickerExtraOptions(extraOptions) {
    var allOptions = {};
    allOptions = jQuery.extend(datePickerOptions, extraOptions);
    return allOptions;
}


/**
 * escape the html special chars
 *
 * @param {string} text
 * @param {array} exceptionTags
 * @returns {string}
 */
function escapeHtml(text, exceptionTags) {
    exceptionTags = exceptionTags || [];
    text = text ? text.toString() : null;

    if (text) {
        // to replace all occurrences of the search term
        String.prototype.replaceAll = function (search, replacement) {
            var target = this;
            return target.split(search).join(replacement);
        };

        var fakeExceptionTags = [];

        // replace these html special characters (<, >) with dummy text to avoid escaping them
        for (var i = 0; i < exceptionTags.length; i++) {
            var tag = exceptionTags[i].replace(">", "||greaterThan||").replace("<", "||lessThan||");
            fakeExceptionTags.push(tag);
            text = text.replaceAll(exceptionTags[i], tag);
        }

        text = text.replace(/[&<>"']/g, function (m) {
            return escapeHtmlMap[m];
        });

        // reverse the replacing process above in the first loop
        for (var i = 0; i < fakeExceptionTags.length; i++) {
            text = text.replaceAll(fakeExceptionTags[i], exceptionTags[i]);
        }

        return text;
    }

    return '';
}

/**
 * check if parameter is a function
 *
 * @param {function} functionToCheck
 * @returns {Boolean}
 */
function isFunction(functionToCheck) {
    return functionToCheck && {}.toString.call(functionToCheck) === '[object Function]';
}

// add scroll top to kendo grid content when clicking on actions wheels if drop down menu don't fit
function animateDropdownMenuInGrids(gridId, scrollTop) {
    scrollTop = scrollTop || 150;
    jQuery('.dropdown-toggle', '#' + gridId + ' .k-grid-content').click(function () {
        gridContentSetAnimation();
    });
    jQuery('.dropdown .dropdown-menu', '#' + gridId + ' .k-grid-content').mouseleave(function () {
        jQuery(this.parentNode).removeClass('open');
    });

    function gridContentSetAnimation() {
        var gridSpace = jQuery('.k-grid-content');
        gridSpace.animate({scrollTop: scrollTop});
    }
}
function customGridToolbarCSSButtons() {
    jQuery('.k-grid-save-changes', '.k-grid-toolbar').removeClass('k-button').addClass('btn btn-info margin-right').css('line-height', '1').find('span').attr('class', '');
    jQuery('.k-grid-cancel-changes', '.k-grid-toolbar').removeClass('k-button').addClass('btn btn-info').css('line-height', '1').find('span').attr('class', '');
    jQuery('.k-grid-add', '.k-grid-toolbar').removeClass('k-button').addClass('btn btn-info margin-right').css('line-height', '1').find('span').attr('class', '');
}
function resetPagination(gridContainer) {
    gridContainer.data('kendoGrid').dataSource.read();
    jQuery('.k-pager-first').click();
}


//highlight the first option of the suggestions
function highLightFirstSuggestion() {
    jQuery('.tt-dataset').each(function () {
        jQuery('.tt-suggestion', this).first().addClass('tt-cursor');
    });
}

/*
 * Lookup for Users(assignee/reporter)
 * Retreiving users on click on the input(retreive all users) or searching for users(depending on the term)
 * @param string lookupField( jQuery selector for lookup input ),hiddenInputIdField( jQuery selector for hidden input field )
 */
function lookUpUsers(lookupField, hiddenInputIdField, errorField, container, formContainer, moreFilters, callback) {
    moreFilters = moreFilters || false;
    callback = callback || false;
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('firstName');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: getBaseURL() + (typeof moreFilters['keyName'] != 'undefined' ? ('users/autocomplete/active?term=%QUERY&more_filters[' + moreFilters['keyName'] + ']=%MORE_FILTERS ') : 'users/autocomplete/active?term=%QUERY'),
            replace: function (url, uriEncodedQuery) {
                if (typeof moreFilters['value'] != 'undefined') {
                    var keyValue = encodeURIComponent(moreFilters['value']);
                    return url.replace('%QUERY', uriEncodedQuery).replace('%MORE_FILTERS', keyValue);
                }
                return url.replace('%QUERY', uriEncodedQuery);
            },
            filter: function (data) {
                return data;
            },
            'cache': false,
            wildcard: '%QUERY',
        },
    });
// Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
// Instantiate the Typeahead UI
    jQuery(lookupField).typeahead({
            hint: false,
            highlight: true,
            minLength: typeof moreFilters['minLength'] != 'undefined' ? moreFilters['minLength'] : 0
        },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                return item.firstName + ' ' + item.lastName
            },
            templates: {
                empty: [
                    '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                header: moreFilters ? '' : '<div class="suggestions-header">' + _lang.allUsers + '</div>'
            }
        }
    ).on('typeahead:selected', function (obj, datum) {
        if (callback['callback'] && isFunction(callback['callback'])) {
            callback.callback(datum, container);
        }
    }).on('typeahead:asyncrequest', function () {
        jQuery('.loader-submit', formContainer).addClass('loading');
    }).on('typeahead:asynccancel typeahead:asyncreceive', function (obj, datum) {
        if (obj.currentTarget['value'] == '' && datum == undefined && moreFilters) {
            //if searching for all result(no term sent) and the response is undefined and moreFilters array is defined
            // then change the response message of the lookup
            jQuery('.empty', '#' + obj.currentTarget['form']['id']).html(moreFilters['messageDisplayed']);
        }
        jQuery('.loader-submit', formContainer).removeClass('loading');
        highLightFirstSuggestion();
    }).on('focus', function () {
        highLightFirstSuggestion();
    });
    if (typeof moreFilters['resize'] == 'undefined') resizeLookupDropDownWidth(container);
    if (!isFunction(callback['onEraseLookup']) || !callback['onEraseLookup']) {
        callback['onEraseLookup'] = false;
    }
    lookupCommonFunctions(lookupField, hiddenInputIdField, errorField, formContainer, callback['onEraseLookup']);
}

function lookUpCollaborators (lookupField, hiddenInputIdField, errorField, container, formContainer, moreFilters, callback) {
    moreFilters = moreFilters || false;
    callback = callback || false;
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: getBaseURL() + 'customer_portal/lookup_collaborators?term=%QUERY',
            filter: function (data) {
                return data;
            },
            replace: function (url, uriEncodedQuery) {
                return url.replace('%QUERY', uriEncodedQuery);
            },
            'cache': false,
            wildcard: '%QUERY',
        }
    });
    // Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
    // Instantiate the Typeahead UI
    jQuery(lookupField).typeahead({
            hint: false,
            highlight: true,
            minLength: typeof moreFilters['minLength'] != 'undefined' ? moreFilters['minLength'] : 0
        },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                return item.firstName + ' ' + item.lastName
            },
            templates: {
                empty: [
                    '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                header: moreFilters ? '' : '<div class="suggestions-header">' + _lang.allCollaborators + '</div>'
            }
        }
    ).on('typeahead:selected', function (obj, datum) {
        if (callback['callback'] && isFunction(callback['callback'])) {
            callback.callback(datum, container);
        }
    }).on('typeahead:asyncrequest', function () {
        jQuery('.loader-submit', formContainer).addClass('loading');
    }).on('typeahead:asynccancel typeahead:asyncreceive', function (obj, datum) {
        if (obj.currentTarget['value'] == '' && datum == undefined && moreFilters) {
            //if searching for all result(no term sent) and the response is undefined and moreFilters array is defined
            // then change the response message of the lookup
            jQuery('.empty', '#' + obj.currentTarget['form']['id']).html(moreFilters['messageDisplayed']);
        }
        jQuery('.loader-submit', formContainer).removeClass('loading');
        highLightFirstSuggestion();
    }).on('focus', function () {
        highLightFirstSuggestion();
    });
    if(typeof moreFilters['resize'] == 'undefined') resizeLookupDropDownWidth(container) ;
    if(!isFunction(callback['onEraseLookup']) || !callback['onEraseLookup']){
        callback['onEraseLookup'] = false;
    }
    lookupCommonFunctions(lookupField, hiddenInputIdField, errorField, formContainer, callback['onEraseLookup']);
}

// Resize the width of the dropdown suggestions for lookup inputs to have the same width as the input width
function resizeLookupDropDownWidth(container) {
    setTimeout(function () {
        jQuery(".tt-menu", container).width(jQuery(".users-lookup-container", container).width() - 3);
    }, 300);
    jQuery(window).bind('resize', (function () {
        jQuery(".tt-menu", container).width(jQuery(".users-lookup-container", container).width() - 3);
    }));
}
function contractActionEvent(action, container, resetStep) {
    resetStep = resetStep || false;
    jQuery('#option', container).val(action);
    if(resetStep){
        step = parseInt(jQuery('.modal-footer', container).attr('data-field'));
        jQuery('.modal-footer', container).attr('data-field',  (step - 1));
    }
    jQuery('.next', container).click();
}
function isOneInputChecked(sel, type) {
    // All <input> tags...
    var inputs = jQuery('input[type='+type+']', sel);
    for (var k=0; k<inputs.length; k++) {
        if(jQuery(inputs[k]).attr('is_required') == 1){
            // If you have more than one radio group, also check the name attribute
            // for the one you want as in && chx[i].name == 'choose'
            // Return true from the function on first match of a checked item
            if( inputs[k].checked )
                return true;
        }else{
            return true;
        }
    }
    // End of the loop, return false
    return false;
}

function rightClickKendo(grid, gridName){
    jQuery(grid).contextmenu(function(e) {
        e.preventDefault();
    });
    jQuery(grid).on("mousedown", "tr[role='row']", function (e) {
        e.preventDefault();
        if (e.which === 3) {
            jQuery(this).addClass("k-state-selected");
            var gridDocument = jQuery(grid).data("kendoGrid");
            var selectedRow = gridDocument.dataItem(jQuery(this));
            jQuery("#"+ gridName + "-actions-menu_"+ selectedRow.id).addClass('open action-hide-kendo');
            jQuery("#"+ gridName + "-dropdown-menu_"+ selectedRow.id).addClass('p-fixed').css(
                {
                    top: e.pageY + "px",
                    left: e.pageX + "px"
                }
            );
            jQuery(this).mouseleave(function() {
                jQuery(grid + " .actions-cell .dropdown").removeClass("action-hide-kendo");
                jQuery("#docs-actions-menu_"+ selectedRow.id).removeClass('open');
                jQuery("#docs-dropdown-menu_"+ selectedRow.id).removeClass('p-fixed').css(
                    {
                        top: "auto",
                        left: "auto"
                    }
                );
                jQuery(this).removeClass("k-state-selected");
            });
        }
    });
}

function selectRemoveAllOptions(container,disabledOptions){
    Selectize.define('select_remove_all_options', function(options) {
        if (this.settings.mode === 'single') return;
    
        var self = this;
    
        self.setup = (function() {
            var original = self.setup;
            return function() {
                original.apply(this, arguments);
                var allBtn = jQuery('<button type="button" class="btn btn-xs btn-allbtn">' + _lang.selectAll + '</button>');
                var clearBtn = jQuery('<button type="button" class="btn btn-xs  btn-primary-outline">' + _lang.clear + '</button>');
                var btnGrp = jQuery('.select-remove-all-options-btn-grp',container);
                btnGrp.append(clearBtn, ' ', allBtn);
                allBtn.on('click', function() {
                    self.setValue(jQuery.map(self.options, function(v, k) {
                        if (jQuery.inArray(k,disabledOptions) === -1) {
                            return k
                        }
                    }));
                });
                clearBtn.on('click', function() {
                    self.setValue([]);
                });
            };
        })();
    });
}
/*
 * Submit add company
 * Save company ,if errors show erros as inline text else success message
 * @parm string container(jQuery selector of modal container)
 */
function companyAddFormSubmit(container , module) {
    module = module || false;
    var formData = jQuery("form", container).serialize();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL(module) + 'companies/add',
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                if (jQuery("#import-company-container").length) {
                    //we are in the import company page so refresh
                    location.reload();
                }

                if (companyContactFormMatrix[container].lookupResultHandler) {
                    companyContactFormMatrix[container].lookupResultHandler(response.records, companyContactFormMatrix[container].referalContainerId);
                }
                if (jQuery('#companyGrid').length) {
                    jQuery('#companyGrid').data("kendoGrid").dataSource.read();
                }
                if (jQuery('#company-group-grid').length) {
                    jQuery('#company-group-grid').data("kendoGrid").dataSource.read();
                }
                updateGetingStartedSteps('company');
                if(module == 'customer-portal'){
                pinesMessage({
                    ty: 'success',
                    m: _lang.feedback_messages.addedNewCompanyInCpSuccessfully
                    });
                }
                else{
                pinesMessage({
                    ty: 'success',
                    m: _lang.feedback_messages.addedNewCompanySuccessfully.sprintf([getBaseURL() + 'companies/tab_company/' + response.records.id, response.records.companyID])
                    });
                }
                jQuery('.modal', container).modal('hide');
                if (jQuery('#money-client-form').length) {
                    // nothing to do as the user is opening the client form and trying
                    // to add a client a account as well so no need to fire the popup of manage accounts
                } else {
                    if (jQuery("#clientLookup", '#legalCaseAddForm').length) {
                        jQuery("#clientLookup", '#legalCaseAddForm').attr("title", response.records.foreignName != null ? response.records.foreignName : '');
                    }
                    if (jQuery("#clientLookup", '#ipEditForm').length) {
                        jQuery("#clientLookup", '#ipEditForm').attr("title", response.records.foreignName != null ? response.records.foreignName : '');
                    }
                    if(module != 'customer-portal') {
                    openManageMoneyAccounts('company', response.records.id, response.records.company_category_id);
                    }
                }
            } else {
                jQuery('.inline-error', container).removeClass('validation-error');
                if (response.validationErrors) {
                    for (i in response.validationErrors) {
                        jQuery("div", container).find("[data-field=" + i + "]").removeClass('d-none').html(response.validationErrors[i]).addClass('validation-error');
                    }
                }
                if (response.address_error) {
                    jQuery.each(response.address_error, function (index, val) {
                        var count = parseInt(index) + 1;
                        jQuery.each(val, function (field, error) {
                            jQuery("div#address-details-container-" + (count), container).find("[data-field=" + field + "]").removeClass('d-none').html(error).addClass('validation-error');
                        });
                    });
                }
                scrollToValidationError(container);
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}
/*
 * Set the values in the company array to be used after submitting the form and Trigger add contact
 * @param string lookupValue () ,string lookupResultHandler ( function to be called after the ) ,string container(id selector of form container)
 */
function triggerAddCompany(lookupValue, lookupResultHandler, container) {
    companyContactFormMatrix.companyDialog = {
        "referalContainerId": (undefined !== companyContactFormMatrix.commonLookup[container] && undefined !== companyContactFormMatrix.commonLookup[container]['referalContainerId']) ? companyContactFormMatrix.commonLookup[container]['referalContainerId'] : '#' + container,
        "lookupResultHandler": lookupResultHandler,
        "lookupValue": lookupValue
    }
    companyAddForm();
}

function updateGetingStartedSteps(stepName) {
    if (jQuery('#getting-started-container').length) {
        var section = stepName + '-section';
        var nextStep = '';
        jQuery('.' + section, '#getting-started-container').addClass('step-done');
        jQuery('.done-sign', '.' + section).removeClass('d-none');
        jQuery.each(jQuery('.section', '.steps-section'), function (key, value) {
            if (!jQuery(this).hasClass('step-done')) {
                nextStep = jQuery(this).attr('id');
                return false;
            }
        });
        if (nextStep) {
            updateGettingStartedHelpers(nextStep);
        } else {
            jQuery('.section-img').tipsy("hide");
            hideGettingStarted();
        }
        var stepsDone = jQuery('.step-done', '#getting-started-container').length;
        jQuery('.gauge-container', '#getting-started-container').kumaGauge('update', {
            value: Math.floor(stepsDone * (100 / 6))
        });
    }
}
function openManageMoneyAccounts(model, modelId, category, target) {
    target = target || '';
    jQuery.ajax({
        beforeSend: function () {
            jQuery("#loader-global").show();
            jQuery('<div id="global-fade-in" class="modal-backdrop fade in"></div>').appendTo('body');
        },
        type: 'POST',
        dataType: 'JSON',
        success: function (response) {
            if (response) {
                jQuery('#global-fade-in').remove();
                jQuery("#loader-global").hide();
                if (response.error) {
                    pinesMessage({ ty: 'information', m: response.error });
                } else if (response.result == true) {
                    if (!jQuery('#manage-money-accounts-dialog').length) {
                        jQuery("<div id='manage-money-accounts-dialog'></div>").appendTo("body");
                    }
                    var manageMoneyAccountsDialog = jQuery("#manage-money-accounts-dialog");
                    manageMoneyAccountsDialog.html(response.html);
                    initializeModalSize(manageMoneyAccountsDialog, 0.3, 'auto');
                    jQuery(".modal", manageMoneyAccountsDialog).modal({
                        keyboard: false,
                        backdrop: "static",
                        show: true
                    });
                    jQuery('.select-picker', manageMoneyAccountsDialog).selectpicker({
                        dropupAuto: false
                    });
                    jQuery(document).keyup(function (e) {
                        if (e.keyCode == 27) {
                            jQuery(".modal", manageMoneyAccountsDialog).modal("hide");
                        }
                    });
                    jQuery('#manage-money-accounts-submit-btn').click(function () {
                        submitManageMoneyAccounts(model, modelId, category);
                    });
                    // One checkbox to check all accounts
                    jQuery('#select-all', manageMoneyAccountsDialog).on('change', function () {
                        if (jQuery(this).prop('checked')) {
                            jQuery('[data-field="record-checkbox"]', manageMoneyAccountsDialog).each(function () {
                                jQuery(this).prop('checked', true).trigger('change');
                            });
                        } else {
                            jQuery('[data-field="record-checkbox"]', manageMoneyAccountsDialog).each(function () {
                                jQuery(this).prop('checked', false).trigger('change');
                            });
                        }
                    });
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler,
        url: getBaseURL() + 'contacts/manage_money_accounts/' + model + '/' + modelId + '/' + category + (target != '' ? '/' + target : '')
    });
}

/*
 * Lookup for contacts
 * Retreiving contacts depending on the term entered with 1 characters and above
 * @param array lookupDetails( details for the lookup input),string container(jQuery selector of modal container),boolean isBoxContainer(whether the lookup field will be set in a box or input)
 */
function lookUpContacts(lookupDetails, container, isBoxContainer, moreFilters, showEmail) {
    moreFilters = moreFilters || false;
    isBoxContainer = isBoxContainer || false;
    showEmail = showEmail || false;
    lookupDetails['onEraseLookup'] = lookupDetails['onEraseLookup'] || false;
    lookupDetails['onChangeEvent'] = lookupDetails['onChangeEvent'] || false;
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: getBaseURL() + (moreFilters ? ('contacts/autocomplete?term=%QUERY&more_filters[' + moreFilters['keyName'] + ']=%MORE_FILTERS ') : 'contacts/autocomplete?term=%QUERY&show_email=' + showEmail),
            replace: function (url, uriEncodedQuery) {
                if (moreFilters) {
                    var keyValue = encodeURIComponent(moreFilters['value']);
                    return url.replace('%QUERY', uriEncodedQuery).replace('%MORE_FILTERS', keyValue);
                }
                return url.replace('%QUERY', uriEncodedQuery);
            },
            filter: function (data) {
                return data;
            },
            'cache': false,
            wildcard: '%QUERY'
        }
    });
    // Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
    // Instantiate the Typeahead UI
    jQuery(lookupDetails['lookupField']).typeahead({
        hint: false,
        highlight: true,
        minLength: 3
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                if (!isBoxContainer) {
                    return (item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName) + (showEmail ? (item.email != null ? ' (' + item.email + ')' : " <span class='text-danger'>(" + _lang.noEmailAddress + ")</span>") : '');
                }
            },
            templates: {
                empty: [
                    '<div class="empty click" ></div>'].join('\n'),
                suggestion: function (data) {
                    foreignFullName = data.foreignFullName.trim();
                    return '<div>' + ((data.father ? data.firstName + ' ' + data.father + ' ' + data.lastName : data.firstName + ' ' + data.lastName) + (foreignFullName ? (' - ' + foreignFullName) : '')) + (showEmail ? (data.email != null ? ' (' + data.email + ')' : " <span class='text-danger'>(" + _lang.noEmailAddress + ")</span>") : '') + '</div>'
                }
            }
        }).on('typeahead:selected', function (obj, datum) {
            if (isBoxContainer) {
                jQuery("div", jQuery('.' + lookupDetails['lookupContainer'], container)).find("[data-field=" + lookupDetails['errorDiv'] + "]").addClass('d-none');
                lookupBoxContainerDesign(jQuery('.' + lookupDetails['lookupContainer'], container));
                setNewBoxElement(lookupDetails['boxId'], lookupDetails['lookupContainer'], '#' + container.attr('id'), {
                    id: datum.id,
                    value: (datum.father ? datum.firstName + ' ' + datum.father + ' ' + datum.lastName : datum.firstName + ' ' + datum.lastName) + (showEmail ? (datum.email != null ? ' (' + datum.email + ')' : " <span class='text-danger'>(" + _lang.noEmailAddress + ")</span>") : ''),
                    name: lookupDetails['boxName']
                });
            }
        }).on('typeahead:asyncrequest', function () {
            jQuery('.loader-submit', container).addClass('loading');
        }).on('typeahead:asynccancel typeahead:asyncreceive', function (obj, datum) {
            if (datum == undefined) {
                //number of dialogs allowed to open is 2(if dialogs count is less than 2,user can open another dialog else user will not have the permission to open a new dialog)
                if (countDialog('contact-dialog-') < 2) {
                    jQuery('.empty', container).html(_lang.no_results_matched_add.sprintf([lookupDetails['lookupField'].val()])).attr('onClick', 'triggerAddContact("' + lookupDetails['lookupField'].val() + '",' + lookupDetails['resultHandler'] + ',"' + container.attr('id') + '")');

                } else {
                    jQuery('.empty', container).html(_lang.no_results_matched).removeClass('click').attr('onClick', '');
                }
            }
            if (obj.currentTarget['value'] == '' && datum == undefined && moreFilters) {
                //if searching for all result(no term sent) and the response is undefined and moreFilters array is defined
                // then change the response message of the lookup
                jQuery('.empty', '#' + obj.currentTarget['form']['id']).html(moreFilters['messageDisplayed']);
            }
            jQuery('.loader-submit', container).removeClass('loading');
        }).on('focus', function () {
            highLightFirstSuggestion();
        }).on('typeahead:change', function () {
            if (lookupDetails['onChangeEvent'] && isFunction(lookupDetails['onChangeEvent'])) {
                lookupDetails['onChangeEvent']();
            }
        });
    if (!isBoxContainer) {
        lookupCommonFunctions(lookupDetails['lookupField'], lookupDetails['hiddenId'], lookupDetails['errorDiv'], container, lookupDetails['onEraseLookup']);
    }
}
/*
 * Submit add contact
 * Save contact ,if errors show erros as inline text else success message
 * @param string container(jQuery selector of form container)
 */
function contactAddFormSubmit(container , module) {
    module = module ||  false;
    var formData = jQuery("form", container).serialize();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL(module) + 'contacts/add',
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                if (jQuery("#import-contact-container").length) {
                    //we are in the import company page so refresh
                    location.reload();
                }
                var contactDialog = jQuery(container);
                if (companyContactFormMatrix[container].lookupResultHandler) {
                    companyContactFormMatrix[container].lookupResultHandler(response.records, companyContactFormMatrix[container].referalContainerId);
                }
                if (jQuery('#relatedContactsGrid').length) {
                    jQuery('#relatedContactsGrid').data('kendoGrid').dataSource.read();
                }
                if (jQuery('#searchResults').length) {
                    jQuery('#searchResults').data("kendoGrid").dataSource.read();
                }
                updateGetingStartedSteps('contact');
                pinesMessage({ ty: 'success', m: _lang.feedback_messages.addedNewContactSuccessfully });
                if (jQuery('#money-client-form').length) {
                    // nothing to do as the user is opening the client form and trying
                    // to add a client and account as well so no need to fire the popup of manage accounts
                } else {
                    if (jQuery("#clientLookup", '#legalCaseAddForm').length) {
                        jQuery("#clientLookup", '#legalCaseAddForm').attr("title", (response.records.foreignFirstName != null ? response.records.foreignFirstName : '') + ' ' + (response.records.foreignLastName != null ? response.records.foreignLastName : ''));
                    }
                    if (jQuery("#clientLookup", '#ipEditForm').length) {
                        jQuery("#clientLookup", '#ipEditForm').attr("title", (response.records.foreignFirstName != null ? response.records.foreignFirstName : '') + ' ' + (response.records.foreignLastName != null ? response.records.foreignLastName : ''));
                    }
                    if(module != 'customer-portal'){
                    openManageMoneyAccounts('contact', response.records.id, response.records.contact_category_id);
                    }
                }
                if (!response.cloned) {
                    jQuery('.modal', contactDialog).modal('hide');
                } else {
                    jQuery("#clone", contactDialog).val("no");
                    jQuery(".value-to-be-uncloned", contactDialog).val("");
                    jQuery('#privateLink', contactDialog).removeClass('d-none');
                    jQuery('#publicLink', contactDialog).addClass('d-none');
                    jQuery('.shared-with-label', contactDialog).removeClass('d-none');
                    jQuery('.users-lookup-container', contactDialog).addClass('d-none');
                    jQuery('#selected-watchers', contactDialog).addClass('d-none');
                    jQuery('.autocomplete-helper', '#contact-privacy-container').addClass('d-none');
                    //to not save the old date chosen
                    jQuery(".date-of-birth", contactDialog).bootstrapDP('setEndDate', getCurrentDate());
                }

            } else {
                jQuery('.inline-error', container).removeClass('validation-error');
                for (i in response.validationErrors) {
                    jQuery("div", container).find("[data-field=" + i + "]").removeClass('d-none').html(response.validationErrors[i]).addClass('validation-error');
                }
                scrollToValidationError(container);
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

/*
 * Set the values in the contact array to be used after submitting the form and Trigger add contact
 * @param string lookupValue () ,string lookupResultHandler ( function to be called after the ) ,string container(id selector of form container)
 */
function triggerAddContact(lookupValue, lookupResultHandler, container, company, customData = null) {
    company = company || null;
    companyContactFormMatrix.contactDialog = {
        "referalContainerId": (undefined !== companyContactFormMatrix.commonLookup[container] && undefined !== companyContactFormMatrix.commonLookup[container]['referalContainerId']) ? companyContactFormMatrix.commonLookup[container]['referalContainerId'] : '#' + container,
        "lookupResultHandler": lookupResultHandler,
        "lookupValue": lookupValue,
        "company": company,
        "customData" : customData
    }
    contactAddForm();
}

/*
 *Set the selected company to contact dialog in a box
 *@parm array company(array of company selected details) , string container(jQuery selector of modal container)
 */
 function setSelectedContactToCompany(contact, container) {
    setNewBoxElement('#selected-lawyers', 'lawyer-lookup-container', container, {
        id: contact.id,
        value: (contact.father ? contact.firstName + ' ' + contact.father + ' ' + contact.lastName : contact.firstName + ' ' + contact.lastName),
        name: 'companyLawyers'
    });
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#lookup-lawyers", container).val('').typeahead('destroy');
        var lookupDetails = {
            'lookupField': jQuery('#lookup-lawyers', container),
            'lookupContainer': 'lawyer-lookup-container',
            'errorDiv': 'lookupLawyers',
            'boxName': 'companyLawyers',
            'boxId': '#selected-lawyers',
            'resultHandler': setSelectedContactToCompany
        };
        lookUpContacts(lookupDetails, jQuery(container), true);
    }
}
/*
 *Show more fields
 *Show more fields on click on the show more link in the dialog to show the rest fields
 *@param string container( jQuery selector for modal container ),string scrollTo field( jQuery selector for the field to scroll to )
 */
 function showMoreFields(container, scrollTo) {
    jQuery(".show-rest-fields", container).addClass('d-none');
    jQuery(".hide-rest-fields", container).removeClass('d-none');
    jQuery(".container-hidden-fields", container).removeClass('d-none');
    jQuery('.modal-body').scrollTo(scrollTo);
}

/*
 *Hide fields
 *Show less fields on click on the show less link in the dialog to hide the rest fields
 *@param string container( jQuery selector for modal container )
 */
function showLessFields(container) {
    jQuery(".hide-rest-fields", container).addClass('d-none');
    jQuery(".show-rest-fields", container).removeClass('d-none');
    jQuery(".container-hidden-fields", container).addClass('d-none');
}

function getCurrentDate() {
    //return the current date
    var today = new Date();
    return today;
}

/*
 * Lookup for nationalities
 * Retreiving nationalities depending on the term entered with 2 characters and above
 * @param string lookupField( jQuery selector for nationality lookup input ),string hiddenInputIdField( jQuery selector for hidden input field ),string inputContainer( jQuery selector for lookup container ),string container(jQuery selector of modal container)
 */
function lookUpNationalities(lookupField, hiddenInputIdField, inputContainer, container , module) {
    module = module || false
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('countryName');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        replace: function (url, uriEncodedQuery) {
            return url + "#" + uriEncodedQuery;
            // the part after the hash is not sent to the server
        },
        remote: {
            url: getBaseURL(module) + 'home/load_country_list?term=%QUERY',
            filter: function (data) {
                return data;
            },
            'cache': false,
            wildcard: '%QUERY',
        }
    });
    // Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
    // Instantiate the Typeahead UI
    jQuery(lookupField).typeahead({
        hint: false,
        highlight: true,
        minLength: 2
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
            },
            templates: {
                empty: [
                    '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                suggestion: function (data) {
                    return '<div>' + data.countryName + '</div>'
                }
            }
        }).on('typeahead:selected', function (obj, datum) {
            jQuery('.' + hiddenInputIdField, inputContainer).addClass('d-none');
            lookupBoxContainerDesign(inputContainer);
            setNewBoxElement('#selected_nationalities', 'nationality-lookup-container', container, {
                id: datum.id,
                value: datum.countryName,
                name: hiddenInputIdField
            });
        }).on('typeahead:asyncrequest', function () {
            jQuery('.loader-submit', container).addClass('loading');
        }).on('typeahead:asynccancel typeahead:asyncreceive', function () {
            jQuery('.loader-submit', container).removeClass('loading');
        });
}
/*
 *Set the selected company to contact dialog in a box
 *If the company has contact details ,it will be set for the contact
 *@parm array company(array of company selected details) , string container(jQuery selector of modal container)
 */
 function setSelectedCompanyToContact(company, container) {
    setNewBoxElement('#selected_companies', 'company-lookup-container', container, {
        id: company.id,
        value: company.name,
        name: 'companies_contacts'
    });
    if (jQuery('.twitter-typeahead', container).length) {
        jQuery("#lookupCompanies", container).val('').typeahead('destroy');
        var lookupDetails = {
            'lookupField': jQuery('#lookupCompanies', container),
            'errorDiv': 'companies_contacts',
            'lookupContainer': jQuery('.company-lookup-container', container),
            'resultHandler': setSelectedCompanyToContact
        };
        lookUpCompanies(lookupDetails, jQuery(container));
    }
    jQuery('#address1', container).val(company.address);
    jQuery('#city', container).val(company.city);
    jQuery('#country_id', container).val(company.country).selectpicker('refresh');
    jQuery('#state', container).val(company.state);
    jQuery('#zip', container).val(company.zip);
    jQuery('#phone', container).val(company.phone);
    jQuery('#fax', container).val(company.fax);
    jQuery('#website', container).val(company.website);
}

/*
 * Lookup for companies
 * Retreiving companies depending on the term entered with 1 character and above
 * @param array lookupDetails(array of data ),string container(jQuery selector of modal container)
 */
function lookUpCompanies(lookupDetails, container , module) {
    module = module || false;
    var isBoxContainer = typeof lookupDetails['isBoxContainer'] !== 'undefined' ? lookupDetails['isBoxContainer'] : true;
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        replace: function (url, uriEncodedQuery) {
            return url + "#" + uriEncodedQuery;
            // the part after the hash is not sent to the server
        },
        remote: {
            url: getBaseURL(module) + 'companies/autocomplete?term=%QUERY',
            filter: function (data) {
                return data;
            },
            'cache': false,
            wildcard: '%QUERY',
        }
    });
    // Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
    // Instantiate the Typeahead UI
    jQuery(lookupDetails['lookupField']).typeahead({
        hint: false,
        highlight: true,
        minLength: 3
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
                if (!isBoxContainer) {
                    return item.name;
                }
            },
            templates: {
                empty: ['<div class="empty click"></div>'].join('\n'),
                suggestion: function (data) {
                    if (data.shortName == null) {
                        return '<div>' + data.name + (null == data.foreignName ? '' : ' - ' + data.foreignName) + '</div>'
                    } else {
                        return '<div>' + data.name + ' (' + data.shortName + ')' + (null == data.foreignName ? '' : ' - ' + data.foreignName) + '</div>'
                    }
                }
            }
        }
    ).on('typeahead:selected', function (obj, datum) {
        if (isBoxContainer) {
            jQuery("div", lookupDetails['lookupContainer']).find("[data-field=" + lookupDetails['errorDiv'] + "]").addClass('d-none');
            lookupBoxContainerDesign(lookupDetails['lookupContainer']);
            setSelectedCompanyToContact(datum, '#' + container.attr('id'));
        }
    }).on('typeahead:asyncrequest', function () {
        jQuery('.loader-submit', container).addClass('loading');
    }).on('typeahead:asynccancel typeahead:asyncreceive', function (obj, datum) {
        if (datum == undefined) {
            //number of dialogs allowed to open is 2(if dialogs count is less than 2,user can open another dialog else user will not have the permission to open a new dialog)
            if (countDialog('company-dialog-') < 1) {
                jQuery('.empty', lookupDetails['lookupContainer']).html(_lang.no_results_matched_add.sprintf([lookupDetails['lookupField'].val()])).attr('onClick', 'triggerAddCompany("' + lookupDetails['lookupField'].val() + '",' + lookupDetails['resultHandler'] + ',"' + container.attr('id') + '");');
            } else {
                jQuery('.empty', lookupDetails['lookupContainer']).html(_lang.no_results_matched).removeClass('click').attr('onClick', '');
            }
        }
        jQuery('.loader-submit', container).removeClass('loading');
    });

    if (!isBoxContainer) {
        lookupCommonFunctions(lookupDetails['lookupField'], lookupDetails['hiddenId'], lookupDetails['errorDiv'], container);
    }
}

/*
 * Lookup for type(company/container) to copy address
 * Retreiving type(company/container) depending on the type and the term entered with 1 character and above
 * @param string lookupField( jQuery selector for the lookup type lookup input ),string container( jQuery selector for modal container )
 */
function lookupTypeToCopyAddress(lookupField, container, initializeFunctions) {
    initializeFunctions = initializeFunctions || false;
    var copyAddressFromContainer = jQuery('#copyAddressFromContainer', container);
    jQuery('#copyAddressFromType', copyAddressFromContainer).change(function () {
        jQuery("#copyAddressFromLookup", copyAddressFromContainer).val('');
    });

    var lookupType = jQuery("select#copyAddressFromType", copyAddressFromContainer).val();
    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        replace: function (url, uriEncodedQuery) {
            return url + "#" + uriEncodedQuery;
            // the part after the hash is not sent to the server
        },
        remote: {
            url: getBaseURL() + lookupType + '/autocomplete?term=%QUERY',
            filter: function (data) {
                return data;
            },
            'cache': false,
            wildcard: '%QUERY',
        }
    });
    // Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
    // Instantiate the Typeahead UI
    var typeahead = jQuery(lookupField).typeahead({
        hint: false,
        highlight: true,
        minLength: 3
    },
        {
            source: mySuggestion.ttAdapter(),
            display: function (item) {
            },
            templates: {
                empty: [
                    '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                suggestion: function (data) {
                    if (lookupType == 'contacts') {
                        foreignFullName = data.foreignFullName.trim();
                        return '<div>' + (data.father ? data.firstName + ' ' + data.father + ' ' + data.lastName : data.firstName + ' ' + data.lastName) + (foreignFullName ? (' - ' + foreignFullName) : '') + '</div>'
                    } else if (lookupType == 'companies' && data.shortName == null) {
                        return '<div>' + data.name + (null == data.foreignName ? '' : ' - ' + data.foreignName) + '</div>'
                    } else {
                        return '<div>' + data.name + ' (' + data.shortName + ')' + (null == data.foreignName ? '' : ' - ' + data.foreignName) + '</div>'
                    }
                }
            }
        });
    if (initializeFunctions) {
        typeahead.on('typeahead:selected', function (obj, datum) {
            if (jQuery("select#copyAddressFromType", container).val() == 'contacts') {
                copyAddressFromContact(datum, container, true);
            } else {
                copyAddressFromCompany(datum, container, true);
            }
        }).on('typeahead:asyncrequest', function () {
            jQuery('.loader-submit', container).addClass('loading');
        }).on('typeahead:asynccancel typeahead:asyncreceive', function (obj, datum) {
            jQuery('.loader-submit', container).removeClass('loading');
        });
    }

}

/**
 * add contacts emails, for add form only
 *
 * @param {String} emailField
 * @param {Object} container
 */
 function addContactEmailRowToAddForm(emailField, container) {
    var email = emailField.val();
    var validation = validateEmail(emailField);
    var contactEmailId = jQuery('element[id^="contact-email-"]', 'element[id^="contact-add-form"]').length;

    if (validation === true) {
        if (!contactEmailAlreadyExists(email, container)) {
            var html = '';

            html += '<div class="row justify-content-between align-items-center multi-option-selected-items no-margin contact-email" id="contact-email' + contactEmailId + '">';
            html += '<span>' + email + '</span>';
            html += '<input type="hidden" name="contact_emails[]" value="' + email + '">';
            html += '<input value="x" type="button" class="btn btn-default btn-sm" onclick="jQuery(this.parentNode).remove();" />';
            html += '</div>';

            container.append(html);
            emailField.val(null);
            jQuery('#contact-emails-error').addClass('d-none');
        } else {
            jQuery('#contact-emails-error').html("<p>Email Address Already Exists</p>").removeClass('d-none');
        }
    } else if (email.length > 0) {
        jQuery('#contact-emails-error').html("<p>Invalid</p>").removeClass('d-none');
    }
}

function validateEmail(field, rules, i, options) {
    var val = field.val();
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,8}(?:\.[a-z]{2})?)$/i;
    if (!re.test(val)) {
        return _lang.invalid_email;
    }

    return true;
}

function contactEmailAlreadyExists(email, container) {
    var result = false;

    container.find('input[name="contact_emails[]"]').each(function () {
        if (jQuery(this).val() == email) {
            result = true;
            return false;
        }
    });

    return result;
}
      
//return the dialog count that starts with specific prefix
function countDialog(prefixId) {
    return jQuery('[id^=' + prefixId + ']').length;
}

/*
 * Resize mini modal dialog (adminisration dialogs and dialogs of small size)
 * if the window width has a max width of 500 px ,then the model will resize to a smaller size and will have a class modal-xs else it will has a fixed width
 * @parm string container(jQuery selector of modal container)
 */
function resizeMiniModal(container) {
    if (jQuery(window).width() <= 500) {
        jQuery('.modal-dialog', container).addClass('modal-xs').removeClass('width-450');
    } else {
        jQuery('.modal-dialog', container).removeClass('modal-xs').addClass('width-450');
    }
}

/*
 * Initialize the size of the dialog
 * set the size of the dialog and force it to start from the top without any scroll on opening the dialog
 * Resize modal dialog
 * Re-set the width and height of the modal as a % of the screen size
 */
function initializeModalSize(container, widthPercentage, heightPercentage) {
    setTimeout(function () {
        dropDownPosition(container);
    });
    widthPercentage = widthPercentage || 0.5;
    heightPercentage = heightPercentage || 0.6;
    container = jQuery('.modal-resizable', container);
    jQuery('.modal-body', container).css('height', jQuery(window).height() * heightPercentage);
    jQuery('.modal-dialog', container).css('max-width', jQuery(window).width() * widthPercentage);
    jQuery('.modal-body', container).animate({
        scrollTop: '0px'
    }, 1000);
    jQuery(window).bind('resize', (function () {
        jQuery('.modal-body', container).css('height', jQuery(window).height() * heightPercentage);
        jQuery('.modal-dialog', container).css('width', jQuery(window).width() * widthPercentage);
    }));
}

function toggleCopyAddressFrom(container) {
    container = ('string' === typeof container) ? jQuery('#' + container) : jQuery(container);
    var lookupContainer = jQuery('#copyAddressFromContainer', container);
    if (lookupContainer.is(':visible')) {
        lookupContainer.addClass('d-none');
    } else {
        lookupContainer.removeClass('d-none');
    }
}

function rowsUpdateCount(object, container) {
    var objectContainer = jQuery('#' + object + '-rows-container', container);
    var nbOfObjects = objectContainer.attr('data-count-row');
    var count = 1;
    jQuery('.' + object + '-div', objectContainer).each(function () {
        if (count <= nbOfObjects) {
            jQuery(this).attr("id", object + '-' + count);
            jQuery('.label-signature', this).attr('id','signature-label-'+count);
            jQuery('.delete-signature', this).attr('onclick', 'removeRow("#' +object + '-' + count +'","'+ container+'")');
            jQuery('.inline-error', this).attr('id', 'upload-error-' + count);
            count++;
        } else {
            return true;
        }
    });
}
                
function replaceHtmlCharacter(character) {
    if(character) {
        return character.replace(/&#039;/g, "'").replace(/&quot;/g ,'"').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&#8230;/g, '...');
    }
    // In case of an empty string, so the value returned won't be `undefined`
    return character; 
}

function checkIfStringIsURIEncoded(string) {
    return string.match(/%[0-9a-f]{2}/i);
}

/*
 * Collapse down up the div
 * @param string id (id of the div to collapse)
 * @param string iconRotateId (id of the icon to rotate)
 * @param string speed (speed of the collapse)
 * @param boolean close (whether to close or open the div)
 */
function collapseDownUp(id, iconRotateId, speed, close) {
    speed = speed || 'fast'
    close = close || false;
    if (close == true) {
        if (!jQuery('#' + iconRotateId).hasClass('rotate-origin-closed')) {
            jQuery('#' + iconRotateId).addClass('rotate-origin-closed');
        }
        else {
            jQuery('#' + iconRotateId).removeClass('rotate-origin-closed');
            jQuery('#' + id).removeClass('d-block');
        }
    }
    else {
        if (!jQuery('#' + iconRotateId).hasClass('rotate-origin')) {
            jQuery('#' + iconRotateId).addClass('rotate-origin');
        }
        else {
            jQuery('#' + iconRotateId).removeClass('rotate-origin');
        }
    }
    jQuery('#' + id).slideToggle(speed)
}