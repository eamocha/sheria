import React from 'react';

import './StageSummary.scss';

import {
    Grid,
    Typography
} from '@material-ui/core';

import {
    formatDateTime,
    getAdvisorUserFullName
} from '../../../../../../APHelpers';

import { useTranslation } from 'react-i18next';

export default React.memo((props) => {

    const [t] = useTranslation();
    return (
        <Grid
            container
            className="litigation-case-stage-summary"
        >
            <Grid
                item
                sm={4}
            >
                <Grid
                    container
                    className="table-row"
                >
                    <Grid
                        item
                        sm={5}
                    >
                        <Typography
                            variant="body1"
                            className="label"
                        >
                            {t("in_this_stage_for_days")}
                        </Typography>
                    </Grid>
                    <Grid
                        item
                        sm={7}
                    >
                        <Typography
                            variant="body1"
                            className="value"
                        >
                            {props?.stage?.durationInDays}
                        </Typography>
                    </Grid>
                </Grid>
            </Grid>
            <Grid
                item
                sm={4}
            >
                <Grid
                    container
                    className="table-row"
                >
                    <Grid
                        item
                        sm={5}
                    >
                        <Typography
                            variant="body1"
                            className="label"
                        >
                            {t("modified_by") + ":"}
                        </Typography>
                    </Grid>
                    <Grid
                        item
                        sm={7}
                    >
                        <Typography
                            variant="body1"
                            className="value"
                        >
                            {getAdvisorUserFullName(props?.stage?.modified_by_user)}
                        </Typography>
                    </Grid>
                </Grid>
            </Grid>
            <Grid
                item
                sm={4}
            >
                <Grid
                    container
                    className="table-row"
                >
                    <Grid
                        item
                        sm={5}
                    >
                        <Typography
                            variant="body1"
                            className="label"
                        >
                            {t("modified_on") + ":"}
                        </Typography>
                    </Grid>
                    <Grid
                        item
                        sm={7}
                    >
                        <Typography
                            variant="body1"
                            className="value"
                        >
                            {formatDateTime(new Date(props?.stage?.modifiedOn))}
                        </Typography>
                    </Grid>
                </Grid>
            </Grid>
        </Grid>
    );
});
