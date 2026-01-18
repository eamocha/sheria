import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './StageContainer.scss';

import {
    Button,
    Collapse,
    Container,
    Divider
} from '@material-ui/core';

import {
    ExpandMore,
    ExpandLess
} from '@material-ui/icons';

import LegalCase from '../../../../../../api/LegalCase';

import {
    useHistory,
    useRouteMatch
} from 'react-router-dom';

import {
    Context,
    initialGlobalState
} from '../../../../../../Store';

import {
    StageStatus,
    StageDetailsTable,
    StageExternalReferencesTable,
    StageOpponentJudgesTable,
    StageOpponentLawyersTable,
    StageSummary
} from '../LitigationCasePageStagesPanel';

import {
    getValueFromLanguage,
    isFunction
} from '../../../../../../APHelpers';

import {
    FORMS_MODAL_TITLES,
    FORMS_NAMES
} from '../../../../../../Constants';
import { getActiveLanguageId } from '../../../../../../i18n';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const [isExpanded, setIsExpanded] = useState(false);

    const { t } = useTranslation();
    const [langId, setLangId] = useState(getActiveLanguageId());

    useEffect(() => {
        setLangId(getActiveLanguageId());
    }, [t]);

    const openLitigationCaseStageEditForm = (e) => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.litigationCaseStageEditForm) + ": M" + props?.litigationCase?.id,
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.litigationCaseStageEditForm,
                    submitCallback: isFunction(props?.loadLitigationCaseData) ? props.loadLitigationCaseData : null,
                    data: {
                        litigationCase: props?.litigationCase,
                        stage: props?.stage
                    }
                }
            }
        });
    }

    return (
        <Container
            maxWidth={false}
            className="litigation-case-stage-container no-padding-h"
        >
            <Button
                color="primary"
                onClick={() => setIsExpanded(prevState => !prevState)}
                className="collapse-btn"
            >
                {isExpanded ? <ExpandLess /> : <ExpandMore />}
            </Button>
            <Button
                color="primary"
                onClick={(e) => openLitigationCaseStageEditForm(e, props.stage)}
                className="edit-btn"
                title="Edit stage"
            >
                {getValueFromLanguage(props?.stage?.stage_name, 'stage_name_languages', langId)}
            </Button>
            <StageStatus
                stageStatus={props.stage.stage_status}
            />
            <Collapse
                in={isExpanded}
                className="collapse"
            >
                <StageDetailsTable
                    stage={props?.stage}
                />
                {
                    props?.stage?.stage_external_references?.length > 0 ||
                        props?.stage?.stage_judges?.length > 0 ||
                        props?.stage?.stage_opponent_lawyers?.length > 0 ?
                        <Container
                            maxWidth={false}
                            className="stage-tables"
                        >
                            <StageExternalReferencesTable
                                stage={props?.stage}
                            />
                            <StageOpponentJudgesTable
                                stage={props?.stage}
                            />
                            <StageOpponentLawyersTable
                                stage={props?.stage}
                            />
                        </Container>
                        :
                        null
                }
                <Divider />
                <StageSummary
                    stage={props?.stage}
                />
            </Collapse>
        </Container>
    );
});
