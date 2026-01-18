import React from 'react';

import './Container.scss';

import { Container } from '@material-ui/core';

import { APCollapseContainer } from '../../../../../common/ap-collapse/APCollapse';

import {
    ActionsToolbar,
    Dashboard,
    Details,
    CustomFields,
    Notes,
} from './../CorporateMatterPageGeneralInfoPanel';

import { APPageActionsToolbar } from './../../../../../common/ap-page/APPage';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {

    const { t } = useTranslation();
    return (
        <Container
            id="corporate-matter-page-general-info-panel-container"
            maxWidth="lg"
            className="no-padding-h"
        >
            <APPageActionsToolbar>
                <ActionsToolbar
                    corporateMatter={props?.corporateMatter}
                    loadCorporateMatterData={props?.loadCorporateMatterData}
                />
            </APPageActionsToolbar>
            <APCollapseContainer
                title={t('matter_dashboard')}
                expanded={1}
            >
                <Dashboard
                    corporateMatter={props?.corporateMatter}
                />
            </APCollapseContainer>
            <APCollapseContainer
                title={t('matter_details')}
                expanded={1}
            >
                <Details
                    corporateMatter={props?.corporateMatter}
                />
            </APCollapseContainer>
            <APCollapseContainer
                title={t('custom_fields')}
            >
                <CustomFields
                    corporateMatter={props?.corporateMatter}
                />
            </APCollapseContainer>
            {/* <APCollapseContainer
                title="Companies & Contacts"
            >
                <LitigationCasePageGeneralInfoPanelCompaniesAndContacts />
            </APCollapseContainer> */}
            {/* <APCollapseContainer
                title="Related Contributors"
            >
                <LitigationCasePageGeneralInfoPanelContributors/>
            </APCollapseContainer> */}
            <APCollapseContainer
                title={t("notes")}
            >
                {/* <LitigationCasePageGeneralInfoPanelNotes
                    litigationCase={props?.litigationCase}
                /> */}
                <Notes
                    corporateMatter={props?.corporateMatter}
                    loadCorporateMatterData={props?.loadCorporateMatterData}
                />
            </APCollapseContainer>
        </Container>
    );
});
