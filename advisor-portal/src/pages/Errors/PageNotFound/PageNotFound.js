import React from 'react';
import './PageNotFound.scss';
import APPageContainer from '../../../components/common/APPageContainer/APPageContainer';
import { PAGES_IDS } from '../../../Constants';
import {
    Box,
    Container,
    Typography
} from '@material-ui/core';
import { Alert } from '@material-ui/lab';
 
export default React.memo((props) => {

    return (
        <APPageContainer
            id={PAGES_IDS.pageNotFound}
        >
            <Container
                maxWidth={false}
                className="no-padding-h"
            >
                <Box
                    display="flex"
                    flexDirection="column"
                    justifyContent="center"
                    alignItems="center"
                    minHeight="80vh"
                    minWidth="100%"
                >
                    <Typography
                        variant="h1"
                    >
                        404
                    </Typography>
                    <Alert
                        variant="filled"
                        severity="error"
                    >
                        Page Not Found!
                    </Alert>
                </Box>
            </Container>
        </APPageContainer>
    );
});
