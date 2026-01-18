import React from 'react';
import './LitigationCasePageHeader.scss';
import { Container } from '@material-ui/core';
import LitigationCasePageHeaderActionsToolbar from '../LitigationCasePageHeaderActionsToolbar/LitigationCasePageHeaderActionsToolbar';
import LitigationCasePageHeaderNav from '../LitigationCasePageHeaderNav/LitigationCasePageHeaderNav.lazy';
 
export default React.memo((props) => {
    
    return (
        <Container
            maxWidth={false}
            className="header no-padding-h"
        >
            <LitigationCasePageHeaderActionsToolbar
                legalCase={props?.legalCase}
                legalCaseWorkflowStatuses={props?.legalCaseWorkflowStatuses}
                legalCaseWorkflowStatusTransitions={props?.legalCaseWorkflowStatusTransitions}
                loadLegalCaseData={props?.loadLegalCaseData}
            />
            <LitigationCasePageHeaderNav
                litigationCase={props?.legalCase}
                navTabName={props?.navTabName}
            />
        </Container>
    );
});
