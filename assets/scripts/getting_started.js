var helperStep = '';
var stepsDone = stepsNb = 0;
var openAvatarForm = false;
jQuery(document).ready(function () {
    if (helperStep) {
        updateGettingStartedHelpers(helperStep);
    }
    if (openAvatarForm) {
        avatarUploaderForm();
    }
    jQuery('.gauge-container', '#getting-started-container').kumaGauge({
        value: Math.floor(stepsDone * (100 / stepsNb)),
        min: 0,
        max: 100,
        showNeedle: false,
        gaugeBackground: '#F1F2F2',
        gaugeWidth: 20,
        radius: (jQuery('#request-demo-container', '#getting-started-container').width()) / 2,
        fill: '#3593F7',
        paddingX: 0,
        paddingY: 20,
        valueLabel: {
            display: true,
            fontFamily: 'Arial',
            fontColor: '#3593F7',
            fontSize: 30,
            fontWeight: 'bold'
        },
        label: {
            display: false
        }
    });
    jQuery("#dont-show-again-checkbox", '#getting-started-container').change(function () {
        if (this.checked) {
            jQuery('.dont-show-again', '#getting-started-container').addClass('d-none');
            jQuery('.confirm-dont-show-again', '#getting-started-container').removeClass('d-none');
            jQuery('#dont-show-again-checkbox', '#getting-started-container').attr('checked', false);
        }
    });

});
