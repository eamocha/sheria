import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './ActiveTimersForm.scss';

import DateFnsUtils from '@date-io/date-fns';

import {
    Button,
    Container,
    Grid
} from '@material-ui/core';

import { MuiPickersUtilsProvider } from '@material-ui/pickers';

import { useTranslation } from 'react-i18next';

import AdvisorTimer from '../../../../api/AdvisorTimer';

import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES
} from '../../../../Constants';

import {
    Context,
    initialGlobalState
} from '../../../../Store';

import APTimerView from '../../../common/ap-timers/ap-timer-view/APTimerView.lazy';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    const { t } = useTranslation();
    const formId = FORMS_NAMES.activeTimersform;

    const [timers, setTimers] = useState([]);

    const closeCallback = () => {

        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal
            }
        });

        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.advisorTimers),
                open: true,
                showSaveButton: false,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.activeTimersform,
                }
            }
        });
    }

    const addTimer = () => {

        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal
            }
        });

        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.addTimer),
                open: true,
                form: {
                    ...initialGlobalState?.modal?.form,
                    id: FORMS_NAMES.timerAddform,
                    closeCallback: closeCallback,
                    //submitCallback: loadData,
                }
            }
        })
    }

    const loadData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        setTimers([]);

        AdvisorTimer.getList().then((response) => {
            let data = response?.data?.data;

            setTimers(data);

        }).catch((error) => {

        }).finally(() => {

            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });
        });
    }

    useEffect(() => {

        loadData();
    }, []);

    let timersContent = timers?.map((item, key) => {
        return (
            <APTimerView
                key={'timer-row-' + key}
                data={item}
                loadData={loadData}
                closeCallback={closeCallback}
            />
        )
    });

    return (
        <MuiPickersUtilsProvider
            utils={DateFnsUtils}
        >
            <Container
                maxWidth={false}
                className="no-padding-h"
                id="ActiveTimersForm"
            >
                <Grid
                    container
                    className="no-padding-h"
                >
                    <Grid
                        item
                        xs={8}
                    >

                    </Grid>
                    <Grid
                        item
                        container
                        xs={4}
                        alignItems="center"
                        justify="flex-end"
                    >
                        <Button
                            color="primary"
                            onClick={addTimer}
                            variant="contained"
                            className={"add-btn"}
                        >
                            {t('add_timer')}
                        </Button>
                    </Grid>
                </Grid>

                <Container
                    maxWidth={false}
                    className="timer-container no-padding-h">
                    {timersContent}
                </Container>

            </Container>
        </MuiPickersUtilsProvider>
    );
});
