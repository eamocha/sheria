import React from 'react';

import './People.scss';

import {
    APCollapseContainer,
    APCollapseRow
} from './../../../common/ap-collapse/APCollapse';
import { getAdvisorUserFullName } from '../../../../APHelpers';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const { t } = useTranslation();
    return (
        <APCollapseContainer
            title={t("people")}
            expanded={1}
        >
            <APCollapseRow
                row={[
                    {
                        label: t("task_assignee"),
                        value: getAdvisorUserFullName(props?.advisorTask?.assignee),
                        fullWidth: true,
                        labelSize: 3,
                        valueSize: 9
                    }
                ]}
                fullWidth={true}
            />
            <APCollapseRow
                row={[
                    {
                        label: t("reporter"),
                        value: getAdvisorUserFullName(props?.advisorTask?.advisor_task_reporter),
                        fullWidth: true,
                        labelSize: 3,
                        valueSize: 9
                    }
                ]}
                fullWidth={true}
            />
            <APCollapseRow
                row={[
                    {
                        label: t("created_by"),
                        value: getAdvisorUserFullName(props?.advisorTask?.created_by_user),
                        fullWidth: true,
                        labelSize: 3,
                        valueSize: 9
                    }
                ]}
                fullWidth={true}
            />
            <APCollapseRow
                row={[
                    {
                        label: t("modified_by"),
                        value: getAdvisorUserFullName(props?.advisorTask?.modified_by_user),
                        fullWidth: true,
                        labelSize: 3,
                        valueSize: 9
                    }
                ]}
                fullWidth={true}
            />
        </APCollapseContainer>
    );
});
