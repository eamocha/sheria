import React, { useContext } from 'react';

import './APNotificationBar.scss';

import {
    makeStyles,
    Snackbar,
    IconButton,
} from '@material-ui/core';

import { Alert } from '@material-ui/lab';

import CloseIcon from '@material-ui/icons/Close';

import { Context } from '../../../Store';

const useStyles = makeStyles({
    snackBarAnchor: {
        left: 'unset',
        right: 30,
        bottom: 'unset',
        top: 40,
        transform: 'none'
    },
    snackBarRoot: {
        alignItems: 'start',
        justifyContent: 'flex-end'
    },
    notificationText: {
        margin: 0,
        marginRight: 20,
        display: 'inline-block'
    }
});

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const handleNotificationBarClose = (e, reason) => {
        if (reason === 'clickaway') {
            return;
        }

        globalStateDispatcher({
            notificationBar: {
                ...globalState?.notificationBar,
                open: false
            }
        });
    };

    const classes = useStyles();

    return (
        <Snackbar
            id='APNotificationBar'
            open={globalState?.notificationBar?.open}
            autoHideDuration={6000}
            onClose={handleNotificationBarClose}
            classes={{
                root: classes.snackBarRoot,
                anchorOriginBottomCenter: classes.snackBarAnchor
            }}
        >
            <Alert
                severity={globalState?.notificationBar?.severity ?? 'error'}
                variant="filled"
            >
                <p
                    className={classes.notificationText}
                >
                    {globalState?.notificationBar?.text}
                </p>
                <IconButton
                    size="small"
                    aria-label="close"
                    color="inherit"
                    className={"close-notification " + globalState.domDirection}
                    onClick={handleNotificationBarClose}
                >
                    <CloseIcon
                        fontSize="small"
                    />
                </IconButton>
            </Alert>
        </Snackbar>
    );
});
