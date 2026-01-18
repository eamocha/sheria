import React from 'react';
import './APPageBody.scss';
import { Container } from '@material-ui/core';
 
export default React.memo((props) => {

    return (
        <Container
            maxWidth={false}
            className="AP-page-body"
        >
            {props?.children}
        </Container>
    );
});
