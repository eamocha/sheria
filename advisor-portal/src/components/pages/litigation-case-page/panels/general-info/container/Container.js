import React from 'react';

import './Container.scss';

import { Container } from '@material-ui/core';

import { APCollapseContainer } from '../../../../../common/ap-collapse/APCollapse';

import {
    CompaniesAndContacts,
    Contributors,
    CustomFields,
    Dashboard,
    Details,
    Notes,
    ActionsToolbar
} from '../LitigationCasePageGeneralInfoPanel';

import { APPageActionsToolbar } from './../../../../../common/ap-page/APPage';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {

    const { t } = useTranslation();
    return (
        <Container
            id="litigation-case-page-general-info-panel-container"
            maxWidth="lg"
            className="no-padding-h"
        >
            <APPageActionsToolbar>
                <ActionsToolbar
                    litigationCase={props?.litigationCase}
                    loadLitigationCaseData={props?.loadLitigationCaseData}
                />
            </APPageActionsToolbar>
            <APCollapseContainer
                title={t('matter_dashboard')}
                expanded={1}
            >
                <Dashboard
                    litigationCase={props?.litigationCase}
                />
            </APCollapseContainer>
            <APCollapseContainer
                title={t('matter_details')}
                expanded={1}
            >
                <Details
                    litigationCase={props?.litigationCase}
                />
            </APCollapseContainer>
            <APCollapseContainer
                title={t('custom_fields')}
            >
                <CustomFields
                    litigationCase={props?.litigationCase}
                />
            </APCollapseContainer>
            {/* <APCollapseContainer
                title="Companies & Contacts"
            >
                <CompaniesAndContacts />
            </APCollapseContainer>
            <APCollapseContainer
                title="Related Contributors"
            >
                <Contributors />
            </APCollapseContainer> */}
            <APCollapseContainer
                title={t("notes")}
            >
                <Notes
                    litigationCase={props?.litigationCase}
                    loadLitigationCaseData={props?.loadLitigationCaseData}
                />
            </APCollapseContainer>
        </Container>
    );
});
