import React from 'react';

import './APCollapseRow.scss';

import {
    Grid,
    Typography
} from '@material-ui/core';
 
export default React.memo((props) => {

    return (
        <Grid
            container
            className="ap-collapse-row"
        >
            {
                props?.fullWidth ? 
                (
                    <Grid
                        item
                        xs={12}
                    >
                        <Grid
                            container 
                            className="ap-collapse-row-item"
                        >
                            <Grid
                                item
                                sm={props?.row?.fullWidth ? 2 : 4}
                            >
                                <Typography
                                    variant="body1"
                                    className="ap-collapse-row-item-label"
                                >
                                    {props?.row?.label + ":"}
                                </Typography>
                            </Grid>
                            <Grid
                                item
                                sm={props?.row?.fullWidth ? 10 : 8}
                                className="ap-collapse-row-item-value-container"
                            >
                                <Typography
                                    variant="body1"
                                    className="ap-collapse-row-item-value"
                                >
                                    {props?.row?.value}
                                </Typography>
                            </Grid>
                        </Grid>
                    </Grid>
                )
                :
                (
                    <React.Fragment>
                        <Grid
                            item
                            sm={6}
                        >
                            <Grid
                                container 
                                className="ap-collapse-row-item"
                            >
                                <Grid
                                    item
                                    sm={props?.row?.fullWidth ? 2 : 4}
                                >
                                    <Typography
                                        variant="body1"
                                        className="ap-collapse-row-item-label"
                                    >
                                        {props?.row?.label + ":"}
                                    </Typography>
                                </Grid>
                                <Grid
                                    item
                                    sm={props?.row?.fullWidth ? 10 : 8}
                                    className="ap-collapse-row-item-value-container"
                                >
                                    <Typography
                                        variant="body1"
                                        className="ap-collapse-row-item-value"
                                    >
                                        {props?.row?.value}
                                    </Typography>
                                </Grid>
                            </Grid>
                        </Grid>
                        {
                            // do we still have one last item?
                            props?.lastRow ?
                            <Grid
                                item
                                sm={6}
                            >
                                <Grid
                                    container 
                                    className="ap-collapse-row-item"
                                >
                                    <Grid
                                        item
                                        sm={props?.row?.fullWidth ? 2 : 4}
                                    >
                                        <Typography
                                            variant="body1"
                                            className="ap-collapse-row-item-label"
                                        >
                                            {props?.row?.label + ":"}
                                        </Typography>
                                    </Grid>
                                    <Grid
                                        item
                                        sm={props?.row?.fullWidth ? 10 : 8}
                                        className="ap-collapse-row-item-value-container"
                                    >
                                        <Typography
                                            variant="body1"
                                            className="ap-collapse-row-item-value"
                                        >
                                            {props?.row?.value}
                                        </Typography>
                                    </Grid>
                                </Grid>
                            </Grid>
                            :
                            <Grid
                                item
                                sm={6}
                            />
                        }
                    </React.Fragment>
                )
            } 
        </Grid>
    );
});
