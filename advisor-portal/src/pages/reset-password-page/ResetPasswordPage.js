import React, { useContext, useState } from 'react';
import './ResetPasswordPage.scss';
import { Container, Typography } from '@material-ui/core';
import Logo from './../../assets/images/a4l_logo_sign_in.png';
import ResetPasswordForm from '../../components/forms/password-reset/reset-password-form/ResetPasswordForm.lazy';
import { useRouteMatch } from 'react-router';
import jwtDecode from 'jwt-decode';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { Context } from '../../Store';
import { SESSION_KEYS } from '../../Constants';
import { buildInstanceURL } from '../../APHelpers';

export default React.memo((props) => {

    const [isError, setError] = useState(false);
    const [validationMessage, setValidationMessage] = useState('');
    const [isTokenExpired, setIsTokenExpired] = useState(false);

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
            id="reset-password-page"
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
                {isTokenExpired ?
                    <div className="center-text">
                        <h2>{t("token_has_expired")}</h2>
                        <Link
                            className="primary-link"
                            to={`${buildInstanceURL()}/login`}
                        >{t("go_back_to_login_page")}</Link>
                    </div>
                    :
                    <ResetPasswordForm
                        setValidationMessage={setValidationMessage}
                        setError={setError}
                        setIsTokenExpired={setIsTokenExpired} />
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
