import React, {
    useContext,
    useEffect
} from 'react';

import './TimeLogPage.scss';

import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES,
    MAIN_MENU_TABS_NAMES,
    PAGES_IDS,
    PAGES_TITLES
} from '../../Constants';

import {
    APPageContainer,
    APPageActionsToolbar,
    APPageBody,
    APPageHeader, 
    APPageTitle
} from '../../components/common/ap-page/APPage';

import { useRouteMatch } from 'react-router-dom';

import { ActionsToolbar } from '../../components/pages/time-log/TimeLogPageComponents';

import { Context } from '../../Store';
 
export default React.memo((props) => {
    const routeMatches = useRouteMatch();
    
    const timeLogId = routeMatches.params?.id;

    const [globalState, globalStateDispatcher] = useContext(Context);

    useEffect(() => {
        loadData();

        globalStateDispatcher({
            mainMenu: {
                ...globalState?.mainMenu,
                activeTab: MAIN_MENU_TABS_NAMES.advisorTimeLogs
            }
        });
    }, []);

    const loadData = () => {}

    return (
        <APPageContainer
            id={PAGES_IDS.timeLogPage}
        >
        <APPageHeader>
            <APPageTitle
                pageTitle={"TL" + timeLogId + ": "}
            />
        </APPageHeader>
        <APPageActionsToolbar>
            <ActionsToolbar
                // openAddForm={openAddForm}
            />
        </APPageActionsToolbar>
        <APPageBody>
            {/* <APMaterialTable
                columns={tableColumns}
                data={data}
                loadData={loadData}
                exportData={exportData}
                handleDelete={deleteData}
            /> */}
        </APPageBody>
        </APPageContainer>
    );
});
