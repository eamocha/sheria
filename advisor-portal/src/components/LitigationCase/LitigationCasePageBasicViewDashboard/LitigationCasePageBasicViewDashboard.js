import React from 'react';
import './LitigationCasePageBasicViewDashboard.scss';
import {
    Container,
    Grid,
} from '@material-ui/core';
import LitigationCasePageBasicViewDashboardItem from '../LitigationCasePageBasicViewDashboardItem/LitigationCasePageBasicViewDashboardItem';
 
export default React.memo((props) => {

    const items = [
        {
            label: "Practice Area",
            value: props?.litigationCase?.legal_case_type?.name
        },
        {
            label: "Workflow Status",
            value: props?.litigationCase?.case_status?.name
        },
        {
            label: "Internal Ref Number",
            value: props?.litigationCase?.internalReference
        },
        {
            label: "Status Comments",
            value: props?.litigationCase?.statusComments
        },
        {
            label: "Matter Priority",
            value: props?.litigationCase?.priority
        },
        {
            label: "Stage",
            value: ""
        },
        {
            label: "Success Probability",
            value: props?.litigationCase?.success_probability ? props?.litigationCase?.success_probability?.success_probability_languages?.[0]?.name : ""
        },
        {
            label: "Arrival Date",
            value: props?.litigationCase?.caseArrivalDate
        },
        {
            label: "Filed On",
            value: props?.litigationCase?.arrivalDate
        },
        {
            label: "Due Date",
            value: props?.litigationCase?.dueDate
        },
        {
            label: "Closed On",
            value: props?.litigationCase?.closedOn
        },
        {
            label: "Client Name",
            value: "ddd",
            fullWidth: true
        }
    ];

    let rows = [];

    for (var i = 0; i < items.length;) {
        let row = items[i];
        let lastRow = items.length - 2 >= i ? items[i+1] : false;

        let record = <Grid
            container
            className="litigation-case-page-basic-view-field-row"
            key={"litigation-case-page-basic-view-field-row-" + i}
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
                            className="litigation-case-page-basic-view-field-row-item"
                        >
                            <LitigationCasePageBasicViewDashboardItem
                                row={row}
                            />
                        </Grid>
                    </Grid>
                )
                :
                (
                    <React.Fragment>
                        <Grid item sm={6}>
                            <LitigationCasePageBasicViewDashboardItem
                                row={row}
                            />
                        </Grid>
                        {
                            // do we still have one last item?
                            lastRow ?
                            <Grid item sm={6}>
                                <LitigationCasePageBasicViewDashboardItem
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
            i+=2;
        }
    }

    return (
        <Container
            id="litigation-case-page-basic-view-dashboard"
            maxWidth={false}
        >
            {rows}
        </Container>
    );
});
