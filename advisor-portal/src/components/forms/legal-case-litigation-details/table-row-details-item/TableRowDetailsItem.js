import React from 'react';

import {
    Grid,
    Typography
} from '@material-ui/core';

import './TableRowDetailsItem.scss';
 
export default React.memo((props) => {
    return (
        <Grid
            container
            className="details-row-container"
        >
            <Grid
                item
                sm={6}
            >
                <Typography
                    variant="body1"
                    className="details-row-label"
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
                    className="details-row-value"
                >
                    {props?.value?.length > 0 ? props.value : '-'}
                </Typography>
            </Grid>
        </Grid>
    );
});
