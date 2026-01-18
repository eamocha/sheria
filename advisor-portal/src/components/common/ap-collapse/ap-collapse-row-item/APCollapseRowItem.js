import React from 'react';

import './APCollapseRowItem.scss';

import {
    Grid,
    Typography
} from '@material-ui/core';

export default React.memo((props) => {

    return (
        <Grid
            container
            className="ap-collapse-row-item"
        >
            {
                props?.item?.label?.length > 0 ?
                <Grid
                    item
                    sm={props?.item?.labelSize ? props?.item?.labelSize : props?.item?.fullWidth ? 2 : 4}
                >
                    <Typography
                        variant="body1"
                        className="ap-collapse-row-item-label"
                    >
                        {props?.item?.label + ":"}
                    </Typography>
                </Grid>
                :
                null
            }
            <Grid
                item
                sm={props?.item?.valueSize ? props?.item?.valueSize : props?.item?.fullWidth ? 10 : 8}
                className="ap-collapse-row-item-value-container"
            >
                <Typography
                    variant="body1"
                    className="ap-collapse-row-item-value"
                >
                    {props?.item?.value}
                </Typography>
            </Grid>
        </Grid>
    );
});
