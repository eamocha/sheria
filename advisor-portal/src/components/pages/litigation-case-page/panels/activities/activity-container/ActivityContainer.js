import React, {
    useEffect,
    useState
} from 'react';

import './ActivityContainer.scss';

import {
    Container,
    Grid,
    Button,
    Typography,
    Collapse
} from '@material-ui/core';

import {
    ExpandLess,
    ExpandMore
} from '@material-ui/icons';

import { getValueFromLanguage } from './../../../../../../APHelpers';

import LegalCaseStageStatus from './../../../../../common/legal-case-stage-status/LegalCaseStageStatus.lazy';

import {
    ActivityDetailsRow,
    ActivityHearingsTable
} from '../LitigationCasePageActivitiesPanel';

import ActivityAdvisorTasksTable from '../activity-advisor-tasks-table/ActivityAdvisorTasksTable';

import { useTranslation } from 'react-i18next';

import { getActiveLanguageId } from '../../../../../../i18n';

export default React.memo((props) => {
    const [stage, setStage] = useState(props?.stage ?? '');
    const [title, setTitle] = useState('');
    const [isExpanded, setIsExpanded] = useState(props?.isExpanded ? props?.isExpanded === 0 ? false : true : true);
    const { t } = useTranslation();

    useEffect(() => {
        setTitle(getValueFromLanguage(props?.stage?.stage_name, 'stage_name_languages', getActiveLanguageId(), ''));
    }, [props?.stage, t]);

    return (
        <Container
            maxWidth={false}
            className="litigation-case-activity"
        >
            <Button
                color="primary"
                onClick={() => setIsExpanded(prevState => !prevState)}
                classes={{ root: "collapse-btn" }}
            >
                {isExpanded ? <ExpandLess /> : <ExpandMore />}
            </Button>
            <Button
                color="primary"
                // onClick={(event) => this.props.openEditModal(event, this.props.stage)}
                className="margin-btn"
            >
                {title}
            </Button>
            <LegalCaseStageStatus
                status={stage?.stage_status}
            />
            <Collapse
                in={isExpanded}
                classes={{ wrapperInner: "collapse" }}
            >
                <Container
                    maxWidth={false}
                    className="collapse-container"
                >
                    <Grid
                        container
                        className="grid-row"
                    >
                        <ActivityDetailsRow
                            label={t("judgment_date")}
                            value={props?.stage?.sentenceDate}
                        />
                        <ActivityDetailsRow
                            label={t("client_position")}
                            value={getValueFromLanguage(props?.stage?.stage_client_position, 'client_position_languages', getActiveLanguageId(), '')}
                        />
                        <ActivityDetailsRow
                            label={t("court_type")}
                            value={props?.stage?.stage_court_type?.name}
                        />
                    </Grid>
                    <Grid
                        container
                        className="grid-row"
                    >
                        <ActivityDetailsRow
                            label={t("court")}
                            value={props?.stage?.stage_court?.name}
                        />
                        <ActivityDetailsRow
                            label={t("court_degree")}
                            value={props?.stage?.stage_court_degree?.name}
                        />
                        <ActivityDetailsRow
                            label={t("court_region")}
                            value={props?.stage?.stage_court_region?.name}
                        />
                    </Grid>
                </Container>
                <Container
                    maxWidth={false}
                    className="related-data-container"
                >
                    <ActivityHearingsTable
                        hearings={props?.stage?.stage_hearings}
                        litigationCase={props?.litigationCase}
                        loadActivities={props?.loadActivities}
                    />
                    {props?.stage?.stage_advisor_tasks.length > 0 ?
                        <ActivityAdvisorTasksTable
                            advisorTasks={props?.stage?.stage_advisor_tasks} />
                        :
                        null
                    }
                    {/* <MatterLitigationActivitiyHearingsTable
                        data={propIsNotEmpty(this.props.stage.stage_hearings) ? this.props.stage.stage_hearings : null}
                        matter={this.props.matter}
                        stage={this.props.stage}
                        setActiveFormModal={this.props.setActiveFormModal}
                        setActiveFormModelData={this.props.setActiveFormModelData}
                        setFormModalState={this.props.setFormModalState}
                        setAfterActionReloadFunction={this.props.setAfterActionReloadFunction}
                        afterActionReloadFunction={this.props.afterActionReloadFunction}
                        setGlobalLoader={this.props.setGlobalLoader}
                        setNotificationBarText={this.props.setNotificationBarText}
                        setNotificationBarSeverity={this.props.setNotificationBarSeverity}
                        setNotificationBarState={this.props.setNotificationBarState}
                    /> */}
                    {/* <MatterLitigationActivitiyAdvisorTasksTable
                        data={propIsNotEmpty(this.props.stage.stage_advisor_tasks) ? this.props.stage.stage_advisor_tasks : null}
                        setActiveFormModal={this.props.setActiveFormModal}
                        setActiveFormModelData={this.props.setActiveFormModelData}
                        setFormModalState={this.props.setFormModalState}
                        setAfterActionReloadFunction={this.props.setAfterActionReloadFunction}
                        afterActionReloadFunction={this.props.afterActionReloadFunction}
                    /> */}
                </Container>
            </Collapse>
        </Container>
    );
});
