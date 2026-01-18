import React, { useState } from 'react';

import './ActivityAdvisorTasksTableRow.scss';

import {
    TableRow,
    TableCell
} from '@material-ui/core';

import APPrioritySign from './../../../../../common/ap-priority-sign/APPrioritySign.lazy';

import APStatusBadge from './../../../../../common/ap-status-badge/APStatusBadge.lazy';

import {
    addEllipsis,
    getAdvisorUserFullName
} from '../../../../../../APHelpers';

import { Link } from 'react-router-dom';

import { AdvisorTasksTableRowMenu } from '../LitigationCasePageActivitiesPanel';

export default React.memo((props) => {
    const [advisorTask, setAdvisorTask] = useState(props?.advisorTask ?? '');

    const openAdvisorTaskSlideView = () => {
        props.setActiveAdvisorTask(advisorTask);
        props.setShowAdvisorTaskSlideView(true);
    }

    let advisorTaskType = '';

    if (advisorTask?.advisor_task_type) {
        for (var i = 0; i < advisorTask?.advisor_task_type?.advisor_task_type_languages.length; i++) {
            let type = advisorTask?.advisor_task_type?.advisor_task_type_languages[i];

            if (type?.language_id === 1) {
                advisorTaskType = type;
                break;
            }
        }
    }

    let advisorTaskAttachments = '';

    if (advisorTask?.attachment) {
        advisorTaskAttachments = advisorTask?.attachments.map((item, key) => {

            return (
                <Link>
                    {item?.name + "." + item?.extension}
                </Link>
            )
        });
    }

    return (
        <TableRow>
            <TableCell>
                {/* <AdvisorTaskRecordDropDownMenu
                    matter={props.matter}
                    currentStage={props.stage}
                    advisorTask={item}
                    setActiveFormModal={props.setActiveFormModal}
                    setActiveFormModelData={props.setActiveFormModelData}
                    setFormModalState={props.setFormModalState}
                    setAfterActionReloadFunction={props.setAfterActionReloadFunction}
                    afterActionReloadFunction={props.afterActionReloadFunction}
                    classes={classes}
                    rowkey={key}
                    setGlobalLoader={props.setGlobalLoader}
                    setNotificationBarText={props.setNotificationBarText}
                    setNotificationBarSeverity={props.setNotificationBarSeverity}
                    handleStateChange={props.handleStateChange}
                /> */}
                <AdvisorTasksTableRowMenu
                    advisorTask={advisorTask}
                    loadActivities={props.loadActivities}
                />
            </TableCell>
            <TableCell>
                <Link
                    component="button"
                    variant="text"
                    color="primary"
                    className="primary-link btn-link"
                    href="#"
                    onClick={() => openAdvisorTaskSlideView()}
                >
                    {'AT' + advisorTask?.id}
                </Link>
            </TableCell>
            <TableCell>
                <Link
                    component="button"
                    variant="text"
                    align="left"
                    color="primary"
                    className="primary-link btn-link text-left"
                    href="#"
                    onClick={() => openAdvisorTaskSlideView()}
                >
                    {addEllipsis(advisorTask?.description)}
                </Link>
            </TableCell>
            <TableCell>
                <APStatusBadge
                    status={advisorTask?.advisor_task_status}
                />
            </TableCell>
            <TableCell>
                {advisorTaskType?.name}
            </TableCell>
            <TableCell>
                {advisorTask?.due_date}
            </TableCell>
            <TableCell>
                <APPrioritySign
                    priority={advisorTask?.priority}
                    priorityText={advisorTask?.priority}
                />
            </TableCell>
            <TableCell>
                {getAdvisorUserFullName(advisorTask?.assignee)}
            </TableCell>
            {/* <TableCell>
                {advisorTaskAttachments}
            </TableCell> */}
        </TableRow>
    );
});
