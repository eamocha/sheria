import React from 'react';

import {
    DialogTitle,
    IconButton,
} from '@material-ui/core';

import CloseIcon from '@material-ui/icons/Close';

import './DialogHeader.scss';
 
export default React.memo((props) => {
    return (
        <DialogTitle
            id="form-dialog-title"
            // className={classes.dialogTitle}
        >
            Stage histories
            <IconButton
                onClick={() => props.setModalState(false)}
                className="close-btn"
            >
                <CloseIcon />
            </IconButton>
        </DialogTitle>
    );
});
