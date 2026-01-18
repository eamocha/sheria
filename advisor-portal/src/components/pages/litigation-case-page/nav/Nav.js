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

import { Context } from './../../../../Store';

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
    
    const [litigationCase, ] = useState(props?.litigationCase);

    const history = useHistory();

    useEffect(() => {
        window.dispatchEvent(new Event('resize'));
    }, [history.location.pathname]);

    const handleNavPanelChange = (e, value) => {
        globalStateDispatcher({
            litigationCasePage: {
                ...globalState?.litigationCasePage,
                activeNavPanelIndex: value
            }
        });
    }

    const classes = useStyles();
    const { t } = useTranslation();
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
                        to={`${buildInstanceURL()}/litigation-case/${litigationCase?.id}`}
                        value={`${buildInstanceURL()}/litigation-case/${litigationCase?.id}`}
                        className={classes.tabBtn}
                    />
                    <APNavTabLink
                        label={t('litigation_stages')}
                        to={`${buildInstanceURL()}/litigation-case/litigation-stages/${litigationCase?.id}`}
                        value={`${buildInstanceURL()}/litigation-case/litigation-stages/${litigationCase?.id}`}
                        className={classes.tabBtn}
                    />
                    <APNavTabLink
                        label={t('activities')}
                        to={`${buildInstanceURL()}/litigation-case/activities/${litigationCase?.id}`}
                        value={`${buildInstanceURL()}/litigation-case/activities/${litigationCase?.id}`}
                        className={classes.tabBtn}
                    />
                    <APNavTabLink
                        label={t('related_tasks')}
                        to={`${buildInstanceURL()}/litigation-case/related-tasks/${litigationCase?.id}`}
                        value={`${buildInstanceURL()}/litigation-case/related-tasks/${litigationCase?.id}`}
                        className={classes.tabBtn}
                    />
                    <APNavTabLink
                        label={t('related_documents')}
                        to={`${buildInstanceURL()}/litigation-case/related-documents/${litigationCase?.id}`}
                        value={`${buildInstanceURL()}/litigation-case/related-documents/${litigationCase?.id}`}
                        className={classes.tabBtn}
                    />
                </Tabs>
            </AppBar>
        </Container>
    );
});
