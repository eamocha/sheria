import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './JudgementForm.scss';

import {
    Context,
    initialGlobalState
} from '../../../../Store';

import {
    FORMS_NAMES,
} from '../../../../Constants';

import DateFnsUtils from '@date-io/date-fns';

import { MuiPickersUtilsProvider } from '@material-ui/pickers';

import {
    APTextFieldInput,
    APAutocompleteList,
    APDatePicker,
} from './../../../common/APForm/APForm';

import {
    loadListWithLanguages,
    formatDate,
    buildErrorMessages,
    formatTime,
    isFunction,
    getValueFromLanguage,
} from '../../../../APHelpers';

import {
    makeStyles
} from '@material-ui/core';

import { isDate } from 'moment';

import AdvisorUser from '../../../../api/AdvisorUser';

import MiscList from '../../../../api/MiscList';

import LegalCase from '../../../../api/LegalCase';

import Hearing from './../../../../api/Hearing';

import { useTranslation } from 'react-i18next';

import { getActiveLanguageId } from '../../../../i18n';

export default React.memo((props) => {
    const formId = FORMS_NAMES.hearingJudgementForm;

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [hearing, setHearing] = useState(globalState?.modal?.form?.data?.hearing);

    const [stageStatusesList, setStageStatusesList] = useState([]);

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
        judged: 'yes',
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
            bindStageStatusesList(response?.data?.data?.stageStatuses);
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


    const bindStageStatusesList = (data) => {
        let result = loadListWithLanguages(data, 'stage_status_languages', 'name', 'id');

        setStageStatusesList(result?.options);
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
            [state]: date === null ? null : time ? (isDate(date) ? date : null) : formatDate(date)
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
                globalState.modal.form.submitCallback();
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

    return (
        <MuiPickersUtilsProvider
            utils={DateFnsUtils}
        >
            <form
                id={formId}
                onSubmit={(e) => submit(e)}
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

            </form>
        </MuiPickersUtilsProvider>
    );
});
