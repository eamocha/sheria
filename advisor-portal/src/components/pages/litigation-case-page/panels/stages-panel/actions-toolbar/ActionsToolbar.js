import React, {
    useContext
} from 'react';

import './ActionsToolbar.scss';

import {
    Button,
    Container,
    Typography
} from '@material-ui/core';

import {
    Context, initialGlobalState
} from '../../../../../../Store';

import {
    getValueFromLanguage,
    isFunction
} from '../../../../../../APHelpers';

import RedoIcon from '@material-ui/icons/Redo';

import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES
} from '../../../../../../Constants';
import { useTranslation } from 'react-i18next';
import { getActiveLanguageId } from '../../../../../../i18n';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);
    const { t } = useTranslation();

    const openLitigationCaseStageEditForm = () => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.litigationCaseStageChangeForm) + ": M" + props?.litigationCase?.id,
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.litigationCaseStageChangeForm,
                    submitCallback: isFunction(props?.loadLitigationCaseData) ? props.loadLitigationCaseData : null,
                    data: {
                        litigationCase: props?.litigationCase
                    }
                }
            }
        });
    }

    return (
        <Container
            maxWidth={false}
            className="litigation-case-page-stages-panel-actions-toolbar btns-container no-padding-h"
        >
            <Typography
                variant="caption"
                className="current-stage-name-label"
            >
                {t("current_stage") + " : "}
            </Typography>
            <Typography
                variant="caption"
                className={"current-stage-name " + globalState.domDirection}
            >
                {props?.litigationCase?.current_stage ? getValueFromLanguage(props?.litigationCase?.current_stage?.stage_name, 'stage_name_languages', getActiveLanguageId()) : 'None'}
            </Typography>
            <Button
                color="default"
                variant="contained"
                onClick={(e) => openLitigationCaseStageEditForm(e)}
                title="change stage"
                startIcon={<RedoIcon />}
            >
                {t("change_stage")}
            </Button>
        </Container>
    );
});
