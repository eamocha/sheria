import React, {
    useContext,
    useEffect
} from 'react';
import './AppLoader.scss';
import {
    Container,
    createMuiTheme,
    jssPreset,
    MuiThemeProvider,
    StylesProvider
} from '@material-ui/core';
import { Context, initialGlobalState } from '../../../Store';
import APRouter from '../ap-router/APRouter.lazy';
import APGlobalLoader from '../APGlobalLoader/APGlobalLoader.lazy';
import APNotificationBar from '../APNotificationBar/APNotificationBar.lazy';
import i18n from "i18next";
import { create } from 'jss';
import rtl from "jss-rtl";
import { useTranslation } from 'react-i18next';
import { chageAppDirection } from '../../../i18n';
import axios from 'axios';
import { BROADCAST_CHANNEL, SESSION_KEYS } from '../../../Constants';
import Authentication from '../../../api/Authentication';
import { useHistory } from 'react-router';
import { buildInstanceURL } from '../../../APHelpers';
import { useIdleTimer } from 'react-idle-timer';
import APGlobalWalkThrough from '../APGlobalWalkThrough/APGlobalWalkThrough.lazy';

export default React.memo((props) => {
    const { t } = useTranslation();
    var theme = createMuiTheme({
        direction: i18n.dir(),
        overrides: {
            MuiFormControl: {
                root: {
                    width: '100%'
                }
            },
            MuiFormGroup: {
                root: {
                    marginBottom: '20px'
                }
            },
            MuiDialogTitle: {
                root: {
                    background: '#f5f5f5',
                    borderBottom: '1px solid #c8c8c8',
                    position: 'relative'
                }
            },
            MuiDialogContent: {
                root: {
                    paddingTop: 20
                }
            },
            MuiDialogActions: {
                root: {
                    background: '#f5f5f5',
                    borderTop: '1px solid #c8c8c8'
                }
            }
        },
        palette: {
            primary: {
                light: '#3b7fc4',
                main: '#205081',
            },
            secondary: {
                // main: '#3b7fc4',
                main: '#c9282d',
                light: '#c9282d'
            },
            error: {
                main: '#c9282d'
            }
        },
        typography: {
            body1: {
                whiteSpace: 'pre-wrap',
            },
            fontFamily: [
                '-apple-system',
                'BlinkMacSystemFont',
                '"Segoe UI"',
                'Roboto',
                '"Helvetica Neue"',
                'Arial',
                'sans-serif',
                '"Apple Color Emoji"',
                '"Segoe UI Emoji"',
                '"Segoe UI Symbol"',
            ].join(','),
        },
    });

    var jss = create({ plugins: [...jssPreset().plugins, rtl()] });
    
    const [globalState, globalStateDispatcher] = useContext(Context);

    const history = useHistory();

    useEffect(() => {
        chageAppDirection();
        globalStateDispatcher({ domDirection: i18n.dir() })
        jss = create({ plugins: [...jssPreset().plugins, rtl()] });
        theme.direction = i18n.dir();
    }, [t]);

    axios.interceptors.request.use(
        (request) => {
            // we can appends the headers here instead of the components
            // we can handle (show) the loader view here instead of the components
            let accessToken = sessionStorage.getItem(SESSION_KEYS.accessToken);
            
            if (accessToken && !request.url.includes('renew-access-token')){
                request.headers['Authorization'] = 'Bearer ' + accessToken;
            }

            return request;
        },
        (error) => {
            return Promise.reject(error);
        }
    );

    axios.interceptors.response.use(
        (response) => {
            // we can handle the success messages here instead of the components
            // we can handle (hide) the loader view here instead of the components
            return response;
        },
        (error) => {
            const originalRequest = error.config;

            if (error?.response?.status === 401 && originalRequest.url.includes('renew-access-token')) {
                invalidateSessionAndRedirectToLogin();
                return;
            }

            if (originalRequest.url.includes('logout')) {
                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        text:  t('session_lost'),
                        severity: "error"
                    }
                });
                
                redirectToLogin();
                
                return;
            }

            if (error?.response?.status === 401 && !originalRequest._retry) {
                originalRequest._retry = true;

                return Authentication.renewAccessToken({ refreshToken: sessionStorage.getItem(SESSION_KEYS.refreshToken) })
                    .then((response) => {
                        if (response?.status === 200) {
                            let accessToken = response?.data?.access_token;
                            let refreshToken = response?.data?.refresh_token;
                            sessionStorage.setItem(SESSION_KEYS.accessToken, accessToken);
                            sessionStorage.setItem(SESSION_KEYS.refreshToken, refreshToken);
                            return axios(originalRequest);
                        }
                    });
            } else {
                // return Error object with Promise
                return Promise.reject(error);
            }
        }
    );

    const handleOnIdle = event => {
        // console.log('user is idle', event)
        // console.log('last active', getLastActiveTime())

        invalidateSessionAndRedirectToLogin();
    }

    const { getRemainingTime, getLastActiveTime } = useIdleTimer({
        timeout: 1000 * 60 * 3, // 3 min
        onIdle: handleOnIdle,
        crossTab: {
            type: undefined,
            channelName: 'a4l-ap-idle-timer',
            emitOnAllTabs: true
        }
    });

    const invalidateSessionAndRedirectToLogin = () => {
        Authentication.logOut().then((response) => {
            if (response?.status === 200) {
                redirectToLogin();
            }
        }).catch((error) => {
            // if no response, so there is a network error

        }).finally(() => {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: false
                }
            });
        });
    }

    const redirectToLogin = () => {
        sessionStorage.removeItem(SESSION_KEYS.accessToken);
        sessionStorage.removeItem(SESSION_KEYS.refreshToken);
        sessionStorage.removeItem(SESSION_KEYS.user);

        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal
            },
            user: {
                loggedIn: false,
                data: {}
            }
        });

        globalState.broadCastChannel.postMessage({
            'cmd': BROADCAST_CHANNEL.logoutUser
        });

        history?.push(`${buildInstanceURL()}/login`);
    }

    return (
        <StylesProvider jss={jss}>
            <MuiThemeProvider theme={theme}>
                <Container
                    maxWidth={false}
                    className="h-100 no-padding-h"
                >
                    <APRouter />
                    <APGlobalLoader />
                    <APGlobalWalkThrough/>
                    <APNotificationBar />
                </Container>
            </MuiThemeProvider>
        </StylesProvider>
    );
});
