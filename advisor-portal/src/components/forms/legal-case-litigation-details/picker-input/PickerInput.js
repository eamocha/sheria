import React, {
    useEffect,
    useState
} from 'react';

import './PickerInput.scss';

import {
    FormGroup,
    Grid,
    Typography,
    Button
} from '@material-ui/core';

import ClearIcon from '@material-ui/icons/Clear';

import { getValueFromLanguage } from './../../../../APHelpers';
import { getActiveLanguageId } from '../../../../i18n';
 
export default React.memo((props) => {
    const [currentStage, setCurrentStage] = useState({});
    const [stageTitle, setStageTitle] = useState('');

    useEffect(() => {

        detectCurrentStage(props?.litigationCaseWithStages);
    }, [props?.litigationCaseWithStages]);

    useEffect(() => {

        setStageTitle(props?.currentStage?.title?.length > 0 ? props.currentStage?.title : getValueFromLanguage(currentStage?.stage_name, 'stage_name_languages', getActiveLanguageId(), ''));
    }, [props?.currentStage, currentStage]);

    const detectCurrentStage = (litigationCaseWithStages) => {
        let currentStageArray = litigationCaseWithStages?.stages.filter((item) => {

            return item?.id === litigationCaseWithStages?.stage
        });

        setCurrentStage(currentStageArray?.[0] ?? {});
    }

    const clearSelectedStage = () => {
        setStageTitle('None');

        props.clearSelectedStage();
    }

    return (
        <FormGroup>
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
                        className="custom-typography"
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
                        className="custom-typography"
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
                        variant="outlined"
                        title="Select Stage"
                        onClick={() => props.setPickerFormState(true)}
                    >
                        Select
                    </Button>
                </Grid>
                <Grid
                    item
                    container
                    justify="flex-end"
                    sm={2}
                >
                    <Button
                        color="primary"
                        onClick={() => clearSelectedStage()}
                        size="small"
                        title="Clear Stage"
                    >
                        <ClearIcon />
                    </Button>
                </Grid>
            </Grid>
        </FormGroup>
    );
});
