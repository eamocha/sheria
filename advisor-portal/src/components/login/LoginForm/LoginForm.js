import React, {
    useContext,
    useState
} from 'react';

import './LoginForm.scss';

import {
    Button,
    FormGroup,
} from '@material-ui/core';

import {
    TextValidator,
    ValidatorForm
} from 'react-material-ui-form-validator';

import { Context } from '../../../Store';

import Authentication from '../../../api/Authentication';

import { SESSION_KEYS } from '../../../Constants';

import {
    Link,
    useHistory
} from 'react-router-dom';

import { useTranslation } from 'react-i18next';

import { buildInstanceURL } from '../../../APHelpers';

export default React.memo((props) => {
    const [formData, setFormData] = useState({
        email: '',
        password: '',
        user_type: 'advisor'
    });

    const [globalState, globalStateDispatcher] = useContext(Context);
    
    const { t } = useTranslation();

    const history = useHistory();

    const handleFormDataChange = (e, stateKey) => {
        e.persist();

        setFormData(prevState => ({
            ...prevState,
            [stateKey]: e?.target?.value
        }));
    }

    const submit = (e) => {
        e.preventDefault();

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        sessionStorage.removeItem(SESSION_KEYS.accessToken);
        sessionStorage.removeItem(SESSION_KEYS.refreshToken);
        sessionStorage.removeItem(SESSION_KEYS.user);

        Authentication.login(formData).then((response) => {
            if (response?.status === 200) {
                if (response?.data?.password_reset_token) {
                    history.push(buildInstanceURL() + "/reset-password/" + response?.data?.password_reset_token);
                    return;
                }

                let userData = JSON.stringify(response?.data?.user);
                let accessToken = response?.data?.access_token;
                let refreshToken = response?.data?.refresh_token;

                sessionStorage.setItem(SESSION_KEYS.accessToken, accessToken);
                sessionStorage.setItem(SESSION_KEYS.refreshToken, refreshToken);
                sessionStorage.setItem(SESSION_KEYS.user, userData);

                globalState.broadCastChannel.postMessage({
                    'cmd': 'authenticateUser',
                    'data': {
                        'app4legal-accessToken': accessToken,
                        'app4legal-refreshToken': refreshToken,
                        'app4legal-user': userData,
                    }
                });
                
                if (response?.data?.password_reset_token) {
                    history.push(buildInstanceURL() + "/reset-password/" + response?.data?.password_reset_token);
                    
                    return;
                }

                globalStateDispatcher({
                    user: {
                        ...globalState?.user,
                        loggedIn: true,
                        data: response?.data?.user
                    }
                });

                if(globalState.urlToGo){
                    history.push(globalState.urlToGo);
                    globalStateDispatcher({
                        urlToGo: null
                    });
                }
               
            }
        }).catch((error) => {
            // if no response, so there is a network error
            if (!error?.response) {
                props.setValidationMessage('Failed to connect to the server. Please check your connection or try again later.');
            } else {
                if (error.response?.status >= 500) {
                    props.setValidationMessage('Server Error. Please try again later or contact support.');
                } else if (error?.response?.data?.error === 'invalid_grant') {
                    props.setValidationMessage(t('invalid_grant'));
                } else {
                    props.setValidationMessage(error.response?.data?.message);
                }
            }

            props.setIsLoginFailed(true);
        }).finally(() => {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: false
                }
            });
        });
    }

    return (
        <ValidatorForm
            onSubmit={(e) => submit(e)}
        >
            <FormGroup>
                <TextValidator
                    label={t("email") + " *"}
                    variant="outlined"
                    value={formData.email}
                    onChange={(e) => handleFormDataChange(e, 'email')}
                    validators={['required']}
                    errorMessages={['This field is required.']}
                />
            </FormGroup>
            <FormGroup>
                <TextValidator
                    type="password"
                    label={t("password") + " *"}
                    variant="outlined"
                    value={formData.password}
                    onChange={(e) => handleFormDataChange(e, 'password')}
                    validators={['required']}
                    errorMessages={['This field is required.']}
                />
            </FormGroup>
            <FormGroup>
                <Link
                    className="primary-link"
                    to={`${buildInstanceURL()}/request-password-reset`}
                >{t("forgot_your_password_question")}</Link>
            </FormGroup>
            <FormGroup>
                <Button
                    variant="contained"
                    color="primary"
                    type="submit"
                >
                    {t('login')}
                </Button>
            </FormGroup>
        </ValidatorForm>
    );
});
