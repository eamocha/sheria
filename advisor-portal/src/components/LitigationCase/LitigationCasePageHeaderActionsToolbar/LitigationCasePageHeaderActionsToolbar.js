import React, {
    useContext,
    useEffect,
    useState
} from 'react';
import './LitigationCasePageHeaderActionsToolbar.scss';
import {
    Button,
    Container,
    Menu,
    MenuItem
} from '@material-ui/core';
import EditIcon from '@material-ui/icons/Edit';
import ArrowDropDownIcon from '@material-ui/icons/ArrowDropDown';
import { Context, initialGlobalState } from '../../../Store';
import LegalCase from '../../../api/LegalCase';
import { isFunction } from '../../../APHelpers';
import { FORMS_MODAL_TITLES, FORMS_NAMES } from '../../../Constants';
 
export default React.memo((props) => {

    const [globalState, globalStateDispatcher] = useContext(Context);
    
    const [legalCase, ] = useState(props?.legalCase);
    const [legalCaseWorkflowStatuses, ] = useState(props?.legalCaseWorkflowStatuses);
    const [legalCaseWorkflowTransitions, ] = useState(props?.legalCaseWorkflowTransitions);
   
    const [menuAnchorEl, setMenuAnchorEl] = useState('');
    const [openMenu, setOpenMenu] = useState(false);

    useEffect(() => {

    }, [props?.legalCase, props?.legalCaseWorkflowStatuses, props?.legalCaseWorkflowTransitions]);

    const changeLitigationCaseWorkflowStatus = (e, data) => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        handleCloseMenu();

        LegalCase.updateStatus(legalCase?.id, data).then((response) => {
            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    severity: "success",
                    text: "Litigation Case Status has been updated successfully!"
                }
            });

            if (isFunction(props?.loadLegalCaseData)) {
                return props.loadLegalCaseData();
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
            if (!isFunction(props?.loadLegalCaseData)) {
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

    const openEditLitigationCaseForm = () => {

        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.litigationCaseEditForm) + ": M" + legalCase?.id,
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.litigationCaseEditForm,
                    submitCallback: isFunction(props?.loadLegalCaseData) ? props.loadLegalCaseData : null,
                    data: {
                        litigationCase: legalCase
                    }
                }
            }
        });
    }

    let legalCaseWorkflowStatusesAndTransitions = legalCaseWorkflowStatuses.concat(legalCaseWorkflowTransitions);

    // these are the shown buttons
    let legalCaseWorkflowStatusesAndTransitionsBtns = legalCaseWorkflowStatusesAndTransitions.slice(0, 3).map((item, key) => {
        return (
            <Button
                className="litigation-case-page-header-actions-toolbar-status-btn"
                variant="contained"
                color="primary"
                key={"litigation-case-status-" + key}
                onClick={(e) => changeLitigationCaseWorkflowStatus(e, { workflowStatus: { value: item.id } })}
            >
                {item?.name}
            </Button>
        );
    });

    // these are the items of the "more" menu
    let legalCaseWorkflowStatusesAndTransitionsMenuItems = legalCaseWorkflowStatusesAndTransitions.slice(3, -1).map((item, key) => {
        
        return (
            <MenuItem
                key={"litigation-case-statuses-menu-item-" + key}
                onClick={(e) => changeLitigationCaseWorkflowStatus(e, { workflowStatus: { value: item.id } })}
            >
                {item?.name}
            </MenuItem>
        );
    });

    return (
        <Container
            maxWidth={false}
            className="litigation-case-page-header-actions-toolbar btns-container no-padding-h"
        >
            <Button
                className="litigation-case-page-header-actions-toolbar-edit-btn"
                variant="contained"
                color="default"
                onClick={() => openEditLitigationCaseForm()}
                startIcon={<EditIcon />}
            >
                Edit
            </Button>
            {
                    legalCaseWorkflowStatusesAndTransitions.length > 0 ?
                    <React.Fragment>
                        {legalCaseWorkflowStatusesAndTransitionsBtns}
                        <Button
                            aria-controls="statuses-menu"
                            aria-haspopup="true"
                            onClick={(e) => handleOpenMenu(e)}
                            variant="outlined"
                            color="primary"
                        >
                            More <ArrowDropDownIcon />
                        </Button>
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
                            {legalCaseWorkflowStatusesAndTransitionsMenuItems}
                        </Menu>
                    </React.Fragment>
                    :
                    null
            }
        </Container>
    );
});
