import React, {
    useContext,
    useEffect,
    useState
} from 'react';
import './Profile.scss';
import { Container } from '@material-ui/core';
import {
    APPageContainer,
    APPageBody,
    APPageHeader,
    APPageTitle,
} from './../../components/common/ap-page/APPage';
import ProfileForm from '../../components/profile/ProfileForm/ProfileForm';
import { PAGES_IDS } from '../../Constants';
import { Context } from '../../Store';
import { MAIN_MENU_TABS_NAMES } from './../../Constants';
import { useTranslation } from 'react-i18next';
 
export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    const [t] = useTranslation();
    useEffect(() => {

        globalStateDispatcher({
            mainMenu: {
                ...globalState?.mainMenu,
                activeTab: MAIN_MENU_TABS_NAMES.profilePage
            }
        });
    }, []);

    return (
        <APPageContainer
            id={PAGES_IDS.profilePage}
        >
            <APPageHeader>
                <APPageTitle
                    pageTitle={t("my_profile")}
                />
            </APPageHeader>
            <APPageBody
                className="d-flex flex-start"
            >
                <Container
                    fixed
                    maxWidth="md"
                    className="user-profile-form-container"
                >
                    <ProfileForm />
                </Container>
            </APPageBody>
        </APPageContainer>
    );
});
