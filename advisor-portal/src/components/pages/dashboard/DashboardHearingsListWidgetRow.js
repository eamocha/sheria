import React from 'react';
import {
    Container,
    Grid,
    Typography
} from '@material-ui/core';
import { Link } from 'react-router-dom';
import {
    addEllipsis,
    getModulePrefix,
    getValueFromLanguage
} from '../Helpers';
import PrioritySign from '../common/PrioritySign';
import StatusBadge from '../common/StatusBadge';

const DashboardHearingsListWidgetRow = React.memo((props) => {

    return (
        <Container
            maxWidth={false}
            className="dashboard-list-widget-row no-padding-h"
        >
            <Container
                maxWidth={false}
                className="dashboard-hearings-list-widget-row-grid no-padding-h"
            >
                <div>
                    
                </div>
            </Container>
            {/* <Grid
                container
                className="dashboard-list-widget-row-grid no-padding-h"
            >
                <Grid
                    item
                    sm="1"
                >
                    <PrioritySign
                        priority={props?.rowData?.priority}
                        priorityText=""
                    />
                </Grid>
                <Grid
                    item
                    sm="8"
                >
                    <div>
                        <p
                            className="no-margin"
                        >
                            <Link
                                title={props?.model == 'AdvisorTask' ? getModulePrefix('advisor-task', props?.rowData?.id) : getModulePrefix('hearing', props?.rowData?.id)}
                                to={props?.model == 'AdvisorTask' ? ('/task/' + props?.rowData?.id) : ('/hearing/' + props?.rowData?.id)}
                                className="primary-link d-inline-important"
                            >
                                {props?.model == 'AdvisorTask' ? getModulePrefix('advisor-task', props?.rowData?.id) : getModulePrefix('hearing', props?.rowData?.id)}
                            </Link>
                            <Typography
                                className="d-inline"
                            >
                                {': ' + addEllipsis(props?.rowData?.description, 20)}
                            </Typography>
                        </p>
                    </div>
                    {getValueFromLanguage(props?.rowData?.advisor_task_type, 'advisor_task_type_languages', 1, '')}
                </Grid>
                <Grid
                    item
                    sm="3"
                    className="d-flex align-items-center justify-content-end"
                >
                    <StatusBadge
                        status={props?.rowData?.advisor_task_status}
                    />
                </Grid>
            </Grid> */}
        </Container>
    );
});

export default DashboardHearingsListWidgetRow;
