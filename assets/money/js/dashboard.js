function showTooltip(x, y, contents) {
    jQuery('<div id="tooltip" class="tooltipflot">' + contents + '</div>').css({
        position: 'absolute',
        display: 'none',
        top: y - 7,
        left: x + 5
    }).appendTo("body").fadeIn(200);
}
function changeYear() {
    if (jQuery('#year:selected').val() != '')
        window.location = getBaseURL('money') + 'money_dashboard/index/' + jQuery('#year').val();
}
jQuery(document).ready(function () {
    jQuery.jqplot.config.enablePlugins = true;
    plot1 = jQuery.jqplot('yearly', [document.yearly], {
        stackSeries: true,
        animate: true,
        animateReplot: true,
        seriesDefaults: {
            renderer: jQuery.jqplot.BarRenderer,
            rendererOptions: {
                barMargin: 8,
                highlightMouseOver: true
            },
            pointLabels: {
                show: true
            }
        },
        axes: {
            xaxis: {
                renderer: jQuery.jqplot.CategoryAxisRenderer,
                ticks: [_lang.expenses, _lang.bills, _lang.billsPaid, _lang.allInvoices, _lang.incomePaidInvoices],
                label: ''
            },
            yaxis: {
                rendererOptions: {
                    forceTickAt0: true
                },
                showTicks: true,
                padMin: 0
            }
        },
        seriesColors: ["#FFA086"],
        highlighter: {
            show: true,
            showlabel: true
        }
    });
    jQuery("#yearly").bind('jqplotDataHighlight', function (ev, seriesIndex, pointIndex, data) {
        showTooltip(ev.pageX, ev.pageY, _lang.total + ": " + number_format(data[1]));
    });
    jQuery("#yearly").bind('jqplotDataUnhighlight', function (ev, seriesIndex, pointIndex, data) {
        jQuery('#tooltip').remove();
    });
    plot2 = jQuery.jqplot('monthly', [document.monthly], {
        stackSeries: true,
        animate: true,
        animateReplot: true,
        seriesDefaults: {
            renderer: jQuery.jqplot.BarRenderer,
            rendererOptions: {
                barMargin: 8,
                highlightMouseOver: true
            },
            pointLabels: {
                show: true
            }
        },
        axes: {
            xaxis: {
                renderer: jQuery.jqplot.CategoryAxisRenderer,
                ticks: [_lang.expenses, _lang.bills, _lang.billsPaid, _lang.allInvoices, _lang.incomePaidInvoices],
                label: ''
            },
            yaxis: {
                rendererOptions: {
                    forceTickAt0: true
                },
                showTicks: true,
                padMin: 0
            }
        },
        seriesColors: ["#4bb2c5", "#EAA228", "#c5b47f", "#579575", "#839557", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc", "#c747a3", "#cddf54", "#FBD178", "#26B4E3", "#bd70c7"],
        highlighter: {
            show: true,
            showlabel: true
        }
    });
    jQuery("#monthly").bind('jqplotDataHighlight', function (ev, seriesIndex, pointIndex, data) {
        showTooltip(ev.pageX, ev.pageY, _lang.total + ": " + number_format(data[1]));
    });
    jQuery("#monthly").bind('jqplotDataUnhighlight', function (ev, seriesIndex, pointIndex, data) {
        jQuery('#tooltip').remove();
    });
    plot3 = jQuery.jqplot('quarterly', [document.quarterly], {
        stackSeries: true,
        animate: true,
        animateReplot: true,
        seriesDefaults: {
            renderer: jQuery.jqplot.BarRenderer,
            rendererOptions: {
                barMargin: 8,
                highlightMouseOver: true
            },
            pointLabels: {
                show: true
            }
        },
        axes: {
            xaxis: {
                renderer: jQuery.jqplot.CategoryAxisRenderer,
                ticks: [_lang.expenses, _lang.bills, _lang.billsPaid, _lang.allInvoices, _lang.incomePaidInvoices],
                label: ''
            },
            yaxis: {
                rendererOptions: {
                    forceTickAt0: true
                },
                showTicks: true,
                padMin: 0
            }
        },
        seriesColors: ["#EAA228", "#c5b47f", "#579575", "#839557", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc", "#c747a3", "#cddf54", "#FBD178", "#26B4E3", "#bd70c7"],
        highlighter: {
            show: true,
            showlabel: true
        }
    });
    jQuery("#quarterly").bind('jqplotDataHighlight', function (ev, seriesIndex, pointIndex, data) {
        showTooltip(ev.pageX, ev.pageY, _lang.total + ": " + number_format(data[1]));
    });
    jQuery("#quarterly").bind('jqplotDataUnhighlight', function (ev, seriesIndex, pointIndex, data) {
        jQuery('#tooltip').remove();
    });
    plot4 = jQuery.jqplot('semesterly', [document.semesterly], {
        stackSeries: true,
        animate: true,
        animateReplot: true,
        seriesDefaults: {
            renderer: jQuery.jqplot.BarRenderer,
            rendererOptions: {
                barMargin: 8,
                highlightMouseOver: true
            },
            pointLabels: {
                show: true
            }
        },
        axes: {
            xaxis: {
                renderer: jQuery.jqplot.CategoryAxisRenderer,
                ticks: [_lang.expenses, _lang.bills, _lang.billsPaid, _lang.allInvoices, _lang.incomePaidInvoices],
                label: ''
            },
            yaxis: {
                rendererOptions: {
                    forceTickAt0: true
                },
                showTicks: true,
                padMin: 0
            }
        },
        seriesColors: ["#cddf54", "#FBD178", "#26B4E3", "#bd70c7"],
        highlighter: {
            show: true,
            showlabel: true
        }
    });
    jQuery("#semesterly").bind('jqplotDataHighlight', function (ev, seriesIndex, pointIndex, data) {
        showTooltip(ev.pageX, ev.pageY, _lang.total + ": " + number_format(data[1]));
    });
    jQuery("#semesterly").bind('jqplotDataUnhighlight', function (ev, seriesIndex, pointIndex, data) {
        jQuery('#tooltip').remove();
    });  
    jQuery(window).resize(function () {
        plot1.replot({axes: {xaxis: {min: null, max: null}}});
        plot2.replot({axes: {xaxis: {min: null, max: null}}});
        plot3.replot({axes: {xaxis: {min: null, max: null}}});
        plot4.replot({axes: {xaxis: {min: null, max: null}}});
    });
});
//function dashboard(){
//	if(jQuery('#organization_id option:selected').val()!='')
//		window.location=getBaseURL('money')+'dashboard/index/'+jQuery('#organization_id').val();
//}