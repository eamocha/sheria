import React, { useContext } from 'react';
import './APGlobalLoader.scss';
import {
    Backdrop,
    CircularProgress
} from '@material-ui/core';
import { Context } from '../../../Store';

export default React.memo((props) => {

    const [globalState] = useContext(Context);

    return (
        <Backdrop
            id="AP-global-loader"
            open={globalState?.globalLoader?.open}
            className={globalState?.modal?.open ? 'AP-global-loader-over-modal' : ''}
        >
            <CircularProgress
                size={50}
            />
        </Backdrop>
    );
});
