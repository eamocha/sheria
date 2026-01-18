import React from 'react';
import {
    Container,
    Grid,
    Typography
} from '@material-ui/core';
import { Link } from 'react-router-dom';
import {
    addEllipsis,
    buildInstanceURL,
    getModulePrefix,
    getValueFromLanguage
} from './../../../APHelpers';
import APPrioritySign from './../../common/ap-priority-sign/APPrioritySign.lazy';
import APStatusBadge from './../../common/ap-status-badge/APStatusBadge.lazy';
import { getActiveLanguageId } from '../../../i18n';

const DashboardListWidgetRow = React.memo((props) => {

    return (
        <Container
            maxWidth={false}
            className="dashboard-list-widget-row no-padding-h"
        >
            <Grid
                container
                className="dashboard-list-widget-row-grid no-padding-h"
            >
                <Grid
                    item
                    sm="1"
                >
                    <APPrioritySign
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
                                title={props?.model === 'AdvisorTask' ? getModulePrefix('advisor-task', props?.rowData?.id) : getModulePrefix('hearing', props?.rowData?.id)}
                                to={props?.model === 'AdvisorTask' ? (`${buildInstanceURL()}/task/${props?.rowData?.id}`) : (`${buildInstanceURL()}/hearing/${props?.rowData?.id}`)}
                                className="primary-link d-inline-important"
                            >
                                {props?.model === 'AdvisorTask' ? getModulePrefix('advisor-task', props?.rowData?.id) : getModulePrefix('hearing', props?.rowData?.id)}
                            </Link>
                            <Typography
                                className="d-inline"
                                variant="body1"
                            >
                                {addEllipsis(props?.rowData?.description, 50)}
                            </Typography>

                            {getValueFromLanguage(props?.rowData?.advisor_task_type, 'advisor_task_type_languages', getActiveLanguageId(), '')}
                        </p>
                    </div>
                  
                </Grid>
                <Grid
                    item
                    sm="3"
                    className="d-flex align-items-center justify-content-end"
                >
                    <APStatusBadge
                        status={props?.rowData?.advisor_task_status}
                    />
                </Grid>
            </Grid>
        </Container>
    );
});

export default DashboardListWidgetRow;
