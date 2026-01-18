import React from 'react';

import './Container.scss';

import {
    APNavTabPanelContainer
} from './../../../../common/ap-nav/APNav';

import {
    Container as GeneralInfoPanelContainer
} from './../general-info/LitigationCasePageGeneralInfoPanel';

import {
    Container as LitigationCasePageStagesPanelContainer
} from './../stages-panel/LitigationCasePageStagesPanel';

import {
    Container as LitigationCasePageActivitiesPanelContainer
} from './../activities/LitigationCasePageActivitiesPanel';

import {
    Container as LitigationCasePageRelatedTasksContainer
} from './../related-tasks/LitigationCasePageRelatedTasksPanel';

import {
    Container as LitigationCasePageRelatedDocumentsContainer
} from './../related-documents/LitigationCasePageRelatedDocumentsPanel';

import { TabContext } from '@material-ui/lab';

import {
    useHistory,
    useRouteMatch
} from 'react-router-dom';
import { buildInstanceURL } from '../../../../../APHelpers';

export default React.memo((props) => {
    const history = useHistory();
    const routeMatches = useRouteMatch();

    return (
        <TabContext
            value={history.location.pathname}
        >
            <APNavTabPanelContainer
                value={`${buildInstanceURL()}/litigation-case/${routeMatches?.params?.id}`}
            >
                <GeneralInfoPanelContainer
                    litigationCase={props?.litigationCase ?? {}}
                    loadLitigationCaseData={props?.loadLitigationCaseData}
                />
            </APNavTabPanelContainer>
            <APNavTabPanelContainer
                value={`${buildInstanceURL()}/litigation-case/litigation-stages/${routeMatches?.params?.id}`}
            >
                <LitigationCasePageStagesPanelContainer
                    litigationCase={props?.litigationCase ?? {}}
                />
            </APNavTabPanelContainer>
            <APNavTabPanelContainer
                value={`${buildInstanceURL()}/litigation-case/activities/${routeMatches?.params?.id}`}
            >
                <LitigationCasePageActivitiesPanelContainer
                    litigationCase={props?.litigationCase ?? {}}
                />
            </APNavTabPanelContainer>
            <APNavTabPanelContainer
                value={`${buildInstanceURL()}/litigation-case/related-tasks/${routeMatches?.params?.id}`}
            >
                <LitigationCasePageRelatedTasksContainer
                    litigationCase={props?.litigationCase ?? {}}
                />
            </APNavTabPanelContainer>
            <APNavTabPanelContainer
                value={`${buildInstanceURL()}/litigation-case/related-documents/${routeMatches?.params?.id}`}
            >
                <LitigationCasePageRelatedDocumentsContainer
                    litigationCase={props?.litigationCase ?? {}}
                />
            </APNavTabPanelContainer>
        </TabContext>
    );
});
