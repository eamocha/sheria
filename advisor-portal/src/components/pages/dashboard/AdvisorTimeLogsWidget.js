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
import moment from 'moment';
import { useTranslation } from 'react-i18next';
import VisibilityOffIcon from '@material-ui/icons/VisibilityOff';
import { Context } from '../../../Store';

const theme = createMuiTheme();

const AdvisorTimeLogsWidget = React.memo((props) => {

    const now = moment();
    const { t } = useTranslation();
    const [globalState, globalStateDispatcher] = useContext(Context);
    const title = t("my_billable_vs_non_billable_hours");
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
            "LogDateByYear": {
                "value": now.year()
            },
            "Advisor": {
                "value": props?.user?.id
            }
        }).then((response) => {
            if (response?.data?.data) {
                let series = generateSeries(response?.data?.data);

                setWidgetContent(<ReactApexChart
                    options={
                        {
                            chart: {
                                type: 'bar',
                            },
                            colors: [
                                "#008FFB",
                                "#808080"
                            ],
                            legend: {
                                show: true,
                                position: 'top',
                            },
                            series: series,
                            plotOptions: {
                                bar: {
                                    vertical: true,
                                    dataLabels: {
                                        position: 'top',
                                    },
                                }
                            },
                            xaxis: {
                                categories: [t("current_week"), t("current_month"), t("current_year")],
                                labels: {
                                    show: true,
                                    style: {
                                        color: 'blue',
                                        fontSize: '12px'
                                    },
                                }
                            },
                            noData: {
                                text: t("no_records_to_display"),
                                align: 'center',
                                verticalAlign: 'middle',
                                offsetX: 0,
                                offsetY: 0,
                                style: {
                                    fontSize: '14px'
                                }
                            }
                        }
                    }
                    series={series}
                    type='bar'
                />);
            }
        }).catch((error) => {

        });
    };

    const generateSeries = (data) => {
        let billablesInCurrentWeek = filterDataByCurrentWeek(data, 'billable');
        let billablesInCurrentMonth = filterDataByCurrentMonth(data, 'billable');
        let billablesInCurrentYear = filterDataByCurrentYear(data, 'billable');

        let nonBillablesInCurrentWeek = filterDataByCurrentWeek(data, 'internal');
        let nonBillablesInCurrentMonth = filterDataByCurrentMonth(data, 'internal');
        let nonBillablesInCurrentYear = filterDataByCurrentYear(data, 'internal');

        let billablesInCurrentWeekHours = billablesInCurrentWeek.map(item => item?.effectiveEffortInHours);
        let billablesInCurrentMonthHours = billablesInCurrentMonth.map(item => item?.effectiveEffortInHours);
        let billablesInCurrentYearHours = billablesInCurrentYear.map(item => item?.effectiveEffortInHours);

        let nonBillablesInCurrentWeekHours = nonBillablesInCurrentWeek.map(item => item?.effectiveEffortInHours);
        let nonBillablesInCurrentMonthHours = nonBillablesInCurrentMonth.map(item => item?.effectiveEffortInHours);
        let nonBillablesInCurrentYearHours = nonBillablesInCurrentYear.map(item => item?.effectiveEffortInHours);

        let billablesInCurrentWeekHoursReduced = billablesInCurrentWeekHours.reduce((accum, val) => accum = parseFloat(accum) + parseFloat(val), 0);
        let billablesInCurrentMonthHoursReduced = billablesInCurrentMonthHours.reduce((accum, val) => accum = parseFloat(accum) + parseFloat(val), 0);
        let billablesInCurrentYearHoursReduced = billablesInCurrentYearHours.reduce((accum, val) => accum = parseFloat(accum) + parseFloat(val), 0);

        let nonBillablesInCurrentWeekHoursReduced = nonBillablesInCurrentWeekHours.reduce((accum, val) => accum = parseFloat(accum) + parseFloat(val), 0);
        let nonBillablesInCurrentMonthHoursReduced = nonBillablesInCurrentMonthHours.reduce((accum, val) => accum = parseFloat(accum) + parseFloat(val), 0);
        let nonBillablesInCurrentYearHoursReduced = nonBillablesInCurrentYearHours.reduce((accum, val) => accum = parseFloat(accum) + parseFloat(val), 0);

        return [
            {
                name: t('billable_hours'),
                data: [
                    billablesInCurrentWeekHoursReduced,
                    billablesInCurrentMonthHoursReduced,
                    billablesInCurrentYearHoursReduced
                ]
            },
            {
                name: t('non_billable_hours'),
                data: [
                    nonBillablesInCurrentWeekHoursReduced,
                    nonBillablesInCurrentMonthHoursReduced,
                    nonBillablesInCurrentYearHoursReduced
                ]
            }
        ];
    }

    const filterDataByCurrentWeek = (data, timeStatus) => {
        return data.filter(item => {
            var input = moment(item?.logDate);

            return item?.timeStatus == timeStatus && now.isoWeek() == input.isoWeek()
        });
    }

    const filterDataByCurrentMonth = (data, timeStatus) => {
        return data.filter(item => {
            var input = moment(item?.logDate);

            return item?.timeStatus == timeStatus && now.year() == input.year() && now.month() == input.month()
        });
    }

    const filterDataByCurrentYear = (data, timeStatus) => {
        return data.filter(item => {
            return item?.timeStatus == timeStatus
        });
    }

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

export default AdvisorTimeLogsWidget;
