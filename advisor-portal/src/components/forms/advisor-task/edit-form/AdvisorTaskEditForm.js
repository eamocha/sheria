import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './AdvisorTaskEditForm.scss';

import {
    DEFAULT_AUTOCOMPLETE_PAGE_SIZE,
    FORMS_NAMES,
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
    formatTime
} from './../../../../APHelpers';

import {
    Context,
    initialGlobalState
} from './../../../../Store';

import AdvisorUser from './../../../../api/AdvisorUser';

import LegalCase from './../../../../api/LegalCase';

import APPrioritySign from './../../../common/ap-priority-sign/APPrioritySign.lazy';

import { isValid } from 'date-fns';

import APDatePicker from './../../../common/APForm/APDatePicker/APDatePicker';

import {
    Button,
    Collapse,
    Container
} from '@material-ui/core';

import ExpandLessIcon from '@material-ui/icons/ExpandLess';

import ExpandMoreIcon from '@material-ui/icons/ExpandMore';

import APMultiFileUploadInput from '../../../common/APForm/APMultiFileUploadInput/APMultiFileUploadInput';

import AdvisorTask from '../../../../api/AdvisorTask';
import { useTranslation } from 'react-i18next';
import { getActiveLanguageId } from '../../../../i18n';

export default React.memo((props) => {
    const formId = FORMS_NAMES.advisorTaskEditForm;

    const today = new Date();
    const tomorrow = new Date(today.getTime() + (24 * 60 * 60 * 1000));

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [advisorTask, setAdvisorTask] = useState(globalState?.modal?.form?.data?.advisorTask);

    const [advisorTaskTypesList, setAdvisorTaskTypesList] = useState([]);
    const [advisorTaskLocationsList, setAdvisorTaskLocationsList] = useState([]);
    const [advisorTaskAssigneesList, setAdvisorTaskAssigneesList] = useState([]);
    const [advisorTaskReportersList, setAdvisorTaskReportersList] = useState([]);
    const [advisorTaskContributorsList, setAdvisorTaskContributorsList] = useState([]);
    const [legalCasesList, setLegalCasesList] = useState([]);

    const [legalCaseLitigationDetailPicker, setLegalCaseLitigationDetailPicker] = useState(false);

    const [moreFields, setMoreFields] = useState(false);

    const { t } = useTranslation();

    var currentStage = globalState?.modal?.form?.data?.currentStage;
    var currentStageName = 'None';
    var formDataStage = '';
    var listValuesCurrentStageValue = 'None';

    let advisorTaskSharedWithUsersIds = [];
    let advisorTaskSharedWithUsersList = [];

    if (advisorTask?.advisor_task_shared_with_users?.length > 0) {
        advisorTaskSharedWithUsersIds = advisorTask?.advisor_task_shared_with_users.map(item => {
            return item?.advisor_id
        });

        advisorTaskSharedWithUsersList = advisorTask?.advisor_task_shared_with_users.map(item => {
            return {
                title: item?.user ? getAdvisorUserFullName(item.user) : '',
                value: item?.advisor_id
            }
        });
    }

    if (currentStage) {
        if (currentStage?.stage_name) {
            currentStageName = getValueFromLanguage(currentStage.stage_name, 'stage_name_language', 1);
        }

        formDataStage = currentStage?.id ?? '';
        listValuesCurrentStageValue = currentStage?.legal_case_stage ?? 'None';
    }

    const [formData, setFormData] = useState({
        legal_case_id: advisorTask?.legal_case_id ?? '',
        stage: formDataStage,
        advisor_id: advisorTask?.advisor_id ?? '',
        assigned_to: advisorTask?.assigned_to ?? '',
        reporter: advisorTask?.reporter ?? '',
        due_date: advisorTask?.due_date ?? formatDate(tomorrow),
        priority: advisorTask?.priority ?? 'medium',
        advisor_task_location_id: advisorTask?.advisor_task_location_id ?? '',
        description: advisorTask?.description ?? '',
        advisor_task_type_id: advisorTask?.advisor_task_type_id ?? '',
        estimated_effort: advisorTask?.estimated_effort ?? '',
        shared_with_users: advisorTaskSharedWithUsersIds,
        files: []
    });

    const [listValues, listValuesDispatcher] = useState({
        advisor_task_type_id: {
            title: advisorTask?.advisor_task_type ? getValueFromLanguage(advisorTask?.advisor_task_type, 'advisor_task_type_languages', getActiveLanguageId()) : '',
            value: advisorTask?.advisor_task_type_id ?? ''
        },
        advisor_task_location_id: {
            title: advisorTask?.advisor_task_location ? advisorTask?.advisor_task_location?.name : '',
            value: advisorTask?.advisor_task_location_id ?? ''
        },
        legal_case_id: {
            subject: advisorTask?.legal_case ? advisorTask?.legal_case?.subject : '',
            value: advisorTask?.legal_case_id ?? ''
        },
        assigned_to: {
            title: getAdvisorUserFullName(advisorTask?.assignee ? advisorTask?.assignee : globalState?.user?.data),
            value: advisorTask?.assigned_to ?? globalState?.user?.data?.id
        },
        reporter: {
            title: getAdvisorUserFullName(advisorTask?.advisor_task_reporter ? advisorTask?.advisor_task_reporter : globalState?.user?.data),
            value: advisorTask?.reporter ?? globalState?.user?.data?.id
        },
        priority: {
            title: 'medium',
            value: advisorTask?.priority ?? 'medium'
        },
        shared_with_users: advisorTaskSharedWithUsersList,
        currentStage: {
            title: currentStageName,
            value: listValuesCurrentStageValue
        },
    });

    useEffect(() => {
        loadListsData();
        if (!advisorTask?.legal_case_id)
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

    const loadAdvisorTaskTypesList = (data) => {
        let result = loadListWithLanguages(data, 'advisor_task_type_languages', 'name', 'id');

        setAdvisorTaskTypesList(result?.options);

        /**
         * Here we are setting the first task type as default value
         * also the formData.advisor_task_type_id should be updated
         */
        // listValuesDispatcher(prevState => ({
        //     ...prevState,
        //     advisor_task_type_id: result?.defaultItem
        // }));

        // setFormData(prevState => ({
        //     ...prevState,
        //     advisor_task_type_id: result?.defaultItemValue
        // }));
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
            }
        };

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

    const handleLegalCaseLitigationDetailPickerClose = () => {
        setLegalCaseLitigationDetailPicker(false);
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

        data['_method'] = 'PUT';

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        AdvisorTask.update(advisorTask?.id, data).then((response) => {
            if (isFunction(globalState?.modal?.form?.submitCallback)) {
                globalState.modal.form.submitCallback('reload');
            }

            globalStateDispatcher({
                modal: initialGlobalState?.modal
            });

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: "Task has been updated successfully",
                    severity: "success"
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
                globalLoader: globalState?.globalLoader
            });
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
                    onChange={handleListChange}
                    textOnChange={getLegalCasesList}
                />
                {
                    // listValues.currentStage.value && globalState?.modal?.form?.openedFromMainMenu ?
                    // <APLegalCaseLitigationDetailPicker
                    //     stageTitle={listValues.currentStage.title}
                    //     clearSelectedStage={clearSelectedStage}
                    //     setPickerState={setLegalCaseLitigationDetailPicker}
                    // />
                    // :
                    // null
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
            </form>
        </MuiPickersUtilsProvider>
    );
});
