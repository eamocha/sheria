import React from 'react';

import './Description.scss';

import {
    APCollapseContainer,
    APCollapseRow
} from '../../../common/ap-collapse/APCollapse';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const { t } = useTranslation();
    return (
        <APCollapseContainer
            title={t("description")}
            expanded={1}
        >
            <APCollapseRow
                row={[
                    {
                        label: null,
                        value: props?.advisorTask?.description,
                        fullWidth: true
                    }
                ]}
                fullWidth={true}
            />
        </APCollapseContainer>
    );
});
