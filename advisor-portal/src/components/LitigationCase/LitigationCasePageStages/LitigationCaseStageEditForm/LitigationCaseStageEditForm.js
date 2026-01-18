import React, {
    useContext,
    useEffect,
    useState
} from 'react';
import './LitigationCaseStageEditForm.scss';
import { FORMS_NAMES } from './../../../../Constants';
import { Context, initialGlobalState } from './../../../../Store';
import {
    Button,
    Collapse,
    Container,
    FormControl,
    FormGroup,
    FormLabel,
    makeStyles
} from '@material-ui/core';
import LitigationCaseStageExternalReferences from './../LitigationCaseStageExternalReferences/LitigationCaseStageExternalReferences.lazy';
import LitigationCaseStageBadge from './../LitigationCaseStageBadge/LitigationCaseStageBadge.lazy';
import APAutocompleteList from '../../../common/APForm/APAutocompleteList/APAutocompleteList';
import APTextFieldInput from '../../../common/APForm/APTextFieldInput/APTextFieldInput.lazy';
import LitigationCaseStageOpponentJudges from './../LitigationCaseStageOpponentJudges/LitigationCaseStageOpponentJudges.lazy';
import LitigationCaseStageOpponentLawyers from './../LitigationCaseStageOpponentLawyers/LitigationCaseStageOpponentLawyers.lazy';
import { MuiPickersUtilsProvider } from '@material-ui/pickers';
import DateFnsUtils from '@date-io/date-fns';
import ExpandLessIcon from '@material-ui/icons/ExpandLess';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import CloseIcon from '@material-ui/icons/Close';
import APDatePicker from '../../../common/APForm/APDatePicker/APDatePicker.lazy';
import { isValid, set } from 'date-fns';
import { defaultLoadList, formatDate, isFunction, loadListWithLanguages } from '../../../../APHelpers';
import LitigationCaseStageExternalReferencesAddForm from './../LitigationCaseStageExternalReferenceAddForm/LitigationCaseStageExternalReferenceAddForm.lazy';
import LitigationCaseStageOpponentJudgeAddForm from '../LitigationCaseStageOpponentJudgeAddForm/LitigationCaseStageOpponentJudgeAddForm.lazy';
import LitigationCaseStageOpponentLawyerAddForm from './../LitigationCaseStageOpponentLawyerAddForm/LitigationCaseStageOpponentLawyerAddForm.lazy';
import MiscList from '../../../../api/MiscList';
import LitigationCaseStage from '../../../../api/LitigationCaseStage';

const useStyles = makeStyles({
    stageOpponentsFieldsContainer: {
        marginTop: 30,
        marginBottom: 30,
        paddingLeft: 0,
        paddingRight: 0
    },
    stageOpponentFieldContainer: {
        marginBottom: 10,
        paddingLeft: 0,
        paddingRight: 0
    },
    stageOpponentHeader: {
        flexDirection: 'row',
        margin: 0,
        marginBottom: 5
    },
    stageOpponentTitle: {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        minHeight: 48
    },
    moreFieldsBtnContainer: {
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        marginTop: 15,
        marginBottom: 15
    },
    moreFieldsBtn: {
        fontWeight: 'bold'
    },
    removeBtn: {
        display: 'inline-block',
        marginLeft: 10,
        fontWeight: 700,
        color: '#f50057'
    }
});
 
