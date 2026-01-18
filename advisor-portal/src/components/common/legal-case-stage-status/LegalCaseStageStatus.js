import React from 'react';

import './LegalCaseStageStatus.scss';

import { getValueFromLanguage } from '../../../APHelpers';
import { getActiveLanguageId } from '../../../i18n'

export default React.memo((props) => {
    let title = getValueFromLanguage(props?.status, 'stage_status_languages', getActiveLanguageId(), '');
    let color = props?.status?.color ?? '';

    return (
        <span
            className="legal-case-stage-status"
            style={{
                backgroundColor: color
            }}
        >
            {title}
        </span>
    );
});
