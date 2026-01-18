import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './CorporateMatterPage.scss';

import {
    APPageContainer,
    APPageBody,
    APPageHeader,
    APPageTitle,
} from './../../components/common/ap-page/APPage';

import {
    Nav as CorporateMatterPageNav,
} from '../../components/pages/corporate-matter-page/CorporateMatterPageComponents';

import {
    Container as CorporateMatterPagePanelsContainer
} from './../../components/pages/corporate-matter-page/panels/CorporateMatterPagePanels';

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

import {
    buildErrorMessages,
    stringTruncate
} from '../../APHelpers';
 
export default React.memo((props) => {
    const history = useHistory();

    const routeMatches = useRouteMatch();
    
    const corporateMatterId = routeMatches.params?.id;

    const [globalState, globalStateDispatcher] = useContext(Context);
    
    const [corporateMatter, setCorporateMatter] = useState({});
    
    const [dataLoaded, setDataLoaded] = useState(false);

    useEffect(() => {
        loadData();

        globalStateDispatcher({
            mainMenu: {
                ...globalState?.mainMenu,
                activeTab: MAIN_MENU_TABS_NAMES.corporateMatters
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

        LegalCase.get(corporateMatterId, [
            ...LegalCase.allRelations,
            'rootFolder'
        ]).then((response) => {
            setCorporateMatter(response?.data?.data);

            globalStateDispatcher({
                CorporateMatterPage: {
                    ...globalState?.CorporateMatterPage,
                    currentId: corporateMatterId
                }
            });
        }).catch((error) => {
            if (error?.response?.status === 404) {
                history.push('/error/page-not-found');
            } else {
                let message = buildErrorMessages(error?.response?.data?.message);

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
            id={PAGES_IDS.CorporateMatterPage}
        >
            <APPageHeader>
                <APPageTitle
                    pageTitle={"M" + corporateMatterId + ": " + stringTruncate(corporateMatter?.subject, 50)}
                >
                    <p>{corporateMatter?.legal_case_type?.name}</p>
                </APPageTitle>
                <CorporateMatterPageNav
                    corporateMatter={corporateMatter}
                />
            </APPageHeader>
            <APPageBody>
                <CorporateMatterPagePanelsContainer
                    corporateMatter={corporateMatter}
                    loadCorporateMatterData={loadData}
                />
            </APPageBody>
        </APPageContainer>
    );
});
