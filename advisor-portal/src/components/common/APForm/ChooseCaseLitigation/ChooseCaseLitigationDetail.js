import React, { useState } from 'react';
import { FormGroup, Grid, Typography, Button } from '@material-ui/core';
import ClearIcon from '@material-ui/icons/Clear';
import { useTranslation } from 'react-i18next';

const ChooseCaseLitigationDetail = (props) => {
    var stageTitle = props?.stageTitle;

    const [title, setTitle] = useState(stageTitle);

    const handleChange = () => {
        setTitle('None');

        props.clearStage();
    };

    const [t] = useTranslation();

    return (
        <FormGroup>
            <Grid container maxWidth={false}>
                <Grid item sm={2}>
                    <Typography variant="body1" className={props.classes.customTypography}>
                        {t("stage") + ":"}
                    </Typography>
                </Grid>
                <Grid item sm={6}>
                    <Typography variant="body1" className={props.classes.customTypography}>
                        {stageTitle}
                    </Typography>
                </Grid>
                <Grid item sm={2}>
                    <Button
                        color="primary"
                        onClick={() => props.handleChooseCaseLitigationDetailModalState(true)}
                    >
                        {t("select")}
                    </Button>
                </Grid>
                <Grid item sm={2}>
                    <Button
                        color="primary"
                        onClick={() => handleChange()}
                    >
                        <ClearIcon />
                    </Button>
                </Grid>
            </Grid>
        </FormGroup>
    );
};

export default ChooseCaseLitigationDetail;
