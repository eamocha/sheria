import './ResetPasswordForm.scss';
import React, {
    useContext,
    useState
} from 'react';

import {
    Button,
    FormGroup,
    FormLabel,
} from '@material-ui/core';

import {
    TextValidator,
    ValidatorForm
} from 'react-material-ui-form-validator';


import { Context } from '../../../../Store';
import Authentication from '../../../../api/Authentication';
import { SESSION_KEYS } from '../../../../Constants';
import AdvisorUser from '../../../../api/AdvisorUser';
import { Redirect, useHistory, useRouteMatch } from 'react-router';
import jwtDecode from 'jwt-decode';
import { useTranslation } from 'react-i18next';
import { buildInstanceURL } from '../../../../APHelpers';

export default React.memo((props) => {

    const routeMatches = useRouteMatch();
    const resetToken = routeMatches.params?.token;

    var decoded = jwtDecode(resetToken);
    var nowTime = Math.floor(Date.now() / 1000);
    var tokenExpired = decoded.exp < nowTime;
    props.setIsTokenExpired(tokenExpired);

    const [formData, setFormData] = useState({
        password: '',
        confirm_password: '',
        token: resetToken
    });

    const [globalState, globalStateDispatcher] = useContext(Context);

    const history = useHistory();

    const [t] = useTranslation();

    const handleFormDataChange = (e, stateKey) => {
        e.persist();

        setFormData(prevState => ({
            ...prevState,
            [stateKey]: e?.target?.value
        }));
    }

    const submit = (e) => {
        e.preventDefault();
        props.setError(false);
        if (formData.password != formData.confirm_password) {
            props.setValidationMessage('Password and confirm password not matched');
            props.setError(true);
            return;
        }

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        Authentication.updatePassword(decoded.user_id, formData).then((response) => {
            if (response?.status === 200) {
                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        text: "Password has been reset successfully",
                        severity: "success"
                    }
                });
                history.push(`${buildInstanceURL()}/login`);
            }
        }).catch((error) => {
            // if no response, so there is a network error
            if (!error?.response) {
                props.setValidationMessage('Failed to connect to the server. Please check your connection or try again later.');
            } else {
                if (error.response?.status >= 500) {
                    props.setValidationMessage('Server Error. Pleas try again later or contact support.');
                } else {
                    props.setValidationMessage(error.response?.data?.message);
                }
            }

            props.setError(true);
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
                <FormLabel>
                    <h2>{t("reset_password")}</h2>
                </FormLabel>
            </FormGroup>
            <FormGroup>
                <FormLabel>
                    {t("please_enter_a_new_password")}
                </FormLabel>
            </FormGroup>
            <FormGroup>
                <TextValidator
                    type="password"
                    label={t("new_password") + " *"}
                    variant="outlined"
                    value={formData.password}
                    onChange={(e) => handleFormDataChange(e, 'password')}
                    validators={['required']}
                    errorMessages={['This field is required.']}
                />
            </FormGroup>
            <FormGroup>
                <TextValidator
                    type="password"
                    label={t("confirm_new_password") + " *"}
                    variant="outlined"
                    value={formData.confirm_password}
                    onChange={(e) => handleFormDataChange(e, 'confirm_password')}
                    validators={['required']}
                    errorMessages={['This field is required.']}
                />
            </FormGroup>

            <FormGroup>
                <Button
                    variant="contained"
                    color="primary"
                    type="submit"
                >
                    {t("reset")}
                </Button>
            </FormGroup>

        </ValidatorForm>
    );
});
