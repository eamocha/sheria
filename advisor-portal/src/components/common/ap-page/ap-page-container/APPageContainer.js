import React, { useContext } from 'react';
import './APPageContainer.scss';
import {
    Container
} from '@material-ui/core';
import { Context } from '../../../../Store';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    return (
        <Container
            id={props?.id}
            maxWidth={false}
            className={"AP-page-container " + globalState.domDirection}
        >
            {props?.children}
        </Container>
    );
});
