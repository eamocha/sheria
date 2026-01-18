import React, { useContext } from 'react';
import './APGlobalWalkThrough.scss';
import {
    Backdrop,
    CircularProgress,
    IconButton,
    makeStyles
} from '@material-ui/core';
import { Context } from '../../../Store';
import CloseIcon from '@material-ui/icons/Close';

const useStyles = makeStyles({
    closeBtn: {
        position: 'absolute',
        right: 10,
        top: 10,
    }
});

export default React.memo((props) => {

    const [globalState, globalStateDispatcher] = useContext(Context);

    const classes = useStyles();

    const closeWalkThrough = () => {
        globalStateDispatcher({
            globalWalkThrough: {
                ...globalState?.globalWalkThrough,
                open:false
            }
        });
    }

    return (
        <Backdrop
            id="AP-global-walk-through"
            open={globalState?.globalWalkThrough?.open}
            className={globalState?.modal?.open ? 'AP-global-walk-through-modal' : ''}
        >
            <IconButton
                onClick={() => closeWalkThrough()}
                classes={{ root: classes.closeBtn }}
            >
                <CloseIcon />
            </IconButton>

        </Backdrop>
    );
});
