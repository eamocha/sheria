import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './Container.scss';

import { APPageActionsToolbar } from '../../../../../common/ap-page/APPage';

import {
    ActionsToolbar,
    StageContainer
} from '../LitigationCasePageStagesPanel';

import { Container } from '@material-ui/core';

import {
    Context,
    initialGlobalState
} from '../../../../../../Store';

import LegalCase from '../../../../../../api/LegalCase';

import {
    useHistory,
    useRouteMatch
} from 'react-router-dom';
 
export default React.memo((props) => {
    const history = useHistory();

    const routeMatches = useRouteMatch();
    
    const litigationCaseId = routeMatches.params?.id;
    
    const [globalState, globalStateDispatcher] = useContext(Context);

    const [litigationCase, setLitigationCase] = useState('');
    
    const [dataLoaded, setDataLoaded] = useState(false);

    const [stages, setStages] = useState([]);

    useEffect(() => {

        loadData();
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

        LegalCase.get(
            litigationCaseId,
            [
                'currentStage.stageName.stageNameLanguages',
                'stages.stageName.stageNameLanguages',
                'stages.stageStatus',
                'stages.stageStatus.stageStatusLanguages',
                'stages.modifiedByUser',
                'stages.stageClientPosition',
                'stages.stageCourt',
                'stages.stageCourtType',
                'stages.stageCourtDegree',
                'stages.stageCourtRegion',
                'stages.stageOpponents',
                'stages.stageExternalReferences',
                'stages.stageOpponentLawyers',
                'stages.stageOpponentLawyers.contactFullDetails',
                'stages.stageOpponentLawyers.contactRoleFullDetails',
                'stages.stageJudges',
                'stages.stageJudges.contactFullDetails',
                'stages.stageJudges.contactRoleFullDetails'
            ]
        ).then((response) => {
            
            setLitigationCase(response?.data?.data);

            setStages(response?.data?.data?.stages);
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

    let stagesContent = stages.map((stage, key) => {

        return <StageContainer
            stage={stage}
            litigationCase={litigationCase}
            loadLitigationCaseData={loadData}
        />
    });

    return (
        <Container
            id="litigation-case-page-stages-panel-container"
            maxWidth="lg"
            className="no-padding-h"
        >
            <APPageActionsToolbar>
                <ActionsToolbar
                    litigationCase={litigationCase}
                    loadLitigationCaseData={loadData}
                />
            </APPageActionsToolbar>
            <Container
                maxWidth={false}
                className="no-padding-h"
            >
                {stagesContent}
            </Container>
        </Container>
    );
});
