import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './EditForm.scss';

import { MuiPickersUtilsProvider } from '@material-ui/pickers';

import DateFnsUtils from '@date-io/date-fns';

import {
    Badge,
    ExternalReferences,
    ExternalReferencesEditForm,
    ExternalReferencesForm,
    OpponentJudges,
    OpponentJudgesEditForm,
    OpponentJudgesForm,
    OpponentLawyers,
    OpponentLawyersForm,
    OpponentLawyersEditForm
} from '../LitigationCaseStageForms';

import {
    Button,
    Collapse,
    Container,
    FormControl,
    FormGroup,
    FormLabel,
    makeStyles
} from '@material-ui/core';

import { FORMS_NAMES } from '../../../../Constants';

import APAutocompleteList from '../../../common/APForm/APAutocompleteList/APAutocompleteList';

import {
    Context,
    initialGlobalState
} from '../../../../Store';

import APDatePicker from '../../../common/APForm/APDatePicker/APDatePicker.lazy';

import APTextFieldInput from '../../../common/APForm/APTextFieldInput/APTextFieldInput.lazy';

import { isValid } from 'date-fns';

import {
    defaultLoadList,
    formatDate,
    getAdvisorUserFullName,
    getValueFromLanguage,
    isFunction,
    loadListWithLanguages
} from '../../../../APHelpers';

import ExpandLessIcon from '@material-ui/icons/ExpandLess';

import ExpandMoreIcon from '@material-ui/icons/ExpandMore';

import LitigationCaseStage from '../../../../api/LitigationCaseStage';

import MiscList from '../../../../api/MiscList';

