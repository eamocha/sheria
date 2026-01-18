import React from 'react';

import './GeneralInfoItem.scss';
 
import {
    Grid,
    Typography
} from '@material-ui/core';
 
export default React.memo((props) => {

    return (
        <Grid
            container 
            className="field-row-item"
        >
            <Grid
                item
                sm={props?.row?.fullWidth ? 2 : 4}
            >
                <Typography
                    variant="body1"
                    className="field-row-item-label"
                >
                    {props?.row?.label + ":"}
                </Typography>
            </Grid>
            <Grid
                item
                sm={props?.row?.fullWidth ? 10 : 8}
            >
                 {
                props?.row.inline_edit
                  ? 
                  props?.row.inline_edit
                  :
                    <Typography
                        variant="body1"
                        className="field-row-item-value"
                    >
                        {props?.row?.value}
                    </Typography>
                }
            </Grid>
        </Grid>
    );
});
