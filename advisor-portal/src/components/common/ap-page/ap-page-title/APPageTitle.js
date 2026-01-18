import React from 'react';
import './APPageTitle.scss';
import {
    Container,
    Typography
} from '@material-ui/core';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const { t } = useTranslation();
    return (
        <Container
            className="AP-page-title"
            maxWidth={false}
        >
            <Typography
                variant="h5"
            >
                {t(props?.pageTitle)}
            </Typography>
            {props?.children}
        </Container>
    );
});
