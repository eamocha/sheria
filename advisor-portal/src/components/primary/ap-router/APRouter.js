import React, {
    useContext, useEffect, useState
} from 'react';

import { Container } from '@material-ui/core';

import {
    BrowserRouter,
    Redirect,
    Route,
    Switch
} from 'react-router-dom';

import HomePage from '../../../pages/home/HomePage';

import Login from '../../../pages/Login/Login';

import { Context, initialGlobalState } from '../../../Store';

import APSidebar from './../ap-sidebar/APSidebar';

import Profile from './../../../pages/Profile/Profile';

import LitigationCasesPage from '../../../pages/litigation-cases-page/LitigationCasesPage.lazy';
import LitigationCasePage from '../../../pages/litigation-case-page/LitigationCasePage.lazy';

import CorporateMattersPage from '../../../pages/corporate-matters-page/CorporateMattersPage.lazy';
import CorporateMatterPage from '../../../pages/corporate-matter-page/CorporateMatterPage.lazy';

import HearingsPage from '../../../pages/hearings-page/HearingsPage.lazy';
// import TimeLogPage from '../../../pages/time-log-page/TimeLogPage.lazy';

import AdvisorTasksPage from '../../../pages/advisor-tasks-page/AdvisorTasksPage.lazy';
import AdvisorTaskPage from '../../../pages/advisor-task-page/AdvisorTaskPage.lazy';

import TimeLogsPage from '../../../pages/time-logs-page/TimeLogsPage.lazy';
import TimeLogPage from '../../../pages/time-log-page/TimeLogPage.lazy';

import RequestPasswordResetPage from '../../../pages/request-password-reset-page/RequestPasswordResetPage.lazy';
import ResetPasswordPage from '../../../pages/reset-password-page/ResetPasswordPage.lazy';

// import PageNotFound from '../../../pages/Errors/PageNotFound/PageNotFound';

import DashboardPreferencesPage from '../../../pages/dashboard-preferences-page/DashboardPreferencesPage';
import { BROADCAST_CHANNEL, SESSION_KEYS } from '../../../Constants';
import Authentication from '../../../api/Authentication';
import APModalForm from '../ap-modal-form/APModalForm.lazy';
import { buildInstanceURL } from '../../../APHelpers';
import { createBrowserHistory } from "history";


