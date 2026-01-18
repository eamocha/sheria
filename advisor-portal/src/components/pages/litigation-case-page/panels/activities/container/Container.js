import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './Container.scss';

import {
    Container,
    Typography
} from '@material-ui/core';

import { APPageActionsToolbar } from '../../../../../common/ap-page/APPage';

import LegalCase from '../../../../../../api/LegalCase';

import AdvisorTask from './../../../../../../api/AdvisorTask';

import {
    ActivityContainer,
    ActionsToolbar,
    ActivityAdvisorTasksTable,
    ActivityHearingsTable
} from './../LitigationCasePageActivitiesPanel';

import {
    Context,
    initialGlobalState
} from '../../../../../../Store';

import {
    useHistory,
    useRouteMatch
} from 'react-router-dom';

import LitigationCaseDetail from '../../../../../../api/LitigationCaseDetail';

import Hearing from './../../../../../../api/Hearing';

import { buildErrorMessages } from '../../../../../../APHelpers';

import {
    SlideView as AdvisorTaskSlideView
} from './../../../../advisor-tasks/AdvisorTasksPageComponents';
import { useTranslation } from 'react-i18next';

export default React.memo((props) => {
    const history = useHistory();

    const routeMatches = useRouteMatch();

    const litigationCaseId = routeMatches.params?.id;

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [litigationCase, setLitigationCase] = useState('');

    const [dataLoaded, setDataLoaded] = useState(false);

    const [stages, setStages] = useState([]);

    const [hearings, setHearings] = useState([]);

    const [advisorTasks, setAdvisorTasks] = useState([]);

    const [activeAdvisorTask, setActiveAdvisorTask] = useState('');

    const [showAdvisorTaskSlideView, setShowAdvisorTaskSlideView] = useState(false);

    const { t } = useTranslation();

    useEffect(() => {

        loadData();
    }, []);

    const loadData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        // re-initiate the dataLoaded state (for reload)
        setDataLoaded(false);

        // load the litigation case
        LegalCase.get(litigationCaseId).then((response) => {
            setLitigationCase(response?.data?.data ?? '');
        }).then(() => {
            /**
             * load the litigation case stages with activities
             * the stages that will be returned here should have at least one of the following:
             * tasks, hearings, events
             */
            return LitigationCaseDetail.getList({
                legalCaseId: {
                    value: litigationCaseId
                },
                hasHearing: {
                    value: true
                },
                hasAdvisorTask: {
                    value: true
                }
            });
        }).then((response) => {
            setStages(response?.data?.data ?? []);
        }).then(() => {
            /**
             * load the tasks that are related to this litigation case
             * but they are not related to any stage
             * so they will be listed under "Other Tasks" or "Misc Tasks"
             */
            return AdvisorTask.getList({
                legalCaseId: {
                    value: litigationCaseId
                },
                stage: {
                    value: null
                }
            })
        }).then((response) => {
            setAdvisorTasks(response?.data?.data ?? []);
        }).then(() => {
            /**
             * load the hearings that are related to this litigation case
             * but they are not related to any stage
             * so they will be listed under "Other Hearings" or "Misc Hearings"
             */
            return Hearing.getList({
                legalCaseId: {
                    value: litigationCaseId
                },
                stage: {
                    value: null
                }
            })
        }).then((response) => {
            setHearings(response?.data?.data ?? []);
        }).catch((error) => {
            let message = buildErrorMessages(error?.response?.data?.message);

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: message,
                    severity: "error"
                }
            });
        }).finally(() => {

            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });

            setDataLoaded(true);
        });

        // LegalCase.get(
        //     litigationCaseId,
        //     [
        //         'currentStage.stageName.stageNameLanguages',
        //         'stages.stageName.stageNameLanguages',
        //         'stages.stageStatus.stageStatusLanguages',
        //         'stages.modifiedByUser',
        //         'stages.stageClientPosition',
        //         'stages.stageCourt',
        //         'stages.stageCourtType',
        //         'stages.stageCourtDegree',
        //         'stages.stageCourtRegion',
        //         'stages.stageOpponents',
        //         'stages.stageExternalReferences',
        //         'stages.stageOpponentLawyers.contactFullDetails',
        //         'stages.stageOpponentLawyers.contactRoleFullDetails',
        //         'stages.stageJudges.contactFullDetails',
        //         'stages.stageJudges.contactRoleFullDetails'
        //     ]
        // ).then((response) => {

        //     setLitigationCase(response?.data?.data);

        //     setStages(response?.data?.data?.stages);
        // }).catch((error) => {
        //     if (error?.response?.status === 404) {
        //         history.push('/error/page-not-found');
        //     } else {
        //         let message = error?.response?.data?.message;

        //         if (error?.response?.data?.message === 'object') {
        //             message = [];

        //             Object.keys(error.response.data.message).map((key, index) => {
        //                 return error.response.data.message?.[key].forEach((item) => {
        //                     message.push(<p key={key}>- {error.response.data.message[key]}: {item}</p>);
        //                 });
        //             });
        //         }

        //         globalStateDispatcher({
        //             notificationBar: {
        //                 ...globalState?.notificationBar,
        //                 open: true,
        //                 text: message,
        //                 severity: "error"
        //             }
        //         });
        //     }
        // }).finally(() => {

        //     globalStateDispatcher({
        //         globalLoader: initialGlobalState?.globalLoader
        //     });

        //     setDataLoaded(true);
        // });
    }

    if (!dataLoaded) {
        return null;
    }

    let stagesContent = stages.map((item, index) => {

        return <ActivityContainer
            key={"litigation-case-page-activity-record-" + index}
            stage={item}
            litigationCase={litigationCase}
            loadActivities={loadData}
        />;
    });

    // let stagesContent = this.state.stages.map((item, index) => {
    //     return <MatterLitigationActivitiesRecord
    //         key={"matter-litigation-activity-record-" + index}
    //         stage={item}
    //         matter={this.state.matter}
    //         openEditModal={this.openEditModal}
    //         setActiveFormModal={this.props.setActiveFormModal}
    //         setActiveFormModelData={this.props.setActiveFormModelData}
    //         setFormModalState={this.props.setFormModalState}
    //         setAfterActionReloadFunction={this.props.setAfterActionReloadFunction}
    //         afterActionReloadFunction={this.loadData}
    //         setGlobalLoader={this.props.setGlobalLoader}
    //         setNotificationBarText={this.props.setNotificationBarText}
    //         setNotificationBarSeverity={this.props.setNotificationBarSeverity}
    //         setNotificationBarState={this.props.setNotificationBarState}
    //     />;
    // });

    return (
        <Container
            id="litigation-case-page-activities-panel-container"
            maxWidth={false}
            className="no-padding-h"
        >
            <APPageActionsToolbar>
                <ActionsToolbar
                    litigationCase={litigationCase}
                    loadActivities={loadData}
                />
            </APPageActionsToolbar>
            <Container
                maxWidth={false}
                className="no-padding-h"
            >
                {
                    (stages.length > 0 || hearings.length > 0 || advisorTasks.length > 0) ?
                        (
                            <React.Fragment>
                                {stagesContent}
                                {/* <MatterLitigationActivitiyHearingsTable
                                data={this.state.hearings}
                                matter={this.state.matter}
                                openEditModal={this.openEditModal}
                                setActiveFormModal={this.props.setActiveFormModal}
                                setActiveFormModelData={this.props.setActiveFormModelData}
                                setFormModalState={this.props.setFormModalState}
                                setAfterActionReloadFunction={this.props.setAfterActionReloadFunction}
                                afterActionReloadFunction={this.loadData}
                            /> */}
                                {/* <MatterLitigationActivitiyAdvisorTasksTable
                                data={this.state.advisorTasks}
                                openEditModal={this.openEditModal}
                                setActiveFormModal={this.props.setActiveFormModal}
                                setActiveFormModelData={this.props.setActiveFormModelData}
                                setFormModalState={this.props.setFormModalState}
                                setAfterActionReloadFunction={this.props.setAfterActionReloadFunction}
                                afterActionReloadFunction={this.loadData}
                                handleStateChange={this.handleStateChange}
                            /> */}
                                <ActivityHearingsTable
                                    title={t("other_hearings")}
                                    hearings={hearings}
                                    afterActionReloadFunction={loadData}
                                />
                                <ActivityAdvisorTasksTable
                                    title={t("other_tasks")}
                                    advisorTasks={advisorTasks}
                                    setActiveAdvisorTask={setActiveAdvisorTask}
                                    setShowAdvisorTaskSlideView={setShowAdvisorTaskSlideView}
                                    loadActivities={loadData}
                                />
                            </React.Fragment>
                        )
                        :
                        <Typography
                            variant="h6"
                        >
                            {t("no_event_on_matter")}
                        </Typography>
                }
            </Container>
            <AdvisorTaskSlideView
                showSlideView={showAdvisorTaskSlideView}
                setShowSlideView={setShowAdvisorTaskSlideView}
                data={activeAdvisorTask}
            />
        </Container>
    );
});
