import React, {
    useContext,
    useEffect,
    useState
} from 'react';
import './LitigationCasePageStagesTab.scss';
import {
    Container,
    Grid,
    Button,
    Typography,
    makeStyles,
} from '@material-ui/core';
import { Context, initialGlobalState } from '../../../../Store';
// import MatterLitigationDataRecord from './MatterLitigationDataRecord';
import RedoIcon from '@material-ui/icons/Redo';
import LegalCase from '../../../../api/LegalCase';
import { FORMS_MODAL_TITLES, FORMS_NAMES } from '../../../../Constants';

const useStyles = makeStyles({
    container: {
        paddingLeft: 0,
        paddingRight: 0
    },
    header: {
        paddingLeft: 0,
        paddingRight: 0,
        marginBottom: 30,
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'flex-start'
    },
    headerControlsContainer: {
        margin: 0,
        paddingLeft: 0,
        paddingRight: 0
    },
    headerControlsGrid: {
        paddingLeft: 0,
        paddingRight: 0
    },
    currentStageLabel: {
        display: 'flex',
        alignItems: 'center'
    },
    currentStageTitle: {
        verticalAlign: 'middle',
        fontWeight: 700,
        marginRight: 5
    },
    changeCurrentStageBtnIcon: {
        fontSize: '1.25rem',
        marginLeft: '5px'
    }
});

export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const [litigationCaseId,] = useState(props?.litigationCaseId);
    const [litigationCase, setLitigationCase] = useState('');
    const [stages, setStages] = useState([]);
    const [dataLoaded, setDataLoaded] = useState(false);

    useEffect(() => {

        loadData();
    }, [props?.litigationCaseId]);

    const loadData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        LegalCase.get(
            litigationCaseId,
            [
                'stages.stageName.stageNameLanguage',
                'stages.stageStatus.stageStatusLanguages',
                'stages.modifiedByUser',
                'stages.stageClientPosition',
                'stages.stageCourt',
                'stages.stageCourtType',
                'stages.stageCourtDegree',
                'stages.stageCourtRegion',
                'stages.stageOpponents',
                'stages.stageExternalReferences',
                'stages.stageOpponentLawyers.contactFullDetails',
                'stages.stageOpponentLawyers.contactRoleFullDetails',
                'stages.stageJudges.contactFullDetails',
                'stages.stageJudges.contactRoleFullDetails'
            ]
        ).then((response) => {
            setLitigationCase(response?.data?.data);

            setStages(response?.data?.data?.stages);
        }).catch((error) => {
            let message = error?.response?.data?.message;

            if (error?.response?.data?.message === 'object') {
                message = [];

                Object.keys(error.response.data.message).map((key, index) => {
                    return error.response.data.message?.[key].forEach((item) => {
                        message.push(<p key={key}>- {error.response.data.message[key]}: {item}</p>);
                    });
                });
            }

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
    }

    const openLitigationCaseStageEditForm = (e, stage) => {
        globalStateDispatcher({
            modal: {
                ...initialGlobalState?.modal,
                title: t(FORMS_MODAL_TITLES.litigationCaseStageEditForm),
                open: true,
                form: {
                    ...globalState?.modal?.form,
                    id: FORMS_NAMES.litigationCaseStageEditForm,
                    submitCallback: loadData,
                    data: {
                        litigationCase: litigationCase,
                        litigationCaseStages: stages
                    }
                }
            }
        });
    }

    const classes = useStyles();

    let currentStage = null;
    let currentStageTitle = '';

    let stagesContent = stages.map((stage, key) => {
        // if (this.state.matter.stage === stage.id) {
        //     currentStage = stage;
        // }

        // return(
        //     <MatterLitigationDataRecord
        //         key={key}
        //         stage={stage}
        //         openEditModal={this.openEditModal}
        //     />
        // );
    });

    // if (currentStage !== null) {
    //     currentStageTitle = currentStage.stage_name.stage_name_language.map((item, key) => {
    //         if (item.language_id === 1) {
    //             return(item.name);
    //         } else {
    //             return null;
    //         }
    //     });
    // }

    if (!dataLoaded) {
        return null;
    }

    return (
        <Container
            id="litigation-case-page-stages-tab"
            maxWidth={false}
            className="no-padding-h"
        >
            <Container
                maxWidth={false}
                className={classes.header + " no-padding-h"}
            >
                <Container
                    maxWidth="xs"
                    className={classes.headerControlsContainer}
                >
                    <Grid
                        container
                        className={classes.headerControlsGrid}
                    >
                        <Grid
                            item
                            sm={12}
                        >
                            <Button
                                color="primary"
                                variant="text"
                                onClick={(e) => openLitigationCaseStageEditForm(e)}
                                title="change stage"
                            >
                                Change Stage
                                <RedoIcon
                                    className={classes.changeCurrentStageBtnIcon}
                                />
                            </Button>
                        </Grid>
                    </Grid>
                </Container>
            </Container>
            <Container
                maxWidth={false}
                className="no-padding-h"
            >
                {
                    stages.length > 0 ?
                        stages
                        :
                        <Typography
                            variant="h5"
                        >
                            There are no stages.
                    </Typography>
                }
            </Container>
        </Container>
    );
});
