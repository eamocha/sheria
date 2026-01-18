import { Container } from '@material-ui/core';
import React from 'react';
import './APPageActionsToolbar.scss';
 
export default React.memo((props) => {

    return (
        <Container
            maxWidth={false}
            className="AP-page-actions-toolbar"
        >
            {props?.children}
        </Container>
    );
});
