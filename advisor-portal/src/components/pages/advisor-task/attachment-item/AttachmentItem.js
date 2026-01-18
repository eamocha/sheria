import React from 'react';

import {
    Card,
    Grid
} from '@material-ui/core';

import {
    AttachmentItemData,
    AttachmentItemFile
} from '../AdvisorTaskPageComponents';

import './AttachmentItem.scss';
 
export default React.memo((props) => {

    return (
        <Grid
            key={"advisor-task-attachment-grid-" + props?.index}
            item
            md={2}
            className="advisor-task-attachment-container"
        >
            <Card
                id={"advisor-task-attachment-card-" + props?.index}
            >
                <AttachmentItemFile
                    attachment={props?.attachment}
                />
                <AttachmentItemData
                    attachment={props?.attachment}
                    loadAdvisorTaskData={props.loadAdvisorTaskData}
                />
            </Card>
        </Grid>
    );
});
