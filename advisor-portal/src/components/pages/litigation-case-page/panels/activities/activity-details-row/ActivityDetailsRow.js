import React from 'react';

import './ActivityDetailsRow.scss';

import {
    Grid,
    Typography,
} from '@material-ui/core';
 
export default React.memo((props) => {
    return (
        <Grid
            item
            sm={4}
            className="litigation-case-activity-details-row"
        >
            <Grid
                container
                className="grid-row"
            >
                <Grid
                    item
                    sm={6}
                >
                    <Typography
                        variant="body1"
                        className="grid-row-label"
                    >
                        {props?.label}
                    </Typography>
                </Grid>
                <Grid
                    item
                    sm={6}
                >
                    <Typography
                        variant="body1"
                        className="grid-row-value"
                    >
                        {props?.value}
                    </Typography>
                </Grid>
            </Grid>
        </Grid>
    );
});
