import React, { useEffect } from 'react';

import './Container.scss';

import {
    Container,
    Grid
} from '@material-ui/core';

import {
    GeneralInfo,
    Description,
    Attachments,
    Activity,
    People,
    Dates,
    TimeTracking,
} from './../AdvisorTaskPageComponents';
 
export default React.memo((props) => {
    useEffect(() => {

    }, [props?.advisorTask]);

    return (
        <Container
            id="advisor-task-page-container"
            maxWidth={false}
            className="no-padding-h"
        >
            <Grid
                container
            >
                <Grid
                    item
                    sm={9}
                >
                    <GeneralInfo
                        advisorTask={props?.advisorTask}
                    />
                    <Description
                        advisorTask={props?.advisorTask}
                    />
                    <Attachments
                        advisorTask={props?.advisorTask}
                        loadAdvisorTaskData={props.loadAdvisorTaskData}
                    />
                    <Activity
                        advisorTask={props?.advisorTask}
                        loadAdvisorTaskData={props.loadAdvisorTaskData}
                    />
                </Grid>
                <Grid
                    item
                    sm={3}
                >
                    <People
                        advisorTask={props?.advisorTask}
                    />
                    <Dates
                        advisorTask={props?.advisorTask}
                    />
                    <TimeTracking
                        advisorTask={props?.advisorTask}
                    />
                </Grid>
            </Grid>
        </Container>
    );
});
