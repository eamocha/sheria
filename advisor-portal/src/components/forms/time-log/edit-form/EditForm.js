import React, {
    useContext,
    useEffect,
    useState
} from 'react';

import './EditForm.scss';

import APAutocompleteList from './../../../common/APForm/APAutocompleteList/APAutocompleteList';

import AdvisorUser from './../../../../api/AdvisorUser';

import {
    Context,
    initialGlobalState
} from './../../../../Store';

import {
    formatDate,
    loadListWithLanguages,
    loadUsersList,
    isFunction,
    getAdvisorUserFullName,
    getValueFromLanguage
} from './../../../../APHelpers';

import {
    Box,
    FormControl,
    FormControlLabel,
    FormGroup,
    Grid,
    Radio,
    RadioGroup,
    Tooltip
} from '@material-ui/core';

import LegalCase from '../../../../api/LegalCase';

import MiscList from '../../../../api/MiscList';

import {
    KeyboardDatePicker,
    MuiPickersUtilsProvider
} from '@material-ui/pickers';

import APTextFieldInput from './../../../common/APForm/APTextFieldInput/APTextFieldInput';

import DateFnsUtils from '@date-io/date-fns';

import AdvisorTimeLog from './../../../../api/AdvisorTimeLog';

import AdvisorTask from './../../../../api/AdvisorTask';

import { isValid } from 'date-fns';

import { DEFAULT_AUTOCOMPLETE_PAGE_SIZE, FORMS_NAMES } from './../../../../Constants';

import { useTranslation } from 'react-i18next';
import { getActiveLanguageId } from '../../../../i18n';

import InfoOutlinedIcon from '@material-ui/icons/InfoOutlined';
import { TextValidator, ValidatorForm } from 'react-material-ui-form-validator';

