import React, { useEffect } from 'react';

import './Dashboard.scss';

import {
    Container,
    Grid,
} from '@material-ui/core';

import { GeneralInfoItem } from '../LitigationCasePageGeneralInfoPanel';

import { buildInstanceURL, getAdvisorUserFullName } from '../../../../../../APHelpers';
import { useTranslation } from 'react-i18next';
import { Link } from 'react-router-dom';

export default React.memo((props) => {
    useEffect(() => {

    }, [props?.litigationCase]);

    const { t } = useTranslation();

    let items = [
        {
            label: t("practice_area"),
            value: props?.litigationCase?.legal_case_type?.name
        },
        {
            label: t("workflow_status"),
            value: props?.litigationCase?.case_status?.name
        },
        {
            label: t("internal_ref_number"),
            value: props?.litigationCase?.internalReference
        },
        {
            label: t("matter_priority"),
            value: props?.litigationCase?.priority
        },
        {
            label: t("stage"),
            value: ""
        },
        {
            label: t("success_probability"),
            value: props?.litigationCase?.success_probability ? props?.litigationCase?.success_probability?.success_probability_languages?.[0]?.name : ""
        },
        {
            label: t("arrival_date"),
            value: props?.litigationCase?.caseArrivalDate
        },
        {
            label: t("filled_on"),
            value: props?.litigationCase?.arrivalDate
        },
        {
            label: t("due_date"),
            value: props?.litigationCase?.dueDate
        },
        {
            label: t("closed_on"),
            value: props?.litigationCase?.closedOn
        },
        {
            label: t("client_name"),
            value: props?.litigationCase?.client?.client_is_company ? props?.litigationCase?.client?.company?.name : getAdvisorUserFullName(props?.litigationCase?.client?.contact)
        },
        {
            label: t("total_spent_hrs"),
            value: <Link
                className="primary-link"
                to={`${buildInstanceURL()}/time-logs?caseId=${props?.litigationCase?.id}`}
            >
                {props?.litigationCase?.totalSpentHrs}
            </Link>
        }
    ];

    let rows = [];

    for (var i = 0; i < items.length;) {
        let row = items[i];
        let lastRow = items.length - 2 >= i ? items[i + 1] : false;

        let record = <Grid
            container
            className="field-row"
            key={"litigation-case-page-general-info-panel-dashboard-field-row-" + i}
        >
            {
                row?.fullWidth ?
                    (
                        <Grid
                            item
                            xs={12}
                        >
                            <Grid
                                container
                                className="field-row-item"
                            >
                                <GeneralInfoItem
                                    row={row}
                                />
                            </Grid>
                        </Grid>
                    )
                    :
                    (
                        <React.Fragment>
                            <Grid item sm={6}>
                                <GeneralInfoItem
                                    row={row}
                                />
                            </Grid>
                            {
                                // do we still have one last item?
                                lastRow ?
                                    <Grid item sm={6}>
                                        <GeneralInfoItem
                                            row={lastRow}
                                        />
                                    </Grid>
                                    :
                                    <Grid item sm={6}></Grid>
                            }
                        </React.Fragment>
                    )
            }
        </Grid>;

        rows.push(record);

        if (row?.fullWidth) {
            i++;
        } else {
            i += 2;
        }
    }

    return (
        <Container
            id="litigation-case-page-general-info-panel-dashboard"
            maxWidth={false}
            className="section"
        >
            {rows}
        </Container>
    );
});
