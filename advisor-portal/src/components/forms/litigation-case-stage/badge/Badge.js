import React from 'react';
import './Badge.scss';
import { getValueFromLanguage } from '../../../../APHelpers';
import { getActiveLanguageId } from '../../../../i18n';
 
export default React.memo((props) => {
    let title = props?.currentStage ? getValueFromLanguage(props?.currentStage?.stage_name, 'stage_name_languages', getActiveLanguageId()) : 'None';

    return (
        <span
            className="litigation-case-stage-badge"
        >
            {title}
        </span>
    );
});
