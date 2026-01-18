import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './AdvisorTaskPage.scss';

import {
    Context,
    initialGlobalState
} from '../../Store';

import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES,
    MAIN_MENU_TABS_NAMES,
    PAGES_IDS,
} from '../../Constants';

import {
    useRouteMatch
} from 'react-router-dom';

import {
    addEllipsis,
    buildErrorMessages,
    getValueFromLanguage
} from './../../APHelpers';

import AdvisorTask from './../../api/AdvisorTask';

import {
    Container as AdvisorTaskPageContainer,
} from './../../components/pages/advisor-task/AdvisorTaskPageComponents';

import {
    APPageActionsToolbar,
    APPageHeader,
    APPageContainer,
    APPageBody,
    APPageTitle
} from '../../components/common/ap-page/APPage';

import { ActionsToolbar } from '../../components/pages/advisor-task/AdvisorTaskPageComponents';
import { useTranslation } from 'react-i18next';
import { getActiveLanguageId } from '../../i18n';
 
export default React.memo((props) => {
    const routeMatches = useRouteMatch();
    
    const advisorTaskId = routeMatches.params?.id;

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [advisorTask, setAdvisorTask] = useState({});

    const [t] = useTranslation();

    useEffect(() => {
        loadData();

        globalStateDispatcher({
            mainMenu: {
                ...globalState?.mainMenu,
                activeTab: MAIN_MENU_TABS_NAMES.advisorTasks
            }
        });
    }, []);

    const loadData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        AdvisorTask.get(advisorTaskId).then((response) => {

            setAdvisorTask(response?.data?.data);
        }).catch((error) => {
            let message = buildErrorMessages(error?.response?.data?.message);

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: message,
                    severity: "error"
                }
            });
        }).finally(() => {
            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });
        });
    }

    const openEditForm = () => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.advisorTaskEditForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.advisorTaskEditForm,
                    submitCallback: loadData,
                    data: {
                        advisorTask: advisorTask
                    }
                }
            }
        });
    }

    return (
        <APPageContainer
            id={PAGES_IDS.advisorTaskPage}
        >
            <APPageHeader>
                <APPageTitle
                    pageTitle={"T" + advisorTaskId + ": " + addEllipsis(advisorTask?.description, 20)}
                />
                <p>{getValueFromLanguage(advisorTask?.advisor_task_type, 'advisor_task_type_languages', getActiveLanguageId())}</p>
            </APPageHeader>
            <APPageActionsToolbar>
                <ActionsToolbar
                    openEditForm={openEditForm}
                    advisorTask={advisorTask}
                    loadData={loadData}
                />
            </APPageActionsToolbar>
            <APPageBody>
                <AdvisorTaskPageContainer
                    advisorTask={advisorTask}
                    loadAdvisorTaskData={loadData}
                />
            </APPageBody>
        </APPageContainer>
    );
});
