import React, { useContext } from 'react';

import './APMainMenu.scss';

import {
    Divider,
    Drawer,
    IconButton,
    List,
    ListItem
} from '@material-ui/core';

import BusinessCenterIcon from '@material-ui/icons/BusinessCenter';
import AssignmentIcon from '@material-ui/icons/Assignment';
import AccessAlarmsIcon from '@material-ui/icons/AccessAlarms';
import AccountBalanceIcon from '@material-ui/icons/AccountBalance';
import AssignmentTurnedInIcon from '@material-ui/icons/AssignmentTurnedIn';

import APMainMenuItem from '../APMainMenuItem/APMainMenuItem.lazy';
import APMainMenuAddItem from '../APMainMenuAddItem/APMainMenuAddItem.lazy';
import APMainMenuLogo from '../APMainMenuLogo/APMainMenuLogo.lazy';
import APMainMenuBottomItems from '../APMainMenuBottomItems/APMainMenuBottomItems.lazy';

import { FORMS_MODAL_TITLES, FORMS_NAMES, MAIN_MENU_TABS_NAMES, PREFERENCES_KEYS } from '../../../../Constants';

import { useTranslation } from 'react-i18next';
import { Context, initialGlobalState } from '../../../../Store';
import AdvisorUserPreferences from '../../../../api/AdvisorUserPreferences';


export default React.memo((props) => {

    const [globalState, globalStateDispatcher] = useContext(Context);
    const { t } = useTranslation();


    const openTimerForm = () => {
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

    return (
        <div
            id="AP-main-menu"
        >
            <Drawer
                variant="permanent"
                anchor="left"
                classes={{ paper: "AP-main-menu-drawer" }}
            >
                <List
                    classes={{ root: "AP-main-menu-list" }}
                >
                    <APMainMenuLogo />
                    <Divider
                        classes={{ root: "AP-main-menu-divider" }}
                    />
                    <APMainMenuAddItem />
                    <APMainMenuItem
                        link="corporate-matters"
                        tabName={MAIN_MENU_TABS_NAMES.corporateMatters}
                        title={t("corporate_matters")}
                        icon={
                            <BusinessCenterIcon
                                classes={{ root: "AP-main-menu-list-item-icon " + globalState.domDirection }}
                            />
                        }
                    />
                    <APMainMenuItem
                        link="litigation-cases"
                        tabName={MAIN_MENU_TABS_NAMES.litigationCases}
                        title={t("litigation_cases")}
                        icon={
                            <AccountBalanceIcon
                                classes={{ root: "AP-main-menu-list-item-icon " + globalState.domDirection }}
                            />
                        }
                    />
                    <APMainMenuItem
                        link="hearings"
                        tabName={MAIN_MENU_TABS_NAMES.hearings}
                        title={t("hearings")}
                        icon={
                            <AssignmentIcon
                                classes={{ root: "AP-main-menu-list-item-icon " + globalState.domDirection }}
                            />
                        }
                    />
                    <APMainMenuItem
                        link="tasks"
                        tabName={MAIN_MENU_TABS_NAMES.advisorTasks}
                        title={t("tasks")}
                        icon={
                            <AssignmentTurnedInIcon
                                classes={{ root: "AP-main-menu-list-item-icon " + globalState.domDirection }}
                            />
                        }
                    />
                    <APMainMenuItem
                        link="time-logs"
                        tabName={MAIN_MENU_TABS_NAMES.advisorTimeLogs}
                        title={t("my_time_logs")}
                        icon={
                            <AccessAlarmsIcon
                                classes={{ root: "AP-main-menu-list-item-icon " + globalState.domDirection }}
                            />
                        }
                    />

                    <ListItem
                        classes={{ root: "AP-main-menu-list-item" }}
                    >
                        <IconButton
                            classes={{ root: "AP-main-menu-list-item-icon-btn", label: "AP-main-menu-list-item-icon-label" }}
                            edge="start"
                            color="inherit"
                            onClick={() => openTimerForm()}
                        >
                            <AccessAlarmsIcon
                                classes={{ root: "AP-main-menu-list-item-icon " + globalState.domDirection }}
                            /> {t("show_timers")}
                        </IconButton>
                    </ListItem>
                    <APMainMenuBottomItems />
                </List>
            </Drawer>
        </div>
    );
});