export default () => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const [instanceName, setInstanceName] = useState(null);

    useEffect(() => {
        if (instanceName == null) {
            Authentication.getInstanceName().then((response) => {
                let instanceName = response.data.data.instance_name;
                setInstanceName(instanceName);
                var bcChannel = new BroadcastChannel(BROADCAST_CHANNEL.channelName + instanceName);

                globalStateDispatcher({ broadCastChannel: bcChannel });

                // if accessToken is not set, ask the other tabs to share you the accessToken & other data
                if (sessionStorage.getItem(SESSION_KEYS.accessToken) === null || sessionStorage.getItem(SESSION_KEYS.accessToken).length <= 0) {
                    bcChannel.postMessage({
                        'cmd': BROADCAST_CHANNEL.requestUserAuthenticationData
                    });
                }

                bcChannel.onmessage = (e) => {

                    if (e.data.cmd === BROADCAST_CHANNEL.logoutUser) {
                        deAuthenticateUser()
                    }
                    if (e.data.cmd === BROADCAST_CHANNEL.requestUserAuthenticationData) {
                        sendUserAuthenticationData(bcChannel);
                    } else if (e.data.cmd === BROADCAST_CHANNEL.responseUserAuthenticationData) {
                        recieveUserAuthenticationData(e.data.data);
                    }
                }
            }).catch(error => {
                console.log(error);
            });
        }
    }, [instanceName]);

    const deAuthenticateUser = () => {
        sessionStorage.removeItem(SESSION_KEYS.accessToken);
        sessionStorage.removeItem(SESSION_KEYS.refreshToken);
        sessionStorage.removeItem(SESSION_KEYS.user);

        globalStateDispatcher({
            user: {
                ...initialGlobalState.user,
                loggedIn: false,
                data: null
            }
        });
    }

    const recieveUserAuthenticationData = (data) => {
        if (data[SESSION_KEYS.accessToken] == null || data[SESSION_KEYS.accessToken] === 'null') {
            return false;
        }

        sessionStorage.setItem(SESSION_KEYS.accessToken, data[SESSION_KEYS.accessToken]);
        sessionStorage.setItem(SESSION_KEYS.refreshToken, data[SESSION_KEYS.refreshToken]);
        sessionStorage.setItem(SESSION_KEYS.user, data[SESSION_KEYS.user]);

        globalStateDispatcher({
            user: {
                ...globalState?.user,
                loggedIn: true,
                data: JSON.parse(data[SESSION_KEYS.user])
            }
        });
    }

    const sendUserAuthenticationData = (bcChannel) => {
        bcChannel.postMessage({
            cmd: BROADCAST_CHANNEL.responseUserAuthenticationData,
            data: {
                [SESSION_KEYS.accessToken]: sessionStorage.getItem(SESSION_KEYS.accessToken),
                [SESSION_KEYS.refreshToken]: sessionStorage.getItem(SESSION_KEYS.refreshToken),
                [SESSION_KEYS.user]: sessionStorage.getItem(SESSION_KEYS.user),
            }
        });
    }

    const browserHistory = createBrowserHistory();

    useEffect(() => {
        if (browserHistory.location.pathname != '/login')
            globalStateDispatcher({
                urlToGo: browserHistory.location.pathname
            });

    }, []);

    return (
        <BrowserRouter>
            {
                globalState?.user?.loggedIn ?
                    <APSidebar />
                    :
                    null
            }
            <Container
                maxWidth={false}
                className="h-100 no-padding-h"
            >
                <Switch>
                    <Route
                        path={`${buildInstanceURL()}/`}
                        exact
                        render={(props) => globalState?.user?.loggedIn ?
                            <HomePage />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/login`}
                            />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/corporate-matters`}
                        exact
                        render={(props) => globalState?.user?.loggedIn ?
                            <CorporateMattersPage />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/login`}
                            />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/corporate-matter/:id`}
                        exact
                        render={(props) => globalState?.user?.loggedIn ?
                            <CorporateMatterPage
                                {...props}
                            />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/login`}
                            />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/corporate-matter/:nav_panel_name/:id`}
                        exact
                        render={
                            (props) => globalState?.user?.loggedIn ?
                                <CorporateMatterPage
                                    {...props}
                                />
                                :
                                <Redirect
                                    to={`${buildInstanceURL()}/login`}
                                />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/litigation-cases`}
                        exact
                        render={(props) => globalState?.user?.loggedIn ?
                            <LitigationCasesPage />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/login`}
                            />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/litigation-case/:id`}
                        exact
                        render={(props) => globalState?.user?.loggedIn ?
                            <LitigationCasePage
                                {...props}
                            />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/login`}
                            />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/litigation-case/:nav_panel_name/:id`}
                        exact
                        render={
                            (props) => globalState?.user?.loggedIn ?
                                <LitigationCasePage
                                    {...props}
                                />
                                :
                                <Redirect
                                    to={`${buildInstanceURL()}/login`}
                                />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/hearings`}
                        exact
                        render={(props) => globalState?.user?.loggedIn ?
                            <HearingsPage />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/login`}
                            />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/hearings/:filter`}
                        exact
                        render={(props) => globalState?.user?.loggedIn ?
                            <HearingsPage />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/login`}
                            />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/tasks`}
                        exact
                        render={(props) => globalState?.user?.loggedIn ?
                            <AdvisorTasksPage
                                {...props}
                            />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/login`}
                            />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/tasks/:filter`}
                        exact
                        render={(props) => globalState?.user?.loggedIn ?
                            <AdvisorTasksPage
                                {...props}
                            />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/login`}
                            />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/task/:id`}
                        exact
                        render={(props) => globalState?.user?.loggedIn ?
                            <AdvisorTaskPage />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/login`}
                            />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/time-logs`}
                        exact
                        render={(props) => globalState?.user?.loggedIn ?
                            <TimeLogsPage />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/login`}
                            />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/time-log/:id`}
                        render={(props) => globalState?.user?.loggedIn ?
                            <TimeLogPage />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/login`}
                            />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/profile`}
                        render={(props) => globalState?.user?.loggedIn ?
                            <Profile />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/login`}
                            />
                        }
                    />
                    {/* <Route
                        path={'/error/page-not-found'}
                        render={(props) => <PageNotFound />}
                    /> */}
                    <Route
                        path={`${buildInstanceURL()}/login`}
                        render={(props) => !globalState?.user?.loggedIn ?
                            <Login
                                {...props} />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/`}
                            />
                        }
                    />
                    <Route
                        path={`${buildInstanceURL()}/request-password-reset`}
                        render={(props) => <RequestPasswordResetPage {...props} />}
                    />
                    <Route
                        path={`${buildInstanceURL()}/reset-password/:token`}
                        render={(props) => <ResetPasswordPage {...props} />}
                    />

                    <Route
                        path={`${buildInstanceURL()}/dashboard-preferences`}
                        exact
                        render={(props) => globalState?.user?.loggedIn ?
                            <DashboardPreferencesPage
                                {...props}
                            />
                            :
                            <Redirect
                                to={`${buildInstanceURL()}/login`}
                            />
                        }
                    />

                </Switch>
            </Container>

            <APModalForm />
        </BrowserRouter>
    );
};
