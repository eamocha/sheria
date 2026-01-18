import React, { useState } from 'react';
import './APPickLegalCaseLitigationDetail.scss';
import {
    FormGroup,
    Grid,
    Typography,
    Button
} from '@material-ui/core';
import ClearIcon from '@material-ui/icons/Clear';
 
export default React.memo((props) => {

    var stageTitle = props?.stageTitle;

    const [title, setTitle] = useState(stageTitle);

    const handleChange = () => {
        setTitle('None');

        props.clearSelectedStage();
    };

    return (
        <FormGroup
            className="AP-pick-legal-case-litigation-detail"
        >
            <Grid
                container
                maxWidth={false}
            >
                <Grid
                    item
                    sm={2}
                >
                    <Typography
                        variant="body1"
                        className="AP-pick-legal-case-litigation-detail-label"
                    >
                        Stage:
                    </Typography>
                </Grid>
                <Grid
                    item
                    sm={6}
                >
                    <Typography
                        variant="body1"
                        className="AP-pick-legal-case-litigation-detail-label"
                    >
                        {stageTitle}
                    </Typography>
                </Grid>
                <Grid
                    item
                    sm={2}
                >
                    <Button
                        color="primary"
                        onClick={() => props.setPickerState(true)}
                    >
                        Select
                    </Button>
                </Grid>
                <Grid
                    item
                    sm={2}
                >
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
});
