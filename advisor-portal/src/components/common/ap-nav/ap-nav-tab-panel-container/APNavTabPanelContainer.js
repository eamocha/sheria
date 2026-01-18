import React from 'react';

import './APNavTabPanelContainer.scss';

import {
    makeStyles
} from '@material-ui/core';

import { TabPanel } from '@material-ui/lab';

const useStyles = makeStyles({
    container: {
        padding: '0px 5px'
    }
});

export default React.memo((props) => {
    const classes = useStyles();

    return (
        <TabPanel
            {...props}
            className={classes.container}
        >
            {props?.children}
        </TabPanel>
    );
});
