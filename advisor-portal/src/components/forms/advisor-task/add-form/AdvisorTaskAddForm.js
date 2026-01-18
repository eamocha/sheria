import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './AdvisorTaskAddForm.scss';

import {
    DEFAULT_AUTOCOMPLETE_PAGE_SIZE,
    FORMS_NAMES,
    LEGAL_CASES_CATEGORIES,
    PRIORITY_OPTIONS
} from './../../../../Constants';

import { MuiPickersUtilsProvider } from '@material-ui/pickers';

import DateFnsUtils from '@date-io/date-fns';

import APAutocompleteList from './../../../common/APForm/APAutocompleteList/APAutocompleteList.lazy';

import APTextFieldInput from './../../../common/APForm/APTextFieldInput/APTextFieldInput.lazy';

import MiscList from './../../../../api/MiscList';

import {
    formatDate,
    getAdvisorUserFullName,
    getValueFromLanguage,
    loadAdvisorUsersList,
    loadListWithLanguages,
    defaultLoadList,
    isFunction,
    formatTime,
    buildErrorMessages
} from './../../../../APHelpers';

import {
    Context,
    initialGlobalState
} from './../../../../Store';

import AdvisorUser from './../../../../api/AdvisorUser';

import LegalCase from './../../../../api/LegalCase';

import APPrioritySign from './../../../common/ap-priority-sign/APPrioritySign.lazy';

import { isValid } from 'date-fns';

import APDatePicker from './../../../common/APForm/APDatePicker/APDatePicker.lazy';

import {
    Button,
    Collapse,
    Container,
    LinearProgress
} from '@material-ui/core';

import ExpandLessIcon from '@material-ui/icons/ExpandLess';

import ExpandMoreIcon from '@material-ui/icons/ExpandMore';

import APMultiFileUploadInput from './../../../common/APForm/APMultiFileUploadInput/APMultiFileUploadInput.lazy';

import AdvisorTask from './../../../../api/AdvisorTask';

import {
    Container as LegalCaseLitigationDetailsPickerForm,
    PickerInput as LegalCaseLitigationDetailsPicker
} from '../../legal-case-litigation-details/LegalCaseLitigationDetailsPickerForm';
import { useTranslation } from 'react-i18next';
import { getActiveLanguageId } from '../../../../i18n';