export default React.memo((props) => {
    const formId = FORMS_NAMES.litigationCaseStageEditForm;

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [stageExternalReferencesAddFormModalState, setStageExternalReferencesAddFormModalState] = useState(false);
    const [stageOpponentLawyerAddFormState, setStageOpponentLawyerAddFormState] = useState(false);
    const [stageOpponentJudgeAddFormState, setStageOpponentJudgeAddFormState] = useState(false);
    const [stageOpponentLawyerAddFormValidation, setStageOpponentLawyerAddFormValidation] = useState(true);
    const [stageOpponentJudgeAddFormValidation, setStageOpponentJudgeAddFormValidation] = useState(true);

    const [stagesList, setStagesList] = useState([]);
    const [clientPositionsList, setClientPositionsList] = useState([]);
    const [opponetPositionsList, setOpponentPositionsList] = useState([]);
    const [stageStatusesList, setStageStatusesList] = useState([]);
    const [courtTypesList, setCourtTypesList] = useState([]);
    const [courtDegreesList, setCourtDegreesList] = useState([]);
    const [courtRegionsList, setCourtRegionsList] = useState([]);
    const [courtsList, setCourtsList] = useState([]);

    const [moreFields, setMoreFields] = useState(false);

    // const [stageExternalReferences, setStageExternalReferences] = useState([]);
    // const [stageOpponentLawyers, setStageOpponentLawyers] = useState([]);
    // const [stageOpponentJudges, setStageOpponentJudges] = useState([]);

    const [formData, setFormData] = useState({
        court_type_id: '',
        court_degree_id: '',
        court_region_id: '',
        court_id: '',
        sentenceDate: null,
        stage_external_references: [],
        stage_opponent_lawyers: [],
        stage_opponent_judges: []

        // legal_case_id: this.props.data.stage.legal_case_id,
        // court_type_id: this.props.data.stage.court_type_id,
        // court_degree_id: this.props.data.stage.court_degree_id,
        // court_region_id: this.props.data.stage.court_region_id,
        // court_id: this.props.data.stage.court_id,
        // sentenceDate: this.props.data.stage.sentenceDate,
        // comments: this.props.data.stage.comments,
        // legal_case_stage: this.props.data.stage.legal_case_stage,
        // client_position: this.props.data.stage.client_position,
        // status: this.props.data.stage.status,
        // user_type: 2,
        // stage_opponents: stageOpponents,
        // stage_external_references: stageExternalReferences,
        // stage_opponent_lawyers: stageOpponentLawyers,
        // stage_judges: stageJudges
    });

    const [listValues, listValuesDispatcher] = useState({
        client_position: {
            title: '',
            value: ''
        },
        status: {
            title: '',
            value: ''
        },
        court_type_id: {
            title: '',
            value: ''
        },
        court_degree_id: {
            title: '',
            value: ''
        },
        court_region_id: {
            title: '',
            value: ''
        },
        court_id: {
            title: '',
            value: ''
        }
    });

    useEffect(() => {

        let listsArr = [
            "clientPositions",
            "stageStatuses",
            "opponentPositions",
            "courts",
            "courtTypes",
            "courtDegrees",
            "courtRegions",
            "stages"
        ];

        // if (changeStageMode === true) {
        //     listsArr.push("stages");
        // }

        loadData({
            lists: listsArr
        });
    }, []);

    const loadData = (query) => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        // if (this.state.changeStageMode === true) {
        //     CaseStage.getList({
        //         otherStages: {
        //             value: this.props.data.stage.legal_case_id
        //         }
        //     }, 'stageLanguages').then((response) => {
        //         this.loadStagesList(response?.data?.data);
        //     }).catch((error) => {
        //         this.props.setGlobalLoader(false);
        //     });
        // }

        MiscList.getList(query).then((response) => {

            // if (this.state.changeStageMode === true) {
            //     this.loadStagesList(response?.data?.data.stages);
            // }

            loadStagesList(response?.data?.data.stages);

            loadClientPositionsList(response?.data?.data?.clientPositions);

            loadStageStatusesList(response?.data?.data?.stageStatuses);

            loadOpponentPositionsList(response?.data?.data?.opponentPositions);

            loadCourtsList(response?.data?.data?.courts);
            loadCourtTypesList(response?.data?.data?.courtTypes);
            loadCourtDegreesList(response?.data?.data?.courtDegrees);
            loadCourtRegionsList(response?.data?.data?.courtRegions);
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
        });
    };

    const loadStagesList = (data) => {
        let result = loadListWithLanguages(data, 'stage_languages', 'name', 'id');

        setStagesList(result?.options);
    }

    const loadClientPositionsList = (data) => {
        let result = loadListWithLanguages(data, 'client_position_languages', 'name', 'id');

        setClientPositionsList(result?.options);
    }

    const loadStageStatusesList = (data) => {
        let result = loadListWithLanguages(data, 'stage_status_languages', 'name', 'id');

        setStageStatusesList(result?.options);
    }

    const loadOpponentPositionsList = (data) => {
        let result = loadListWithLanguages(data, 'opponent_position_languages', 'name', 'id');

        setOpponentPositionsList(result?.options);
    }

    const loadCourtsList = (data) => {
        let result = defaultLoadList(data, 'name', 'id');

        setCourtsList(result);
    }

    const loadCourtTypesList = (data) => {
        let result = defaultLoadList(data, 'name', 'id');

        setCourtTypesList(result);
    }

    const loadCourtDegreesList = (data) => {
        let result = defaultLoadList(data, 'name', 'id');

        setCourtDegreesList(result);
    }

    const loadCourtRegionsList = (data) => {
        let result = defaultLoadList(data, 'name', 'id');

        setCourtRegionsList(result);
    }

    const handleListChange = (state, stateValue, defaultValues, multipleSelection, defaultValuesWithMultipleSelection, changeDefaultValues) => {
        setFormData(prevState => ({
            ...prevState,
            [state]: stateValue
        }));

        if (changeDefaultValues) {
            listValuesDispatcher(prevState => ({
                ...prevState,
                [state]: multipleSelection ? defaultValuesWithMultipleSelection : defaultValues
            }));
        }
    }

    const handleObjectChange = (e, stateKey) => {
        e.persist();

        setFormData(prevState => ({
            ...prevState,
            [stateKey]: e?.target?.value
        }));
    }

    const handleDatePickerChange = (state, date, time = false) => {
        setFormData(prevState => ({
            ...prevState,
            [state]: date === null ? null : time ? (isValid(date) ? date : null) : formatDate(date)
        }));
    }

    const addStageExternalReference = (e, item) => {
        let list = formData.stage_external_references;

        list.push(item);

        setFormData(prevState => ({
            ...prevState.formData,
            stage_external_references: list
        }));
    }

    const removeStageExternalReference = (e, itemId) => {
        let list = formData.stage_external_references;

        list.map((item, key) => {
            if (key === itemId) {
                list.splice(key, 1);
            }
        });

        setFormData(prevState => ({
            ...prevState.formData,
            stage_external_references: list
        }));
    }

    const addStageOpponentLawyer = (e, item) => {
        let list = formData.stage_opponent_lawyers;

        let valid = true;

        list.map((listItem, key) => {
            if (listItem.contact === item.contact) {
                stageOpponentLawyerAddFormValidation(false);

                valid = false;

                return null;
            }
        });

        if (valid === true) {
            list.push(item);

            setFormData(prevState => ({
                ...prevState.formData,
                stage_opponent_lawyers: list
            }));

            stageOpponentLawyerAddFormValidation(true);
        }
    }

    const removeStageOpponentLawyer = (e, itemId) => {
        let list = formData.stage_opponent_lawyers;

        list.map((item, key) => {
            if (key === itemId) {
                list.splice(key, 1);
            }
        });

        setFormData(prevState => ({
            ...prevState.formData,
            stage_opponent_lawyers: list
        }));
    }

    const addStageOpponentJudge = (e, item) => {
        let list = formData.stage_opponent_judges;

        let valid = true;

        list.map((listItem, key) => {
            if (listItem.contact === item.contact) {
                setStageOpponentJudgeAddFormValidation(false);

                valid = false;

                return null;
            }
        });

        if (valid === true) {
            list.push(item);

            setFormData(prevState => ({
                ...prevState.formData,
                stage_opponent_judges: list
            }));

            setStageOpponentJudgeAddFormValidation(true);
        }
    }

    const removeStageOpponentJudge = (e, itemId) => {
        let list = formData.stage_opponent_judges;

        list.map((item, key) => {
            if (key === itemId) {
                list.splice(key, 1);
            }
        });

        setFormData(prevState => ({
            ...prevState.formData,
            stage_opponent_judges: list
        }));
    }

    const submit = (e) => {
        e.preventDefault();

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });
        
        let data = prepareRequestData(formData);

        /**
         * Remove empty opponents
         */
        if (data?.stage_opponents) {
            data.stage_opponents.map((item, key) => {
                if ((!item?.opponent_id || item.opponent_id.length <= 0)
                    && (!item?.position_id || item.position_id.length <= 0)
                ) {
                    data.stage_opponents.splice(key, 1);
                }
            });
        }

        // let apiAction = this.state.changeStageMode ? CaseStage.create(data) : CaseStage.update(this.props.data.stage.id, data);
        let apiAction = LitigationCaseStage.create(data);

        apiAction.then((response) => {
            if (isFunction(globalState?.modal?.form?.submitCallback)) {
                globalState.modal.form.submitCallback();
            }

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    severity: "success",
                    text: "Stage has been updated successfully"
                }
            });
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
        });
    }

    const prepareRequestData = (data) => {
        let result = {};
    
        for (let [key, value] of Object.entries(data)) {
            if (value != null && value !== '') {
                result[key] = value;
            }
        }
    
        return result;
    }

    const classes = useStyles();

    return (
        <MuiPickersUtilsProvider
            utils={DateFnsUtils}
        >
            <form
                id={formId}
                onSubmit={(e) => submit(e)}
            >
                <FormGroup>
                    <LitigationCaseStageExternalReferences
                        stageExternalReferences={formData.stage_external_references}
                        removeItem={removeStageExternalReference}
                        openForm={setStageExternalReferencesAddFormModalState}
                    />
                </FormGroup>
                <FormGroup>
                    <FormControl>
                        <FormLabel>
                            {/* <span>{this.state.changeStageMode ? 'From' : 'Matter Stage'}:&nbsp;</span> */}
                            <LitigationCaseStageBadge
                                // stage={this.props.data.stage}
                            />
                        </FormLabel>
                    </FormControl>
                </FormGroup>
                    <APAutocompleteList
                        label="To"
                        required
                        textRequired={true}
                        options={stagesList}
                        optionsLabel="title"
                        stateKey="legal_case_stage"
                        value={listValues.legal_case_stage}
                        valueKey="value"
                        onChange={handleListChange}
                    />
                {
                    // this.state.changeStageMode ?
                    // (
                    //     <FormGroup>
                    //         <FormControl>
                    //             <Autocomplete
                    //                 label="To"
                    //                 variant="outlined"
                    //                 name="legal_case_stage"
                    //                 autoComplete
                    //                 options={this.state.stagesList}
                    //                 getOptionLabel={option => typeof option.title !== "undefined" ? option.title : ''}
                    //                 onChange={(event, value) => this.handleListChange('legal_case_stage', event, value, 'value')}
                    //                 validators={['required']}
                    //                 value={this.state.defaultValues.legal_case_stage}
                    //                 required
                    //                 renderInput={
                    //                     params => (
                    //                         <TextField
                    //                             {...params}
                    //                             label="To"
                    //                             variant="outlined"
                    //                             fullWidth
                    //                             required
                    //                         />
                    //                     )
                    //                 }
                    //                 renderOption={ option => {
                    //                         return <span>{option.title}</span>
                    //                     }
                    //                 }
                    //             />
                    //         </FormControl>
                    //     </FormGroup>
                    // )
                    // :
                    // null
                }
                <APAutocompleteList
                    label="Client Position"
                    required
                    textRequired={true}
                    options={clientPositionsList}
                    optionsLabel="title"
                    stateKey="client_position"
                    value={listValues.client_position}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <APAutocompleteList
                    label="Stage Status"
                    required
                    textRequired={true}
                    options={stageStatusesList}
                    optionsLabel="title"
                    stateKey="status"
                    value={listValues.status}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <APTextFieldInput
                    label="Comments"
                    stateKey="comments"
                    rows={5}
                    multiline={true}
                    value={formData.comments}
                    handleChange={handleObjectChange}
                />
                {/** stage opponents */}
                <APAutocompleteList
                    label="Court Type"
                    required
                    textRequired={true}
                    options={courtTypesList}
                    optionsLabel="title"
                    stateKey="court_type_id"
                    value={listValues.court_type_id}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <APAutocompleteList
                    label="Court Degree"
                    required
                    textRequired={true}
                    options={courtDegreesList}
                    optionsLabel="title"
                    stateKey="court_degree_id"
                    value={listValues.court_degree_id}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <APAutocompleteList
                    label="Court Region"
                    required
                    textRequired={true}
                    options={courtRegionsList}
                    optionsLabel="title"
                    stateKey="court_region_id"
                    value={listValues.court_region_id}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <APAutocompleteList
                    label="Court"
                    required
                    textRequired={true}
                    options={courtsList}
                    optionsLabel="title"
                    stateKey="court_id"
                    value={listValues.court_id}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <Collapse
                    in={moreFields}
                >
                    <FormGroup>
                        <LitigationCaseStageOpponentJudges
                            stageJudges={formData.stage_judges}
                            removeItem={removeStageOpponentJudge}
                            openForm={setStageOpponentJudgeAddFormState}
                        />
                    </FormGroup>
                    <APDatePicker
                        label="Judgment Date"
                        stateKey="sentenceDate"
                        format="yyyy-MM-dd"
                        value={formData.sentenceDate}
                        handleChange={handleDatePickerChange}
                    />
                    <FormGroup>
                        <LitigationCaseStageOpponentLawyers
                            stageOpponentLawyers={formData.stage_opponent_lawyers}
                            removeItem={removeStageOpponentLawyer}
                            openForm={setStageOpponentLawyerAddFormState}
                        />
                    </FormGroup>
                </Collapse>
                <Container
                    className={classes.moreFieldsBtnContainer}
                >
                    <Button
                        className={classes.moreFieldsBtn}
                        variant="text"
                        color="primary"
                        onClick={() => setMoreFields(prevState => !prevState)}
                        startIcon={moreFields ? <ExpandLessIcon /> : <ExpandMoreIcon />}
                    >
                        {moreFields ? 'Less Fields' : 'More fields'}
                    </Button>
                </Container>
            </form>
            <LitigationCaseStageExternalReferencesAddForm
                addStageExternalReference={addStageExternalReference}
                formModalState={stageExternalReferencesAddFormModalState}
                closeForm={setStageExternalReferencesAddFormModalState}
            />
            <LitigationCaseStageOpponentJudgeAddForm
                addMatterStageJudge={addStageOpponentJudge}
                formModalState={stageOpponentJudgeAddFormState}
                closeForm={setStageOpponentJudgeAddFormState}
                formValidation={stageOpponentJudgeAddFormValidation}
            />
            <LitigationCaseStageOpponentLawyerAddForm
                addMatterStageOpponentLawyer={addStageOpponentLawyer}
                formModalState={stageOpponentLawyerAddFormState}
                closeForm={setStageOpponentLawyerAddFormState}
                formValidation={stageOpponentLawyerAddFormValidation}

            />
        </MuiPickersUtilsProvider>
    );
});
