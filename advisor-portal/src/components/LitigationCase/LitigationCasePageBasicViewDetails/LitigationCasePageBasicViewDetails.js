import React from 'react';
import './LitigationCasePageBasicViewDetails.scss';
import {
    Container,
    Grid,
    Typography
} from '@material-ui/core';
import LitigationCasePageBasicViewDashboardItem from '../LitigationCasePageBasicViewDashboardItem/LitigationCasePageBasicViewDashboardItem';
 
export default React.memo((props) => {

    const items = [
        {
            label: "Name",
            value: props?.litigationCase?.subject
        },
        {
            label: "Value",
            value: props?.litigationCase?.caseValue
        },
        {
            label: "Description",
            value: props?.litigationCase?.description,
            fullWidth: true
        },
        {
            label: "Latest Development",
            value: props?.litigationCase?.latest_development,
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
                            className="litigation-case-page-basic-view-field-row"
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
                        <Grid
                            item
                            sm={6}
                            className="litigation-case-page-basic-view-field-row"
                        >
                            <LitigationCasePageBasicViewDashboardItem
                                row={row}
                            />
                        </Grid>
                        {
                            // do we still have one last item?
                            lastRow ?
                            <Grid
                                item
                                sm={6}
                                className="litigation-case-page-basic-view-field-row"
                            >
                                <LitigationCasePageBasicViewDashboardItem
                                    row={lastRow}
                                />
                            </Grid>
                            :
                            <Grid
                                item
                                sm={6}
                                className="litigation-case-page-basic-view-field-row"
                            />
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
            id="litigation-case-page-basic-view-details"
            maxWidth={false}
        >
            {rows}
        </Container>
    );
});
