import React from 'react';

import './StageDetailsTable.scss';

import { Container } from '@material-ui/core';

import {
    StageDetailsTableRow,
    StageDetailsTableRowItem
} from '../LitigationCasePageStagesPanel';

import { getValueFromLanguage } from '../../../../../../APHelpers';

import { useTranslation } from 'react-i18next';
import { getActiveLanguageId } from '../../../../../../i18n';
 
export default React.memo((props) => {
    const { t } = useTranslation();
    const stageDetailsTableItems = [
        [
            {
                label: t("judgment_date"),
                value: props?.stage?.sentenceDate
            },
            {
                label: t("client_position"),
                value: getValueFromLanguage(props?.stage?.stage_client_position, 'client_position_languages', getActiveLanguageId())
            },
            {
                label: t("court_type"),
                value: props?.stage?.stage_court_type?.name
            }
        ],
        [
            {
                label: t("court"),
                value: props?.stage?.stage_court?.name
            },
            {
                label: t("court_degree"),
                value: props?.stage?.stage_court_degree?.name
            },
            {
                label: t("court_region"),
                value: props?.stage?.stage_court_region?.name
            }
        ]
    ];

    let stageDetailsTable = stageDetailsTableItems.map((row, rowKey) => {
        let items = row.map((item, itemKey) => {

            return <StageDetailsTableRowItem
                key={"litigation-case-stage-table-row-item-" + itemKey}
                label={item?.label}
                value={item?.value}
            />
        });

        return (
            <StageDetailsTableRow
                key={"litigation-case-stage-table-row-" + rowKey}
            >
                {items}
            </StageDetailsTableRow>
        );
    });

    return (
        <Container
            maxWidth={false}
            className="litigation-case-stage-details-table"
        >
            {stageDetailsTable}
        </Container>
    );
});
