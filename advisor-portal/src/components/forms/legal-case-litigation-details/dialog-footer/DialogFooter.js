import React from 'react';

import {
    Button,
    DialogActions,
} from '@material-ui/core';

import './DialogFooter.scss';
 
export default React.memo((props) => {
    return (
        <DialogActions>
            <Button
                color="primary"
                variant="contained"
                onClick={() => props.setModalState(false)}
                form="case-litigation-details-histories-form"
            >
                Save
            </Button>
            <Button
                color="secondary"
                onClick={() => props.setModalState(false)}
            >
                Cancel
            </Button>
        </DialogActions>
    );
});