export default React.memo((props) => {
    const formId = FORMS_NAMES.advisorTaskAddForm;
    const today = new Date();
    const tomorrow = new Date(today.getTime() + (24 * 60 * 60 * 1000));

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [advisorTaskTypesList, setAdvisorTaskTypesList] = useState([]);
    const [advisorTaskLocationsList, setAdvisorTaskLocationsList] = useState([]);
    const [advisorTaskAssigneesList, setAdvisorTaskAssigneesList] = useState([]);
    const [advisorTaskReportersList, setAdvisorTaskReportersList] = useState([]);
    const [advisorTaskContributorsList, setAdvisorTaskContributorsList] = useState([]);
    const [legalCasesList, setLegalCasesList] = useState([]);

    const [legalCaseLitigationDetailPicker, setLegalCaseLitigationDetailPicker] = useState(false);

    const [moreFields, setMoreFields] = useState(false);
    const [dataLoaded, setDataLoaded] = useState(false);

    const [litigationCaseWithStages, setLitigationCaseWithStages] = useState('');
    const [loadingLitigationCaseStages, setLoadingLitigationCaseStages] = useState(false);
    const [showLitigationCaseStages, setShowLitigationCaseStages] = useState(false);
    const [showLegalCaseLitigationDetailsPickerForm, setShowLegalCaseLitigationDetailsPickerForm] = useState(false);

    const { t } = useTranslation();

    var currentStage = globalState?.modal?.form?.data?.currentStage;
    var currentStageName = 'None';
    var formDataStage = '';
    var listValuesCurrentStageValue = 'None';

    if (currentStage) {
        if (currentStage?.stage_name) {
            currentStageName = getValueFromLanguage(currentStage.stage_name, 'stage_name_language', 1);
        }

        formDataStage = currentStage?.id ?? '';
        listValuesCurrentStageValue = currentStage?.legal_case_stage ?? 'None';
    }

    var addAdvisorTaskOnLegalCase = globalState?.modal?.form?.data?.addAdvisorTaskOnLegalCase ?? false;
    var legalCase = false;

    if (addAdvisorTaskOnLegalCase) {
        legalCase = globalState?.modal?.form?.data?.legalCase;
    }

    const [formData, setFormData] = useState({
        legal_case_id: legalCase ? legalCase?.id ?? '' : '',
        stage: globalState?.modal?.form?.data?.legalCase?.stage,
        advisor_id: globalState?.user?.data?.id, // the task assignee, no longer used, had been replaced by assigned_to
        assigned_to: globalState?.user?.data?.id,
        reporter: globalState?.user?.data?.id,
        due_date: formatDate(tomorrow),
        priority: 'medium',
        advisor_task_location_id: '',
        description: '',
        advisor_task_type_id: '',
        estimated_effort: '',
        shared_with_users: [],
        files: []
    });

    const [listValues, listValuesDispatcher] = useState({
        advisor_task_type_id: {
            title: '',
            value: ''
        },
        advisor_task_location_id: {
            title: '',
            value: ''
        },
        legal_case_id: {
            subject: legalCase ? legalCase?.subject ?? '' : '',
            value: legalCase ? legalCase?.id ?? '' : ''
        },
        assigned_to: {
            title: getAdvisorUserFullName(globalState?.user?.data),
            value: globalState?.user?.data?.id
        },
        reporter: {
            title: getAdvisorUserFullName(globalState?.user?.data),
            value: globalState?.user?.data?.id
        },
        priority: {
            title: 'medium',
            value: 'medium'
        },
        shared_with_users: [],
        currentStage: { // the current stage can be changed directly through a list, so it's different than the other listValues attributes
            title: currentStageName,
            value: listValuesCurrentStageValue
        },
    });

    useEffect(() => {

        setDataLoaded(false);

        loadListsData();

        loadLegalCasesList(null, { page: 1, pageSize: DEFAULT_AUTOCOMPLETE_PAGE_SIZE });
    }, []);

    const loadListsData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        MiscList.getList({
            lists: [
                "advisorTaskTypes",
                "advisorTaskLocations"
            ]
        }).then((response) => {
            loadAdvisorTaskTypesList(response?.data?.data?.advisorTaskTypes);

            loadAdvisorTaskLocationsList(response?.data?.data?.advisorTaskLocations);
        }).then((response) => {
            return AdvisorUser.getList();
        }).then((response) => {

            // Assignees & Reporters & Contributors have the same data in Add Form
            loadAdvisorTaskAssigneesList(response?.data?.data);
            loadAdvisorTaskReportersList(response?.data?.data);
            loadAdvisorTaskContributorsList(response?.data?.data);
        }).catch((error) => {
            let message = buildErrorMessages(error?.response?.data?.message);

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

            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });

            setDataLoaded(true);
        });
    }

    const loadAdvisorTaskTypesList = (data) => {
        let result = loadListWithLanguages(data, 'advisor_task_type_languages', 'name', 'id');

        setAdvisorTaskTypesList(result?.options);

        /**
         * Here we are setting the first task type as default value
         * also the formData.advisor_task_type_id should be updated
         */
        listValuesDispatcher(prevState => ({
            ...prevState,
            advisor_task_type_id: result?.defaultItem
        }));

        setFormData(prevState => ({
            ...prevState,
            advisor_task_type_id: result?.defaultItemValue
        }));
    }

    const loadAdvisorTaskLocationsList = (data) => {
        let result = defaultLoadList(data, 'name', 'id');

        setAdvisorTaskLocationsList(result);
    }

    const loadAdvisorTaskAssigneesList = (data) => {
        let result = loadAdvisorUsersList(data);

        setAdvisorTaskAssigneesList(result);
    };

    const loadAdvisorTaskReportersList = (data) => {
        let result = loadAdvisorUsersList(data);

        setAdvisorTaskReportersList(result);
    };

    const loadAdvisorTaskContributorsList = (data) => {
        let result = loadAdvisorUsersList(data);

        setAdvisorTaskContributorsList(result);
    };

    const getLegalCasesList = (e) => {
        e.persist();

        let value = e.target.value;

        if (value.length == 0) {
            loadLegalCasesList(null, { page: 1, pageSize: DEFAULT_AUTOCOMPLETE_PAGE_SIZE });
        }
        if (value.length >= 2) {
            loadLegalCasesList(value);
        }
    }

    const loadLegalCasesList = (filterValue = null, pagination = null) => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        let filters = {};

        if (filterValue) {
            filters['SubjectOrPrefix'] =
            {
                subject: '%' + filterValue + '%',
                prefix: '%' + filterValue + '%',
                operator: 'or'
            };
        }

        LegalCase.getList(filters, [], pagination).then((response) => {

            if (pagination)
                setLegalCasesList(response?.data?.data?.data);
            else
                setLegalCasesList(response?.data?.data);

        }).catch((error) => {

        }).finally(() => {

            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });
        });

    }

    const handleObjectChange = (e, stateKey) => {
        e.persist();

        setFormData(prevState => ({
            ...prevState,
            [stateKey]: e?.target?.value
        }));
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

    const handleLegalCaseListChange = (state, stateValue, defaultValues, multipleSelection, defaultValuesWithMultipleSelection, changeDefaultValues, additionalData = {}) => {
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

        // if the select legal case is a litigation case, load the stages, else: reset & hide the picker
        if (additionalData?.selectedObject?.category === LEGAL_CASES_CATEGORIES.litigationCases) {
            getLitigationCaseStages(additionalData?.selectedObject);
        } else {
            setLoadingLitigationCaseStages(false);
            setShowLitigationCaseStages(false);
        }
    }

    const handleDatePickerChange = (state, date, time = false) => {
        setFormData(prevState => ({
            ...prevState,
            [state]: date === null ? null : time ? (isValid(date) ? date : null) : formatDate(date)
        }));
    }

    const handleFilesChange = (files) => {
        setFormData(prevState => ({
            ...prevState,
            files: files
        }));
    }

    const getLitigationCaseStages = (litigationCase) => {
        setLoadingLitigationCaseStages(true);

        LegalCase.get(litigationCase?.id, [
            'stages.stageName.stageNameLanguages',
            'stages.stageCourt',
            'stages.stageCourtType',
            'stages.stageCourtDegree',
            'stages.stageCourtRegion',
            'stages.stageExternalReferences',
            'stages.stageStatus.stageStatusLanguages',
            'stages.legalCase.client.contact',
            'stages.legalCase.client.company',
            'stages.stageClientPosition'
        ]).then((response) => {

            setLitigationCaseWithStages(response?.data?.data);

            setShowLitigationCaseStages(true);
        }).catch((error) => {

        }).finally(() => {
            setLoadingLitigationCaseStages(false);
        });
    }

    const handleLegalCaseLitigationDetailPickerClose = () => {
        setLegalCaseLitigationDetailPicker(false);
    }

    const handleStageChange = (e, stage) => {
        e.persist();

        setFormData(prevState => ({
            ...prevState,
            stage: stage?.id
        }));

        listValuesDispatcher(prevState => ({
            ...prevState,
            currentStage: {
                title: getValueFromLanguage(stage?.stage_name, 'stage_name_languages', getActiveLanguageId(), ''),
                value: stage?.id
            }
        }));
    }

    const clearSelectedStage = () => {
        setFormData(prevState => ({
            ...prevState,
            stage: null
        }));

        listValuesDispatcher(prevState => ({
            ...prevState,
            currentStage: {
                title: 'None',
                value: 'None'
            }
        }));
    }

    const submit = (e) => {
        e.preventDefault();

        let data = prepSubmitFormData();

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        AdvisorTask.create(data).then((response) => {
            if (isFunction(globalState?.modal?.form?.submitCallback)) {
                globalState.modal.form.submitCallback('reload');
            }

            /**
             * Name of the grid that should be reloaded after successfull submit
             */
            let gridToReload = globalState?.modal?.form?.targetGrid;

            globalStateDispatcher({
                gridToReload: gridToReload,
                modal: initialGlobalState?.modal
            });

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: "Task has been created successfully",
                    severity: "success"
                }
            });
        }).catch((error) => {
            let message = buildErrorMessages(error?.response?.data?.message);

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: message,
                    severity: "error"
                },
                globalLoader: globalState?.globalLoader
            });
        }).finally(() => {
            if (!isFunction(globalState?.modal?.form?.submitCallback)) {
                globalStateDispatcher({
                    globalLoader: globalState?.globalLoader
                });
            }
        });
    }

    const prepSubmitFormData = () => {
        let submitFormData = new FormData();

        for (let [key, value] of Object.entries(formData)) {
            if (value && key !== "files") {
                if (key === "assignees") {
                    for (var i = 0; i < value.length; i++) {
                        submitFormData.append("assignees[]", value[i]);
                    }
                } else {
                    if (key === "startTime" || key === "postponedTime") {
                        value = formatTime(value);
                    }

                    submitFormData.append(key, value);
                }
            } else if (value && key === "files") {
                for (var i = 0; i < value.length; i++) {
                    submitFormData.append("files[]", value[i]?.value);
                }
            }
        }

        return submitFormData;
    }

    if (!dataLoaded) {
        return <LinearProgress
            size={50}
        />
    }

    return (
        <MuiPickersUtilsProvider
            utils={DateFnsUtils}
        >
            <form
                id={formId}
                onSubmit={(e) => submit(e)}
            >
                <APAutocompleteList
                    label={t("task_type")}
                    required
                    textRequired={true}
                    options={advisorTaskTypesList}
                    optionsLabel="title"
                    stateKey="advisor_task_type_id"
                    value={listValues.advisor_task_type_id}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <APTextFieldInput
                    label={t("description")}
                    stateKey="description"
                    rows={5}
                    multiline={true}
                    value={formData.description}
                    required
                    handleChange={handleObjectChange}
                />
                <APAutocompleteList
                    label={t("related_matter")}
                    options={legalCasesList}
                    optionsLabel="subject"
                    optionsLabelArray={['prefix', "subject"]}
                    stateKey="legal_case_id"
                    value={listValues.legal_case_id}
                    valueKey="id"
                    onChange={handleLegalCaseListChange}
                    textOnChange={getLegalCasesList}
                    disabled={addAdvisorTaskOnLegalCase === true}
                />
                {
                    loadingLitigationCaseStages ?
                        <LinearProgress
                            className="linear-progress"
                            size={50}
                        />
                        :
                        null
                }
                {
                    showLitigationCaseStages ?
                        <LegalCaseLitigationDetailsPicker
                            litigationCaseWithStages={litigationCaseWithStages}
                            currentStage={listValues.currentStage}
                            setPickerFormState={setShowLegalCaseLitigationDetailsPickerForm}
                            clearSelectedStage={clearSelectedStage}
                        />
                        :
                        null
                }
                <APAutocompleteList
                    label={t("assignee")}
                    options={advisorTaskAssigneesList}
                    optionsLabel="title"
                    stateKey="assigned_to"
                    value={listValues.assigned_to}
                    valueKey="value"
                    onChange={handleListChange}
                    required
                    textRequired={true}
                />
                <APAutocompleteList
                    label={t("requested_by")}
                    options={advisorTaskReportersList}
                    optionsLabel="title"
                    stateKey="reporter"
                    value={listValues.reporter}
                    valueKey="value"
                    onChange={handleListChange}
                    required
                    textRequired={true}
                />
                <APAutocompleteList
                    label={t("priority")}
                    options={PRIORITY_OPTIONS}
                    optionsLabel="title"
                    stateKey="priority"
                    value={listValues.priority}
                    valueKey="value"
                    onChange={handleListChange}
                    textOnChange={handleObjectChange}
                    renderOption={option => <APPrioritySign
                        priority={option?.["title"]}
                    />
                    }
                />
                <APDatePicker
                    label={t("due_date")}
                    stateKey="due_date"
                    format="yyyy-MM-dd"
                    value={formData.due_date}
                    required
                    handleChange={handleDatePickerChange}
                />
                <Collapse
                    in={moreFields}
                >
                    <APTextFieldInput
                        label={t("estimated_effort")}
                        stateKey="estimated_effort"
                        value={formData.estimated_effort}
                        handleChange={handleObjectChange}
                    />
                    <APAutocompleteList
                        multiple
                        multipleSelection={true}
                        label={t("contributors")}
                        options={advisorTaskContributorsList}
                        optionsLabel="title"
                        stateKey="shared_with_users"
                        value={listValues.shared_with_users}
                        valueKey="value"
                        onChange={handleListChange}
                        textOnChange={handleListChange}
                    />
                    <APAutocompleteList
                        label={t("location")}
                        options={advisorTaskLocationsList}
                        optionsLabel="title"
                        stateKey="advisor_task_location_id"
                        value={listValues.advisor_task_location_id}
                        valueKey="value"
                        onChange={handleListChange}
                        textOnChange={handleListChange}
                    />
                    <APMultiFileUploadInput
                        handleFilesChange={handleFilesChange}
                    />
                </Collapse>
                <Container
                    className="more-fields-btn-container"
                >
                    <Button
                        className="more-fields-btn"
                        variant="text"
                        color="primary"
                        onClick={() => setMoreFields(prevState => !prevState)}
                        startIcon={moreFields ? <ExpandLessIcon /> : <ExpandMoreIcon />}
                    >
                        {moreFields ? t('less_fields') : t('more_fields')}
                    </Button>
                </Container>
                <LegalCaseLitigationDetailsPickerForm
                    modalState={showLegalCaseLitigationDetailsPickerForm}
                    setModalState={setShowLegalCaseLitigationDetailsPickerForm}
                    stageId={formData.stage}
                    litigationCaseWithStages={litigationCaseWithStages}
                    handleStageChange={handleStageChange}
                />
            </form>
        </MuiPickersUtilsProvider>
    );
});
