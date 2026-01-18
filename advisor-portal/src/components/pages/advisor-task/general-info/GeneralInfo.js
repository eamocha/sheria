import React, { useEffect } from 'react';

import './GeneralInfo.scss';

import {
    APCollapseContainer,
    APCollapseRow
} from './../../../common/ap-collapse/APCollapse';

import { buildInstanceURL, getValueFromLanguage } from '../../../../APHelpers';

import APStatusBadge from '../../../common/ap-status-badge/APStatusBadge.lazy';

import APPrioritySign from '../../../common/ap-priority-sign/APPrioritySign.lazy';

import { Link } from 'react-router-dom';

import { LEGAL_CASES_CATEGORIES } from '../../../../Constants';
import { useTranslation } from 'react-i18next';
import { getActiveLanguageId } from '../../../../i18n';

export default React.memo((props) => {
    const { t } = useTranslation();
    useEffect(() => {

    }, [props?.advisorTask]);

    return (
        <APCollapseContainer
            title={t("general_info")}
            expanded={1}
        >
            <APCollapseRow
                row={[
                    {
                        label: t("type"),
                        value: getValueFromLanguage(props?.advisorTask?.advisor_task_type, 'advisor_task_type_languages', getActiveLanguageId())
                    },
                    {
                        label: t("workflow_status"),
                        value: <APStatusBadge
                            status={props?.advisorTask?.advisor_task_status}
                        />
                    }
                ]}
            />
            <APCollapseRow
                row={[
                    {
                        label: t("priority"),
                        value: <APPrioritySign
                            priority={props?.advisorTask?.priority}
                        />
                    },
                    {
                        label: t("location"),
                        value: props?.advisorTask?.advisor_task_location?.name ?? "None"
                    }
                ]}
            />
            <APCollapseRow
                row={[
                    {
                        label: t("related_matter"),
                        value: props?.advisorTask?.legal_case ?
                            <Link
                                className="primary-link"
                                to={(props?.advisorTask?.legal_case?.category == LEGAL_CASES_CATEGORIES.litigationCases ? `${buildInstanceURL()}/litigation-case/` : `${buildInstanceURL()}/corporate-matter/`) + props?.advisorTask?.legal_case?.id}
                            >
                                {props?.advisorTask?.legal_case?.subject}
                            </Link>
                            :
                            t("none"),
                        fullWidth: true
                    }
                ]}
                fullWidth={true}
            />
        </APCollapseContainer>
    );
});
