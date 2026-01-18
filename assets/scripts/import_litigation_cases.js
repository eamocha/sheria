/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 jQuery(document).ready(function () {
        jQuery("form#importLitigationCaseForm").validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomRight', scroll: false});
        var $wizard = jQuery('#rootwizard');
        $wizard.bootstrapWizard({
            onNext: function (tab, navigation, index) {
                jQuery('li.previous', $wizard).removeClass('disabled');
                jQuery('#importLink', $wizard).text(_lang.import).click(function () {
                    if (jQuery("form#importLitigationCaseForm").validationEngine('validate') && !jQuery('li.previous', $wizard).hasClass('disabled')) {
                        jQuery("form#importLitigationCaseForm").submit();
                        jQuery("#loader-global").show();
                    }
                });
                showProgressPercent($wizard, '50');
            },
            onPrevious: function (tab, navigation, index) {
                navigation.find('li').removeClass('active');
                navigation.find('li:first').addClass('active');
                jQuery('li.previous', $wizard).addClass('disabled');
                jQuery('#importLink', $wizard).text(_lang.next);
                showProgressPercent($wizard, '25');
            }
        });
    });
    function showProgressPercent($wizard, $percent) {
        $wizard.find('.progress-bar').css({width: $percent + '%'});
    }

