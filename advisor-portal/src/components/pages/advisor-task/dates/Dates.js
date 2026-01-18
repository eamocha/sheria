import React from 'react';

import './Dates.scss';

import {
    APCollapseContainer,
    APCollapseRow
} from './../../../common/ap-collapse/APCollapse';

import { useTranslation } from 'react-i18next';

import { formatDateTime } from '../../../../APHelpers';

export default React.memo((props) => {
    const { t } = useTranslation();
    
    return (
        <APCollapseContainer
            title={t("dates")}
            expanded={1}
        >
            <APCollapseRow
                row={[
                    {
                        label: t("due_date"),
                        value: props?.advisorTask?.due_date,
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
                        label: t("created_on"),
                        value: formatDateTime(new Date(props?.advisorTask?.createdOn)),
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
                        label: t("modified_on"),
                        value: formatDateTime(new Date(props?.advisorTask?.modifiedOn)),
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
