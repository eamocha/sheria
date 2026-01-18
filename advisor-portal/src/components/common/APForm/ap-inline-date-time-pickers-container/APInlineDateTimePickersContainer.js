import React from 'react';

import './APInlineDateTimePickersContainer.scss';

import {
    FormGroup,
    FormControl,
    Grid
} from '@material-ui/core';
 
export default React.memo((props) => {

    return (
        <FormGroup className="APInlineDateTimePickersContainer">
            <FormControl>
                <Grid
                    container
                >
                    <Grid
                        item
                        xs={9}
                    >
                        {props?.datePicker}
                    </Grid>
                    <Grid
                        item
                        xs={3}
                    >
                        {props?.timePicker}
                    </Grid>
                </Grid>
            </FormControl>
        </FormGroup>
    );;
});
