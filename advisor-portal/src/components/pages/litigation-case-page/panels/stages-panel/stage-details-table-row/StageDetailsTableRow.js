import React from 'react';

import './StageDetailsTableRow.scss';

import { Grid } from '@material-ui/core';
 
export default React.memo((props) => {

    return (
        <Grid
            container
            className="table-row"
        >
            {props?.children}
        </Grid>
    );
});
