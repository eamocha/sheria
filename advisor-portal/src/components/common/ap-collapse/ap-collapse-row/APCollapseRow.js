import React from 'react';

import './APCollapseRow.scss';

import {
    Grid
} from '@material-ui/core';

import { APCollapseRowItem } from '../APCollapse';
 
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
                        <APCollapseRowItem
                            item={props?.row?.[0]}
                        />
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
                                <APCollapseRowItem
                                    item={props?.row?.[0]}
                                />
                            </Grid>
                        </Grid>
                        <Grid
                            item
                            sm={6}
                        >
                            <Grid
                                container 
                                className="ap-collapse-row-item"
                            >
                                <APCollapseRowItem
                                    item={props?.row?.[1]}
                                />
                            </Grid>
                        </Grid>
                    </React.Fragment>
                )
            } 
        </Grid>
    );
});
