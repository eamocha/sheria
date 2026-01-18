import React, { useContext } from 'react';
import './APSidebar.scss';
import { Container } from '@material-ui/core';
import APMainMenu from '../ap-main-menu/APMainMenu/APMainMenu.lazy';
import { Context } from '../../../Store';
 
export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    return (
        <Container
            id="AP-sidebar"
            className={globalState.domDirection}
            fixed
        >
            <APMainMenu />
        </Container>
    );
});
