import React from 'react';

import './StageDetailsTableRowItem.scss';

import {
    Grid,
    Typography
} from '@material-ui/core';
 
export default React.memo((props) => {

    return (
        <Grid
            item
            sm={4}
            className="litigation-case-stage-details-table-row-item"
        >
            <Grid
                container
                className="litigation-case-stage-details-table-row-item-row"
            >
                <Grid
                    item
                    sm={4}
                >
                    <Typography
                        variant="body1"
                        className="label"
                    >
                        {props?.label + ": "}
                    </Typography>
                </Grid>
                <Grid
                    item
                    sm={8}
                >
                    <Typography
                        variant="body1"
                        className="value"
                    >
                        {props?.value}
                    </Typography>
                </Grid>
            </Grid>
        </Grid>
    );
});
