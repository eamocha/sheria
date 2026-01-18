import React, { useEffect } from 'react';

import './TimeTracking.scss';

import {
    APCollapseContainer,
    APCollapseRow
} from './../../../common/ap-collapse/APCollapse';

import TimeMask from '../../../../TimeMask';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    let estimatedEffort = props?.advisorTask?.estimated_effort?.length > 0 ? props?.advisorTask?.estimated_effort : 0;
    let estimatedEffortInHours = props?.advisorTask?.estimated_effort_in_hours?.length > 0 ? props?.advisorTask?.estimated_effort_in_hours : 0;

    let loggedTime = props?.advisorTask?.logged_time?.length > 0 ? props?.advisorTask?.logged_time : 0;
    let loggedTimeInHours = props?.advisorTask?.logged_time_in_hours?.length > 0 ? props?.advisorTask?.logged_time_in_hours : 0;

    let remainingTime = estimatedEffortInHours && loggedTimeInHours ? ((estimatedEffortInHours * 100 - loggedTimeInHours * 100) / 100) : 0;

    let remainingTime_TimeMask = new TimeMask(remainingTime);

    const { t } = useTranslation();

    useEffect(() => {

    }, [props?.advisorTask]);

    return (
        <APCollapseContainer
            title={t("time_tracking")}
            expanded={1}
        >
            <APCollapseRow
                row={[
                    {
                        label: t("estimated"),
                        value: estimatedEffort === 0 ? 'None' : estimatedEffort,
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
                        label: t("remaining"),
                        value: remainingTime === 0 ? 'None' : remainingTime_TimeMask.timeToHumanReadable(),
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
                        label: t("logged"),
                        value: loggedTime === 0 ? 'None' : loggedTime,
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
