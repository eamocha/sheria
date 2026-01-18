import React, { useContext, useEffect, useState } from 'react';

import './APMainMenuBottomItems.scss';

import {
    IconButton,
    ListItem
} from '@material-ui/core';

import { Context } from '../../../../Store';

import PowerSettingsNewIcon from '@material-ui/icons/PowerSettingsNew';

import PersonIcon from '@material-ui/icons/Person';

import APMainMenuItem from '../APMainMenuItem/APMainMenuItem.lazy';

import { BROADCAST_CHANNEL, SESSION_KEYS } from '../../../../Constants';
import i18n from "i18next";
import { useTranslation } from 'react-i18next';
import { APNativeSelectList } from '../../../common/APForm/APForm';
import Authentication from '../../../../api/Authentication';

export default React.memo((props) => {

    const [globalState, globalStateDispatcher] = useContext(Context);

    const logout = () => {

        Authentication.logOut().then((response) => {
            if (response?.status === 200) {
                sessionStorage.removeItem(SESSION_KEYS.accessToken);
                sessionStorage.removeItem(SESSION_KEYS.refreshToken);
                sessionStorage.removeItem(SESSION_KEYS.user);

                globalStateDispatcher({
                    user: {
                        loggedIn: false,
                        data: {}
                    }
                });

                globalState.broadCastChannel.postMessage({
                    'cmd': BROADCAST_CHANNEL.logoutUser
                });
            }
        }).catch((error) => {
            // if no response, so there is a network error

        }).finally(() => {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: false
                }
            });
        });
    }

    const { t } = useTranslation();

    const [selectedLanguage, setSelectedLanguage] = useState(i18n.language);
    const languages = [
        {
            "label": 'English',
            "value": 'en'
        },
        {
            "label": 'العربية',
            "value": 'ar'
        }, , {
            "label": 'Français',
            "value": 'fr'
        }, {
            "label": 'Español',
            "value": 'sp'
        }
    ]

    useEffect(() => {
        i18n.changeLanguage(selectedLanguage);
    }, [selectedLanguage]);


    return (
        <div
            className={"AP-main-menu-bottom-items " + globalState.domDirection}
        >

            <APNativeSelectList
                options={languages}
                value={selectedLanguage}
                valueKey="value"
                onChange={setSelectedLanguage}
                classes="AP-language-select-list"
            />


            <APMainMenuItem
                link="profile"
                title={t("my_profile")}
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
                    classes={{ root: "AP-main-menu-list-item-icon-btn", label: "AP-main-menu-list-item-icon-label" }}
                    edge="start"
                    color="inherit"
                    onClick={() => logout()}
                >
                    <PowerSettingsNewIcon
                        classes={{ root: "AP-main-menu-list-item-icon " + globalState.domDirection }}
                    /> {t("logout")}
                </IconButton>
            </ListItem>
        </div>
    );
});
