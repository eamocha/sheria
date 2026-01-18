import './RequestPasswordResetForm.scss';

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
import { useTranslation } from 'react-i18next';



export default React.memo((props) => {

    const [formData, setFormData] = useState({
        email: '',
        user_type: 'advisor'
    });

    const [globalState, globalStateDispatcher] = useContext(Context);

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

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        Authentication.requestPasswordReset(formData).then((response) => {
            if (response?.status === 200) {
                props.setSuccess(true);
            }
        }).catch((error) => {
            console.log(error)
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
                    {t("reset_password")}
                </FormLabel>
            </FormGroup>
            <FormGroup>
                <TextValidator
                    label={t("email_address") + " *"}
                    variant="outlined"
                    value={formData.email}
                    onChange={(e) => handleFormDataChange(e, 'email')}
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
