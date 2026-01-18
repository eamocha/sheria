import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './APTimerView.scss';

import {
    Container,
    Button,
    makeStyles,
    Grid,
} from '@material-ui/core';

import PauseIcon from '@material-ui/icons/Pause';

import PlayArrowIcon from '@material-ui/icons/PlayArrow';

import {
    buildErrorMessages,
    buildInstanceURL,
    formatMSTime,
    isFunction
} from '../../../../APHelpers';

import {
    Context,
    initialGlobalState
} from '../../../../Store';

import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES
} from '../../../../Constants';

import { useTranslation } from 'react-i18next';

import AdvisorTimer from '../../../../api/AdvisorTimer';

import { Link } from 'react-router-dom';
import { millisecondsToMinutes } from 'date-fns';

const useStyles = makeStyles({
    container: {
        paddingLeft: 0,
        paddingRight: 0,
        marginBottom: 30,
        borderBottom: '1px solid #e6e6e6',
        '&:last-child': {
            borderBottom: 'none'
        }
    },
    collapse: {
        paddingLeft: 20
    },
    btn: {
        color: '#000',
        fontWeight: '100',
        paddingLeft: 2
    }
});

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    const [timer] = useState(props?.data);
    const [t] = useTranslation();

    const deleteTimer = () => {
        if (window.confirm("Are you sure you want to delete this timer?")) {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: true
                }
            });

            AdvisorTimer.delete(timer.id).then((response) => {
                if (isFunction(props?.loadData)) {
                    props.loadData();
                }

                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        severity: "success",
                        text: "Timer has been deleted successfully!"
                    }
                });
            }).catch((error) => {
                let message = buildErrorMessages(error?.response?.data?.message);

                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        text: message,
                        severity: "error"
                    },
                    globalLoader: initialGlobalState?.globalLoader
                });
            }).finally(() => {
                if (!isFunction(props?.loadData)) {
                    globalStateDispatcher({
                        globalLoader: globalState?.globalLoader
                    });
                }
            });
        }
    }

    const editTimer = () => {
        if (timer.status == 'paused') {

            timer.status = 'active';
            timer.startDate = Date.now();

            AdvisorTimer.update(timer?.id, timer).then((response) => {
                if (isFunction(props?.loadData)) {
                    props.loadData();
                }

                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        text: "Timer has been updated successfully",
                        severity: "success"
                    }

                });
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
                    globalLoader: {
                        ...globalState?.globalLoader,
                        open: false
                    }
                });
            });
        } else {
            globalStateDispatcher({
                modal: {
                    ...initialGlobalState?.modal,
                    title: timer.status == 'paused' ? t(FORMS_MODAL_TITLES.resumeTime) : t(FORMS_MODAL_TITLES.pauseTime),
                    open: true,
                    form: {
                        ...globalState?.modal?.form,
                        id: FORMS_NAMES.timerEditform,
                        closeCallback: props?.closeCallback,
                        data: {
                            timer: timer,
                            status: timer.status == 'paused' ? 'active' : 'paused'
                        }
                    }
                }
            });
        }
    }

    const endTimer = () => {
        if (millisecondsToMinutes(timerTime) < 1) {

            if (window.confirm(t("timer_less_than_one_minute"))) {
                globalStateDispatcher({
                    modal: {
                        ...initialGlobalState?.modal,
                        title: t(FORMS_MODAL_TITLES.endTimer),
                        open: true,
                        form: {
                            ...globalState?.modal?.form,
                            id: FORMS_NAMES.timerEditform,
                            closeCallback: props?.closeCallback,
                            data: {
                                timer: timer,
                                status: timer.status = 'end'
                            }
                        }
                    }
                });
            }

        } else {
            globalStateDispatcher({
                modal: {
                    ...initialGlobalState?.modal,
                    title: t(FORMS_MODAL_TITLES.endTimer),
                    open: true,
                    form: {
                        ...globalState?.modal?.form,
                        id: FORMS_NAMES.timerEditform,
                        closeCallback: props?.closeCallback,
                        data: {
                            timer: timer,
                            status: timer.status = 'end'
                        }
                    }
                }
            });
        }
    }

    const classes = useStyles();

    const [time, setTime] = useState(Date.now());
    const [timerTime, setTimerTime] = useState(0);

    useEffect(() => {
        const interval = setInterval(() => setTime(Date.now()), 1000);

        return () => {
            clearInterval(interval);
        };
    }, []);

    useEffect(() => {
        let time = 0;

        timer.time_logs.forEach((timeLog) => {
            if (timeLog.endDate) {
                time += timeLog.endDate - timeLog.startDate
            } else {
                time += Date.now() - timeLog.startDate
            }
        })

        setTimerTime(time);
    }, [time]);

    const hidePopup = () => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal
            }
        });
    }

    return (
        <Container
            maxWidth={false}
            className={classes.container + ' APTimerView'}
        >
            <Container
                maxWidth={false}
                className="no-padding-h"
            >
                <Grid
                    container
                    className="no-padding-h"
                >
                    <Grid
                        item
                        xs={8}
                    >
                        {
                            timer.advisor_task ?
                                <React.Fragment>
                                    <Grid
                                        item
                                        xs={12}
                                        className="task-section"
                                    >
                                        <Link
                                            to={`${buildInstanceURL()}/task/${timer.advisor_task.id}`}
                                            className="primary-link"
                                            onClick={hidePopup}
                                        >
                                            {'T' + timer.advisor_task.id}
                                        </Link>
                                    </Grid>
                                    {
                                        timer.advisor_task?.description ?
                                            <Grid
                                                item
                                                xs={12}
                                            >
                                                <Link
                                                    to={`${buildInstanceURL()}/task/${timer.advisor_task.id}`}
                                                    className="primary-link"
                                                    onClick={hidePopup}
                                                >
                                                    {timer.advisor_task?.description}
                                                </Link>
                                            </Grid>
                                            :
                                            t("no_description")
                                    }
                                </React.Fragment>
                                :
                                <Container
                                    maxWidth={false}
                                    className="no-padding-h task-section">
                                    {t("no_task")}
                                </Container>

                        }
                        {
                            timer.legal_case ?
                                <Grid
                                    item
                                    xs={12}
                                    className="legal-case-section"
                                >
                                    <Link
                                        to={`${buildInstanceURL()}${timer.legal_case.category == 'Matter' ? '/corporate-matter/' : '/litigation-case/'}${timer.legal_case.id}`}
                                        className="primary-link"
                                        onClick={hidePopup}
                                    >
                                        {timer.legal_case.subject}
                                    </Link>
                                </Grid>
                                :
                                <Container
                                    maxWidth={false}
                                    className="no-padding-h task-section">
                                    {t("no_matter")}
                                </Container>
                        }
                        <Grid
                            item
                            xs={12}
                            className="timer-comment-section"
                        >
                            {timer.comments ?? t("no_description")}
                        </Grid>
                    </Grid>
                    <Grid
                        item
                        container
                        xs={4}
                        alignItems="center"
                        justify="flex-end"
                    >
                        <Grid
                            item
                            xs={12}
                        >
                            <Button
                                className={"btn " + globalState.domDirection}
                                variant="outlined"
                                color="default"
                                startIcon={timer.status == 'paused' ? <PlayArrowIcon /> : <PauseIcon />}
                                onClick={() => editTimer()}
                            >
                                {formatMSTime(timerTime)}
                            </Button>
                        </Grid>
                        <Grid
                            item
                            xs={12}
                        >
                            <Button
                                className={"btn " + globalState.domDirection}
                                variant="outlined"
                                color="default"

                                onClick={() => deleteTimer()}
                            >
                                {t("cancel_timer")}
                            </Button>
                        </Grid>
                        <Grid
                            item
                            xs={12}
                        >
                            <Button
                                className={"btn " + globalState.domDirection}
                                variant="outlined"
                                color="default"
                                onClick={() => endTimer()}
                            >
                                {t("end_timer")}
                            </Button>
                        </Grid>
                    </Grid>
                </Grid>
            </Container>
        </Container>
    );
});
