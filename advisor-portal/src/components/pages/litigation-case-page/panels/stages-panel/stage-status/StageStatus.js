import React from 'react';
import { getValueFromLanguage } from '../../../../../../APHelpers';
import { getActiveLanguageId } from '../../../../../../i18n';

import './StageStatus.scss';

export default React.memo((props) => {
    let title = getValueFromLanguage(props?.stageStatus, 'stage_status_languages', getActiveLanguageId());
    let color = props?.stageStatus?.color;

    return (
        <span
            className="litigation-case-stage-status"
            style={{backgroundColor: color}}
        >
            {title}
        </span>
    );
});
