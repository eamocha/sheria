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

    const [corporateMatter, ] = useState(props?.corporateMatter);

    const [corporateMatterWorkflowStatuses, setCorporateMatterWorkflowStatuses] = useState([]);

    const [corporateMatterWorkflowTransitions, setCorporateMatterWorkflowTransitions] = useState([]);

    const [menuAnchorEl, setMenuAnchorEl] = useState('');

    const [dataLoaded, setDataLoaded] = useState(false);

    const [openMenu, setOpenMenu] = useState(false);

    const { t } = useTranslation();

    useEffect(() => {

        loadData();
    }, [props?.corporateMatter]);

    const loadData = () => {
        // re-initiate the dataLoaded state (for reload)
        setDataLoaded(false);

        WorkflowStatus.getList({
            "legalCaseId": {
                "value": corporateMatter?.id
            }
        }).then((response) => {

            setCorporateMatterWorkflowStatuses(response?.data?.data);

            // return WorkflowStatusTransition.getList({
            //     legalCaseId: {
            //         value: corporateMatter?.id
            //     },
            // });
        }).then((response) => {

            // setCorporateMatterWorkflowTransitions(response?.data?.data);
        }).catch((error) => {

            console.log('loading corporate matter workflow statuses and transitions', error);
        }).finally(() => {

            setDataLoaded(true);
        });
    };

    const changeCorporateMatterWorkflowStatus = (e, data) => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        handleCloseMenu();

        LegalCase.updateStatus(corporateMatter?.id, data).then((response) => {
            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    severity: "success",
                    text: "Corporate Matter Workflow Status has been updated successfully!"
                }
            });

            if (isFunction(props?.loadCorporateMatterData)) {
                return props.loadCorporateMatterData();
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
            if (!isFunction(props?.loadCorporateMatterData)) {
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

    const openCorporateMatterEditForm = () => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.corporateMatterEditForm) + ": M" + corporateMatter?.id,
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.corporateMatterEditForm,
                    submitCallback: isFunction(props?.loadCorporateMatterData) ? props.loadCorporateMatterData : null,
                    data: {
                        corporateMatter: corporateMatter
                    }
                }
            }
        });
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
                    submitCallback: isFunction(props?.loadCorporateMatterData) ? props.loadCorporateMatterData : null,
                    data: {
                        legalCase: corporateMatter
                    }
                }
            }
        });
    }

    let corporateMatterWorkflowStatusesAndTransitions = corporateMatterWorkflowStatuses ? corporateMatterWorkflowStatuses.concat(corporateMatterWorkflowTransitions) : [];

    // these are the shown buttons
    let corporateMatterWorkflowStatusesAndTransitionsBtns = corporateMatterWorkflowStatusesAndTransitions.slice(0, 3).map((item, key) => {
        return (
            <Button
                className={"status-btn " + globalState.domDirection}
                variant="outlined"
                color="primary"
                key={"corporate-matter-status-" + key}
                onClick={(e) => changeCorporateMatterWorkflowStatus(e, { workflowStatus: { value: item.id } })}
            >
                {item?.name}
            </Button>
        );
    });

    // these are the items of the "more" menu
    let corporateMatterWorkflowStatusesAndTransitionsMenuItems = corporateMatterWorkflowStatusesAndTransitions.slice(3).map((item, key) => {

        return (
            <MenuItem
                key={"corporate-matter-statuses-menu-item-" + key}
                onClick={(e) => changeCorporateMatterWorkflowStatus(e, { workflowStatus: { value: item.id } })}
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
            className="corporate-matter-page-general-info-panel-actions-toolbar btns-container no-padding-h"
        >
            <Button
                className={"edit-btn " + globalState.domDirection}
                variant="contained"
                color="default"
                onClick={() => openCorporateMatterEditForm()}
                startIcon={<EditIcon />}
            >
                {t("edit")}
            </Button>
            {
                corporateMatterWorkflowStatusesAndTransitions.length > 0 ?
                <React.Fragment>
                    {corporateMatterWorkflowStatusesAndTransitionsBtns}
                    {
                        corporateMatterWorkflowStatusesAndTransitionsMenuItems.length > 0 ?
                            <Button
                                aria-controls="statuses-menu"
                                aria-haspopup="true"
                                onClick={(e) => handleOpenMenu(e)}
                                variant="outlined"
                                color="primary"
                            >
                                More <ArrowDropDownIcon />
                            </Button>

                            : null}
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
                        {corporateMatterWorkflowStatusesAndTransitionsMenuItems}
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
