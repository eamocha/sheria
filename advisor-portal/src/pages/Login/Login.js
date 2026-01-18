import React, { useState } from 'react';

import './Login.scss';

import {
    Container,
    Typography
} from '@material-ui/core';

import LoginForm from '../../components/login/LoginForm/LoginForm.lazy';

import Logo from './../../assets/images/a4l_logo_sign_in.png';
 
export default React.memo((props) => {
    const [isLoginFailed, setIsLoginFailed] = useState(false);
    const [validationMessage, setValidationMessage] = useState('');

    return (
        <Container
            id="login-page"
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
                <LoginForm
                    setIsLoginFailed={setIsLoginFailed}
                    setValidationMessage={setValidationMessage}
                />
                {
                    isLoginFailed ? 
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
