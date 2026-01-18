import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './Nav.scss';

import {
    AppBar,
    Container,
    makeStyles,
    Tabs
} from '@material-ui/core';

import { APNavTabLink } from './../../../common/ap-nav/APNav';

import { Context } from '../../../../Store';
import { useHistory } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { buildInstanceURL } from '../../../../APHelpers';

const useStyles = makeStyles({
    appBar: {
        position: 'static',
        backgroundColor: 'transparent',
        maxWidth: '1000px',
        boxShadow: 'inset 0 -1px 0 0 #E6ECF0',
    },
    tabBtn: {
        fontWeight: 600,
        fontSize: 16
    },
    bodyContainer: {
        paddingLeft: 0,
        paddingRight: 0,
    }
});
 
export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    
    const [corporateMatter, ] = useState(props?.corporateMatter);

    const history = useHistory();

    const classes = useStyles();
    
    const { t } = useTranslation();

    useEffect(() => {
        window.dispatchEvent(new Event('resize'));
    }, [history.location.pathname]);

    const handleNavPanelChange = (e, value) => {
        globalStateDispatcher({
            corporateMatterPage: {
                ...globalState?.corporateMatterPage,
                activeNavPanelIndex: value
            }
        });
    }

    return (
        <Container
            maxWidth={false}
            className={classes.bodyContainer}
        >
            <AppBar
                classes={{
                    positionFixed: classes.appBar
                }}
            >
                <Tabs
                    indicatorColor="primary"
                    textColor="primary"
                    variant="fullWidth"
                    value={history.location.pathname}
                    onChange={handleNavPanelChange}
                >
                    <APNavTabLink
                        label={t('general_info')}
                        to={`${buildInstanceURL()}/corporate-matter/${corporateMatter?.id}`}
                        value={`${buildInstanceURL()}/corporate-matter/${corporateMatter?.id}`}
                        className={classes.tabBtn}
                    />
                    <APNavTabLink
                        label={t('related_tasks')}
                        to={`${buildInstanceURL()}/corporate-matter/related-tasks/${corporateMatter?.id}`}
                        value={`${buildInstanceURL()}/corporate-matter/related-tasks/${corporateMatter?.id}`}
                        className={classes.tabBtn}
                    />
                    <APNavTabLink
                        label={t('related_documents')}
                        to={`${buildInstanceURL()}/corporate-matter/related-documents/${corporateMatter?.id}`}
                        value={`${buildInstanceURL()}/corporate-matter/related-documents/${corporateMatter?.id}`}
                        className={classes.tabBtn}
                    />
                </Tabs>
            </AppBar>
        </Container>
    );
});
