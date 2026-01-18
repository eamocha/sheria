import React, {
    useContext,
    useEffect,
    useState
} from 'react';
import {
    CircularProgress,
    Container,
    createMuiTheme,
    Grid,
    IconButton,
    MuiThemeProvider,
    Typography
} from '@material-ui/core';
import ReactApexChart from 'react-apexcharts';
import AdvisorTimeLog from './../../../api/AdvisorTimeLog';
import { formatDate } from './../../../APHelpers';
import { useTranslation } from 'react-i18next';
import VisibilityOffIcon from '@material-ui/icons/VisibilityOff';
import { Context } from '../../../Store';

const theme = createMuiTheme();

const AdvisorTimeLogsTodayWidget = React.memo((props) => {

    const today = new Date();
    const { t } = useTranslation();
    const [globalState, globalStateDispatcher] = useContext(Context);
    const title = t("tasks_requested_by_me_by_status_logged_today");
    const circularProgress = <Container
        maxWidth={false}
        className="dashboard-widget-loader-container d-flex align-items-center justify-content-center no-padding-h"
    >
        <CircularProgress />
    </Container>;

    const [widgetContent, setWidgetContent] = useState(circularProgress);

    const header = <Grid
        container
        className="no-padding-h"
    >
        <Grid
            sm="10"
            className="dashboard-widget-title-container"
        >
            <Typography
                className="dashboard-widget-title"
            >
                {title}
            </Typography>
        </Grid>
        <IconButton
            className={"hide-wideget-btn close-button " + globalState.domDirection}
            onClick={(e) => props?.hideWidget(props?.name)}
            size="small"
            title={t("hide_widget")}
            color="inherit"
        >
            <VisibilityOffIcon
                fontSize="small"
            />
        </IconButton>
        <Grid
            sm="2"
            className="d-flex align-items-center justify-content-end"
        >
        </Grid>
    </Grid>;

    useEffect(() => {

        loadData();
    }, [t]);

    const loadData = () => {
        setWidgetContent(circularProgress);

        AdvisorTimeLog.getList({
            "LogDate": {
                "value": formatDate(today)
            },
            "Advisor": {
                "value": props?.user?.id
            }
        }).then((response) => {
            let data = response?.data?.data;

            let billables = data.filter(item => item?.timeStatus == 'billable');
            let billablesHours = billables.map(item => item?.effectiveEffortInHours);
            let billablesHoursReduced = billablesHours.reduce((accum, val) => accum = parseFloat(accum) + parseFloat(val), 0);

            let nonBillables = data.filter(item => item?.timeStatus == 'internal');
            let nonBillablesHours = nonBillables.map(item => item?.effectiveEffortInHours);
            let nonBillablesHoursReduced = nonBillablesHours.reduce((accum, val) => accum = parseFloat(accum) + parseFloat(val), 0);

            let series = [billablesHoursReduced, nonBillablesHoursReduced];

            setWidgetContent(<ReactApexChart
                options={
                    {
                        chart: {
                            type: 'donut',
                        },
                        legend: {
                            position: 'top'
                        },
                        labels: [
                            t("billable_hours"),
                            t("non_billable_hours")
                        ],
                        colors: [
                            "#008FFB",
                            "#808080"
                        ],
                        plotOptions: {
                            pie: {
                                donut: {
                                    labels: {
                                        show: true,
                                        total: {
                                            show: true,
                                            label: t('total_hours'),
                                            fontSize: '22px',
                                            fontFamily: 'Helvetica, Arial, sans-serif',
                                            fontWeight: 600,
                                            color: '#373d3f',
                                            formatter: function (w) {
                                                return w.globals.seriesTotals.reduce((a, b) => {
                                                    return a + b
                                                }, 0)
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            formatter: function (val, opts) {
                                return opts.w.config.series[opts.seriesIndex];
                            }
                        },
                        noData: {
                            text: t("no_records_to_display"),
                            align: 'center',
                            verticalAlign: 'middle',
                            offsetX: 0,
                            offsetY: 0,
                            style: {
                                fontSize: '14px',
                            }
                        }
                    }
                }
                series={series}
                type='donut'
            />);
        }).catch((error) => {

        });
    };

    return (
        // <MuiThemeProvider
        //     theme={theme}
        // >
        <Container
            className="dashboard-widget no-padding-h"
        >
            <Container
                className="dashboard-widget-header"
            >
                {header}
            </Container>
            <Container
                maxWidth={false}
                className="dashboard-widget-body"
            >
                <Container
                    maxWidth={false}
                    className="dashboard-widget-filters-container no-padding-h"
                >

                </Container>
                {widgetContent}
            </Container>
        </Container>
        // </MuiThemeProvider>
    );
});

export default AdvisorTimeLogsTodayWidget;
