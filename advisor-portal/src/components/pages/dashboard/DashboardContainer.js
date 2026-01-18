import React, { useContext, useEffect, useState } from 'react';

import {
    Container,
    Grid,
    Typography
} from '@material-ui/core';

import DashboardWidget from './DashboardListWidget';

import './DashboardContainer.scss';

import { format } from 'date-fns';

import AdvisorAssignedTasksWidget from './AdvisorAssignedTasksWidget';

import AdvisorRequestedTasksWidget from './AdvisorRequestedTasksWidget';

import DashboardHearingsListWidget from './DashboardHearingsListWidget';

import AdvisorTimeLogsTodayWidget from './AdvisorTimeLogsTodayWidget';

import AdvisorTimeLogsWidget from './AdvisorTimeLogsWidget';

import { Context, initialGlobalState } from '../../../Store';

import { useTranslation } from 'react-i18next';

import AdvisorUserPreferences, { widgetsArray } from '../../../api/AdvisorUserPreferences';

import { PREFERENCES_KEYS } from '../../../Constants';

const DashboardContainer = React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    const { t } = useTranslation();

    useEffect(() => {

        loadData();
    }, []);

    const [widgetsState, setWidgetsState] = useState([]);
    const [recordState, setRecordState] = useState(null);

    const loadData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        AdvisorUserPreferences.get({ keyName: { value: PREFERENCES_KEYS.dashboardWidgets } }).then((response) => {
            setWidgetsState([]); 
            let data = response?.data?.data;
            var widgetsConfigRecord = data != null ? data :
                {
                    id: null,
                    advisor_user_id: AdvisorUserPreferences.getCurrentAdvisor().id,
                    keyName: PREFERENCES_KEYS.dashboardWidgets,
                    keyValue: null,

                };
            setRecordState(widgetsConfigRecord);
            if (widgetsConfigRecord && widgetsConfigRecord.keyValue) {
                var widgetsConfig = JSON.parse(widgetsConfigRecord.keyValue);
                var filterdWidgets = widgetsConfig.filter(x => x.isVisible);
                if (filterdWidgets) {
                    setWidgetsState(filterdWidgets);
                }
            } else {
                setWidgetsState(widgetsArray);
            }

        }).catch((error) => {

        }).finally(() => {

            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });
        });
    }

    const hideWidget = (widgetKey) => {
        widgetsArray.forEach(x => {
            if (x.key == widgetKey) {
                x.isVisible = false;
            }
        })

        savePreferences();
    }

    const savePreferences = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        recordState.keyValue = JSON.stringify(widgetsArray);
        AdvisorUserPreferences.update(recordState.id, recordState).then((response) => {

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: "Preferences updated successfully",
                    severity: "success"
                }
            });

            loadData();
        }).catch((error) => {
            let message = error?.response?.data?.message;

            if (error?.response?.data?.message === 'object') {
                message = [];

                Object.keys(error.response.data.message).map((key, index) => {
                    return error.response.data.message?.[key].forEach((item) => {
                        message.push(<p key={key}>- {error.response.data.message[key]}: {item}</p>);
                    });
                });
            }

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: message,
                    severity: "error"
                }
            });
        }).finally(() => {
            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });
        });
    }

    var widgetTemplates = {
        DashboardWidgetToday:
        {
            template: <DashboardWidget
                widgetTitle={t('my_Tasks_for_today')}
                name="DashboardWidgetToday"
                hideWidget={hideWidget}
                model={'AdvisorTask'}
                query={{
                    "DueDate": {
                        "value": format(
                            new Date(),
                            'yyyy-MM-dd'
                        )
                    },
                    "Assignee": {
                        "value": globalState?.user?.data?.id
                    }
                }}
                widgetId="today-tasks"
            />,
            class: "",
            size: 4
        },
        DashboardWidgetUpcoming: {
            template: <DashboardWidget
                widgetTitle={t('my_upcoming_tasks')}
                name="DashboardWidgetUpcoming"
                hideWidget={hideWidget}
                model={'AdvisorTask'}
                query={{
                    "DueDate": {
                        "value": format(
                            new Date(),
                            'yyyy-MM-dd'
                        ),
                        "operator": "greaterThan"
                    },
                    "Assignee": {
                        "value": globalState?.user?.data?.id
                    }
                }}
                widgetId="up-coming-tasks"
            />,
            class: "",
            size: 4
        },
        AdvisorAssignedTasksWidget: {
            template: <AdvisorAssignedTasksWidget
                name="AdvisorAssignedTasksWidget"
                hideWidget={hideWidget}
                user={globalState?.user?.data}
            />,
            class: "",
            size: 4
        },
        DashboardHearingsListWidgetToday: {
            template: <DashboardHearingsListWidget
                widgetTitle={t('my_hearings_today')}
                model={'Hearing'}
                name="DashboardHearingsListWidgetToday"
                hideWidget={hideWidget}
                query={{
                    "StartDate": {
                        "value": format(
                            new Date(),
                            'yyyy-MM-dd'
                        )
                    },
                    "Assignees": {
                        "value": [
                            globalState?.user?.data?.id
                        ]
                    }
                }}
                widgetId="today-hearings"
            />,
            class: "dashboard-hearings-list-widget-container-1 " + globalState.domDirection,
            size: 6
        },
        DashboardHearingsListWidgetUpcoming: {
            template: <DashboardHearingsListWidget
                widgetTitle={t('my_upcoming_hearings')}
                model={'Hearing'}
                name="DashboardHearingsListWidgetUpcoming"
                hideWidget={hideWidget}
                query={{
                    "StartDate": {
                        "value": format(
                            new Date(),
                            'yyyy-MM-dd'
                        ),
                        "operator": "greaterThan"
                    },
                    "Assignees": {
                        "value": [
                            globalState?.user?.data?.id
                        ]
                    }
                }}
                widgetId="up-coming-hearings"
            />,
            class: "dashboard-hearings-list-widget-container-2 " + globalState.domDirection,
            size: 6
        },
        AdvisorRequestedTasksWidget: {
            template: <AdvisorRequestedTasksWidget
                name="AdvisorRequestedTasksWidget"
                hideWidget={hideWidget}
                setGlobalLoader={props?.setGlobalLoader}
                user={props?.user}
            />,
            class: "dashboard-advisor-requested-tasks-widget-container",
            size: 4
        },
        AdvisorTimeLogsWidget: {
            template: <AdvisorTimeLogsWidget
                name="AdvisorTimeLogsWidget"
                hideWidget={hideWidget}
                setGlobalLoader={props?.setGlobalLoader}
                user={props?.user}
            />,
            class: "dashboard-time-logs-widget-container",
            size: 4
        },
        AdvisorTimeLogsTodayWidget: {
            template: <AdvisorTimeLogsTodayWidget
                name="AdvisorTimeLogsTodayWidget"
                hideWidget={hideWidget}
                setGlobalLoader={props?.setGlobalLoader}
                user={props?.user}
            />,
            class: "dashboard-time-logs-today-widget-container",
            size: 4
        }

    }

    const widgets = [];
    const [widgetsTemplatesState, setWidgetsTemplatesState] = useState([])

    useEffect(() => {
        for (var i = 0; i < widgetsState.length; i++) {
            let widget = widgetsState[i];

            widgets.push(
                <Grid
                    item
                    lg={6}
                    sm={6}
                    xs={12}
                    className={"dashboard-widget-container " + widgetTemplates[widget.key].class}
                >
                    {widgetTemplates[widget.key].template}
                </Grid>
            )
        }

        setWidgetsTemplatesState(widgets);
    }, [widgetsState, t]);

    return (
        <Container
            maxWidth={false}
            className="dashboard-container no-padding-h no-margin-h"
        >
            <Grid
                container
                className="dashboard-widgets-row no-padding-h"
            >
                {
                    widgetsTemplatesState.length > 0 ?
                        widgetsTemplatesState :
                        <Typography
                            variant="body1"
                        >
                            {t("no_visbible_widgets")}
                        </Typography>
                }
            </Grid>
        </Container >
    );
});

export default DashboardContainer;
