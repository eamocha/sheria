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
    MenuItem
} from '@material-ui/core';

import EditIcon from '@material-ui/icons/Edit';

import ArrowDropDownIcon from '@material-ui/icons/ArrowDropDown';

import {
    Context,
    initialGlobalState
} from '../../../../../../Store';

import {
    buildErrorMessages,
    isFunction
} from '../../../../../../APHelpers';

import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES
} from '../../../../../../Constants';

import WorkflowStatus from '../../../../../../api/WorkflowStatus';

import WorkflowStatusTransition from '../../../../../../api/WorkflowStatusTransition';

import LegalCase from '../../../../../../api/LegalCase';

import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const [litigationCase, ] = useState(props?.litigationCase);

    const [litigationCaseWorkflowStatuses, setLitigationCaseWorkflowStatuses] = useState([]);

    const [litigationCaseWorkflowTransitions, setLitigationCaseWorkflowTransitions] = useState([]);

    const [menuAnchorEl, setMenuAnchorEl] = useState('');

    const [dataLoaded, setDataLoaded] = useState(false);

    const [openMenu, setOpenMenu] = useState(false);

    const { t } = useTranslation();

    useEffect(() => {

        loadData();
    }, [props?.litigationCase]);

    const loadData = () => {
        // re-initiate the dataLoaded state (for reload)
        setDataLoaded(false);

        WorkflowStatus.getList({
            legalCaseId: {
                value: litigationCase?.id
            },
        }).then((response) => {

            setLitigationCaseWorkflowStatuses(response?.data?.data);

            return WorkflowStatusTransition.getList({
                legalCaseId: {
                    value: litigationCase?.id
                },
            });
        }).then((response) => {

            setLitigationCaseWorkflowTransitions(response?.data?.data);
        }).catch((error) => {

            console.log('loading litigation case workflow statuses and transitions', error);
        }).finally(() => {

            setDataLoaded(true);
        });
    };

    const changeLitigationCaseWorkflowStatus = (e, data) => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        handleCloseMenu();

        LegalCase.updateStatus(litigationCase?.id, data).then((response) => {
            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    severity: "success",
                    text: "Litigation Case Status has been updated successfully!"
                }
            });

            if (isFunction(props?.loadLitigationCaseData)) {
                return props.loadLitigationCaseData();
            }
        }).catch((error) => {
            let message = buildErrorMessages(error?.response?.data?.message);

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
            if (!isFunction(props?.loadLitigationCaseData)) {
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

    const openLitigationCaseEditForm = () => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.litigationCaseEditForm) + ": M" + litigationCase?.id,
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.litigationCaseEditForm,
                    submitCallback: isFunction(props?.loadLitigationCaseData) ? props.loadLitigationCaseData : null,
                    data: {
                        litigationCase: litigationCase
                    }
                }
            }
        });
    }

    const buildWorkflowStatusUpdateObj = (workflowStatus) => {
        if (workflowStatus?.fromStep) {
            return {
                workflowStatusTransition: {
                    value: workflowStatus?.id
                }
            };
        }

        return {
            workflowStatus: {
                value: workflowStatus?.id
            }
        };
    }

    const openAddNoteForm = () => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.legalCaseNoteAddForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.legalCaseNoteAddForm,
                    submitCallback: isFunction(props?.loadLitigationCaseData) ? props.loadLitigationCaseData : null,
                    data: {
                        legalCase: litigationCase
                    }
                }
            }
        });
    }

    let litigationCaseWorkflowStatusesAndTransitions = litigationCaseWorkflowStatuses ? litigationCaseWorkflowStatuses.concat(litigationCaseWorkflowTransitions) : [];

    // these are the shown buttons
    let litigationCaseWorkflowStatusesAndTransitionsBtns = litigationCaseWorkflowStatusesAndTransitions.slice(0, 3).map((item, key) => {
        return (
            <Button
                className={"status-btn " + globalState.domDirection}
                variant="outlined"
                color="primary"
                key={"litigation-case-status-" + key}
                onClick={(e) => changeLitigationCaseWorkflowStatus(e, buildWorkflowStatusUpdateObj(item))}
            >
                {item?.name}
            </Button>
        );
    });

    // these are the items of the "more" menu
    let litigationCaseWorkflowStatusesAndTransitionsMenuItems = litigationCaseWorkflowStatusesAndTransitions.slice(3).map((item, key) => {

        return (
            <MenuItem
                key={"litigation-case-statuses-menu-item-" + key}
                onClick={(e) => changeLitigationCaseWorkflowStatus(e, buildWorkflowStatusUpdateObj(item))}
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
            className="litigation-case-page-general-info-panel-actions-toolbar btns-container no-padding-h"
        >

            <Button
                className={"edit-btn " + globalState.domDirection}
                variant="contained"
                color="default"
                onClick={() => openLitigationCaseEditForm()}
                startIcon={<EditIcon />}
            >
                {t("edit")}
            </Button>
            {
                litigationCaseWorkflowStatusesAndTransitions.length > 0 ?
                <React.Fragment>
                    {litigationCaseWorkflowStatusesAndTransitionsBtns}
                    {
                        litigationCaseWorkflowStatusesAndTransitionsMenuItems.length > 0 ?
                            <Button
                                aria-controls="statuses-menu"
                                aria-haspopup="true"
                                onClick={(e) => handleOpenMenu(e)}
                                variant="outlined"
                                color="primary"
                            >
                                More <ArrowDropDownIcon />
                            </Button>
                            : null
                    }
                    <Menu
                        id="statuses-menu"
                        anchorEl={menuAnchorEl}
                        keepMounted
                        open={Boolean(openMenu)}
                        onClose={handleCloseMenu}
                        getContentAnchorEl={null}
                        anchorOrigin={{
                            vertical: "bottom",
                            horizontal: "center"
                        }}
                        transformOrigin={{
                            vertical: "top",
                            horizontal: "center"
                        }}
                    >
                        {litigationCaseWorkflowStatusesAndTransitionsMenuItems}
                    </Menu>
                </React.Fragment>
                :
                null
            }
            <Button
                className="add-note-btn"
                color="primary"
                variant="outlined"
                onClick={() => openAddNoteForm()}
            >
                {t("add_note")}
            </Button>
        </Container>
    );
});
