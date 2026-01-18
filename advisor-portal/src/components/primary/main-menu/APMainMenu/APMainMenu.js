import React from 'react';
import './APMainMenu.scss';
import {
    Divider,
    Drawer,
    List
} from '@material-ui/core';
import BusinessCenterIcon from '@material-ui/icons/BusinessCenter';
import AssignmentIcon from '@material-ui/icons/Assignment';
import AccessAlarmsIcon from '@material-ui/icons/AccessAlarms';
import AccountBalanceIcon from '@material-ui/icons/AccountBalance';
import APMainMenuItem from '../APMainMenuItem/APMainMenuItem.lazy';
import APMainMenuAddItem from '../APMainMenuAddItem/APMainMenuAddItem.lazy';
import APMainMenuLogo from '../APMainMenuLogo/APMainMenuLogo.lazy';
import APMainMenuBottomItems from '../APMainMenuBottomItems/APMainMenuBottomItems.lazy';
import { MAIN_MENU_TABS_NAMES } from '../../../../Constants';

export default React.memo((props) => {

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
                        title="Corporate Matters"
                        icon={
                            <BusinessCenterIcon
                                classes={{ root: "AP-main-menu-list-item-icon " + globalState.domDirection }}
                            />
                        }
                    />
                    <APMainMenuItem
                        link="litigation-cases"
                        tabName={MAIN_MENU_TABS_NAMES.litigationCases}
                        title="Litigation Cases"
                        icon={
                            <AccountBalanceIcon
                                classes={{ root: "AP-main-menu-list-item-icon " + globalState.domDirection }}
                            />
                        }
                    />
                    <APMainMenuItem
                        link="tasks"
                        tabName={MAIN_MENU_TABS_NAMES.advisorTasks}
                        title="Tasks"
                        icon={
                            <AssignmentIcon
                                classes={{ root: "AP-main-menu-list-item-icon " + globalState.domDirection }}
                            />
                        }
                    />
                    <APMainMenuItem
                        link="time-logs"
                        tabName={MAIN_MENU_TABS_NAMES.advisorTimeLogs}
                        title="My Time Logs"
                        icon={
                            <AccessAlarmsIcon
                                classes={{ root: "AP-main-menu-list-item-icon " + globalState.domDirection }}
                            />
                        }
                    />
                    <APMainMenuBottomItems />
                </List>
            </Drawer>
        </div>
    );
});