export default React.memo((props) => {
    const [t] = useTranslation();
    const formId = FORMS_NAMES.advisorTimeLogEditForm;

    const logTimeOnOptions = {
        legalCase: {
            label: t("matter"),
            value: 'legalCase'
        },
        advisorTask: {
            label: t("task"),
            value: 'advisorTask'
        }
    };

    const advisorTimeLogStatusOptions = {
        billable: {
            label: t("billable"),
            value: 'billable'
        },
        internal: {
            label: t("non_billable"),
            value: 'internal'
        }
    };

    const [globalState, globalStateDispatcher] = useContext(Context);

    const [timeLog, setTimeLog] = useState(globalState?.modal?.form?.data?.timeLog);

    const [formData, setFormData] = useState({
        advisor_id: timeLog?.advisor_id ?? '',
        legal_case_id: timeLog?.legal_case_id ?? '',
        advisor_task_id: timeLog?.advisor_task_id ?? '',
        timeStatus: timeLog?.timeStatus ?? '',
        time_type_id: timeLog?.time_type_id ?? '',
        logDate: timeLog?.logDate ?? null,
        effectiveEffort: timeLog?.effectiveEffort ?? '',
        comments: timeLog?.comments ?? '',
    });

    const [logTimeOn, setLogTimeOn] = useState(timeLog?.legal_case ? logTimeOnOptions.legalCase.value : logTimeOnOptions.advisorTask.value);

    const [advisorsList, setAdvisorsList] = useState([]);
    const [legalCasesList, setLegalCasesList] = useState([]);
    const [advisorTasksList, setAdvisorTasksList] = useState([]);
    const [timeTypesList, setTimeTypesList] = useState([]);

    const [listValues, listValuesDispatcher] = useState({
        advisor_id: {
            title: timeLog?.advisor_user ? getAdvisorUserFullName(timeLog?.advisor_user) : '',
            value: timeLog?.advisor_id ?? ''
        },
        legal_case_id: {
            subject: timeLog?.legal_case?.prefix + ' - ' + timeLog?.legal_case?.subject ?? '',
            value: timeLog?.legal_case_id ?? ''
        },
        advisor_task_id: {
            prefix: timeLog?.advisor_task?.prefix ?? '',
            value: timeLog?.advisor_task_id ?? ''
        },
        time_type_id: {
            title: timeLog?.time_type ? getValueFromLanguage(timeLog?.time_type, 'time_type_languages', getActiveLanguageId()) : '',
            value: timeLog?.time_type_id ?? ''
        }
    });

    useEffect(() => {

        ValidatorForm.addValidationRule('isRegexMatch', (value) => {
            if (value.charAt(0) == '-') {
                return false;
            }
            if (value.match(/^([0-9]+h?|[0-9]+h[0-9]m|[0-9]+h[0-9]+m|^[0-9]m|[0-9]+:[0-5]*[0-9]|[0-9]+m?|^[+-]?((\d+(\.\d*)?)|(\.\d+))h?$)$/i)) {
                return true;
            }
            return false;
        });

        loadListsData();

        if (!timeLog?.legal_case_id)
            loadLegalCasesList(null, { page: 1, pageSize: DEFAULT_AUTOCOMPLETE_PAGE_SIZE });

        if (!timeLog?.advisor_task_id)
            loadAdvisorTasksList(null, { page: 1, pageSize: DEFAULT_AUTOCOMPLETE_PAGE_SIZE });
    }, []);

    const loadListsData = () => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        AdvisorUser.getList().then((response) => {
            let data = loadUsersList(response?.data?.data);

            setAdvisorsList(data);

            return MiscList.getList({
                lists: [
                    "timeTypes"
                ]
            });
        }).then((response) => {

            loadTimeTypesList(response?.data?.data?.timeTypes);
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

    const handleLegalCaseListChange = (state, stateValue, defaultValues, multipleSelection, defaultValuesWithMultipleSelection, changeDefaultValues) => {
        setFormData(prevState => ({
            ...prevState,
            [state]: stateValue,
            advisor_task_id: ''
        }));

        if (changeDefaultValues) {
            listValuesDispatcher(prevState => ({
                ...prevState,
                [state]: multipleSelection ? defaultValuesWithMultipleSelection : defaultValues
            }));
        }
    }

    const handleTaskListChange = (state, stateValue, defaultValues, multipleSelection, defaultValuesWithMultipleSelection, changeDefaultValues) => {
        setFormData(prevState => ({
            ...prevState,
            [state]: stateValue,
            legal_case_id: ''
        }));

        if (changeDefaultValues) {
            listValuesDispatcher(prevState => ({
                ...prevState,
                [state]: multipleSelection ? defaultValuesWithMultipleSelection : defaultValues
            }));
        }
    }

    const handleDatePickerChange = (state, date, isTime = false) => {

        setFormData(prevState => ({
            ...prevState,
            [state]: date === null ? null : isTime ? (isValid(date) ? date : null) : formatDate(date)
        }));
    }

    const loadTimeTypesList = (data) => {
        let results = loadListWithLanguages(data, 'time_type_languages', 'name', 'id');

        setTimeTypesList(results?.options);

        /**
         * Here we are setting the first task type as default value
         * also the formData.time_type_id should be updated
         */
        listValuesDispatcher(prevState => ({
            ...prevState,
            time_type_id: results?.defaultItem
        }));

        setFormData(prevState => ({
            ...prevState,
            time_type_id: results?.defaultItemValue
        }));
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


    const getAdvisorTasksList = (e) => {
        e.persist();

        let value = e.target.value;

        if (value.length == 0) {
            loadAdvisorTasksList(null, { page: 1, pageSize: DEFAULT_AUTOCOMPLETE_PAGE_SIZE });
        }
        if (value.length >= 2) {
            loadAdvisorTasksList(value);
        }
    }

    const loadAdvisorTasksList = (filterValue = null, pagination = null) => {
        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        let filters = {}

        if (filterValue) {
            filters['taskPrefix'] =
                { "value": filterValue }
        }

        AdvisorTask.getList(filters, [], pagination).then((response) => {

            if (pagination)
                setAdvisorTasksList(response?.data?.data?.data);
            else
                setAdvisorTasksList(response?.data?.data);

        }).catch((error) => {

        }).finally(() => {

            globalStateDispatcher({
                globalLoader: initialGlobalState?.globalLoader
            });
        });
    }
    const submit = (e) => {
        e.preventDefault();

        globalStateDispatcher({
            globalLoader: {
                ...globalState?.globalLoader,
                open: true
            }
        });

        let requestData = prepareRequestData();

        AdvisorTimeLog.update(timeLog?.id, requestData).then((response) => {

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
                    text: "Time Log has been updated successfully",
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
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: false
                }
            });
        });
    }

    const prepareRequestData = () => {
        let result = {};

        for (let [key, value] of Object.entries(formData)) {
            if (value !== null && value !== '') {
                result[key] = value;
            }
        }

        return result;
    }

    return (
        <MuiPickersUtilsProvider
            utils={DateFnsUtils}
        >
            <ValidatorForm
                id={formId}
                onSubmit={(e) => submit(e)}
            >
                <APAutocompleteList
                    label={t("user")}
                    required
                    textRequired={true}
                    options={advisorsList}
                    optionsLabel="title"
                    stateKey="advisor_id"
                    value={listValues.advisor_id}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <FormGroup
                    className="ap-radio-group"
                >
                    <FormControl
                        component="fieldset"
                    >
                        <RadioGroup
                            name="time-log-on-radio-group"
                            value={logTimeOn}
                            row
                        >
                            <FormControlLabel
                                value={logTimeOnOptions.legalCase.value}
                                control={
                                    <Radio
                                        checked={logTimeOn === logTimeOnOptions.legalCase.value}
                                    />
                                }
                                label={logTimeOnOptions.legalCase.label}
                                onChange={() => setLogTimeOn(logTimeOnOptions.legalCase.value)}
                            />
                            <FormControlLabel
                                value={logTimeOnOptions.advisorTask.value}
                                control={
                                    <Radio
                                        checked={logTimeOn === logTimeOnOptions.advisorTask.value}
                                    />
                                }
                                label={logTimeOnOptions.advisorTask.label}
                                onChange={() => setLogTimeOn(logTimeOnOptions.advisorTask.value)}
                            />
                        </RadioGroup>
                    </FormControl>
                </FormGroup>
                {
                    logTimeOn === logTimeOnOptions.legalCase.value ?
                        (
                            <APAutocompleteList
                                label={t("matter")}
                                required
                                textRequired={true}
                                options={legalCasesList}
                                optionsLabel="subject"
                                optionsLabelArray={['prefix', "subject"]}
                                stateKey="legal_case_id"
                                value={listValues.legal_case_id}
                                valueKey="id"
                                onChange={handleLegalCaseListChange}
                                textOnChange={getLegalCasesList}
                            />
                        )
                        :
                        (
                            <APAutocompleteList
                                label={t("task") + ": e.g. T20"}
                                required
                                textRequired={true}
                                options={advisorTasksList}
                                optionsLabel="prefix"
                                stateKey="advisor_task_id"
                                value={listValues.advisor_task_id}
                                valueKey="id"
                                onChange={handleTaskListChange}
                                textOnChange={getAdvisorTasksList}
                                show={logTimeOn !== logTimeOnOptions.legalCase.value}
                                style={{
                                    display: logTimeOn === logTimeOnOptions.legalCase.value ? 'none' : 'block'
                                }}
                            />
                        )
                }
                <FormGroup
                    className="ap-radio-group"
                >
                    <FormControl
                        component="fieldset"
                    >
                        <RadioGroup
                            name="time-log-status-radio-group"
                            value={formData.timeStatus}
                            onChange={(e) => handleObjectChange(e, 'timeStatus')}
                            row
                        >
                            <FormControlLabel
                                value={advisorTimeLogStatusOptions.billable.value}
                                control={
                                    <Radio
                                        checked={formData.timeStatus === advisorTimeLogStatusOptions.billable.value}
                                    />
                                }
                                label={advisorTimeLogStatusOptions.billable.label}
                            />
                            <FormControlLabel
                                value={advisorTimeLogStatusOptions.internal.value}
                                control={
                                    <Radio
                                        checked={formData.timeStatus === advisorTimeLogStatusOptions.internal.value}
                                    />
                                }
                                label={advisorTimeLogStatusOptions.internal.label}
                            />
                        </RadioGroup>
                    </FormControl>
                </FormGroup>
                <APAutocompleteList
                    label={t("time_spent_on")}
                    options={timeTypesList}
                    optionsLabel="title"
                    stateKey="time_type_id"
                    value={listValues.time_type_id}
                    valueKey="value"
                    onChange={handleListChange}
                />
                <FormGroup>
                    <FormControl>
                        <KeyboardDatePicker
                            label={t("date")}
                            required
                            inputVariant="outlined"
                            autoOk
                            variant="inline"
                            format="yyyy-MM-dd"
                            value={formData.logDate}
                            onChange={(date) => handleDatePickerChange('logDate', date)}
                            inputadornmentprops={{ position: "end" }}
                        />
                    </FormControl>
                </FormGroup>

                <Grid container>
                    <Grid
                        item
                        xs={11}
                    >
                        <FormGroup>
                            <FormControl>
                                <TextValidator
                                    label={t("effective_effort")}
                                    stateKey="effectiveEffort"
                                    variant="outlined"
                                    required
                                    value={formData.effectiveEffort}
                                    validators={['isRegexMatch', 'required']}
                                    errorMessages={[t("effective_effort_format")]}
                                    onChange={(e) => handleObjectChange(e, 'effectiveEffort')}
                                />
                            </FormControl>
                        </FormGroup>
                    </Grid>
                    <Grid
                        item
                        xs={1}
                    >
                        <Box
                            display="flex"
                            flexDirection="column"
                            justifyContent="center"
                            alignItems="center"
                            minHeight="50px"
                            minWidth="100%"
                        >
                            <Tooltip title={t("effective_effort_exmaple")}>
                                <InfoOutlinedIcon />
                            </Tooltip>
                        </Box>
                    </Grid>
                </Grid>

                <APTextFieldInput
                    label={t("comments")}
                    stateKey="comments"
                    rows={5}
                    multiline={true}
                    value={formData.comments}
                    handleChange={handleObjectChange}
                />
            </ValidatorForm>
        </MuiPickersUtilsProvider>
    );
});
