import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import {
    initialGlobalState,
    Context
} from '../../Store';

import {
    PAGES_IDS,
    PAGES_TITLES,
} from '../../Constants';

import {
    APPageContainer,
    APPageBody,
    APPageHeader,
    APPageTitle,
} from './../../components/common/ap-page/APPage';

import './HomePage.scss';

import DashboardContainer from './../../components/pages/dashboard/DashboardContainer';

import { Button } from '@material-ui/core';

import APPageActionsToolbar from '../../components/common/ap-page/ap-page-actions-toolbar/APPageActionsToolbar';

import MenuIcon from '@material-ui/icons/Menu';

import { useHistory } from 'react-router-dom';

import { useTranslation } from 'react-i18next';
import { buildInstanceURL } from '../../APHelpers';

const Home = React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    const [t] = useTranslation();

    useEffect(() => {

        globalStateDispatcher({
            mainMenu: initialGlobalState?.mainMenu
        });
    }, []);

    const history = useHistory();
    const handlePreferencesBtnClick = () => {
        history.push(`${buildInstanceURL()}/dashboard-preferences`);
    }

    return (
        <APPageContainer
            id={PAGES_IDS.homePage}
        >
            <APPageHeader>
                <APPageTitle
                    pageTitle={PAGES_TITLES.homePage}
                    children={
                        <Button
                            className={"preferences-btn " + globalState.domDirection}
                            onClick={(e) => handlePreferencesBtnClick(e)}
                            size="small"
                            title={t("widgets_preferences")}
                        >
                            <MenuIcon
                                fontSize="small"
                            />
                        </Button>
                    }
                />
            </APPageHeader>
            <APPageBody>
                <DashboardContainer />
            </APPageBody>
        </APPageContainer>
    );
});

export default Home;
