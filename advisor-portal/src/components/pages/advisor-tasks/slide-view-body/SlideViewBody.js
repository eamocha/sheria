import React from 'react';

import './SlideViewBody.scss';

import { Container } from '@material-ui/core';

import {
    APCollapseContainer,
    APCollapseRow
} from './../../../common/ap-collapse/APCollapse';

import APPrioritySign from '../../../common/ap-priority-sign/APPrioritySign.lazy';

import {
    formatDateTime,
    getAdvisorUserFullName
} from '../../../../APHelpers';

import { useTranslation } from 'react-i18next';
 
export default React.memo((props) => {
    const [t] = useTranslation();
    return (
        <Container
            className="slide-view-body"
        >
            <APCollapseContainer
                title={t('details')}
                expanded={1}
            >
                <APCollapseRow
                    row={[
                        {
                            label: t('priority'),
                            value: <APPrioritySign
                                priority={props?.data?.priority}
                                priorityText={props?.data?.priority}
                            />
                        }
                    ]}
                    fullWidth={true}
                />
                <APCollapseRow
                    row={[
                        {
                            label: t('estimated_effort'),
                            value: props?.data?.estimated_effort
                        }
                    ]}
                    fullWidth={true}
                />
            </APCollapseContainer>
            <APCollapseContainer
                title={t('people')}
                expanded={1}
            >
                <APCollapseRow
                    row={[
                        {
                            label: t('reporter'),
                            value: getAdvisorUserFullName(props?.data?.advisor_task_reporter)
                        }
                    ]}
                    fullWidth={true}
                />
                <APCollapseRow
                    row={[
                        {
                            label: t('assignee'),
                            value: getAdvisorUserFullName(props?.data?.assignee)
                        }
                    ]}
                    fullWidth={true}
                />
            </APCollapseContainer>
            <APCollapseContainer
                title={t('dates')}
                expanded={1}
            >
                <APCollapseRow
                    row={[
                        {
                            label: t('due_date'),
                            value: props?.data?.due_date
                        }
                    ]}
                    fullWidth={true}
                />
                <APCollapseRow
                    row={[
                        {
                            label: t('created_on'),
                            value: formatDateTime(new Date(props?.data?.createdOn))
                        }
                    ]}
                    fullWidth={true}
                />
                <APCollapseRow
                    row={[
                        {
                            label: t('modified_on'),
                            value: formatDateTime(new Date(props?.data?.modifiedOn))
                        }
                    ]}
                    fullWidth={true}
                />
            </APCollapseContainer>
        </Container>
    );
});
