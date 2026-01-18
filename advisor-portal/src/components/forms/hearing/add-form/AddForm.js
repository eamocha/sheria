import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './AddForm.scss';

import {
    Context,
    initialGlobalState
} from '../../../../Store';

import {
    DEFAULT_AUTOCOMPLETE_PAGE_SIZE,
    FORMS_NAMES,
    LEGAL_CASES_CATEGORIES
} from '../../../../Constants';

import DateFnsUtils from '@date-io/date-fns';

import { MuiPickersUtilsProvider } from '@material-ui/pickers';

import {
    APTextFieldInput,
    APAutocompleteList,
    APCheckboxBtn,
    APDatePicker,
    APInlineDateTimePickersContainer,
    APTimePicker,
    APMultiFileUploadInput
} from './../../../common/APForm/APForm';

import {
    loadListWithLanguages,
    formatDate,
    buildErrorMessages,
    formatTime,
    isFunction,
    getValueFromLanguage
} from '../../../../APHelpers';

import {
    Collapse,
    makeStyles
} from '@material-ui/core';

import { isDate } from 'moment';

import AdvisorUser from '../../../../api/AdvisorUser';

import MiscList from '../../../../api/MiscList';

import LegalCase from '../../../../api/LegalCase';

import Hearing from './../../../../api/Hearing';

import { useTranslation } from 'react-i18next';

import { getActiveLanguageId } from '../../../../i18n';

import ChooseCaseLitigationDetail from '../../../common/APForm/ChooseCaseLitigation/ChooseCaseLitigationDetail';

import ChooseCaseLitigationDetailModal from '../../../common/APForm/ChooseCaseLitigation/ChooseCaseLitigationDetailModal';

