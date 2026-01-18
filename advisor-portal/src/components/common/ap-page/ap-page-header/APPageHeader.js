import React from 'react';
import './APPageHeader.scss';
import { Container } from '@material-ui/core';
 
export default React.memo((props) => {

    return (
        <Container
            maxWidth={false}
            className="AP-page-header"
        >
            {props?.children}
        </Container>
    );
});