import { useTranslation } from 'react-i18next';
import { getActiveLanguageId } from '../../../../i18n';

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

    const [litigationCase,] = useState(globalState?.modal?.form?.data?.litigationCase);
    const [stage,] = useState(globalState?.modal?.form?.data?.stage);

    const [t] = useTranslation();

    const [stageExternalReferencesAddFormModalState, setStageExternalReferencesAddFormModalState] = useState(false);
    const [stageExternalReferencesEditFormModalState, setStageExternalReferencesEditFormModalState] = useState(false);
    const [stageExternalReferenceEditItem, setStageExternalReferenceEditItem] = useState('');

    const [stageOpponentJudgeAddFormState, setStageOpponentJudgeAddFormState] = useState(false);
    const [stageOpponentJudgeEditFormState, setStageOpponentJudgeEditFormState] = useState(false);
    const [stageOpponentJudgeEditItem, setStageOpponentJudgeEditItem] = useState('');
    const [stageOpponentJudgeAddFormValidation, setStageOpponentJudgeAddFormValidation] = useState(true);

    const [stageOpponentLawyerAddFormState, setStageOpponentLawyerAddFormState] = useState(false);
    const [stageOpponentLawyerEditFormState, setStageOpponentLawyerEditFormState] = useState(false);
    const [stageOpponentLawyerEditItem, setStageOpponentLawyerEditItem] = useState('');
    const [stageOpponentLawyerAddFormValidation, setStageOpponentLawyerAddFormValidation] = useState(true);

    const [moreFields, setMoreFields] = useState(false);

    const [clientPositionsList, setClientPositionsList] = useState([]);
    const [opponentPositionsList, setOpponentPositionsList] = useState([]);
    const [stageStatusesList, setStageStatusesList] = useState([]);
    const [courtTypesList, setCourtTypesList] = useState([]);
    const [courtDegreesList, setCourtDegreesList] = useState([]);
    const [courtRegionsList, setCourtRegionsList] = useState([]);
    const [courtsList, setCourtsList] = useState([]);

    const [formData, setFormData] = useState({
        legal_case_id: litigationCase?.id ?? '',
        court_type_id: stage?.court_type_id ?? '',
        court_degree_id: stage?.court_degree_id ?? '',
        court_region_id: stage?.court_region_id ?? '',
        court_id: stage?.court_id ?? '',
        sentenceDate: stage?.sentenceDate ?? null,
        comments: stage?.comments ?? '',
        legal_case_stage: stage?.legal_case_stage ?? '',
        client_position: stage?.client_position ?? '',
        status: stage?.status ?? '',
        stage_opponents: [],
        stage_external_references: stage?.stage_external_references ? stage.stage_external_references.map(item => ({
            number: item?.number,
            refDate: item?.refDate,
            comments: item?.comments
        })) : [],
        stage_opponent_lawyers: stage?.stage_opponent_lawyers ? stage.stage_opponent_lawyers.map(item => ({
            contact: item?.contact,
            contact_name: item?.contact_full_details ? getAdvisorUserFullName(item?.contact_full_details) : '',
            contact_role: item?.contact_role,
            comments: item?.comments
        })) : [],
        stage_opponent_judges: stage?.stage_judges ? stage.stage_judges.map(item => ({
            contact: item?.contact,
            contact_name: item?.contact_full_details ? getAdvisorUserFullName(item?.contact_full_details) : '',
            contact_role: item?.contact_role,
            comments: item?.comments
        })) : []
    });

    const [listValues, listValuesDispatcher] = useState({
        client_position: {
            title: getValueFromLanguage(stage?.stage_client_position, 'client_position_languages', getActiveLanguageId(), ''),
            value: stage?.stage_client_position?.id ?? ''
        },
        status: {
            title: getValueFromLanguage(stage?.stage_status, 'stage_status_languages', getActiveLanguageId(), ''),
            value: stage?.stage_status?.id ?? ''
        },
        court_type_id: {
            title: stage?.stage_court_type?.name ?? '',
            value: stage?.stage_court_type?.id ?? ''
        },
        court_degree_id: {
            title: stage?.stage_court_degree?.name ?? '',
            value: stage?.stage_court_degree?.id ?? ''
        },
        court_region_id: {
            title: stage?.stage_court_region?.name ?? '',
            value: stage?.stage_court_region?.id ?? ''
        },
        court_id: {
            title: stage?.stage_court?.name ?? '',
            value: stage?.stage_court?.id ?? ''
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

        // we can get them as a neutral list then remove the current stage from the list

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
            ...prevState,
            stage_external_references: list
        }));
    }

    const handleSetStageExternalReferenceEditItem = (e, item) => {
        setStageExternalReferenceEditItem(item);
    }

    const editStageExternalReference = (e, editedItem) => {
        let list = formData.stage_external_references;

        list = list.map((item, key) => {
            if (key === editedItem?.key) {
                return {
                    number: editedItem?.number,
                    refDate: editedItem?.refDate,
                    comments: editedItem?.comments
                }
            } else {
                return item;
            }
        });

        setFormData(prevState => ({
            ...prevState,
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
            ...prevState,
            stage_external_references: list
        }));
    }

    const addStageOpponentJudge = (e, item) => {
        let list = formData.stage_opponent_judges;

        /**
         * check if the judge already exists in the list or not
         */
        let valid = true;

        list.map((listItem,) => {
            if (listItem.contact === item.contact) {
                setStageOpponentJudgeAddFormValidation(false);

                valid = false;

                return null;
            }
        });

        if (valid === true) {
            list.push(item);

            setFormData(prevState => ({
                ...prevState,
                stage_opponent_judges: list
            }));

            setStageOpponentJudgeAddFormValidation(true);
        }
    }

    const editStageOpponentJudge = (e, editedItem) => {
        let list = formData.stage_opponent_judges;

        /**
         * check if the judge already exists in the list or not
         */
        let valid = true;

        list = list.map((listItem, key) => {
            if (key === editedItem?.key) {
                return {
                    contact: editedItem?.contact ?? '',
                    contact_name: editedItem?.contact_name ?? '',
                    contact_role: editedItem?.contact_role ?? '',
                    contact_role_name: editedItem?.contact_role_name ?? '',
                    comments: editedItem?.comments ?? ''
                }
            } else {
                return listItem
            }
        });

        if (valid === true) {
            setFormData(prevState => ({
                ...prevState,
                stage_opponent_judges: list
            }));

            setStageOpponentJudgeAddFormValidation(true);
        }
    }

    const handleSetStageOpponentJudgeEditItem = (item) => {
        setStageOpponentJudgeEditItem(item);
    }

    const removeStageOpponentJudge = (e, itemId) => {
        let list = formData.stage_opponent_judges;

        list.map((item, key) => {
            if (key === itemId) {
                list.splice(key, 1);
            }
        });

        setFormData(prevState => ({
            ...prevState,
            stage_opponent_judges: list
        }));
    }

    const addStageOpponentLawyer = (e, item) => {
        let list = formData.stage_opponent_lawyers;

        /**
         * check if the lawyer already exists in the list or not
         */
        let valid = true;

        list.map((listItem) => {
            if (listItem.contact === item.contact) {
                stageOpponentLawyerAddFormValidation(false);

                valid = false;

                return null;
            }
        });

        if (valid === true) {
            list.push(item);

            setFormData(prevState => ({
                ...prevState,
                stage_opponent_lawyers: list
            }));

            setStageOpponentLawyerAddFormValidation(true);
        }
    }

    const editStageOpponentLawyer = (e, editedItem) => {
        let list = formData.stage_opponent_lawyers;
        /**
         * check if the lawyer already exists in the list or not
         */
        let valid = true;

        list = list.map((listItem, key) => {
            if (key === editedItem?.key) {
                return {
                    contact: editedItem?.contact ?? '',
                    contact_name: editedItem?.contact_name ?? '',
                    contact_role: editedItem?.contact_role ?? '',
                    contact_role_name: editedItem?.contact_role_name ?? '',
                    comments: editedItem?.comments ?? ''
                }
            } else {
                return listItem
            }
        });

        if (valid === true) {
            setFormData(prevState => ({
                ...prevState,
                stage_opponent_lawyers: list
            }));

            setStageOpponentLawyerAddFormValidation(true);
        }
    }

    const handleSetStageOpponentLawyerEditItem = (item) => {
        setStageOpponentLawyerEditItem(item);
    }

    const removeStageOpponentLawyer = (e, itemId) => {
        let list = formData.stage_opponent_lawyers;

        list.map((item, key) => {
            if (key === itemId) {
                list.splice(key, 1);
            }
        });

        setFormData(prevState => ({
            ...prevState,
            stage_opponent_lawyers: list
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

        LitigationCaseStage.update(stage?.id, data).then((response) => {
            if (isFunction(globalState?.modal?.form?.submitCallback)) {
                globalState.modal.form.submitCallback();
            }

            globalStateDispatcher({
                modal: initialGlobalState?.modal
            });

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

            if (message === 'object') {
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
                },
                globalLoader: initialGlobalState?.globalLoader
            });
        }).finally(() => {
            if (!isFunction(globalState?.modal?.form?.submitCallback)) {
                globalStateDispatcher({
                    globalLoader: initialGlobalState?.globalLoader
                });
            }
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
                    <ExternalReferences
                        isExpanded={1}
                        stageExternalReferences={formData.stage_external_references}
                        openForm={setStageExternalReferencesAddFormModalState}
                        handleSetEditItem={handleSetStageExternalReferenceEditItem}
                        openEditForm={setStageExternalReferencesEditFormModalState}
                        removeItem={removeStageExternalReference}
                    />
                </FormGroup>
                <FormGroup
                    className="litigation-case-stage-badge-container"
                >
                    <FormControl>
                        <FormLabel>
                            <span>Stage:&nbsp;</span>
                            <Badge
                                currentStage={globalState?.modal?.form?.data?.litigationCase?.current_stage}
                            />
                        </FormLabel>
                    </FormControl>
                </FormGroup>
                <APAutocompleteList
                    label={t("client_position")}
                    options={clientPositionsList}
                    optionsLabel="title"
                    stateKey="client_position"
                    value={listValues.client_position}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <APAutocompleteList
                    label={t("stage_status")}
                    options={stageStatusesList}
                    optionsLabel="title"
                    stateKey="status"
                    value={listValues.status}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <APTextFieldInput
                    label={t("comments")}
                    stateKey="comments"
                    rows={5}
                    multiline={true}
                    value={formData.comments}
                    handleChange={handleObjectChange}
                />
                <APDatePicker
                    label={t("judgment_date")}
                    stateKey="sentenceDate"
                    format="yyyy-MM-dd"
                    value={formData.sentenceDate}
                    handleChange={handleDatePickerChange}
                />
                {/** stage opponents */}
                <APAutocompleteList
                    label={t("court_type")}
                    options={courtTypesList}
                    optionsLabel="title"
                    stateKey="court_type_id"
                    value={listValues.court_type_id}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <APAutocompleteList
                    label={t("court_degree")}
                    options={courtDegreesList}
                    optionsLabel="title"
                    stateKey="court_degree_id"
                    value={listValues.court_degree_id}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <APAutocompleteList
                    label={t("court_region")}
                    options={courtRegionsList}
                    optionsLabel="title"
                    stateKey="court_region_id"
                    value={listValues.court_region_id}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <APAutocompleteList
                    label={t("court")}
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
                        <OpponentJudges
                            isExpanded={1}
                            stageJudges={formData.stage_opponent_judges}
                            openForm={setStageOpponentJudgeAddFormState}
                            openEditForm={setStageOpponentJudgeEditFormState}
                            setEditItem={handleSetStageOpponentJudgeEditItem}
                            removeOpponentJudge={removeStageOpponentJudge}
                        />
                    </FormGroup>
                    <FormGroup>
                        <OpponentLawyers
                            isExpanded={1}
                            stageOpponentLawyers={formData.stage_opponent_lawyers}
                            openForm={setStageOpponentLawyerAddFormState}
                            openEditForm={setStageOpponentLawyerEditFormState}
                            setEditItem={handleSetStageOpponentLawyerEditItem}
                            removeOpponentLawyer={removeStageOpponentLawyer}
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
                        {moreFields ? t('less_fields') : t('more_fields')}
                    </Button>
                </Container>
            </form>
            <ExternalReferencesForm
                addStageExternalReference={addStageExternalReference}
                formModalState={stageExternalReferencesAddFormModalState}
                closeForm={setStageExternalReferencesAddFormModalState}
            />
            <ExternalReferencesEditForm
                handleEditItem={editStageExternalReference}
                item={stageExternalReferenceEditItem}
                formModalState={stageExternalReferencesEditFormModalState}
                closeForm={setStageExternalReferencesEditFormModalState}
            />
            <OpponentJudgesForm
                addStageOpponentJudge={addStageOpponentJudge}
                formModalState={stageOpponentJudgeAddFormState}
                closeForm={setStageOpponentJudgeAddFormState}
                formValidation={stageOpponentJudgeAddFormValidation}
            />
            <OpponentJudgesEditForm
                handleEditItem={editStageOpponentJudge}
                item={stageOpponentJudgeEditItem}
                formModalState={stageOpponentJudgeEditFormState}
                closeForm={setStageOpponentJudgeEditFormState}
            />
            <OpponentLawyersForm
                addStageOpponentLawyer={addStageOpponentLawyer}
                formModalState={stageOpponentLawyerAddFormState}
                closeForm={setStageOpponentLawyerAddFormState}
                formValidation={stageOpponentLawyerAddFormValidation}
            />
            <OpponentLawyersEditForm
                handleEditItem={editStageOpponentLawyer}
                item={stageOpponentLawyerEditItem}
                formModalState={stageOpponentLawyerEditFormState}
                closeForm={setStageOpponentLawyerEditFormState}
            />
        </MuiPickersUtilsProvider>
    );
});