export default React.memo((props) => {
    const formId = FORMS_NAMES.hearingAddForm;

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [legalCasesList, setLegalCasesList] = useState([]);
    const [hearingTypesList, setHearingTypesList] = useState([]);
    const [hearingAssigneesList, setHearingAssigneesList] = useState([]);
    const [stageStatusesList, setStageStatusesList] = useState([]);
    const [stagesList, setStagesList] = useState([]);

    const [t] = useTranslation();

    var addHearingOnLegalCase = globalState?.modal?.form?.data?.addHearingOnLegalCase ?? false;
    var legalCase = false;

    if (addHearingOnLegalCase) {
        legalCase = globalState?.modal?.form?.data?.legalCase;
    }

    var formDataStage = '';

    const [formData, setFormData] = useState({
        legal_case_id: legalCase ? legalCase?.id ?? '' : '',
        stage: formDataStage,
        startDate: formatDate(new Date()),
        startTime: new Date(),
        type: '',
        summary: '',
        comments: '',
        judged: 'no',
        judgmentDate: formatDate(new Date()),
        judgment: '',
        judgmentValue: '',
        assignees: [],
        assignees_user_type: 'AP', // should be always AP (which means the user type is an advisor)
        files: [],
        time_spent: ""
    });

    var currentStageName = 'None';
    var defaultValuesCurrentStageValue = 'None';

    const [listValues, listValuesDispatcher] = useState({
        legal_case_id: {
            subject: legalCase ? legalCase?.subject ?? '' : '',
            value: legalCase ? legalCase?.id ?? '' : ''
        },
        type: {
            title: '',
            value: ''
        },
        currentStage: {
            title: currentStageName,
            value: defaultValuesCurrentStageValue
        },
        time_spent: ""
    });

    const clearStage = () => {
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

    const useStyles = makeStyles({
        startDate: {
            width: '96%'
        },
        customTypography: {
            lineHeight: '34px'
        },
        fileInputRow: {
            marginTop: '15px'
        },
        addMoreFileInputRow: {
            marginTop: '20px'
        }
    });

    const classes = useStyles();
    useEffect(() => {
        loadData({
            lists: [
                "hearingTypes",
                "stages",
                "stageStatuses"
            ]
        });

        loadLegalCasesList(null, { page: 1, pageSize: DEFAULT_AUTOCOMPLETE_PAGE_SIZE });
    }, []);

    const loadData = (query) => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        MiscList.getList(query).then((response) => {
            bindHearingTypesList(response?.data?.data?.hearingTypes);
            bindStageStatusesList(response?.data?.data?.stageStatuses);
        }).then(() => {
            return AdvisorUser.getList();
        }).then((response) => {
            bindHearingAssigneesList(response?.data?.data);
        }).then(() => {
            if (addHearingOnLegalCase && legalCase) {
                return LegalCase.get(
                    legalCase?.id,
                    [
                        'stages',
                        'stages.stageName',
                        'stages.stageName.stageNameLanguages',
                        'stages.stageStatus',
                        'stages.stageStatus.stageStatusLanguages',
                        'stages.stageCourt',
                        'stages.stageCourtType',
                        'stages.stageCourtDegree',
                        'stages.stageCourtRegion',
                        'stages.stageExternalReferences',
                        'stages.legalCase.client.contact',
                        'stages.legalCase.client.company',
                        'stages.stageClientPosition'
                    ]
                );
            }
        }).then((response) => {
            if (response?.data?.data) {
                setStagesList(response.data.data?.stages);

                if (legalCase.stage) {
                    var stage = response.data.data?.stages.find(obj => obj.id == legalCase.stage);
                    if (stage) {
                        listValuesDispatcher(prevState => ({
                            ...prevState,
                            currentStage: {
                                title: getValueFromLanguage(stage.stage_name, 'stage_name_languages', getActiveLanguageId(), ''),
                                value: stage.id
                            }
                        }));
                    }
                }
            }
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
        });
    }
    const bindHearingTypesList = (data) => {
        let result = loadListWithLanguages(data, 'hearing_type_languages', 'name', 'id', formData.type);

        setHearingTypesList(result?.options);

        listValuesDispatcher(prevState => ({
            ...prevState,
            type: result?.currentItem
        }));
    }

    const bindStageStatusesList = (data) => {
        let result = loadListWithLanguages(data, 'stage_status_languages', 'name', 'id');

        setStageStatusesList(result?.options);
    }

    const bindHearingAssigneesList = (assignees) => {
        let list = assignees;
        let options = [];

        for (var i = 0; i < list.length; i++) {
            let item = list[i];

            options.push({
                title: item?.firstName + " " + item?.lastName,
                value: item?.id
            });
        }

        setHearingAssigneesList(options);
    }

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

        let filters = {
            category: {
                value: LEGAL_CASES_CATEGORIES.litigationCases
            }
        };

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

        if (state == "legal_case_id") {
            if (!addHearingOnLegalCase && stateValue) {
                setStagesList([]);

                LegalCase.get(
                    stateValue,
                    [
                        'stages',
                        'stages.stageName',
                        'stages.stageName.stageNameLanguages',
                        'stages.stageStatus',
                        'stages.stageStatus.stageStatusLanguages',
                        'stages.stageCourt',
                        'stages.stageCourtType',
                        'stages.stageCourtDegree',
                        'stages.stageCourtRegion',
                        'stages.stageExternalReferences',
                        'stages.legalCase.client.contact',
                        'stages.legalCase.client.company',
                        'stages.stageClientPosition'
                    ]
                ).then((response) => {
                    if (response?.data?.data) {
                        setStagesList(response.data.data?.stages);
                    }
                })
            }
        }
    }

    const handleDatePickerChange = (state, date, time = false) => {
        setFormData(prevState => ({
            ...prevState,
            [state]: date === null ? null : time ? (isDate(date) ? date : null) : formatDate(date)
        }));
    }

    const handleFilesChange = (files) => {
        setFormData(prevState => ({
            ...prevState,
            files: files
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

        let submitData = prepareRequestData(formData);

        Hearing.create(submitData).then((response) => {
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
                    text: "Hearing has been created successfully",
                    severity: "success"
                }
            });

            if (isFunction(globalState?.modal?.form?.submitCallback)) {
                globalState.modal.form.submitCallback('reload');
            }

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
            if (!isFunction(globalState?.modal?.form?.submitCallback)) {
                globalStateDispatcher({
                    globalLoader: globalState?.globalLoader
                });
            }
        });
    }

    const prepareRequestData = (data) => {
        let formData = new FormData();

        for (let [key, value] of Object.entries(data)) {
            // if (value && key !== "files") {
            //     if (key === "assignees") {
            //         for (var i = 0; i < value.length; i++) {
            //             formData.append("assignees[]", value[i]);
            //         }
            //     } else {
            //         if (key === "startTime" || key === "postponedTime") {
            //             value = formatTime(value);
            //         }

            //         formData.append(key, value);
            //     }
            // } else if (value && key === "files") {
            //     for (var i = 0; i < value.length; i++) {
            //         formData.append("files[]", value[i]?.value, 'value');
            //     }
            // }

            if (value && key === "files") {
                for (var i = 0; i < value.length; i++) {
                    formData.append("files[]", value[i]?.value);
                }
            } else {
                if (key === "assignees") {
                    for (var i = 0; i < value.length; i++) {
                        formData.append("assignees[]", JSON.stringify({ id: value[i], user_type: "AP" }));
                    }
                } else {
                    if (key === "startTime" || key === "postponedTime") {
                        value = formatTime(value);
                    }

                    formData.append(key, value);
                }
            }
        }

        return formData;
    }

    const [chooseCaseLitigationDetailModalState, setChooseCaseLitigationDetailModalState] = useState(false);

    const handleChooseCaseLitigationDetailModalState = (state) => {
        setChooseCaseLitigationDetailModalState(state);
    }

    const handleChooseCaseLitigationDetailModalClose = () => {
        setChooseCaseLitigationDetailModalState(false);
    }

    const handleStageChange = (event, stage) => {
        event.persist();
        setFormData(prevState => ({
            ...prevState,
            stage: stage.id
        }));

        listValuesDispatcher(prevState => ({
            ...prevState,
            currentStage: {
                title: getValueFromLanguage(stage.stage_name, 'stage_name_languages', getActiveLanguageId(), ''),
                value: stage.id
            }
        }));
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
                    label={t("litigation_case")}
                    options={legalCasesList}
                    optionsLabel="subject"
                    optionsLabelArray={['prefix', "subject"]}
                    stateKey="legal_case_id"
                    value={listValues.legal_case_id}
                    valueKey="id"
                    required
                    textRequired={true}
                    onChange={handleListChange}
                    textOnChange={getLegalCasesList}
                    disabled={addHearingOnLegalCase === true}
                />

                <ChooseCaseLitigationDetail
                    stageTitle={listValues.currentStage.title}
                    clearStage={clearStage}
                    handleChooseCaseLitigationDetailModalState={handleChooseCaseLitigationDetailModalState}
                    classes={classes}
                />

                <APAutocompleteList
                    label={t("type")}
                    options={hearingTypesList}
                    optionsLabel="title"
                    stateKey="type"
                    value={listValues.type}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <APInlineDateTimePickersContainer
                    datePicker={
                        <APDatePicker
                            className="hearing-start-date"
                            label={t("date")}
                            stateKey="startDate"
                            required
                            format="yyyy-MM-dd"
                            value={formData.startDate}
                            handleChange={handleDatePickerChange}
                        />
                    }
                    timePicker={
                        <APTimePicker
                            ampm={false}
                            label="HH:MM"
                            stateKey="startTime"
                            required
                            value={formData.startTime}
                            handleChange={handleDatePickerChange}
                        />
                    }
                />
                <APAutocompleteList
                    multiple
                    label={t("assignee_s")}
                    options={hearingAssigneesList}
                    optionsLabel="title"
                    stateKey="assignees"
                    value={listValues.assignees}
                    valueKey="value"
                    onChange={handleListChange}
                    multipleSelection={true}
                    changeDefaultValues={true}
                />

                <APTextFieldInput
                    label="Time Spent"
                    stateKey="time_spent"
                    value={formData.time_spent}
                    handleChange={handleObjectChange}
                />
                <APTextFieldInput
                    label={t("comments")}
                    stateKey="comments"
                    rows={3}
                    multiline={true}
                    value={formData.comments}
                    handleChange={handleObjectChange}
                />
                <APTextFieldInput
                    label={t("summary")}
                    stateKey="summary"
                    rows={5}
                    multiline={true}
                    value={formData.summary}
                    handleChange={handleObjectChange}
                />
                <APCheckboxBtn
                    label={t("judged_question")}
                    stateKey="judged"
                    color="primary"
                    value={formData.judged}
                    handleChange={handleObjectChange}
                    checkedValue="yes"
                    uncheckedValue="no"
                />
                <Collapse
                    in={formData.judged === "yes"}
                >
                    <APDatePicker
                        label={t("judgment_date")}
                        stateKey="judgmentDate"
                        format="yyyy-MM-dd"
                        value={formData.judgmentDate}
                        handleChange={handleDatePickerChange}
                    />
                    <APTextFieldInput
                        label={t("judgment")}
                        stateKey="judgment"
                        rows={3}
                        multiline={true}
                        value={formData.judgment}
                        handleChange={handleObjectChange}
                    />
                    <APAutocompleteList
                        label={t("stage_status")}
                        options={stageStatusesList}
                        optionsLabel="title"
                        stateKey="stage_status"
                        value={listValues.stageStatus}
                        valueKey="value"
                        onChange={handleListChange}
                    />
                    <APTextFieldInput
                        label={t("judgment_value")}
                        stateKey="judgmentValue"
                        type="number"
                        value={formData.judgmentValue}
                        handleChange={handleObjectChange}
                    />
                </Collapse>
                <APMultiFileUploadInput
                    handleFilesChange={handleFilesChange}
                // classes={classes}
                />

                {
                    listValues.currentStage ?
                        <ChooseCaseLitigationDetailModal
                            modalState={chooseCaseLitigationDetailModalState}
                            setModalState={handleChooseCaseLitigationDetailModalState}
                            handleModalClose={handleChooseCaseLitigationDetailModalClose}
                            data={stagesList}
                            handleChange={handleStageChange}
                            stage={formData.stage}
                        />
                        :
                        null
                }
            </form>
        </MuiPickersUtilsProvider>
    );
});
