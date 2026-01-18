import React, { useEffect } from 'react';

import './Attachments.scss';

import { APCollapseContainer } from '../../../common/ap-collapse/APCollapse';

import { Grid } from '@material-ui/core';

import { AttachmentItem } from '../AdvisorTaskPageComponents';
import { useTranslation } from 'react-i18next';
 
export default React.memo((props) => {
    const { t } = useTranslation();
    useEffect(() => {

    }, [props?.advisorTask]);
    
    let attachments = '';

    attachments = props?.advisorTask?.attachments?.map((item, key) => {

        return (
            <AttachmentItem
                key={"advisor-task-attachment-item-" + key}
                attachment={item}
                loadAdvisorTaskData={props.loadAdvisorTaskData}
            />
        );
    });

    return (
        <APCollapseContainer
            title={t("attachments")}
        >
            <Grid
                container
                className="advisor-task-attachments-container"
                maxWidth={false}
                droppable
            >
                {
                    attachments?.length > 0 ?
                    attachments
                    :
                    <p>{t("no_attachments_to_display")}</p>
                }
            </Grid>
        </APCollapseContainer>
    );
});
