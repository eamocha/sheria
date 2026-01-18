import React from 'react';

import './ActionsToolbar.scss';

import {
    Button,
    Container,
} from '@material-ui/core';

import AddIcon from '@material-ui/icons/Add';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const { t } = useTranslation();
    return (
        <Container
            maxWidth={false}
            className="time-logs-page-actions-toolbar btns-container no-padding-h"
        >
            <Button
                className="add-btn"
                variant="contained"
                color="default"
                startIcon={<AddIcon />}
                onClick={() => props.openAddForm()}
            >
                {t("add")}
            </Button>
        </Container>
    );
});
