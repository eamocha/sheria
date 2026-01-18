import React, { useContext } from 'react';

import './APMainMenuBottomItems.scss';

import {
    IconButton,
    ListItem
} from '@material-ui/core';

import { Context } from '../../../../Store';

import PowerSettingsNewIcon from '@material-ui/icons/PowerSettingsNew';

import PersonIcon from '@material-ui/icons/Person';

import APMainMenuItem from '../APMainMenuItem/APMainMenuItem.lazy';

import { MAIN_MENU_TABS_NAMES } from '../../../../Constants';

export default React.memo((props) => {
    const [, globalStateDispatcher] = useContext(Context);

    const logout = () => {
        globalStateDispatcher({
            user: {
                loggedIn: false,
                data: {}
            }
        });
    }

    return (
        <div
            className="AP-main-menu-bottom-items"
        >
            <APMainMenuItem
                link="profile"
                title="My Profile"
                tabName={MAIN_MENU_TABS_NAMES.profilePage}
                icon={
                    <PersonIcon
                        classes={{ root: "AP-main-menu-list-item-icon " + globalState.domDirection }}
                    />
                }
            />
            <ListItem
                classes={{ root: "AP-main-menu-list-item" }}
            >
                <IconButton
                    classes={{
                        root: "AP-main-menu-list-item-icon-btn",
                        label: "AP-main-menu-list-item-icon-label"
                    }}
                    edge="start"
                    color="inherit"
                    onClick={() => logout()}
                >
                    <PowerSettingsNewIcon
                        classes={{ root: "AP-main-menu-list-item-icon " + globalState.domDirection }}
                    /> Logout
                </IconButton>
            </ListItem>
        </div>
    );
});
