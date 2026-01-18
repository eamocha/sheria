import React, {
    useContext,
    useState
} from 'react';

import './AdvisorTasksTableRowMenu.scss';

import {
    Button,
    Menu,
    MenuItem
} from '@material-ui/core';

import MenuIcon from '@material-ui/icons/Menu';

import { useHistory } from 'react-router-dom';

import { PUBLIC_URL } from '../../../../../../Constants';

import AdvisorTask from '../../../../../../api/AdvisorTask';

import { buildErrorMessages, isFunction } from '../../../../../../APHelpers';

import {
    Context,
    initialGlobalState
} from '../../../../../../Store';
 
export default React.memo((props) => {
    const history = useHistory();
    
    const [globalState, globalStateDispatcher] = useContext(Context);

    const [actionsMenuAnchorEl, setActionsMenuAnchorEl] = useState(false);
    
    const handleActionsMenuClick = (e) => {
        setActionsMenuAnchorEl(e.currentTarget);
    };

    const handleActionsMenuClose = (target) => {
        switch (target) {
            // case 'RecordExpense':
            //     handleExpenseForm();

            //     break;

            // case 'BulkExpenses':
            //     handleExpenseForm();

            //     break;

            case 'AdvisorTaskView':
                handleRedirectToView();

                break;

            case 'DeleteAdvisorTask':
                handleDeleteAdvisorTask();    

                break;
        
            default:
                setActionsMenuAnchorEl(null);

                break;
        }
    };

    const handleExpenseForm = () => {
        // props.setActiveFormModal('AdvisorTaskForm');
        // props.setActiveFormModelData({
        //     matter: props.matter,
        //     currentStage: props.currentStage,
        //     advisorTask: props.advisorTask,
        //     mode: 'edit'
        // });
        // props.setFormModalState(true);
        // props.setAfterActionReloadFunction(() => props.afterActionReloadFunction);

        // setActionsMenuAnchorEl(null);
    };

    const handleRedirectToView = () => {
        history.push(`${PUBLIC_URL}/task/${props?.advisorTask?.id}`);

        setActionsMenuAnchorEl(null);
    };

    const handleDeleteAdvisorTask = () => {
        setActionsMenuAnchorEl(null);

        let advisorTaskId = props?.advisorTask?.id;

        if (window.confirm("Are you sure you want to delete this task?")) {
        //     props.setGlobalLoader(true);

        //     AdvisorTask.delete(advisorTaskId).then(response => {
        //         props.setGlobalLoader(false);
        //         props.setNotificationBarText("Advisor Task " + (advisorTaskId ? ("AT" + advisorTaskId + " ") : "") + "has been deleted successfully");
        //         props.setNotificationBarSeverity("success");
        //         props.setNotificationBarState(true);
        //         props.afterActionReloadFunction();
        //     }).catch(error => {

        //         props.setGlobalLoader(false);
        //     });

            AdvisorTask.delete(advisorTaskId).then((response) => {
                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        text: "Advisor Task " + (advisorTaskId ? ("AT" + advisorTaskId + " ") : "") + "has been deleted successfully",
                        severity: "success"
                    },
                });

                if (isFunction(props.loadActivities)) {
                    props.loadActivities();
                }
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
                if (!isFunction(props.loadActivities)) {
                    globalStateDispatcher({
                    globalLoader: initialGlobalState?.globalLoader
                    });
                }
            });
        }
    };

    return (
        <React.Fragment>
            <Button
                className="actions-menu-btn"
                aria-controls="litigation-case-activity-advisor-task-item-actions-menu"
                aria-haspopup="true"
                onClick={(e) => handleActionsMenuClick(e)}
                size="small"
            >
                <MenuIcon
                    fontSize="small"
                />
            </Button>
            <Menu
                id={"litigation-case-activity-advisor-task-item-actions-menu-" + props.rowkey}
                anchorEl={actionsMenuAnchorEl}
                keepMounted
                getContentAnchorEl={null}
                anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
                transformOrigin={{ vertical: "top", horizontal: "center" }}
                open={Boolean(actionsMenuAnchorEl)}
                onClose={handleActionsMenuClose}
            >
                <MenuItem
                    onClick={() => handleActionsMenuClose('AdvisorTaskView')}
                >
                    Show Full Details
                </MenuItem>
                {/* <MenuItem
                    onClick={() => handleActionsMenuClose('RecordExpense')}
                >
                    Record Expense
                </MenuItem>
                <MenuItem
                    onClick={() => handleActionsMenuClose('BulkExpenses')}
                >
                    Bulk Expenses
                </MenuItem> */}
                <MenuItem
                    onClick={() => handleActionsMenuClose('DeleteAdvisorTask')}
                >
                    Delete
                </MenuItem>
            </Menu>
        </React.Fragment>
    );
});
