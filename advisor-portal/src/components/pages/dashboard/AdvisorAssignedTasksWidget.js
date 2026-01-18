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

import { APAutocompleteList, APNativeSelectList } from './../../common/APForm/APForm';

import ReactApexChart from 'react-apexcharts';

import AdvisorTask from './../../../api/AdvisorTask';

import {
    generateListOfYears,
    generateListOfNumbers
} from './../../../APHelpers';

import MiscList from './../../../api/MiscList';

import {
    Context,
    initialGlobalState
} from '../../../Store';

import { useTranslation } from 'react-i18next';

import VisibilityOffIcon from '@material-ui/icons/VisibilityOff';

const theme = createMuiTheme();

const AdvisorAssignedTasksWidget = React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    
    const { t } = useTranslation();
    
    const today = new Date();
    
    const title = t("tasks_assigned_to_me_by_status");
    
    const circularProgress = <Container
        maxWidth={false}
        className="dashboard-widget-loader-container d-flex align-items-center justify-content-center no-padding-h"
    >
        <CircularProgress />
    </Container>;

    const [practiceArea, setPracticeArea] = useState(null);
    
    const [dueDateByYear, setDueDateByYear] = useState(today.getFullYear());
    
    const [dueDateByMonth, setDueDateByMonth] = useState(null);
    
    const [practiceAreasList, setPracticeAreasList] = useState([]);
    
    const [defaultValues, setDefaultValues] = useState({
        practiceArea: {
            title: "All",
            value: ""
        },
        dueDateByMonth: {
            title: "All",
            value: null
        }
    });
    const [widgetContent, setWidgetContent] = useState(circularProgress);

    const years = generateListOfYears();
    
    const months = generateListOfNumbers();

    const handlePracticeAreaChange = (state, stateValue, defaultValues, multipleSelection, defaultValuesWithMultipleSelection, changeDefaultValues) => {
        setPracticeArea(stateValue);

        if (changeDefaultValues) {
            setDefaultValues(prevState => ({
                ...prevState,
                [state]: multipleSelection ? defaultValuesWithMultipleSelection : defaultValues
            }));
        }
    }

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

    const filters = <Grid
        container
        className="d-flex justify-content-space-between no-padding-h"
    >
        <Grid
            item
            xl={6}
            md={12}
            className="dashboard-widget-filter-container no-padding-h"
        >
            <APAutocompleteList
                label={t("practice_area")}
                options={[
                    {
                        "title": "All",
                        "value": null
                    },
                    ...practiceAreasList
                ]}
                optionsLabel="title"
                stateKey="PracticeArea"
                value={defaultValues.practiceArea}
                valueKey="value"
                onChange={handlePracticeAreaChange}
            />
        </Grid>
        <Grid
            item
            xl={3}
            md={6}
            className="dashboard-widget-filter-container no-padding-h"
        >
            <APNativeSelectList
                options={[
                    {
                        "label": "All",
                        "value": null
                    },
                    ...years.map(item => {
                        return {
                            "label": item,
                            "value": item
                        };
                    })
                ]}
                label={t("year")}
                optionsLabel="Year"
                stateKey="dueDateByYear"
                value={dueDateByYear}
                valueKey="value"
                onChange={setDueDateByYear}
            />
        </Grid>
        <Grid
            item
            xl={3}
            md={6}
            className="dashboard-widget-filter-container no-padding-h"
        >
            <APNativeSelectList
                options={[
                    {
                        "label": "All",
                        "value": null
                    },
                    ...months.map(item => {
                        return {
                            "label": item,
                            "value": item
                        };
                    })
                ]}
                label={t("month")}
                optionsLabel="Month"
                stateKey="dueDataByMonth"
                value={dueDateByMonth}
                valueKey="value"
                onChange={setDueDateByMonth}
            />
        </Grid>
    </Grid>;

    useEffect(() => {

        loadPracticeAreasList();
    }, []);

    useEffect(() => {

        loadData();
    }, [dueDateByYear, dueDateByMonth, practiceArea, t]);

    const loadPracticeAreasList = async () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        await MiscList.getList({
            lists: [
                "caseTypes"
            ]
        }).then((response) => {

            let options = [];
            let data = response?.data?.data?.caseTypes;

            for (var i = 0; i < data.length; i++) {
                let item = data[i];

                options.push({
                    title: item.name,
                    value: item.id
                });
            }

            setPracticeAreasList(options);


        }).catch((error) => {
        }).finally(() => {

            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });
        });
    }

    const loadData = () => {
        setWidgetContent(circularProgress);

        AdvisorTask.getList({
            "PracticeArea": {
                "value": practiceArea
            },
            "DueDateByYear": {
                "value": dueDateByYear
            },
            "DueDateByMonth": {
                "value": dueDateByMonth
            },
            "Assignee": {
                "value": props?.user?.id
            }
        }).then((response) => {
            if (response?.data?.data) {
                let series = [];
                let data = response?.data?.data;
                let dataByStatus = data.map(item => item?.advisor_task_status?.name);
                let labels = dataByStatus.filter((value, index, self) => self.indexOf(value) === index);
                let counts = {};

                for (var i = 0; i < dataByStatus.length; i++) {
                    let item = dataByStatus[i];

                    counts[item] = counts[item] ? counts[item] + 1 : 1;
                }

                for (var i = 0; i < labels.length; i++) {
                    let item = labels[i];

                    series.push(counts[item]);
                }

                setWidgetContent(<ReactApexChart
                    options={
                        {
                            chart: {
                                type: 'donut'
                            },
                            legend: {
                                position: 'bottom'
                            },
                            labels: labels,
                            plotOptions: {
                                pie: {
                                    donut: {
                                        labels: {
                                            show: true,
                                            total: {
                                                show: true,
                                                label: t('total'),
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
                                    fontSize: '14px'
                                }
                            }
                        }
                    }
                    series={series}
                    type='donut'
                    width='300'
                    height='300'
                />);
            }
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
                    {filters}
                </Container>
                {widgetContent}
            </Container>
        </Container>
        // </MuiThemeProvider>
    );
});

export default AdvisorAssignedTasksWidget;
