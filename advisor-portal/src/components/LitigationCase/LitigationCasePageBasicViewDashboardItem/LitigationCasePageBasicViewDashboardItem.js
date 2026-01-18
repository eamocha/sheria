import React from 'react';
import './LitigationCasePageBasicViewDashboardItem.scss';
import {
    Grid,
    Typography
} from '@material-ui/core';
 
export default React.memo((props) => {

    return (
        <Grid
            container 
            className="litigation-case-page-basic-view-field-row-item"
        >
            <Grid
                item
                sm={props?.row?.fullWidth ? 3 : 6}
            >
                <Typography
                    variant="body1"
                    className="litigation-case-page-basic-view-field-row-item-label"
                >
                    {props?.row?.label + ":"}
                </Typography>
            </Grid>
            <Grid
                item
                sm={props?.row?.fullWidth ? 9 : 6}
            >
                <Typography
                    variant="body1"
                    className="litigation-case-page-basic-view-field-row-item-value"
                >
                    {props?.row?.value}
                </Typography>
            </Grid>
        </Grid>
    );
});
