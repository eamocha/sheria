import React, {
    useContext,
    useEffect,
    useState
} from 'react';
import './LitigationCasePageHeaderNav.scss';
import {
    AppBar,
    Container,
    makeStyles,
    Tabs
} from '@material-ui/core';
import APNavLinkTab from '../../common/ap-nav/ap-nav-tab-link/APNavTabLink';
import { Context } from '../../../Store';

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
        paddingTop: 20,
        marginTop: 20
    }
});
 
export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    
    const [litigationCase, ] = useState(props?.litigationCase);
    const [navTabName, ] = useState(props?.navTabName);
    const [activeNavTab, setActiveNavTab] = useState(globalState?.litigationCasePage?.activeNavTabIndex);

    useEffect(() => {

        if (navTabName) {
            detectActiveNavTab();
        }
    }, []);

    const handleNavTabChange = (e, value) => {
        setActiveNavTab(value);

        globalStateDispatcher({
            litigationCasePage: {
                ...globalState?.litigationCasePage,
                activeNavTabIndex: value
            }
        });
    }

    /**
     * Detect the active tab index according to the tab_name that has been sent via URL
     * 
     * Tabs Index:
     * [0] => Litigation Case View
     * [1] => Litigation Data
     * [2] => Activities
     * [3] => Related Tasks
     * [4] => Related Documents
     */
    const detectActiveNavTab = () => {
        switch (navTabName) {
            case 'litigation-data':
                setActiveNavTab(1);

                globalStateDispatcher({
                    litigationCasePage: {
                        ...globalState?.litigationCasePage,
                        activeNavTabIndex: 1
                    }
                });
                break;
            
            case 'activities':
                setActiveNavTab(2);

                globalStateDispatcher({
                    litigationCasePage: {
                        ...globalState?.litigationCasePage,
                        activeNavTabIndex: 2
                    }
                });
                break;

            case 'related-tasks':
                setActiveNavTab(3);

                globalStateDispatcher({
                    litigationCasePage: {
                        ...globalState?.litigationCasePage,
                        activeNavTabIndex: 3
                    }
                });
                break;
            
            case 'related-documents':
                setActiveNavTab(4);

                globalStateDispatcher({
                    litigationCasePage: {
                        ...globalState?.litigationCasePage,
                        activeNavTabIndex: 4
                    }
                });
                break;
            
            default:
                setActiveNavTab(0);

                globalStateDispatcher({
                    litigationCasePage: {
                        ...globalState?.litigationCasePage,
                        activeNavTabIndex: 0
                    }
                });
                break;
        }
    }

    const classes = useStyles();

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
                    value={activeNavTab}
                    onChange={handleNavTabChange}
                >
                    <APNavLinkTab
                        label="General Info"
                        href={"/litigation-case/" + litigationCase?.id}
                        className={classes.tabBtn}
                    />
                    <APNavLinkTab
                        label="Litigation Stages"
                        href={"/litigation-case/litigation-data/" + litigationCase?.id}
                        className={classes.tabBtn}
                    />
                    <APNavLinkTab
                        label="Activities"
                        href={"/litigation-case/activities/" + litigationCase?.id}
                        className={classes.tabBtn}
                    />
                    <APNavLinkTab
                        label="Related Tasks"
                        href={"/litigation-case/related-tasks/" + litigationCase?.id}
                        className={classes.tabBtn}
                    />
                    <APNavLinkTab
                        label="Related Documents"
                        href={"/litigation-case/related-documents/" + litigationCase?.id}
                        className={classes.tabBtn}
                    />
                </Tabs>
            </AppBar>
        </Container>
    );
});
