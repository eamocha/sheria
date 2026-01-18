import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './LitigationCasePage.scss';

import {
    APPageContainer,
    APPageBody,
    APPageHeader,
    APPageTitle,
} from './../../components/common/ap-page/APPage';

import {
    Nav as LitigationCasePageNav,
} from '../../components/pages/litigation-case-page/LitigationCasePageComponents';

import {
    Container as LitigationCasePagePanelsContainer
} from './../../components/pages/litigation-case-page/panels/LitigationCasePagePanels';

import {
    useHistory,
    useRouteMatch
} from 'react-router-dom';

import {
    MAIN_MENU_TABS_NAMES,
    PAGES_IDS
} from '../../Constants';

import {
    Context,
    initialGlobalState
} from '../../Store';

import LegalCase from '../../api/LegalCase';

import { stringTruncate } from '../../APHelpers';
 
export default React.memo((props) => {
    const history = useHistory();

    const routeMatches = useRouteMatch();
    
    const litigationCaseId = routeMatches.params?.id;

    const [globalState, globalStateDispatcher] = useContext(Context);
    
    const [litigationCase, setLitigationCase] = useState({});
    
    const [dataLoaded, setDataLoaded] = useState(false);

    useEffect(() => {
        loadData();

        globalStateDispatcher({
            mainMenu: {
                ...globalState?.mainMenu,
                activeTab: MAIN_MENU_TABS_NAMES.litigationCases
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

        // re-initiate the dataLoaded state (for reload)
        setDataLoaded(false);

        LegalCase.get(litigationCaseId).then((response) => {

            setLitigationCase(response?.data?.data);

            globalStateDispatcher({
                litigationCasePage: {
                    ...globalState?.litigationCasePage,
                    currentId: litigationCaseId
                }
            });
        }).catch((error) => {
            if (error?.response?.status === 404) {
                history.push('/error/page-not-found');
            } else {
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
                        text: message,
                        severity: "error"
                    }
                });
            }
        }).finally(() => {
            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });

            setDataLoaded(true);
        });
    }

    if (!dataLoaded) {
        return null;
    }

    return (
        <APPageContainer
            id={PAGES_IDS.litigationCasePage}
        >
            <APPageHeader>
                <APPageTitle
                    pageTitle={"M" + litigationCaseId + ": " + stringTruncate(litigationCase?.subject, 50)}
                >
                    <p>{litigationCase?.legal_case_type?.name}</p>        
                </APPageTitle>
                <LitigationCasePageNav
                    litigationCase={litigationCase}
                />
            </APPageHeader>
            <APPageBody>
                <LitigationCasePagePanelsContainer
                    litigationCase={litigationCase}
                    loadLitigationCaseData={loadData}
                />
            </APPageBody>
        </APPageContainer>
    );
});
