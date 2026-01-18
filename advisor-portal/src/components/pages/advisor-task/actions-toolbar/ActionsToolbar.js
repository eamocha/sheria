import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './ActionsToolbar.scss';

import {
    Button,
    Container,
    LinearProgress,
    Menu,
    MenuItem,
} from '@material-ui/core';

import EditIcon from '@material-ui/icons/Edit';

import ArrowDropDownIcon from '@material-ui/icons/ArrowDropDown';

import AdvisorTask from '../../../../api/AdvisorTask';

import AdvisorTaskStatus from '../../../../api/AdvisorTaskStatus';

import {
    Context,
    initialGlobalState
} from '../../../../Store';

import {
    isFunction,
    concatArrays
} from '../../../../APHelpers';

import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const advisorTask = props?.advisorTask;

    const [advisorTaskStatuses, setAdvisorTaskStatuses] = useState([]);

    const [advisorTaskTransitions, setAdvisorTaskTransitions] = useState([]);

    const [menuAnchorEl, setMenuAnchorEl] = useState('');

    const [dataLoaded, setDataLoaded] = useState(false);

    const [openMenu, setOpenMenu] = useState(false);

    const { t } = useTranslation();

    useEffect(() => {

        loadData();
    }, [props?.advisorTask]);

    const loadData = () => {
        // re-initiate the dataLoaded state (for reload)
        setDataLoaded(false);

        AdvisorTaskStatus.getList({
            "advisorTaskId": {
                "value": advisorTask?.id
            },
            "workflow": {
                "value": advisorTask?.workflow ?? 1
            }
        }).then((response) => {
            setAdvisorTaskStatuses(response?.data?.data ?? []);

            setAdvisorTaskTransitions(response?.data?.data?.[0]?.transitions ?? [])
        }).catch((error) => {

            console.log('loading advisor task workflow statuses and transitions', error);
        }).finally(() => {

            setDataLoaded(true);
        });
    };

    const changeAdvisorTaskStatus = (e, data) => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        handleCloseMenu();

        AdvisorTask.updateStatus(advisorTask?.id, data).then((response) => {
            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    severity: "success",
                    text: "Task Status has been updated successfully!"
                }
            });

            if (isFunction(props?.loadData)) {
                return props.loadData();
            }
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
                    severity: "error",
                    text: message
                },
                globalLoader: initialGlobalState?.globalLoader
            });
        }).finally(() => {
            /**
             * so if the reload function is not set we have to hide the global loader,
             * otherwise, the reload function will hide the global loader itself
             */
            if (!isFunction(props?.loadData)) {
                globalStateDispatcher({
                    globalLoader: initialGlobalState?.globalLoader
                });
            }
        });
    }

    const handleOpenMenu = (e) => {
        setMenuAnchorEl(e?.currentTarget);
        setOpenMenu(true);
    }

    const handleCloseMenu = () => {
        setMenuAnchorEl('');
        setOpenMenu(false);
    }

    let statusesAndTransitionsArray = concatArrays(advisorTaskStatuses, advisorTaskTransitions);

    let statusesAndTransitionsBtns = statusesAndTransitionsArray.slice(0, 3).map((item, key) => {
        return (
            <Button
                className={"status-btn " + globalState.domDirection}
                variant="contained"
                color="primary"
                key={key}
                onClick={(e) => changeAdvisorTaskStatus(e, { advisor_task_status_id: item.id })}
            >
                {item?.name}
            </Button>
        );
    });

    let statusesAndTransitionsList = statusesAndTransitionsArray.slice(3, statusesAndTransitionsArray.length).map((item, key) => {
        return (
            <MenuItem
                key={key}
                onClick={(e) => changeAdvisorTaskStatus(e, { advisor_task_status_id: item.id })}
            >
                {item?.name}
            </MenuItem>
        );
    });

    if (!dataLoaded) {
        return (
            <LinearProgress
                size={50}
            />
        );
    }

    return (
        <Container
            maxWidth={false}
            className="advisor-task-page-actions-toolbar btns-container no-padding-h"
        >
            <Button
                className={"edit-btn " + globalState.domDirection}
                variant="contained"
                color="default"
                startIcon={<EditIcon />}
                onClick={() => props.openEditForm()}
            >
                {t("edit")}
            </Button>
            {statusesAndTransitionsBtns}
            {advisorTaskStatuses.length > 2
                ?
                <React.Fragment>
                    <Button
                        aria-controls="statuses-menu"
                        aria-haspopup="true"
                        onClick={(e) => handleOpenMenu(e)}
                        variant="outlined"
                        color="primary"
                    >
                        {t("more")} <ArrowDropDownIcon />
                    </Button>
                    <Menu
                        id="statuses-menu"
                        anchorEl={menuAnchorEl}
                        keepMounted
                        open={Boolean(openMenu)}
                        onClose={handleCloseMenu}
                        getContentAnchorEl={null}
                        anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
                        transformOrigin={{ vertical: "top", horizontal: "center" }}
                    >
                        {statusesAndTransitionsList}
                    </Menu>
                </React.Fragment>
                : null
            }

        </Container>
    );
});
