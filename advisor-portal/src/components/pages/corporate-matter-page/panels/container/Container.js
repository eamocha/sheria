import React from 'react';

import './Container.scss';

import {
    APNavTabPanelContainer
} from '../../../../common/ap-nav/APNav';

import {
    Container as GeneralInfoPanelContainer
} from './../general-info/CorporateMatterPageGeneralInfoPanel';

import {
    Container as RelatedTasksContainer
} from './../related-tasks/CorporateMatterPageRelatedTasksPanel';

import {
    Container as RelatedDocumentsContainer
} from './../related-documents/CorporateMatterPageRelatedDocumentsPanel';

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
                value={`${buildInstanceURL()}/corporate-matter/${routeMatches?.params?.id}`}
            >
                <GeneralInfoPanelContainer
                    corporateMatter={props?.corporateMatter ?? {}}
                    loadCorporateMatterData={props?.loadCorporateMatterData}
                />
            </APNavTabPanelContainer>
            <APNavTabPanelContainer
                value={`${buildInstanceURL()}/corporate-matter/related-tasks/${routeMatches?.params?.id}`}
            >
                <RelatedTasksContainer
                    corporateMatter={props?.corporateMatter ?? {}}
                />
            </APNavTabPanelContainer>
            <APNavTabPanelContainer
                value={`${buildInstanceURL()}/corporate-matter/related-documents/${routeMatches?.params?.id}`}
            >
                <RelatedDocumentsContainer
                    corporateMatter={props?.corporateMatter ?? {}}
                />
            </APNavTabPanelContainer>
        </TabContext>
    );
});
