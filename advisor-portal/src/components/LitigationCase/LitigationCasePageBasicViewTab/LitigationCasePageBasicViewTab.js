import React from 'react';
import './LitigationCasePageBasicViewTab.scss';
import LegalCaseComment from './../../../api/LegalCaseComment';
import APCollapseContainer from '../../common/ap-collapse/ap-collapse-container/APCollapseContainer.lazy';
import APCommentsContainer from '../../common/APCommentsContainer/APCommentsContainer.lazy';
import LitigationCasePageBasicViewDashboard from './../LitigationCasePageBasicViewDashboard/LitigationCasePageBasicViewDashboard.lazy';
import LitigationCasePageBasicViewDetails from './../LitigationCasePageBasicViewDetails/LitigationCasePageBasicViewDetails.lazy';
import LitigationCasePageBasicViewCustomFields from './../LitigationCasePageBasicViewCustomFields/LitigationCasePageBasicViewCustomFields.lazy';
import LitigationCasePageBasicViewCompaniesAndContacts from './../LitigationCasePageBasicViewCompaniesAndContacts/LitigationCasePageBasicViewCompaniesAndContacts.lazy';
import LitigationCasePageBasicViewContributors from './../LitigationCasePageBasicViewContributors/LitigationCasePageBasicViewContributors.lazy';
import { Container } from '@material-ui/core';
 
export default React.memo((props) => {

    return (
        <Container
            id="litigation-case-page-basic-view-tab"
            maxWidth="md"
            className="no-padding-h"
        >
            <APCollapseContainer
                title="Matter Dashboard"
                expanded={1}
            >
                <LitigationCasePageBasicViewDashboard
                    litigationCase={props?.litigationCase}
                />
            </APCollapseContainer>
            <APCollapseContainer
                title="Matter Details"
            >
                <LitigationCasePageBasicViewDetails
                    litigationCase={props?.litigationCase}
                />
            </APCollapseContainer>
            <APCollapseContainer
                title="Custom Fields"
            >
                <LitigationCasePageBasicViewCustomFields
                    litigationCase={props?.litigationCase}
                    litigationCaseCustomFields={props?.litigationCaseCustomFields}
                    loadLitigationCaseData={props?.loadLitigationCaseData}
                />
            </APCollapseContainer>
            {/* <APCollapseContainer
                title="Companies & Contacts"
            >
                <LitigationCasePageBasicViewCompaniesAndContacts
                    litigationCase={props?.litigationCase}
                />
            </APCollapseContainer>
            <APCollapseContainer
                title="Related Contributors"
            >
                <LitigationCasePageBasicViewContributors
                    litigationCase={props?.litigationCase}
                />
            </APCollapseContainer> */}
            <APCollapseContainer
                title="Notes"
            >
                <APCommentsContainer
                    model={new LegalCaseComment()}
                    modelName="legalCase"
                    legalCase={props?.litigationCase}
                    loadData={props?.loadLitigationCaseData}
                    query={{
                        'legalCaseId': {
                            'value': props?.litigationCase?.id
                        }
                    }}
                />
            </APCollapseContainer>
        </Container>
    );
});
