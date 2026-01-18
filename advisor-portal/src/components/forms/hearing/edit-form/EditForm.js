import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './EditForm.scss';

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
    getValueFromLanguage,
    getAdvisorUserFullName
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

import ChooseCaseLitigationDetail from '../../../common/APForm/ChooseCaseLitigation/ChooseCaseLitigationDetail.lazy';

import ChooseCaseLitigationDetailModal from '../../../common/APForm/ChooseCaseLitigation/ChooseCaseLitigationDetailModal';

import { getActiveLanguageId } from '../../../../i18n';

import { OpenInBrowser } from '@material-ui/icons';

export default React.memo((props) => {
    const formId = FORMS_NAMES.hearingEditForm;

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [hearing, setHearing] = useState(globalState?.modal?.form?.data?.hearing);

    const [legalCasesList, setLegalCasesList] = useState([]);
    const [hearingTypesList, setHearingTypesList] = useState([]);
    const [hearingAssigneesList, setHearingAssigneesList] = useState([]);
    const [stageStatusesList, setStageStatusesList] = useState([]);
    const [stagesList, setStagesList] = useState([]);

    // These two states are used only in case of postponement & in Edit Mode only
    const [showAddNewHearing, setShowAddNewHearing] = useState(false);
    const [disableAddNewHearing, setDisableAddNewHearing] = useState(false);

    const [t] = useTranslation();

    var addHearingOnLegalCase = globalState?.modal?.form?.data?.addHearingOnLegalCase ?? false;
    var legalCase = false;

    if (addHearingOnLegalCase) {
        legalCase = globalState?.modal?.form?.data?.legalCase;
    }

    const [formData, setFormData] = useState({
        legal_case_id: hearing?.legal_case?.id ?? '',
        stage: hearing?.stage ?? '',
        startDate: hearing?.startDate ?? formatDate(new Date()),
        startTime: (new Date(hearing?.startDate + " " + hearing?.startTime)) ?? new Date(),
        type: hearing?.type ?? '',
        summary: hearing?.summary ?? '',
        comments: hearing?.comments ?? '',
        judged: hearing?.judged ?? 'no',
        judgmentDate: hearing?.judgmentDate ?? formatDate(new Date()),
        judgment: hearing?.judgment ?? '',
        judgmentValue: hearing?.legal_case?.judgmentValue ?? '',
        postponedDate: hearing?.postponedDate ?? null,
        postponedTime: hearing?.postponedDate ? (new Date(hearing?.postponedDate + " " + hearing?.postponedTime)) : null,
        reasons_of_postponement: hearing?.reasons_of_postponement ?? '',
        assignees: hearing?.assignees ? hearing?.assignees?.filter((item) => {
            if (item.user_type == "AP") {
                return item;
            }
        }).map((item, index) => {
            return item?.user?.id
        }) : [],
        time_spent: ""
        //assignees_user_type: hearing?.assignees.length > 0 ? hearing?.assignees[0].user_type : "AP", // should be always AP (which means the user type is an advisor)
    });

    var currentStageName = 'None';
    var defaultValuesCurrentStageValue = 'None';

    const [listValues, listValuesDispatcher] = useState({
        legal_case_id: {
            subject: hearing?.legal_case?.subject ?? '',
            value: hearing?.legal_case?.id ?? ''
        },
        type: {
            title: getValueFromLanguage(hearing?.hearing_type, 'hearing_type_languages', getActiveLanguageId(), ''),
            value: hearing?.hearing_type?.id ?? ''
        },
        assignees: hearing?.assignees ? hearing?.assignees?.filter((item) => {
            if (item.user_type == "AP") {
                return item;
            }
        }).map((item, index) => {

            return {
                title: item?.user?.firstName + " " + item?.user?.lastName,
                value: item?.user?.id
            }

        }) : [],
        core_assignees: hearing?.assignees ? hearing?.assignees?.filter((item) => {
            if (item.user_type == "A4L") {
                return item;
            }
        }).map((item, index) => {
            return {
                title: item?.user?.user_profile?.firstName + " " + item?.user?.user_profile?.lastName,
                value: item?.user?.user_profile?.id
            }

        }) : [],
        stageStatus: {
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
            if (addHearingOnLegalCase || hearing?.legal_case?.id) {
                return LegalCase.get(
                    hearing?.legal_case?.id,
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
                setLegalCasesList([response?.data?.data]);

                setStagesList(response.data.data?.stages);

                if (hearing.stage) {
                    var stage = response.data.data?.stages.find(obj => obj.id == hearing.stage);

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
        if (state === 'postponedDate' || state === 'postponedTime') {
            let postponementOtherState = false;

            if (state === 'postponedDate') {
                postponementOtherState = formData.postponedTime !== null;

                setShowAddNewHearing(true);

                setDisableAddNewHearing(date === null || !postponementOtherState);

                setFormData(prevState => ({
                    ...prevState,
                    [state]: date === null ? null : time ? (isDate(date) ? date : null) : formatDate(date),
                    // addNewHearing:  date !== null && postponementOtherState
                }));
            } else if (state === 'postponedTime') {
                postponementOtherState = formData.postponedDate !== null;

                setShowAddNewHearing(true);

                setDisableAddNewHearing(date === null || !postponementOtherState);

                setFormData(prevState => ({
                    ...prevState,
                    [state]: date,
                    // addNewHearing:  date !== null && postponementOtherState
                }));
            }
        } else {
            setFormData(prevState => ({
                ...prevState,
                [state]: date === null ? null : time ? (isDate(date) ? date : null) : formatDate(date)
            }));
        }
    }

    const handleFilesChange = (files) => {
        setFormData(prevState => ({
            ...prevState,
            files: files
        }));
    }

    const handleToggleObjectChange = (e, stateKey) => {
        e.persist();

        setFormData(prevState => ({
            ...prevState,
            [stateKey]: !prevState[stateKey]
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

        Hearing.update(hearing?.id, submitData).then((response) => {
            globalStateDispatcher({
                modal: initialGlobalState?.modal
            });

            globalStateDispatcher({
                notificationBar: {
                    ...globalState?.notificationBar,
                    open: true,
                    text: "Hearing has been updated successfully",
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

            var assignees = [];

            if (value && key === "files") {
                for (var i = 0; i < value.length; i++) {
                    formData.append("files[]", value[i]?.value);
                }
            } else {
                if (key === "assignees") {
                    for (var i = 0; i < value.length; i++) {
                        formData.append("assignees[]", JSON.stringify({ id: value[i], user_type: "AP" }));
                    }
                }
                else {
                    if (key === "startTime" || key === "postponedTime") {
                        value = formatTime(value);
                    }

                    formData.append(key, value);
                }
            }
        }

        for (var i = 0; i < listValues.core_assignees.length; i++) {
            formData.append("assignees[]", JSON.stringify({ id: listValues.core_assignees[i].value, user_type: "A4L" }));
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
                    label={t("litigation_case") + "*"}
                    options={legalCasesList}
                    optionsLabel="subject"
                    optionsLabelArray={['prefix', "subject"]}
                    stateKey="legal_case_id"
                    value={listValues.legal_case_id}
                    valueKey="id"
                    required
                    onChange={handleListChange}
                    textOnChange={getLegalCasesList}
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

                {
                    listValues.core_assignees.length > 0 ?
                        <label
                            style={{
                                marginBottom: 20,
                                display: 'block'
                            }}> {t("core_assignees")}: {listValues.core_assignees?.map((item, index) => {
                                return item.title
                            }).join(', ')}</label> :
                        ""
                }

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

                <APInlineDateTimePickersContainer
                    datePicker={
                        <APDatePicker
                            className="hearing-start-date"
                            label={t("postponed_until")}
                            stateKey="postponedDate"
                            format="yyyy-MM-dd"
                            value={formData.postponedDate}
                            handleChange={handleDatePickerChange}
                        />
                    }
                    timePicker={
                        <APTimePicker
                            ampm={false}
                            label="HH:MM"
                            stateKey="postponedTime"
                            value={formData.postponedTime}
                            handleChange={handleDatePickerChange}
                        />
                    }
                />

                {
                    showAddNewHearing ?
                        (
                            <APCheckboxBtn
                                disabled={disableAddNewHearing}
                                label={t("add_new_hearing")}
                                stateKey="addNewHearing"
                                color="primary"
                                value={formData.addNewHearing}
                                handleChange={handleToggleObjectChange}
                            />
                        )
                        :
                        null
                }

                <APTextFieldInput
                    label={t("reasons_of_postponement")}
                    stateKey="reasons_of_postponement"
                    rows={3}
                    multiline={true}
                    value={formData.reasons_of_postponement}
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
