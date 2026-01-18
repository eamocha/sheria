import React, { useContext, useState } from 'react';
import './RequestPasswordResetPage.scss';
import { Container, Typography } from '@material-ui/core';
import Logo from './../../assets/images/a4l_logo_sign_in.png';
import RequestPasswordResetForm from '../../components/forms/password-reset/request-password-reset-form/RequestPasswordResetForm.lazy';
import { Link } from 'react-router-dom';
import { SESSION_KEYS } from '../../Constants';
import { Context } from '../../Store';
import { useTranslation } from 'react-i18next';
import { buildInstanceURL } from '../../APHelpers';

export default React.memo((props) => {

    const [isError, setError] = useState(false);
    const [validationMessage, setValidationMessage] = useState('');

    const [isSuccess, setSuccess] = useState(false);

    const [t] = useTranslation();


    const [globalState, globalStateDispatcher] = useContext(Context);

    if (globalState?.user?.loggedIn) {
        sessionStorage.removeItem(SESSION_KEYS.accessToken);
        sessionStorage.removeItem(SESSION_KEYS.refreshToken);
        sessionStorage.removeItem(SESSION_KEYS.user);

        globalStateDispatcher({
            user: {
                loggedIn: false,
                data: {}
            }
        });
    }

    return (
        <Container
            id="request-password-reset-page"
            fixed
            maxWidth="sm"
            className="h-100 d-flex align-items-center justify-content-center"
        >
            <Container
                fixed
                maxWidth="sm"
            >
                <img
                    src={Logo}
                    className="AP-logo img-center"
                    alt="Sheria360 External Counsel Portal"
                />
                {
                    isSuccess ?
                        <div className="center-text">
                            <h2>{t("please_check_your_email")}</h2>
                            <Link
                                className="primary-link"
                                to={`${buildInstanceURL()}/login`}
                            >
                                {t("go_back_to_login_page")}
                            </Link>
                        </div>

                        :
                        <div>
                            <RequestPasswordResetForm
                                setValidationMessage={setValidationMessage}
                                setError={setError}
                                setSuccess={setSuccess} />

                            <div className="center-text">
                                <Link
                                    className="primary-link"
                                    to={`${buildInstanceURL()}/login`}
                                >
                                    {t("go_back_to_login_page")}
                                </Link>
                            </div>
                        </div>
                }
                {
                    isError ?
                        <Typography
                            variant="body1"
                            color="error"
                        >
                            {validationMessage}
                        </Typography>
                        :
                        null
                }
            </Container>
        </Container>
    );
});
