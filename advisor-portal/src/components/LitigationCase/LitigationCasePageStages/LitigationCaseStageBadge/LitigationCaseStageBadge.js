import React from 'react';
import './LitigationCaseStageBadge.scss';
import { getValueFromLanguage } from '../../../../APHelpers';
 
export default React.memo((props) => {
    let title = props?.stage?.stage_name ? getValueFromLanguage(props?.stage?.stage_name, 'stage_name_language', 1) : 'None';

    return (
        <span
            className="litigation-case-stage-badge"
        >
            {title}
        </span>
    );
});
