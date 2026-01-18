import React from 'react';

import './ActionsToolbar.scss';

import {
    Button,
    Container,
} from '@material-ui/core';

import EditIcon from '@material-ui/icons/Edit';

export default React.memo((props) => {
    return (
        <Container
            maxWidth={false}
            className="time-log-page-actions-toolbar btns-container no-padding-h"
        >
            <Button
                className="edit-btn"
                variant="contained"
                color="default"
                startIcon={<EditIcon />}
                onClick={() => props.openAddForm()}
            >
                Edit
            </Button>
        </Container>
    );
});
